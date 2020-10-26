<?php


namespace tests\db\lib\db;


use lib\db\Connection;
use lib\db\Users;
use PHPUnit\Framework\TestCase;

class UsersTest extends TestCase
{

    /**
     * @before
     * @throws \Exception
     */
    public function clearUserTable()
    {
        $pdo = Connection::get_db_pdo();

        // need to delete messages first, because of foreign key constraints.
        $stmt = $pdo->prepare('DELETE FROM messages');
        $stmt->execute();

        $stmt = $pdo->prepare('DELETE FROM users;');
        $stmt->execute();
    }

    /**
     * @throws \Exception
     */
    public function test_registerNewUser_creates_a_user()
    {
        $pdo = Connection::get_db_pdo();
        $users = new Users($pdo);
        $ok = $users->registerNewUser('peter', 'peter@example.com', 'very-secret');
        $this->assertTrue($ok, 'Failed to create new user.');

        // verify registered user
        $peter = $users->loadUserByUsername('peter');
        $this->assertNotNull($peter);

        // make sure that the password was hashed
        $this->assertNotEquals('very-secret', $peter->getPassword(), 'The password loaded from DB should be hashed!');
    }

}