<?php


namespace tests\db\lib\db;


use Exception;
use lib\db\Connection;
use lib\db\Messages;
use lib\db\Users;
use lib\model\Message;
use lib\model\User;
use lib\service\SymmetricEncryption;
use PHPUnit\Framework\TestCase;

class MessagesTest extends TestCase
{

    /**
     * @var User
     */
    private $sender;

    /**
     * @var User
     */
    private $recipient;

    /**
     * The system under test.
     *
     * @var Messages
     */
    private $messages;

    /**
     * @before
     * @throws Exception
     */
    public function setupDatabase()
    {
        $pdo = Connection::get_db_pdo();

        // need to delete messages first, because of foreign key constraints.
        $stmt = $pdo->prepare('DELETE FROM messages');
        $stmt->execute();

        // clear users
        $stmt = $pdo->prepare('DELETE FROM users;');
        $stmt->execute();

        // create two dummy users, as sender/recipient
        $users = new Users($pdo);
        $users->registerNewUser('peter', 'peter@example.com', 'very-secret');
        $users->registerNewUser('lisa', 'lisa@example.com', 'very-secret');

        $this->sender = $users->loadUserByUsername('peter');
        $this->recipient = $users->loadUserByUsername('lisa');

        $this->messages = new Messages($pdo, $this->createMock(SymmetricEncryption::class));
    }

    public function test_insert_new_message_works()
    {
        $ok = $this->messages->insertNewMessage(
            $this->sender->getId(),
            $this->recipient->getId(),
            'Hi joe',
            'test-message',
            '2020-03-03 10:10:10',
            false
        );
        $this->assertTrue($ok);
    }

    public function test_loadMessageById_returns_the_message()
    {
        $ok = $this->messages->insertNewMessage(
            $this->sender->getId(),
            $this->recipient->getId(),
            'Hi joe',
            'test-message',
            '2020-03-03 10:10:10',
            false
        );
        $this->assertTrue($ok);

        // This must be the only message in the db, since we cleared the table in `setupDatabase`.
        // Find out the message id
        $pdo = Connection::get_db_pdo();
        $stmt = $pdo->prepare('SELECT id FROM messages');
        $stmt->execute();

        $msgId = $stmt->fetchColumn(0);

        $this->assertNotNull($msgId);
        $this->assertNotFalse($msgId);

        $message = $this->messages->loadMessageById($msgId);
        $this->assertNotNull($message);
        $this->assertSame('Hi joe', $message->getTitle());
    }

    public function test_loadMessageById_returns_null_if_message_does_not_exist()
    {
        // messages table is empty at the beginning
        $message = $this->messages->loadMessageById(7);
        $this->assertNull($message);
    }

    public function test_loadMessagesBySender_returns_list_of_messages_with_matching_sender()
    {
        $this->insert3Messages();

        $result = $this->messages->loadMessagesBySender($this->sender->getId());
        $titles = array_map(function (Message $m) { return $m->getTitle(); }, $result);

        // expect result ordered by date descending; so message 3, then message 1
        $this->assertEquals(['message 3', 'message 1'], $titles);
    }

    public function test_loadMessagesByRecipient_returns_list_of_messages_with_matching_recipient()
    {
        $this->insert3Messages();

        $result = $this->messages->loadMessagesByRecipient($this->recipient->getId());
        $titles = array_map(function (Message $m) { return $m->getTitle(); }, $result);

        // expect result ordered by date descending; so message 3, then message 1
        $this->assertEquals(['message 3', 'message 1'], $titles);

        // check messages to 'sender'
        $msgToSender = $this->messages->loadMessagesByRecipient($this->sender->getId());
        $titles = array_map(function (Message $m) { return $m->getTitle(); }, $msgToSender);
        $this->assertEquals(['message 2'], $titles);
    }

    private function insert3Messages()
    {
        // message 1 from sender to recipient
        $ok = $this->messages->insertNewMessage(
            $this->sender->getId(),
            $this->recipient->getId(),
            'message 1',
            'test-message',
            '2020-03-03 10:10:10',
            false
        );
        $this->assertTrue($ok, 'failed inserting message 1');

        // message 2 has another sender ($this->recipient) !
        $ok = $this->messages->insertNewMessage(
            $this->recipient->getId(),
            $this->sender->getId(),
            'message 2',
            'test-message',
            '2020-03-03 10:11:10',
            false
        );
        $this->assertTrue($ok, 'failed inserting message 2');

        // message 3 is from sender to recipient again
        $ok = $this->messages->insertNewMessage(
            $this->sender->getId(),
            $this->recipient->getId(),
            'message 3',
            'test-message',
            '2020-03-03 10:12:10',
            false
        );
        $this->assertTrue($ok, 'failed inserting message 3');
    }

    public function test_markAsRead_sets_the_read_flag_of_the_given_message()
    {
        // message 1 from sender to recipient
        $ok = $this->messages->insertNewMessage(
            $this->sender->getId(),
            $this->recipient->getId(),
            'message 1',
            'test-message',
            '2020-03-03 10:10:10',
            false
        );
        $this->assertTrue($ok, 'failed inserting message 1');

        $msgs = $this->messages->loadMessagesBySender($this->sender->getId());

        $message = $msgs[0];
        $id = $message->getId();

        // the actual test: mark message as read
        $ok = $this->messages->markAsRead($id, $this->recipient->getId());
        $this->assertTrue($ok);
        $this->assertTrue($this->messages->loadMessageById($id)->isRead());
    }

    public function test_markAsRead_does_not_set_read_flag_if_recipient_does_not_match()
    {
        // message 1 from sender to recipient
        $ok = $this->messages->insertNewMessage(
            $this->sender->getId(),
            $this->recipient->getId(),
            'message 1',
            'test-message',
            '2020-03-03 10:10:10',
            false
        );
        $this->assertTrue($ok, 'failed inserting message 1');

        $msgs = $this->messages->loadMessagesBySender($this->sender->getId());

        $message = $msgs[0];
        $id = $message->getId();

        // the sender tries to mark the message as read (which is not allowed)
        $ok = $this->messages->markAsRead($id, $this->sender->getId());
        $this->assertFalse($ok);
        $this->assertFalse($this->messages->loadMessageById($id)->isRead());
    }
}
