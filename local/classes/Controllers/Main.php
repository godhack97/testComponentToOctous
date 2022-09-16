<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Products;

class Main extends BaseController
{
    public function getMainAction()
    {
        $carousel = [
            "type"  => "carousel",
            "title" => "This Week’s Highlights",
            "weight" => 100,
            "items" => []
        ];

        $popular = [
            "type" => "highlighted_items",
            "title" => "Popular Today",
            "subtitle" => "Hand-picked daily from the world’s best brands and boutiques",
            "btn_name" => "Shop Now",
            "weight" => 90,
            "items" => []
        ];

        $article = [
            "type" => "article",
            "title" => "Trending Article",
            "description" => "When inspiration \nis boundless",
            "detail_description" => "We are sincerely inspired by modern women, \nwho are always in the spotlight.",
            "btn_name" => "Read the full article",
            "action_type" => "open_link",
            "action_value" => "https://sodamoda.ru/",
            "weight" => 80,
        ];

        $categories = [
            "type" => "highlighted_categories",
            "title" => "Catalog",
            "subtitle" => "Shop the world's best boutiques",
            "btn_name" => "Shop Now",
            "weight" => 70,
            "items" => []
        ];

        $new = [
            "type" => "highlighted_items",
            "title" => "New In",
            "subtitle" => "Discover new styles, just for you",
            "btn_name" => "Shop Now",
            "weight" => 60,
            "items" => []
        ];

        $alsoLike = [
            "type" => "like_items",
            "title" => "You may also like",
            "subtitle" => "Discover new styles, just for you",
            "btn_name" => "Shop Now",
            "weight" => 60,
            "items" => []
        ];

        $certificate = [
            "type" => "single_highlighted_item",
            "title" => "Trending Now",
            "subtitle" => "",
            "description" => "These Knit Are Trending",
            "detail_description" => "From Greg Lauren's patchwork designs to logo-centric print",
            "background_image" => "",
            "thumb" => "",
            "btn_name" => "Shop Now",
            "action_type" => "open_link",
            "action_value" => "https://sodamoda.ru/",
            "weight" => 80,
        ];

        $contact = [
            "type" => "contact_use",
            "phone" => "+78003505670",
            "whatsapp" => "https://api.whatsapp.com/send?phone=79688700070",
            "telegram" => "https://t.me/SODAMODA_CHAT",
        ];

        // sliders
        $obSlider = \CIBlockElement::GetList(
            ["ACTIVE_FROM" => "DESC"],
            ["IBLOCK_ID" => "34", "ACTIVE" => "Y"],
            false,
            ["nPageSize" => 5],
            ["ID", "NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "PROPERTY_TYPE_ACTION", "PROPERTY_CATEGORY_ID", "PROPERTY_PRODUCT_ID"]
        );

        while ($arSlider = $obSlider->GetNext()) {
            $arSlider["PREVIEW_PICTURE"] = \CFile::GetPath($arSlider["PREVIEW_PICTURE"]);
            $item = [
                "name"         => $arSlider["NAME"],
                "description"  => $arSlider["PREVIEW_TEXT"],
                "thumb"        => $arSlider["PREVIEW_PICTURE"],
                "action_type"  => $arSlider["PROPERTY_TYPE_ACTION_VALUE"],
                "action_value" => ($arSlider["PROPERTY_CATEGORY_ID_VALUE"]) ? $arSlider["PROPERTY_CATEGORY_ID_VALUE"] : $arSlider["PROPERTY_PRODUCT_ID_VALUE"],
            ];
            $carousel["items"][] = $item;
        }

        // popular products
        $res = \CIBlockElement::GetList(
            ["SORT" => "ASC"],
            ["IBLOCK_ID" => "32", "ACTIVE" => "Y"],
            false,
            ["nTopCount" => 5],
            ["ID", "NAME", "CREATED_DATE", "PREVIEW_TEXT", "PREVIEW_PICTURE", "PROPERTY_TYPE_ACTION", "PROPERTY_CATEGORY_ID", "PROPERTY_PRODUCT_ID", "PROPERTY_BTN_NAME"]
        );

        while ($ob = $res->Fetch()) {

            $latestText = Products::getLatestText($ob["PROPERTY_PRODUCT_ID_VALUE"], 3, $this->lang);

            $arPrice = \CCatalogProduct::GetOptimalPrice($ob["PROPERTY_PRODUCT_ID_VALUE"], 1, []);
            if (!$arPrice || count($arPrice) <= 0) {
                if (\CCatalogProduct::GetNearestQuantityPrice($ob["PROPERTY_PRODUCT_ID_VALUE"], 1, [])) {
                    $arPrice = \CCatalogProduct::GetOptimalPrice($ob["PROPERTY_PRODUCT_ID_VALUE"], 1, []);
                }
            }
            
            $arPhoto = \CFile::ResizeImageGet(
                $ob["PREVIEW_PICTURE"],
                ["width" => 414, "height" => 621],
                BX_RESIZE_IMAGE_EXACT
            );
            
            $item = [
                "name"         => $ob["NAME"],
                "description"  => $ob["PREVIEW_TEXT"],
                "sub_text"     => $latestText,
                "thumb"        => $arPhoto["src"],
                "price"        => (float)$arPrice["PRICE"]["PRICE"],
                "currency"     => strtolower($arPrice["PRICE"]["CURRENCY"]),
                "action_type"  => $ob["PROPERTY_TYPE_ACTION_VALUE"],
                "action_value" => $ob["PROPERTY_PRODUCT_ID_VALUE"],
            ];

            $popular["items"][] = $item;
            $new["items"][] = $item;
        }

        // you may also like
        $res = \CIBlockElement::GetList(
            ["SORT" => "ASC"],
            ["IBLOCK_ID" => "36", "ACTIVE" => "Y"],
            false,
            ["nTopCount" => 5],
            ["ID", "NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "PROPERTY_TYPE_ACTION", "PROPERTY_CATEGORY_ID", "PROPERTY_PRODUCT_ID", "PROPERTY_BTN_NAME"]
        );

        while ($ob = $res->Fetch()) {
            $arPrice = \CCatalogProduct::GetOptimalPrice($ob["PROPERTY_PRODUCT_ID_VALUE"], 1, []);
            if (!$arPrice || count($arPrice) <= 0) {
                if (\CCatalogProduct::GetNearestQuantityPrice($ob["PROPERTY_PRODUCT_ID_VALUE"], 1, [])) {
                    $arPrice = \CCatalogProduct::GetOptimalPrice($ob["PROPERTY_PRODUCT_ID_VALUE"], 1, []);
                }
            }
            
            $arPhoto = \CFile::ResizeImageGet(
                $ob["PREVIEW_PICTURE"],
                ["width" => 414, "height" => 621],
                BX_RESIZE_IMAGE_EXACT
            );
            
            $item = [
                "name" => $ob["NAME"],
                "description" => $ob["PREVIEW_TEXT"],
                "thumb" => $$arPhoto["src"],
                "price" => (float)$arPrice["PRICE"]["PRICE"],
                "currency" => strtolower($arPrice["PRICE"]["CURRENCY"]),
                "action_type" => $ob["PROPERTY_TYPE_ACTION_VALUE"],
                "action_value" => $ob["PROPERTY_PRODUCT_ID_VALUE"],
            ];
            $alsoLike["items"][] = $item;
        }

        //popular category
        $res = \CIBlockElement::GetList(
            ["ACTIVE_FROM" => "DESC"],
            ["IBLOCK_ID" => "31", "ACTIVE" => "Y"],
            false,
            false,
            ["ID", "NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "PROPERTY_TYPE_ACTION", "PROPERTY_CATEGORY_ID"]
        );

        while ($ob = $res->GetNext()) {
            $ob["PREVIEW_PICTURE"] = \CFile::GetPath($ob["PREVIEW_PICTURE"]);
            $item = [
                "name" => $ob["NAME"],
                "thumb" => $ob["PREVIEW_PICTURE"],
                "action_type" => $ob["PROPERTY_TYPE_ACTION_VALUE"],
                "action_value" => !empty($ob["PROPERTY_CATEGORY_ID_VALUE"]) ? $ob["PROPERTY_CATEGORY_ID_VALUE"] : $ob["PROPERTY_PRODUCT_ID_VALUE"],
            ];
            $categories["items"][] = $item;
        }

        //certificate
        $res = \CIBlockElement::GetList(
            ["ACTIVE_FROM" => "DESC"],
            ["IBLOCK_ID" => "33", "ACTIVE" => "Y"],
            false,
            false,
            ["ID", "NAME", "PREVIEW_TEXT", "DETAIL_TEXT", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PROPERTY_TYPE_ACTION", "PROPERTY_LINK", "PROPERTY_BTN_NAME", "PROPERTY_CATEGORY_ID"]
        );

        while ($ob = $res->GetNext()) {
            $ob["PREVIEW_PICTURE"] = \CFile::GetPath($ob["PREVIEW_PICTURE"]);
            $ob["DETAIL_PICTURE"] = \CFile::GetPath($ob["DETAIL_PICTURE"]);

            $certificate["subtitle"] = $ob["NAME"];
            $certificate["description"] = $ob["PREVIEW_TEXT"];
            $certificate["detail_description"] = $ob["DETAIL_TEXT"];
            $certificate["thumb"] = $ob["PREVIEW_PICTURE"];
            $certificate["action_type"] = $ob["PROPERTY_TYPE_ACTION_VALUE"];
            $certificate["action_value"] = $ob["PROPERTY_CATEGORY_ID_VALUE"];
        }

        //article
        $res = \CIBlockElement::GetList(
            ["ACTIVE_FROM" => "DESC"],
            ["IBLOCK_ID" => "35", "ACTIVE" => "Y"],
            false,
            false,
            ["ID", "NAME", "PREVIEW_TEXT", "DETAIL_TEXT", "PREVIEW_PICTURE", "PROPERTY_TYPE_ACTION", "PROPERTY_LINK", "PROPERTY_BTN_NAME", "DETAIL_PAGE_URL"]
        );

        while ($ob = $res->GetNext()) {
            $arPhoto = \CFile::ResizeImageGet(
                $ob["PREVIEW_PICTURE"],
                ["width" => 1366, "height" => 2049],
                BX_RESIZE_IMAGE_EXACT
            );

            $article["thumb"] = $arPhoto["src"];
            $article["description"] = $ob["PREVIEW_TEXT"];
            $article["detail_description"] = $ob["DETAIL_TEXT"];
            $article["action_type"] = $ob["PROPERTY_TYPE_ACTION_VALUE"];
            $article["action_value"] = "$ob[ID]";
        }

        return [$carousel, $popular, $article, $categories, $new, $certificate, $contact, $alsoLike];
    }
}
