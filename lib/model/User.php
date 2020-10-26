<?php


namespace lib\model;

use DateTime;

/**
 * A user entity
 *
 */
class User extends UserInfo
{

    /**
     * @var string
     */
    private $email;

    /**
     * The password hash (not: this is not the cleartext password, but an output of `password_hash`).
     *
     * @var string
     */
    private $password;

    /**
     * @var string | null
     */
    private $loginIp;

    /**
     * @var int
     */
    private $loginAttempts;

    /**
     * @var DateTime | null
     */
    private $lockedTime;

    public function __construct(
        int $id,
        string $username,
        string $email,
        string $password,
        ?string $loginIp,
        int $loginAttempts,
        ?DateTime $lockedTime
    ) {
        parent::__construct($id, $username);
        $this->email = $email;
        $this->password = $password;
        $this->loginIp = $loginIp;
        $this->loginAttempts = $loginAttempts;
        $this->lockedTime = $lockedTime;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getLoginIp(): ?string
    {
        return $this->loginIp;
    }

    /**
     * @return int
     */
    public function getLoginAttempts(): int
    {
        return $this->loginAttempts;
    }

    /**
     * The timestamp when the user was locked.
     *
     * @return DateTime|null
     */
    public function getLockedTime(): ?DateTime
    {
        return $this->lockedTime;
    }
}