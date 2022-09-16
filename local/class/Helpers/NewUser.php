<?php

namespace local\Helpers;


class NewUser
{
    function OnBeforeUserRegisterHandler(&$arFields)
    {
        /*
        if (!isset($arFields["USER_PHONE"]) || strlen($arFields["USER_PHONE"])<=0)
        {
            $GLOBALS['APPLICATION']->ThrowException('Поле телефон обязательно для регистрации');
            return false;
        }
        */
    }
}