<?php

namespace App\Helpers;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;

class Language
{
    const LANG = [
        "RU" => "UF_RUS",
        "EN" => "UF_EN",
    ];

    public static function getLanguage(string $lang): array
    {
        Loader::includeModule("highloadblock");

        $arResult = [];

        $hlblock = HL\HighloadBlockTable::getList([
            "filter" => ['=NAME' => "language"]
        ])->fetch();

        $hlblock = HL\HighloadBlockTable::getById($hlblock["ID"])->fetch();

        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entityDataClass = $entity->getDataClass();

        $rsData = $entityDataClass::getList(array(
            "select" => ["UF_CODE", self::LANG[$lang]],
            "order" => ["UF_CODE" => "ASC"]
        ));

        while($arData = $rsData->Fetch()){
            $arResult[$arData["UF_CODE"]] = $arData[self::LANG[$lang]];
         }

        return $arResult;
    }

    public static function getLanguageBySiteId(): array
    {
        Loader::includeModule("highloadblock");

        $arResult = [];

        $hlblock = HL\HighloadBlockTable::getList([
            "filter" => ['=NAME' => "language"]
        ])->fetch();

        $hlblock = HL\HighloadBlockTable::getById($hlblock["ID"])->fetch();

        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entityDataClass = $entity->getDataClass();

        if (SITE_ID === "s1") {
            $lang = "UF_RUS";
        } else {
            $lang = "UF_EN";
        }

        $rsData = $entityDataClass::getList(array(
            "select" => ["UF_CODE", $lang],
            "order" => ["UF_CODE" => "ASC"]
        ));

        while($arData = $rsData->Fetch()){
            $arResult[$arData["UF_CODE"]] = $arData[$lang];
         }

        return $arResult;
    }
}
