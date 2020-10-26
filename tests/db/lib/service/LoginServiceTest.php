<?php


namespace tests\db\lib\service;


use Exception;
use lib\db\Connection;
use lib\db\Users;
use lib\model\User;
use lib\service\LoginService;
use lib\service\SessionManager;
use lib\utils\ClockImpl;
use PDO;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use tests\unit\lib\ClockStub;

/**
 * Note: this is an integration test, testing how LoginService works together with the database and database code.
 * That's why we have two `LoginServiceTest` -- one for unit tests, and this one for integration testing.
 *
 * @package tests\db\lib\service
 */
class LoginServiceTest extends TestCase
{

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var LoginService
     */
    private $loginService;

    /**
     * @var MockObject|SessionManager
     */
    private $sessionMock;

    /**
     * @var Users
     */
    private $dbUsers;

    private $dummyUserName = 'peter';

    private $dummyUserPassword = 'very-secret';

    /**
     * @before
     * @throws Exception
     */
    public function setupUserTableAndServices()
    {
        $this->pdo = Connection::get_db_pdo();
        $stmt = $this->pdo->prepare('DELETE FROM users;');
        $stmt->execute();

        $clock = new ClockStub(50);
        $this->dbUsers = new Users($this->pdo, $clock);

        // we need to mock sessions, since PHP sessions are heavily tight to the web environment and cause problems
        // when used in our test environment.
        $this->sessionMock = $this->createMock(SessionManager::class);

        $maxLoginAttempts = 2;
        $this->loginService = new LoginService($this->dbUsers, $this->sessionMock, $clock, 120, $maxLoginAttempts);

        // create a dummy user
        $this->dbUsers->registerNewUser($this->dummyUserName, 'peter@example.com', $this->dummyUserPassword);
    }

    /**
     * @throws Exception
     */
    public function test_tryLogin_works_if_credentials_match()
    {
        // verify that the use has been set to the session
        $this->sessionMock->expects($this->once())
            ->method('setAuthenticatedUser')
            ->with($this->callback(function ($subject) {
                /* @var $subject User */
                return $subject->getUsername() === 'peter';
            }));

        $result = $this->loginService->tryLogin($this->dummyUserName, $this->dummyUserPassword, '1.2.3.4');
        $this->assertTrue($result->isSuccessful(), 'Expected login to be successful');
    }

    /**
     * @throws Exception
     */
    public function test_tryLogin_fails_if_password_is_wrong()
    {
        $this->sessionMock->expects($this->never())
            ->method('setAuthenticatedUser');

        $result = $this->loginService->tryLogin($this->dummyUserName, 'some-bad-password', '1.2.3.4');

        $this->assertFalse($result->isSuccessful(), 'Expected login to be successful');
        $this->assertTrue($result->isWrongCredentialsProvided());
    }

    /**
     * @throws Exception
     */
    public function test_tryLogin_locks_account_after_too_many_failed_attempts()
    {
        $this->sessionMock->expects($this->never())
            ->method('setAuthenticatedUser');

        // 1. wrong credentials
        $result = $this->loginService->tryLogin($this->dummyUserName, 'some-bad-password', '1.2.3.4');
        $this->assertTrue($result->isWrongCredentialsProvided());

        // 2. wrong credentials
        $result = $this->loginService->tryLogin($this->dummyUserName, 'some-bad-password', '1.2.3.4');
        $this->assertTrue($result->isWrongCredentialsProvided());

        // 3. the user is locked now. Even if the correct password is given, the login fails. Note that our 'clock' always returns the same time,
        // which simulates that the login attempts all occurred in the same second.
        $result = $this->loginService->tryLogin($this->dummyUserName, $this->dummyUserPassword, '1.2.3.4');
        $this->assertFalse($result->isSuccessful(), 'Expected login to be successful');
        $this->assertTrue($result->isLocked(), 'User is expected to be locked by now...');
    }

}