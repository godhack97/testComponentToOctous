<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Bitrix\Main\Context;
use App\Helpers\HlBlock;
use App\Helpers\Products;

class Favorites extends BaseController
{
    public function addAction()
    {
        global $USER;

        $request = Context::getCurrent()->getRequest();
        $id = $request->get("id");

        $entityDataClass = HlBlock::getDataClass("Favorites");

        $arFavorites = $this->getAction();

        if ($arFavorites["element_id"]) {
            $arFavorites["items"][] = $id;
            $entityDataClass::update($arFavorites["element_id"], [
                "UF_FAVORITES" => $arFavorites["items"]
            ]);
        } else {
            $entityDataClass::add([
                "UF_USER_ID"   => $USER->GetID(),
                "UF_DEVICE_ID" => $this->deviceId,
                "UF_FAVORITES" => [$id],
            ]);
        }
    }

    public function getAction()
    {
        global $USER;

        $arResult = [];

        $entityDataClass = HlBlock::getDataClass("Favorites");

        $arFilter = [
            [
                "LOGIC" => "OR",
                ["=UF_USER_ID" => $USER->GetID() ?? "not"],
                ["=UF_DEVICE_ID" => $this->deviceId]
            ]
        ];

        $rsData = $entityDataClass::getList([
            "filter" => $arFilter
        ]);

        $ids = [];
        while ($arData = $rsData->fetch()) {
            foreach ($arData["UF_FAVORITES"] as $favorites) {
                $ids[] = $favorites;
            }
        }

        $arResult = Products::getProductsByIds($ids, 1);

        return $arResult;
    }

    public function deleteAction()
    {
        $request = Context::getCurrent()->getRequest();
        $id = $request->get("id");

        $entityDataClass = HlBlock::getDataClass("Favorites");

        $arFavorites = $this->getAction();

        $entityDataClass::update($arFavorites["element_id"], [
            "UF_FAVORITES" => array_diff($arFavorites["items"], $id)
        ]);
    }
}
