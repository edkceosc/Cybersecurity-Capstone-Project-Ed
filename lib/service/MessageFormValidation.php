<?php


namespace lib\service;


use lib\db\Users;

/**
 *
 * Validates input values for a new message
 *
 * @package lib\service
 */
class MessageFormValidation
{

    /**
     * @var Users
     */
    private $dbUsers;

    /**
     * @var int|null
     */
    private $recipientId = null;

    /**
     * @var string
     */
    private $recipientName = '';

    /**
     * @var string
     */
    private $recipientErr = '';

    /**
     * @var string
     */
    private $title = '';

    /**
     * @var string
     */
    private $titleErr = '';

    /**
     * @var string
     */
    private $msgBody = '';

    /**
     * @var string
     */
    private $msgBodyErr = '';

    private $isValid = false;

    /**
     * MessageFormValidation constructor.
     *
     * @param Users $dbUsers
     */
    public function __construct(Users $dbUsers)
    {
        $this->dbUsers = $dbUsers;
    }

    public function validateInput(array $formData)
    {
        $this->isValid = true;

        if (empty($formData['recipient'])) {
            $this->isValid = false;
            $this->recipientErr = 'recipient is required';
        } else {
            $this->recipientName = $formData['recipient'];
            $user = $this->dbUsers->loadUserByUsername($formData['recipient']);
            if (!$user) {
                $this->isValid = false;
                $this->recipientErr = 'This user does not exist';
            } else {
                $this->recipientId = $user->getId();
            }
        }

        if (empty($formData['title']) || strlen($formData['title']) > 255) {
            $this->isValid = false;
            $this->titleErr = 'The title must be between 1 and 255 characters long';
        } else {
            $this->title = $formData['title'];
        }

        if (empty($formData['message-body'])) {
            $this->isValid = false;
            $this->msgBodyErr = 'The message is required';
        } else {
            $this->msgBody = $formData['message-body'];
        }
    }

    /**
     * @return int|null
     */
    public function getRecipientId(): ?int
    {
        return $this->recipientId;
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
    public function getRecipientErr(): string
    {
        return $this->recipientErr;
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
    public function getTitleErr(): string
    {
        return $this->titleErr;
    }

    /**
     * @return string
     */
    public function getMsgBody(): string
    {
        return $this->msgBody;
    }

    /**
     * @return string
     */
    public function getMsgBodyErr(): string
    {
        return $this->msgBodyErr;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }
}