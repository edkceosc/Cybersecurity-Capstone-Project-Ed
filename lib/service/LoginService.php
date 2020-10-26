<?php


namespace lib\service;

use Exception;
use lib\db\Users;
use lib\model\User;
use lib\utils\Clock;
use lib\utils\ClockImpl;

class LoginService
{

    /**
     * How long a user gets locked after some failed login attempts
     *
     * @var int
     */
    private $lockDurationSeconds;

    /**
     * @var int
     */
    private $maxNumOfLoginAttempts;

    /**
     * @var Users
     */
    private $dbUsers;

    /**
     * @var Clock
     */
    private $clock;

    /**
     * @var SessionManager
     */
    private $sessionMgr;

    public function __construct(
        Users $dbUsers,
        ?SessionManager $sessionMgr = null,
        ?Clock $clock = null,
        int $lockDurationSeconds = 120,
        int $maxNumOfLoginAttempts = 5
    )
    {
        $this->dbUsers = $dbUsers;
        $this->clock = $clock ?? new ClockImpl();
        $this->sessionMgr = $sessionMgr ?? new SessionManagerPhp();
        $this->lockDurationSeconds = $lockDurationSeconds;
        $this->maxNumOfLoginAttempts = $maxNumOfLoginAttempts;
    }

    /**
     * This function loads the user with the given $username, then validates the $cleartextPassword
     * against the password hash
     * stored for that user. If the password matches, the user is returned.
     * Otherwise the login_attempts counter will be incremented for that user's IP address.
     * If the counter exceeds the max number of allowed login attempts, then the user gets locked for some time.
     *
     * @param string $username
     * @param string $cleartextPassword
     * @param string|null $requestIp
     *
     * @return LoginResult
     * @throws Exception
     */
    public function tryLogin(string $username, string $cleartextPassword, ?string $requestIp): LoginResult
    {
        $user = $this->dbUsers->loadUserByUsername($username);
        if ($user === null) {
            // User does not exist.
            // To hide the information, whether or not the user exists, do a password_hash,
            // to have similar response_time compared to the other branch.
            password_hash($cleartextPassword, PASSWORD_DEFAULT); // ignore result
            return LoginResult::createWrongCrendentialsResult();
        }
        if ($this->isUserLocked($user, $requestIp)) {
            return LoginResult::createUserLockedResult();
        }
        $pwHash = $user->getPassword();
        $ok = password_verify($cleartextPassword, $pwHash);
        if ($ok) {
            $this->dbUsers->resetLoginAttemptsCounter($user->getId());
            // best practice is to give the session a new id after successful login
            $this->sessionMgr->regenerateId();
            $this->sessionMgr->setAuthenticatedUser($user);
            return LoginResult::createSuccessfulResult($user);
        }

        $loginAttempts = $user->getLoginAttempts() + 1;
        if ($loginAttempts >= $this->maxNumOfLoginAttempts) {
            // Note: even if the user gets locked now, *this* request was denied
            // because of wrong credentials, not locked user.
            // (The user sees the "you're locked out" message after the next attempt)
            if ($requestIp) { // note: without ip, it makes no sense to lock the user...
                $this->dbUsers->lockUser($user->getId(), $requestIp);
            }
        } else {
            if ($requestIp) {
                $this->dbUsers->incrementLoginAttempts($user->getId(), $requestIp);
            }
        }
        return LoginResult::createWrongCrendentialsResult();
    }



    /**
     * Checks if a given user is locked or not.
     *
     * See issue #12.
     *
     * @param User $user The user that should be checked. Can be loaded via `loadUserByUsername`
     *                   or similar methods.
     * @param string | null $requestIpAddr       The client ip address of the http request.
     *
     * @return bool
     */
    public function isUserLocked(User $user, ?string $requestIpAddr)
    {
        $userLockedSince = $user->getLockedTime();
        if ($userLockedSince === null) {
            // user is not locked
            return false;
        }
        // assuming, the user was locked in the past, $diffSeconds will be negative
        $diffSeconds = $this->clock->diffSecondsToCurrentTime($userLockedSince);
        if ($diffSeconds + $this->lockDurationSeconds <= 0) {
            // user was locked, but lock duration has expired...
            return false;
        }
        // User is locked. But maybe only locked for the attacker's IP; and now,
        // the real user wants to access the account.
        // We allows this, even if it is a trade-off.
        // It means that an attacker can simply (in larger scale it's probably not so simple) change his IP
        // and then retry.
        // On the other hand, considering the IP prevents the attacker from intentionally locking accounts
        // (which would be a kind of "denial of service").
        $lockedFor = $user->getLoginIp();
        if ($lockedFor === null) {
            Logger::getInstance()->logMessage(
                Logger::LEVEL_WARN,
                'Locked account, but no IP is stored. Allow current request. User id: ' . $user->getId()
            );
        } else {
            if ($requestIpAddr !== $lockedFor) {
                // Locked only for someone else... log a message, since this could indicate malicious behavior.
                Logger::getInstance()->logMessage(
                    Logger::LEVEL_INFO,
                    'Account was locked for other IP. Allow current request'
                );
                return false;
            }
        }
        return true;
    }

}