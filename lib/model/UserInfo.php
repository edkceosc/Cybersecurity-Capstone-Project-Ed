<?php


namespace lib\model;

/**
 * A subset of information about a user.
 *
 * @package lib\model
 */
class UserInfo
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * UserInfo constructor.
     *
     * @param int    $id
     * @param string $username
     */
    public function __construct(int $id, string $username)
    {
        $this->id = $id;
        $this->username = $username;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

}