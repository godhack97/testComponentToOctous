<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Bitrix\Main\Context;
use Bitrix\Main\UserPhoneAuthTable;

class Register extends BaseController
{
    protected $requiredFields = [
        "email",
        "password",
        "full_name",
        "phone",
    ];

    public function registrationAction()
    {
        $request = Context::getCurrent()->getRequest();

        foreach ($this->requiredFields as $field) {
            if (!$request->get($field)) {
                throw new \Exception("Не передано поле ${field}");
            }
        }

        $email = $request->get("email");
        $password = $request->get("password");
        $fullName = explode(" ", $request->get("full_name"));
        $phone = $request->get("phone");

        $arFields = [
            "NAME"              => $fullName[0],
            "LAST_NAME"         => $fullName[1],
            "EMAIL"             => $email,
            "PHONE_NUMBER"      => $phone,
            "LOGIN"             => $email,
            "LID"               => "ru",
            "ACTIVE"            => "N",
            "GROUP_ID"          => [2, 6],
            "PASSWORD"          => $password,
            "CONFIRM_PASSWORD"  => $password,
            "UF_DEVICE_ID"      => $this->deviceId
        ];

        $user = new \CUser();
        $id = $user->Add($arFields);

        if (intval($id) > 0) {
            \CUser::SendPhoneCode(UserPhoneAuthTable::normalizePhoneNumber($phone), 'SMS_USER_CONFIRM_NUMBER');
            return ["id" => $id];
        } else {
            throw new \Exception($user->LAST_ERROR);
        }
    }

    public function verifyPhoneCodeAction()
    {
        $request = Context::getCurrent()->getRequest();
        $code = $request->get("code");

        if (!$code) {
            throw new \Exception("Не передан параметр code");
        }

        $arUser = \Bitrix\Main\UserTable::getList(array(
            "select" => ["ID", "EMAIL"],
            "filter" => ["UF_DEVICE_ID" => $this->deviceId]
        ))->fetch();

        $userPhone = UserPhoneAuthTable::getList(["filter" => ["USER_ID" => $arUser["ID"]]])->fetch();
        
        if (\CUser::VerifyPhoneCode($userPhone["PHONE_NUMBER"], $code)) {
            $token = Login::generateJWT($arUser["EMAIL"]);
            $user = new \CUser();
            $user->Update($arUser["ID"], ["ACTIVE" => "Y", "UF_REST_API_TOKEN" => $token]);

            return ["token" => $token];
        }
    }
}
