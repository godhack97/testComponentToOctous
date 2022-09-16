<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\User;
use Bitrix\Main\Context;

class Profile extends BaseController
{
    public function getAction()
    {
        $arResult = [];
        $rsUsers = \CUser::GetList([], false, ["UF_REST_API_TOKEN" => $this->token]);
        if ($arUser = $rsUsers->GetNext()) {
            $arResult = [
                "first_name" => $arUser["NAME"],
                "last_name"  => $arUser["LAST_NAME"],
                "email"      => $arUser["EMAIL"],
                "birthdate"  => $arUser["PERSONAL_BIRTHDAY"],
                "gender"     => $arUser["PERSONAL_GENDER"],
            ];
        };
        return $arResult;
    }

    public function updateAction()
    {
        $request = Context::getCurrent()->getRequest();
        $arFields = [];

        if ($request->get("first_name")) {
            $arFields["NAME"] = $request->get("first_name");
        }

        if ($request->get("last_name")) {
            $arFields["LAST_NAME"] = $request->get("last_name");
        }

        if ($request->get("email")) {
            $arFields["EMAIL"] = $request->get("email");
        }

        if ($request->get("birthdate")) {
            $arFields["PERSONAL_BIRTHDAY"] = $request->get("birthdate");
        }

        if ($request->get("gender")) {
            $arFields["PERSONAL_GENDER"] = $request->get("gender");
        }

        $user = new \CUser;
        $user->Update(User::getUserIdByToken($this->token), $arFields);

        if ($user->LAST_ERROR) {
            throw new \Exception($user->LAST_ERROR);
        }
    }
}
