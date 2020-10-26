<?php


namespace lib\service;


use lib\db\Users;
use lib\utils\Clock;

class UserService
{

    /**
     * @var Users
     */
    private $dbUsers;

    /**
     * @var Clock
     */
    private $clock;

    /**
     * UserService constructor.
     *
     * @param Users $dbUsers
     * @param Clock $clock
     */
    public function __construct(Users $dbUsers, Clock $clock)
    {
        $this->dbUsers = $dbUsers;
        $this->clock = $clock;
    }

}