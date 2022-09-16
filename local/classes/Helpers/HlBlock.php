<?php

namespace App\Helpers;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;

class HlBlock
{
    public static function getDataClass(string $code)
    {
        Loader::includeModule("highloadblock");

        $hlblock = HL\HighloadBlockTable::getList([
            "filter" => ['=NAME' => $code]
        ])->fetch();

        $hlblock = HL\HighloadBlockTable::getById($hlblock["ID"])->fetch();

        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        return $entity->getDataClass();
    }
}
