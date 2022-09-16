<?php
namespace Xwild\Restapi\Foundation;

use Bitrix\Main\UserTable;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

class User
{
    private static $_instance;

    /**
     * Возвращает данные токена по ID пользователя
     * @param int $userId
     * @return array|false
     */
    public function getToken(int $userId)
    {
        try {
            return UserTable::getList([
                'select' => [
                    Settings::getInstance()->getTokenField(),
                    Settings::getInstance()->getTokenExpireField(),
                ],
                'filter' => [
                    '=ID' => $userId,
                ],
                'limit' => 1,
            ])->fetch();
        }
        catch (ObjectPropertyException | SystemException $e) {}
        return false;
    }

    /**
     * Сгенерировать токен для пользователя
     * @param int $userId
     * @return bool|string
     */
    public function updateToken(int $userId)
    {
        try {
            $data = UserTable::getList([
                'select' => ['ID', 'LOGIN'],
                'filter' => [
                    '=ID' => $userId
                ],
                'limit' => 1,
            ])->fetchRaw();
            if ($data) {
                $token = Helper::getInstance()->generateToken($data['ID'], $data['LOGIN']);
                $user = new \CUser();
                $updated = $user->Update($data['ID'], [
                    Settings::getInstance()->getTokenField() => $token,
                    Settings::getInstance()->getTokenExpireField() => Settings::getInstance()->getTokenExpire(),
                ]);
                if ($updated) {
                    return true;
                } else {
                    return $user->LAST_ERROR;
                }
            }
        }
        catch (ObjectPropertyException | SystemException $e) {}
        return false;
    }

    public static function getInstance(): User {
        if (is_null(self::$_instance)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    private function __construct(){}

    public function __call($name, $arguments)
    {
        response()->internalServerError('Method \'' . $name . '\' is not defined User');
    }

    private function __clone(){}
}
