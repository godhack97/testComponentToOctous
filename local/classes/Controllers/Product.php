<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Context;

class Product extends BaseController
{
    public function getProductAction($productID)
    {
        Loader::includeModule("highloadblock");
        Loader::includeModule("catalog");

        $hlblock = HighloadBlockTable::getById(2)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlblock);
        $PsuTaskControl = $entity->getDataClass();
        $request = Context::getCurrent()->getRequest();

        // ВСЕ названия материалов для SELECT
        $enumMaterialsObj = \CIBlockElement::getList([], ["IBLOCK_ID" => 21], false, false, ["ID", "IBLOCK_ID", "NAME", "PROPERTY_NAME_EN"]);
        while ($tmp = $enumMaterialsObj->GetNext()) {
            $arResult["ENUM"]["MATERIALS"][$tmp["ID"]] = ["NAME" => $tmp["NAME"], "NAME_EN" => $tmp["PROPERTY_NAME_EN_VALUE"]];
        }

        $enumCareObj = \CIBlockElement::getList([], ["IBLOCK_ID" => 5], false, false, ["ID", "IBLOCK_ID", "NAME", "PROPERTY_NAME_EN"]);
        while ($tmp = $enumCareObj->GetNext()) {
            $arResult["ENUM"]["CARE"][$tmp["ID"]] = ["NAME" => $tmp["NAME"], "NAME_EN" => $tmp["PROPERTY_NAME_EN_VALUE"]];
        }

        // ВСЕ цвета для SELECT
        $enumColorsObj = $PsuTaskControl::getList();
        while ($arItemColor = $enumColorsObj->Fetch()) {
            $arResult["ENUM"]["COLORS"][$arItemColor["UF_XML_ID"]] = ["NAME" => mb_strtoupper($arItemColor["UF_NAME"]), "NAME_EN" => strtoupper($arItemColor["UF_NAME_EN"]), "HEX" => $arItemColor["UF_HEX"]];
        }

        // Собираем данные для продукта ----------------------------------------------------------
        $arProduct = \CCatalogProduct::GetByIDEx($productID, true);

        if (!$arProduct) {
            $productsObj = \CIBlockElement::GetByID($productID);
            if ($rsProduct = $productsObj->GetNextElement()) {
                $arProduct = $rsProduct->GetFields();
                $arProduct["PROPERTIES"] = $rsProduct->GetProperties();
            }
        }

        $db_old_groups = \CIBlockElement::GetElementGroups($productID, true);
        while ($ar_group = $db_old_groups->Fetch()) {
            $ar_new_groups[] = $ar_group["ID"];
        }
        $arResult["ID"] = $productID;
        $arResult["ACTIVE"] = $arProduct["ACTIVE"];
        $arResult["NAME"] = $arProduct["NAME"];
        $arResult["URL"] = $this->domain . $arProduct["DETAIL_PAGE_URL"];
        $arResult["SECTION"] = $ar_new_groups;
        $arResult["DETAIL_TEXT"] = $arProduct["DETAIL_TEXT"];
        $arResult["ARTICLE"] = $arProduct["PROPERTIES"]["CML2_ARTICLE"]["VALUE"];
        $arResult["NAME_EN"] = $arProduct["PROPERTIES"]["ENG_NAIMENOVANIE"]["VALUE"];
        $arResult["DETAIL_TEXT_EN"] = html_entity_decode($arProduct["PROPERTIES"]["ENG_DESCRIPTION"]["VALUE"]["TEXT"]);
        $arResult["PROPS"] = $arProduct["PROPERTIES"];

        // Цены
        foreach ($arProduct["PRICES"] as $arPrice) {
            if ($arPrice["CURRENCY"] == "USD") {
                $arResult["PRICE_USD"] = $arPrice["PRICE"];
            }

            if ($arPrice["CURRENCY"] == "RUB" && (!isset($arResult["PRICE_RUB"]) || $arResult["PRICE_RUB"] < $arPrice["PRICE"])) {
                $arResult["PRICE_RUB"] = $arPrice["PRICE"];
            }
        }

        $arSizeCropTablet = ["width" => 768, "height" => 1152];
        $arSizeCropMobile = ["width" => 620, "height" => 930];
        $arSizeCropPreload = ["width" => 192, "height" => 288];

        $arPhotoElObj = \CIBlockElement::getList([], ["IBLOCK_ID" => 11, "PROPERTY_PARENT" => $productID], false, false, ["ID", "IBLOCK_ID", "ACTIVE", "PROPERTY_IMAGES", "PROPERTY_COLOR", "PROPERTY_CONCOMITANT"]);
        while ($arPhoto = $arPhotoElObj->GetNext()) {
            $tmp = [];

            $tmp["small"] = $this->domain . \CFile::ResizeImageGet($arPhoto["PROPERTY_IMAGES_VALUE"], $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT)["src"];
            $tmp["medium"] = $this->domain . \CFile::ResizeImageGet($arPhoto["PROPERTY_IMAGES_VALUE"], $arSizeCropMobile, BX_RESIZE_IMAGE_EXACT)["src"];
            $tmp["large"] = $this->domain . \CFile::ResizeImageGet($arPhoto["PROPERTY_IMAGES_VALUE"], $arSizeCropTablet, BX_RESIZE_IMAGE_EXACT)["src"];

            $arResult["COLORS_SIZES"][$arPhoto["PROPERTY_COLOR_VALUE"]]["ACTIVE"] = $arPhoto["ACTIVE"];
            $arResult["COLORS_SIZES"][$arPhoto["PROPERTY_COLOR_VALUE"]]["PHOTOS"][] = $tmp;
            $arResult["COLORS_SIZES"][$arPhoto["PROPERTY_COLOR_VALUE"]]["CONCOMITANT"][$arPhoto["PROPERTY_CONCOMITANT_VALUE"]] = $arPhoto["PROPERTY_CONCOMITANT_VALUE"];
        }

        foreach ($arResult["COLORS_SIZES"] as $colorID => $color) {
            if (!isset($color["PHOTOS"])) {
                unset($arResult["COLORS_SIZES"][$colorID]);
            }
        }

        if (!$request->get("color")) {
            $selectColor = array_key_first($arResult["COLORS_SIZES"]);
        } else {
            $selectColor = $request->get("color");
        }

        // Получим все оферы товара
        $res = \CCatalogSKU::getOffersList($productID, 0, ["ACTIVE" => "Y"]);
        foreach ($res[$productID] as $offerID => $val) {
            // Получим данные офера
            $arOffer = \CCatalogProduct::GetByIDEx($val["ID"], true);

            // Собираем для цвета оффера его размеры
            $arResult["COLORS_SIZES"][$arOffer["PROPERTIES"]["COLOR"]["VALUE"]]["SIZES"][] = $arOffer["PROPERTIES"]["SIZES"]["VALUE_ENUM"];

            if (!in_array($arOffer["PROPERTIES"]["SIZES"]["VALUE_ENUM"], $arResult["SIZES"][$arOffer["PROPERTIES"]["COLOR"]["VALUE"]])) {
                $arResult["SIZES"][$arOffer["PROPERTIES"]["COLOR"]["VALUE"]][trim($arOffer["PROPERTIES"]["SIZES"]["VALUE_ENUM"])] = [
                    "id" => trim($arOffer["ID"]),
                    "size" => trim($arOffer["PROPERTIES"]["SIZES"]["VALUE_ENUM"]),
                    "left" => count($arResult["COLORS_SIZES"][$arOffer["PROPERTIES"]["COLOR"]["VALUE"]]["SIZES"]),
                ];
            }
        }

        // Получим все данные о материалах
        $materials = [];
        $arMaterialObj = \CIBlockElement::getList([], ["IBLOCK_ID" => 22, "PROPERTY_PARENT" => $productID], false, false, ["ID", "IBLOCK_ID", "PROPERTY_MATERIAL", "PROPERTY_VALUE"]);
        while ($arMaterial = $arMaterialObj->GetNext()) {
            // Процент содержания
            $percent = trim($arMaterial["PROPERTY_VALUE_VALUE"], "% ");
            $materials[] = $arResult["ENUM"]["MATERIALS"][$arMaterial["PROPERTY_MATERIAL_VALUE"]]["NAME"] . " " . $percent . " %";
        }

        $care = "";
        foreach ($arResult["PROPS"] as $propCode => $prop) {
            switch ($propCode) {
                case "CARE":
                    foreach ($prop["VALUE"] as $id) {
                        if (!$id) {
                            continue;
                        }
                        $care .= $arResult["ENUM"]["CARE"][$id]["NAME"] . ". ";
                    }
                    break;
            }
        }

        if (!$request->get("size")) {
            $selectSize = array_key_first($arResult["SIZES"][$selectColor]);
        } else {
            $selectSize = $request->get("size");
        }

        $care = str_replace([".. ", ".."], ".", $care);
        $size = $arResult["SIZES"][$selectColor][strtoupper($selectSize)];
        $available_size = [];

        foreach ($arResult["SIZES"][$selectColor] as $item) {
            $available_size[] = $item;
        }

        $style = [
            "id" => $selectColor,
            "color" => $arResult["ENUM"]["COLORS"][$selectColor]["HEX"],
            "name" => $arResult["ENUM"]["COLORS"][$selectColor]["NAME"],
            "name_en" => $arResult["ENUM"]["COLORS"][$selectColor]["NAME_EN"],
            "images" => $arResult["COLORS_SIZES"][$selectColor]["PHOTOS"]
        ];

        $available_styles = [];
        foreach ($arResult["ENUM"]["COLORS"] as $id => $color) {
            if (isset($arResult["COLORS_SIZES"][$id]["PHOTOS"])) {
                $available_styles[] = [
                    "id" => $id,
                    "color" => $color["HEX"],
                    "name" => $color["NAME"],
                    "name_en" => $color["NAME_EN"],
                    "images" => $arResult["COLORS_SIZES"][$id]["PHOTOS"]
                ];
            }
        }

        $recommendations = [];
        if (isset($arResult["COLORS_SIZES"][$selectColor]["CONCOMITANT"]) && count($arResult["COLORS_SIZES"][$selectColor]["CONCOMITANT"])) {
            foreach ($arResult["COLORS_SIZES"][$selectColor]["CONCOMITANT"] as $item) {
                // Собираем данные для продукта
                $product = \CCatalogProduct::GetByIDEx($item, true);

                if (!$product) {
                    $productsObj = \CIBlockElement::GetByID($item["ID"]);
                    if ($rsProduct = $productsObj->GetNextElement()) {
                        $product = $rsProduct->GetFields();
                        $product["PROPERTIES"] = $rsProduct->GetProperties();
                    }
                }
                $res = \CCatalogSKU::getOffersList($product["PROPERTIES"]["PARENT"]["VALUE"], 0, ["ACTIVE" => "Y"]);

                // Для каждого офера
                foreach ($res[$product["PROPERTIES"]["PARENT"]["VALUE"]] as $offerID => $val) {
                    // Получим данные офера
                    $arOffer = \CCatalogProduct::GetByIDEx($val["ID"], true);
                    foreach ($arOffer["PRICES"] as $arPrice) {
                        if ($arPrice["CURRENCY"] == "USD") {
                            $product["PRICE_USD"] = $arPrice["PRICE"];
                        }

                        if ($arPrice["CURRENCY"] == "RUB" && (!isset($product["PRICE_RUB"]) || $product["PRICE_RUB"] < $arPrice["PRICE"])) {
                            $product["PRICE_RUB"] = $arPrice["PRICE"];
                        }
                    }
                }

                $imagesRecommendation = [];
                $imagesRecommendation["small"] = $this->domain . \CFile::ResizeImageGet($product["PROPERTIES"]["IMAGES"]["VALUE"][0], $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT)["src"];
                $imagesRecommendation["medium"] = $this->domain .\ CFile::ResizeImageGet($product["PROPERTIES"]["IMAGES"]["VALUE"][0], $arSizeCropMobile, BX_RESIZE_IMAGE_EXACT)["src"];
                $imagesRecommendation["large"] = $this->domain . \CFile::ResizeImageGet($product["PROPERTIES"]["IMAGES"]["VALUE"][0], $arSizeCropTablet, BX_RESIZE_IMAGE_EXACT)["src"];

                $productData = [
                    "id" => $product["ID"],
                    "title" => $product["NAME"],
                    "description" => $product["DETAIL_TEXT"],
                    "price" => (float)$product["PRICE_RUB"],
                    "currency" => "rub",
                    "thumb" => $imagesRecommendation
                ];
                $recommendations[] = $productData;
            }
        }

        return [
            "id" => $arResult["ID"],
            "title" => $arResult["NAME"],
            "article" => $arResult["ARTICLE"],
            "direct_web_url" => $arResult["URL"],
            "currency" => "rub",
            "price" => (float)$arResult["PRICE_RUB"],
            "composition_and_care" => [
                "composition" => $materials,
                "washing_instructions" => $care,
            ],
            "size" => $size,
            "available_sizes" => $available_size,
            "style" => $style,
            "available_styles" => $available_styles,
            "product_description" => $arResult["DETAIL_TEXT"],
            "recommendations"=> $recommendations,
            "contac_use"=> [
                    "phone" => "+78003505670",
                    "whatsapp" => "https://api.whatsapp.com/send?phone=79688700070",
                    "telegram" => "https://t.me/SODAMODA_CHAT",
            ],
        ];
    }
}
