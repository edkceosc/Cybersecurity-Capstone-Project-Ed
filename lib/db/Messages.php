<?php


namespace lib\db;

use Exception;
use lib\model\Message;
use lib\model\MessageView;
use lib\service\SymmetricEncryption;
use PDO;
use PDOStatement;

/**
 * Access to database table 'messages'
 */
class Messages
{

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var SymmetricEncryption
     */
    private $encryption;

    /**
     * Messages constructor.
     *
     * @throws Exception
     */
    public function __construct(PDO $pdo, SymmetricEncryption $encryption)
    {
        $this->pdo = $pdo;
        $this->encryption = $encryption;
    }

    /**
     * Returns the message with the given message id. If no such message exists, this function returns null.
     *
     * @param int $messageId
     *
     * @return Message|null
     */
    public function loadMessageById(int $messageId): ?Message
    {

        $sql = 'SELECT id, sender, recipient, title, message, date, read FROM messages WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $messageId, PDO::PARAM_INT);
        $stmt->execute();
        $message = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$message) {
            // not found
            return null;
        }
        return $this->createMessageEntityFromDbRecord($message);
    }


    /**
     * Returns messages sent by the given senderId.
     *
     * @param int $senderId
     *
     * @return Message[]
     */
    public function loadMessagesBySender(int $senderId): array
    {
        $sql = 'SELECT id, sender, recipient, title, message, date, read FROM messages WHERE sender = ? ORDER BY date desc';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $senderId, PDO::PARAM_INT);
        $messages = [];
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $messages[] = $this->createMessageEntityFromDbRecord($row);
            }
        }
        return $messages;
    }

    /**
     * @param int $senderId
     *
     * @return MessageView[]
     * @throws Exception
     */
    public function loadMessageViewsBySender(int $senderId): array
    {
        $sql = 'SELECT m.id,
                m.sender, s.username AS sender_name,
                m.recipient, r.username AS recipient_name,
                m.title, m.message, m.date, m.read FROM messages AS m 
                INNER JOIN users AS r ON r.id = m.recipient
                INNER JOIN users AS s ON s.id = m.sender
                WHERE sender = ? ORDER BY date desc';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $senderId, PDO::PARAM_INT);
        return $this->fetchListOfMessageViews($stmt);
    }

    /**
     * Returns messages whith the given recipient.
     *
     * @param int $recipientId
     *
     * @return Message[]
     */
    public function loadMessagesByRecipient(int $recipientId): array
    {
        $sql = 'SELECT id, sender, recipient, title, message, date, read FROM messages WHERE recipient = ? ORDER BY date desc';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $recipientId, PDO::PARAM_INT);
        $messages = [];
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $messages[] = $this->createMessageEntityFromDbRecord($row);
            }
        }
        return $messages;
    }

    /**
     * @param int $recipientId
     *
     * @return MessageView[]
     * @throws Exception
     */
    public function loadMessageViewsByRecipient(int $recipientId): array
    {
        $sql = 'SELECT m.id,
                m.sender, s.username AS sender_name,
                m.recipient, r.username AS recipient_name,
                m.title, m.message, m.date, m.read FROM messages AS m 
                INNER JOIN users AS r ON r.id = m.recipient
                INNER JOIN users AS s ON s.id = m.sender
                WHERE recipient = ?
                ORDER BY read, date desc';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $recipientId, PDO::PARAM_INT);
        return $this->fetchListOfMessageViews($stmt);
    }

    public function insertNewMessage(int $sender, int $recipient, string $title, string $message, string $date, bool $read)
    {

        $sql = 'INSERT INTO messages (sender, recipient, title, message, date, read) VALUES (?, ?, ?, ?, ?, ?)';


        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->bindValue(1, $sender);
            $stmt->bindValue(2, $recipient);
            $stmt->bindValue(3, $title);
            $stmt->bindValue(4, $message);
            $stmt->bindValue(5, $date);
            $stmt->bindValue(6, $read, PDO::PARAM_BOOL);
            $stmt->execute();
            $cnt = $stmt->rowCount();
            if ($cnt < 1) {
                return false;
            }
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Marks the message as read, if and only if it was sent to the recipient $recipientId
     *
     * @param int $msgId The message that should be marked as 'read'
     * @param int $recipientId The recipient of the message.
     *
     * @return bool Whether or not the message was updated.
     */
    public function markAsRead(int $msgId, int $recipientId): bool
    {
        $sql = 'UPDATE messages SET read = true WHERE id = ? AND recipient = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $msgId, PDO::PARAM_INT);
        $stmt->bindValue(2, $recipientId, PDO::PARAM_INT);
        $stmt->execute();
        $updatedCnt = $stmt->rowCount();
        if ($updatedCnt < 1) {
            // no rows updated: this means that either the message does not exist, or the message was not sent
            // to $recipientId. (Or the message was already on state 'read = true')
            return false;
        }
        return true;
    }

    private function createMessageEntityFromDbRecord(array $dbRecord): Message
    {
        return new Message(
            intval($dbRecord['id']),
            intval($dbRecord['sender']),
            intval($dbRecord['recipient']),
            $dbRecord['title'],
            $dbRecord['message'],
            $dbRecord['date'],
            boolval($dbRecord['read'])

        );
    }

    /**
     * @param PDOStatement $stmt
     *
     * @return MessageView[]
     * @throws Exception
     */
    private function fetchListOfMessageViews(PDOStatement $stmt): array
    {
        $messages = [];
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $msg = $this->createMessageEntityFromDbRecord($row);
                $messages[] = new MessageView(
                    $msg,
                    $row['sender_name'],
                    $row['recipient_name'],
                    $this->encryption->decrypt($msg->getTitle()),
                    $this->encryption->decrypt($msg->getMessage())
                );
            }
        }
        return $messages;
    }
}