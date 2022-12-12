<?php
namespace app\shared;

use app\account_management\infrastructure\AccountGateway;

class Context
{
    private static AccountGateway $accountGateway;
    public static function getAccountGateway(): AccountGateway
    {
        if (!isset(self::$accountGateway))
            self::$accountGateway = new AccountGateway();
        return self::$accountGateway;
    }
}