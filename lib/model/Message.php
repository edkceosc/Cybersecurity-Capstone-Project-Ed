<?php


namespace lib\model;

/**
 * A message entity
 *
 */
class Message
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $sender;

    /**
     * @var int
     */
    private $recipient;

     /**
     ** @var string
     */
    private $title;


    /**
     ** @var string
     */
    private $message;
    
     /**
     ** @var bool
     */
    private $read;

    /**
     * @var string
     */
    private $messageDate;

    public function __construct(
        int $id,
        int $sender,
        int $recipient,
        string $title,
        string $message,
        string $messageDate,
        bool $read
    )
    {
        $this->id = $id;
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->title = $title;
        $this->message = $message;
        $this->messageDate = $messageDate;
        $this->read = $read;
        }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSender(): int
    {
        return $this->sender;
    }

    /**
     * @return int
     */
    public function getRecipient(): int
    {
        return $this->recipient;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
    
     /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getMessageDate(): string
    {
        return $this->messageDate;
    }

    /**
     * @return bool
     */
    public function isRead(): bool
    {
        return $this->read;
    }

    
}