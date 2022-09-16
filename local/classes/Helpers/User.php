<?php

namespace App\Helpers;

use Bitrix\Main\UserTable;

class User
{
    public static function getUserIdByToken(string $token): int
    {
        $arUser = UserTable::getList([
            "filter" => ["=UF_REST_API_TOKEN" => $token],
            "select" => ["ID"],
            "limit" => 1
        ])->fetch();

        if (!$arUser) {
            throw new \Exception("Токен не действительный");
        }

        return $arUser["ID"];
    }
}
