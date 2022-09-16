<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\User;

class Logout extends BaseController
{
    public function logoutAction()
    {
        $user = new \CUser();

        $arFilter = [
            [
                "LOGIC" => "AND",
                ["UF_REST_API_TOKEN" => $this->token],
                ["UF_DEVICE_ID" => $this->deviceId],
            ],
        ];

        $arUsers = \CUser::GetList([], false, $arFilter)->fetch();

        if (!$arUsers) {
            throw new \Exception("Пользователь с таким токеном и id не найден");
        }

        $user->Update($arUsers["ID"], ["UF_REST_API_TOKEN" => ""]);
    }
}
