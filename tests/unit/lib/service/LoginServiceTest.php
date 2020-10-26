<?php


namespace tests\unit\lib\service;


use DateTime;
use Exception;
use lib\db\Users;
use lib\model\User;
use lib\service\LoginService;
use PHPUnit\Framework\TestCase;
use tests\unit\lib\ClockStub;

class LoginServiceTest extends TestCase
{

    /**
     * If the user account has recently (within lock-duration time) been locked, because of
     * too many failed login attempts *from the same ip*, then `isUserLocked` should return true.
     *
     * @throws Exception
     */
    public function test_isUserLocked_returns_true_if_locking_duration_has_not_expired_yet_and_ip_matches()
    {
        $now = 100; // current time: 100 seconds after beginning of time

        $lockDuration = 60;

        $clock = new ClockStub($now);
        $usersDao = $this->createStub(Users::class);

        // system under test
        $sut = new LoginService($usersDao, null, $clock, $lockDuration);

        // the time when the user was locked.
        // Note: locked at time 50; plus 60 seconds lock duration, is > than 100 (current time).
        // So the user is still locked (for 10 more seconds...)
        $lockedTime = new DateTime();
        $lockedTime->setTimestamp(50);
        $userIp = '1.2.3.4';
        $user = new User(1, 'username', 'email@example.com', 'pass', $userIp, 0, $lockedTime);

        $this->assertTrue($sut->isUserLocked($user, $userIp), 'This user is expected to be locked');
    }

    /**
     * If the user was locked by an attackers IP that failed to authenticate, the actual user should  *not* be locked.
     */
    public function test_isUserLocked_returns_false_if_request_ip_does_not_match_stored_login_ip()
    {
        $now = 100;
        $lockDuration = 60;
        $clock = new ClockStub($now);
        $usersDao = $this->createStub(Users::class);

        $sut = new LoginService($usersDao, null, $clock, $lockDuration);

        $lockedTime = new DateTime();
        $lockedTime->setTimestamp(50);
        $attackerIp = '1.2.3.4';
        $user = new User(1, 'username', 'email@example.com', 'pass', $attackerIp, 0, $lockedTime);

        // differs from the ip stored in `User`, which is the ip that caused to account lock.
        $realUserIp = '1.2.1.9';
        $this->assertFalse($sut->isUserLocked($user, $realUserIp), 'The real user should not be locked.');
    }

    public function test_isUserLocked_returns_false_if_locking_period_has_expired()
    {
        $now = 200;
        $lockDuration = 60;
        $clock = new ClockStub($now);
        $usersDao = $this->createStub(Users::class);

        $sut = new LoginService($usersDao, null, $clock, $lockDuration);

        // lockedTime + lockDuration = 50 + 60 = 110 < 200 (current time)
        $lockedTime = new DateTime();
        $lockedTime->setTimestamp(50);
        $userIp = '1.2.3.4';
        $user = new User(1, 'username', 'email@example.com', 'pass', $userIp, 0, $lockedTime);

        $this->assertFalse($sut->isUserLocked($user, $userIp), 'Should not be locked, locking period has expired.');
    }
}