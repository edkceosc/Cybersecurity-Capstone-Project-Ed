<?php


namespace lib\db;

use DateTime;
use DateTimeZone;
use Exception;
use lib\model\User;
use lib\utils\Clock;
use lib\utils\ClockImpl;
use PDO;
use PDOException;

/**
 * Access to database table 'users'
 */
class Users
{

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var Clock
     */
    private $clock;

    public function __construct(PDO $pdo, ?Clock $clock = null)
    {
        $this->clock = $clock ?? new ClockImpl();
        $this->pdo = $pdo;
    }

    /**
     * Returns the user with the given user id. If no such user exists, this function returns null.
     *
     * @param int $userId
     *
     * @return User|null
     */
    public function loadUserById(int $userId): ?User
    {
        $sql = 'SELECT id, username, email, password, login_ip, login_attempts, locked_time FROM users WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            // not found
            return null;
        }
        return $this->createUserEntityFromDbRecord($user);
    }

    /**
     * Returns the user with the given username. If no such user exists, this function returns null.
     *
     * Note that this can also be used to check if a username is "free", e.g. during registration.
     *
     * @param string $username
     *
     * @return User|null
     */
    public function loadUserByUsername(string $username): ?User
    {
        $sql = 'SELECT id, username, email, password, login_ip, login_attempts, locked_time FROM users WHERE username = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            // not found
            return null;
        }
        return $this->createUserEntityFromDbRecord($user);
    }

    /**
     * Returns the user with the given email. If no such user exists, this function returns null.
     *
     * Note that this can also be used to check if an email address is "free", e.g. during registration.
     *
     * @param string $email
     *
     * @return User|null
     */
    public function loadUserByEmail(string $email): ?User
    {
        $sql = 'SELECT id, username, email, password, login_ip, login_attempts, locked_time FROM users WHERE email = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            // not found
            return null;
        }
        return $this->createUserEntityFromDbRecord($user);
    }

    /**
     * Locks the user (sets 'locked_time' to current timestamp).
     * The 'login_attempts' counter will be reset to 0.
     *
     * @param int    $userId
     *
     * @param string $requestIp
     *
     * @throws Exception
     */
    public function lockUser(int $userId, string $requestIp)
    {
        $sql = 'UPDATE users SET login_attempts = 0, locked_time = ?, login_ip = ? WHERE id = ?';
        // Note: we generate the time via PHP, not SQL.
        // Times may differ (slightly) between SQL and PHP server.
        // When we decide if a user is allowed to login again, we use PHP to get the current time.
        // Because of this, it's more robust if we also use PHP when storing the 'locked_time'.
        $now = new DateTime();
        $now->setTimestamp($this->clock->getCurrentTimestamp());
        $now->setTimezone(new DateTimeZone('UTC'));
        $nowStr = $now->format(DbUtils::SQL_DATE_TIME_FORMAT);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$nowStr, $requestIp, $userId]);
        $updatedRows = $stmt->rowCount();
        if ($updatedRows !== 1) {
            throw new Exception(
                'Unexpected rowCount: ' . $updatedRows . ' were affected by the lockUser update statement'
            );
        }
    }

    public function registerNewUser(string $username, string $email, string $cleartextPassword)
    {
        $sql = 'INSERT INTO users (username, email, password) VALUES (?, ?, ?)';

        // Includes a salt.
        // PASSWORD_DEFAULT is currently bcrypt.
        // See https://www.php.net/manual/en/function.password-hash.php for details.
        $pwHash = password_hash($cleartextPassword, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([$username, $email, $pwHash]);
            $cnt = $stmt->rowCount();
            if ($cnt < 1) {
                // probably username or email UNIQUE constraint violated?
                return false;
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * @param int    $userId
     *
     * @param string $requestIp
     *
     * @throws Exception
     */
    public function incrementLoginAttempts(int $userId, string $requestIp)
    {
        // This statement *increments* the login_attempts counter ONLY if the login_ip matches the $requestIp.
        // If the login_ip differs from $requestIp, we assume the previous client gave up, and use login_ip field
        // for a new counter for the $requestIp... (starting at 1)
        $sql = 'UPDATE users SET login_attempts = login_attempts + 1 WHERE id = ? AND login_ip = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $requestIp]);
        $updatedRows = $stmt->rowCount();
        if ($updatedRows === 0) {
            // no match, start new counter for $requestIp
            $sql = 'UPDATE users SET login_attempts = 1, login_ip = ? WHERE id = ?';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$requestIp, $userId]);
            $updatedRows = $stmt->rowCount();
        }
        if ($updatedRows !== 1) {
            throw new Exception(
                'Unexpected rowCount: ' . $updatedRows . ' were affected by the lockUser update statement'
            );
        }
    }

    /**
     * Reset the login attempt counter.
     *
     * This can be called after a successful login.
     *
     * @param int $userId
     */
    public function resetLoginAttemptsCounter(int $userId)
    {
        $sql = 'UPDATE users SET login_attempts = 0, locked_time = NULL, login_ip = NULL WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
    }

    private function createUserEntityFromDbRecord(array $dbRecord): User
    {
        $lockedTimeStr = $dbRecord['locked_time'];
        if ($lockedTimeStr !== null) {
            $lockedTime = DateTime::createFromFormat(
                DbUtils::SQL_DATE_TIME_FORMAT,
                $lockedTimeStr,
                new DateTimeZone('UTC')
            );
        } else {
            $lockedTime = null;
        }
        return new User(
            intval($dbRecord['id']),
            $dbRecord['username'],
            $dbRecord['email'],
            $dbRecord['password'],
            $dbRecord['login_ip'],
            intval($dbRecord['login_attempts']),
            $lockedTime
        );
    }
}
