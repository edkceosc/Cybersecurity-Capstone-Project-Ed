<?php


namespace lib\model;


/**
 * Similar to Message, but prepared to be rendered in a "view".
 * For example, instead of sender / recipient id, the username is used.
 */
class MessageView
{

    /**
     * @var Message
     */
    private $message;

    /**
     * @var string
     */
    private $senderName;

    /**
     * @var string
     */
    private $recipientName;

    /**
     * @var string
     */
    private $decryptedTitle;

    /**
     * @var string
     */
    private $decryptedMessageBody;

    /**
     * MessageView constructor.
     *
     * @param Message $message
     * @param string  $senderName
     * @param string  $recipientName
     * @param string  $decryptedTitle
     * @param string  $decryptedMessageBody
     */
    public function __construct(
        Message $message,
        string $senderName,
        string $recipientName,
        string $decryptedTitle,
        string $decryptedMessageBody
    ) {
        $this->message = $message;
        $this->senderName = $senderName;
        $this->recipientName = $recipientName;
        $this->decryptedTitle = $decryptedTitle;
        $this->decryptedMessageBody = $decryptedMessageBody;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * @return string
     */
    public function getRecipientName(): string
    {
        return $this->recipientName;
    }

    /**
     * @return string
     */
    public function getDecryptedTitle(): string
    {
        return $this->decryptedTitle;
    }

    /**
     * @return string
     */
    public function getDecryptedMessageBody(): string
    {
        return $this->decryptedMessageBody;
    }
}