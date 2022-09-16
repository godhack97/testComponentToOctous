<?php

namespace App\Helpers;

use Bitrix\Main\Loader;

class Products
{
    public static function getProductsByIds($ids, $page)
    {
        Loader::includeModule("highloadblock");

        $sourceItems = [];

        $arFilter = [
            "IBLOCK_ID" => CATALOG_ID,
            "ID" => $ids,
            "ACTIVE" => "Y",
            "INCLUDE_SUBSECTIONS" => "Y",
            "OFFERS" => [
                "=CATALOG_AVAILABLE" => "Y",
            ],
        ];

        $obProduct = \CIBlockElement::GetList([], $arFilter, false, false, ["ID", "NAME", "SORT", "CODE", "PROPERTY_CML2_ARTICLE"]);

        while ($product = $obProduct->fetch()) {
            if (\CCatalogSKU::getExistOffers($product["ID"])[$product["ID"]]) {
                $res = \CCatalogSKU::getOffersList(
                    $product["ID"],
                    0,
                    ["CATALOG_AVAILABLE" => "Y", "ACTIVE" => "Y"],
                    ["CODE" => "CATALOG_PRICE_1"],
                    ["CODE" => ["COLOR", "SIZES", "CML2_LINK"]]
                );
                $product["OFFERS"] = $res[$product["ID"]];
            }
            $sourceItems[] = $product;
        }

        $arResult = [];

        foreach ($sourceItems as $arItem) {
            $offer = $arItem["OFFERS"][array_key_first($arItem["OFFERS"])];
            $price = \CCatalogProduct::GetOptimalPrice($offer['ID']);

            $arItem["price"] = $price["RESULT_PRICE"];
            $arItem["size"]  = $offer["PROPERTIES"]["SIZE"]["VALUE"];
            $arItem["article"]  = $offer["PROPERTIES"]["CML2_LINK"]["VALUE"];

            if ($offer["PROPERTIES"]["CML2_LINK"]["VALUE"] && $offer["PROPERTIES"]["COLOR"]["VALUE"]) {
                $arItem["image"] = Images::getImageByLinkAndColor($offer["PROPERTIES"]["CML2_LINK"]["VALUE"], $offer["PROPERTIES"]["COLOR"]["VALUE"]);
                $arItem["color"] = Color::getColorById($offer["PROPERTIES"]["COLOR"]["VALUE"]);
            }

            $arResult[] = $arItem;
        }

        uasort($arResult, function ($a, $b) {
            return $a["SORT"] <=> $b["SORT"];
        });

        // $response = [
        //     "page"        => $page,
        //     "total_page"  => 1,
        //     "total_items" => 0,
        // ];


        foreach ($arResult as $item) {
            $response["items"][] = [
                "id"        => $item["ID"],
                "name"      => $item["NAME"],
                "picture"   => $item["image"],
                "article"   => $item["PROPERTY_CML2_ARTICLE_VALUE"],
                "price"     => $item["price"]["BASE_PRICE"],
                "price_old" => $item["price"]["DISCOUNT_PRICE"],
                "discount"  => $item["price"]["DISCOUNT"] > 0,
                "sort"      => $item["SORT"],
            ];
        }

        $response["total_items"] = count($arResult);
        // $response["total_page"]  = count(array_chunk($arResult, 6));

        return $response;
    }

    public static function getLatestText($id, $mount, $lang): string
    {
        $result = "";

        $element = \CIBlockElement::GetByID($id)->GetNext();

        $dateNow = new \DateTime("now");
        $dateCreated = new \DateTime(str_replace(".", "-", $element["CREATED_DATE"]));

        if ($dateNow->diff($dateCreated)->m < $mount) {
            $result = ($lang === "RU") ? "Новинка" : "New season";
        } else {
            $arFilter = ["IBLOCK_ID" => CATALOG_ID, "ID" => $element["IBLOCK_SECTION_ID"]];
            $section = \CIBlockSection::GetList([], $arFilter, false, ["NAME", "UF_NAME_EN"])->GetNext();

            $result = ($lang === "RU") ? $section["NAME"] : $section["UF_NAME_EN"];
        }

        return $result;
    }

    public static function getEngName($skuId)
    {
        $mxResult = \CCatalogSku::GetProductInfo($skuId);

        $arFilter = [
            "IBLOCK_ID" => CATALOG_ID,
            "ID" => $mxResult["ID"],
        ];

        $obProduct = \CIBlockElement::GetList([], $arFilter, false, false, ["PROPERTY_ENG_NAIMENOVANIE"]);

        if ($product = $obProduct->fetch()) {
            dd($product);
        }

    }
}
