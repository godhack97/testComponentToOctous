<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Bitrix\Main\Context;
use Bitrix\Main\UserTable;
use Bitrix\Main\Security\Password;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Login extends BaseController
{
    const KEY = "NJ0hENpCigbrWHjF9c4KI2CR7GybEqei";
    const ALGO = "HS256";

    public function getResultAction()
    {
        $request = Context::getCurrent()->getRequest();
        $email = $request->get("email");
        $password = $request->get("password");

        if (!$email) {
            throw new \Exception("Не передано поле email");
        }

        if (!$password) {
            throw new \Exception("Не передано поле password");
        }

        $arUser = UserTable::getList([
            "filter" => ["=EMAIL" => $email],
            "select" => ["ID", "EMAIL", "PASSWORD"],
            "order" => ["LAST_LOGIN" => "DESC"],
            "limit" => 1
        ])->fetch();

        if (!$arUser) {
            throw new \Exception("Пользователь с таким логином и паролем не нашелся");
        }

        if (!Password::equals($arUser["PASSWORD"], $password)) {
            throw new \Exception("Не правильный логин или пароль");
        }

        $token = self::generateJWT($arUser["EMAIL"]);
        self::validateJWT($token);
        self::updateToken($arUser["ID"], $token);

        return ["token" => $token];
    }

    public static function generateJWT($email): ?string
    {
        $issuedAt = new \DateTimeImmutable();

        $data = array(
            "iss" => $_SERVER["SERVER_NAME"],
            "iat" => $issuedAt,
            "nbf" => $issuedAt,
            "email" => $email
        );

        return JWT::encode($data, self::KEY, self::ALGO);
    }

    public static function validateJWT($token)
    {
        return JWT::decode($token, new Key(self::KEY, self::ALGO));
    }

    public static function updateToken($userId, $token): bool
    {
        $user = new \CUser;
        return $user->Update($userId, ["UF_REST_API_TOKEN" => $token]);
    }

    public static function validateUserByToken($email, $token): ?int
    {
        $arUser = UserTable::getList([
            "filter" => ["=EMAIL" => $email, "=UF_REST_API_TOKEN" => $token],
            "select" => ["ID"],
            "limit" => 1
        ])->fetch();

        return $arUser["ID"];
    }
}
