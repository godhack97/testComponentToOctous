<?php

namespace App\Helpers;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;

class Color
{
    public static function getColorById(string $id): array
    {
        Loader::includeModule("highloadblock");

        $arResult = [];

        $hlblock = HL\HighloadBlockTable::getList([
            "filter" => ['=NAME' => "COLOR"]
        ])->fetch();

        $hlblock = HL\HighloadBlockTable::getById($hlblock["ID"])->fetch();

        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entityDataClass = $entity->getDataClass();

        $arResult = $entityDataClass::getList(array(
            "filter" => ["=UF_XML_ID" => $id],
            "select" => ["*"],
            "limit" => 1
        ))->Fetch();

        return $arResult;
    }
}
