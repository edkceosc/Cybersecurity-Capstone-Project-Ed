<?php


namespace lib\service;


use lib\model\User;

class LoginResult
{

    const RESULT_SUCCESS = 1;
    const RESULT_WRONG_CREDENTIALS = 2;
    const RESULT_LOCKED = 3;

    /**
     * @var int
     */
    private $resultCode;

    /**
     * @var User|null
     */
    private $user;

    /**
     * @param int       $resultCode
     * @param User|null $user
     */
    private function __construct(int $resultCode, ?User $user)
    {
        $this->resultCode = $resultCode;
        $this->user = $user;
    }

    public static function createSuccessfulResult(User $user): LoginResult
    {
        return new LoginResult(self::RESULT_SUCCESS, $user);
    }

    public static function createWrongCrendentialsResult(): LoginResult
    {
        return new LoginResult(self::RESULT_WRONG_CREDENTIALS, null);
    }

    public static function createUserLockedResult(): LoginResult
    {
        return new LoginResult(self::RESULT_LOCKED, null);
    }

    public function isSuccessful(): bool
    {
        return $this->resultCode === self::RESULT_SUCCESS;
    }

    public function isLocked(): bool
    {
        return $this->resultCode === self::RESULT_LOCKED;
    }

    public function isWrongCredentialsProvided(): bool
    {
        return $this->resultCode === self::RESULT_WRONG_CREDENTIALS;
    }

    /**
     * Returns the user entity. Note that this is always defined for successful results.
     * For failed login attempts, this is always null.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        if ($this->isSuccessful()) {
            return $this->user;
        }
        return null;
    }
}