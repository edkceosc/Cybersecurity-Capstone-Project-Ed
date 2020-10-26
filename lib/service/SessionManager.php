<?php


namespace lib\service;

use lib\model\UserInfo;

interface SessionManager
{

    function regenerateId();

    function getAuthenticatedUser(): ?UserInfo;

    function setAuthenticatedUser(UserInfo $user);

    function logout();
}