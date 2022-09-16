<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use App\Helpers\Color;
use App\Helpers\Images;

class Products extends BaseController
{
    public function getProductsAction($section)
    {
        Loader::includeModule("highloadblock");

        $request = Context::getCurrent()->getRequest();

        $size = $request->get("size");
        $color = $request->get("color");

        $sourceItems = [];

        $arFilter = [
            "IBLOCK_ID" => self::CATALOG_ID,
            "SECTION_ID" => $section,
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
        $arColor = [];
        $arSize = [];

        foreach ($sourceItems as $arItem) {
            $propertiesColor = array_column($arItem["OFFERS"], "PROPERTIES");
            $colors = array_column($propertiesColor, "COLOR");
            $colorValue = array_column($colors, "VALUE");

            $propertiesSize = array_column($arItem["OFFERS"], "PROPERTIES");
            $sizes = array_column($propertiesSize, "SIZES");
            $sizeValue = array_map("trim", array_column($sizes, "VALUE"));

            $arColor[] = $colorValue[0];

            $arSize[] = $sizeValue[0];

            if ($color && !in_array($color, $colorValue)) {
                continue;
            }

            if ($size && !in_array($size, $sizeValue)) {
                continue;
            }

            $offer = $arItem["OFFERS"][array_key_first($arItem["OFFERS"])];
            $price = \CCatalogProduct::GetOptimalPrice($offer['ID']);

            $arItem["price"] = $price["RESULT_PRICE"];
            $arItem["size"]  = $offer["PROPERTIES"]["SIZE"]["VALUE"];
            $arItem["article"]  = $offer["PROPERTIES"]["CML2_LINK"]["VALUE"];

            if ($offer["PROPERTIES"]["CML2_LINK"]["VALUE"] && $offer["PROPERTIES"]["COLOR"]["VALUE"]) {
                $arItem["image"] = Images::getImageByLinkAndColor($offer["PROPERTIES"]["CML2_LINK"]["VALUE"], $offer["PROPERTIES"]["COLOR"]["VALUE"]);
            }

            $arResult[] = $arItem;
        }

        uasort($arResult, function ($a, $b) {
            return $a["SORT"] <=> $b["SORT"];
        });

        $arColor = array_unique($arColor);
        $arSize = array_unique($arSize);

        foreach ($arColor as $key => $color) {
            if (!$color) {
                unset($arColor[$key]);
            } else {
                $arColor[$key] = Color::getColorById($color)["UF_NAME"];
            }
        }

        foreach ($arSize as $key => $size) {
            if (!$size) {
                unset($arSize[$key]);
            }
        }

        $response = [
            "page"        => (int)$request->get("page"),
            "total_page"  => 1,
            "total_items" => 0,
            "colors"      => array_values($arColor),
            "sizes"       => array_values($arSize)
        ];


        foreach (array_slice($arResult, $response["page"] === 1 ? 0 : ($response["page"] - 1) * 6, 6) as $item) {
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
        $response["total_page"]  = count(array_chunk($arResult, 6));

        return $response;
    }
}
