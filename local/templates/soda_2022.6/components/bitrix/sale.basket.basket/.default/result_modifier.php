<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

use Bitrix\Main;
use Bitrix\Main\Loader;

global $USER;
Loader::includeModule("iblock");

$defaultParams = array(
    "TEMPLATE_THEME" => "blue"
);
$arParams = array_merge($defaultParams, $arParams);
unset($defaultParams);

$arParams["TEMPLATE_THEME"] = (string)($arParams["TEMPLATE_THEME"]);
if ("" != $arParams["TEMPLATE_THEME"]) {
    $arParams["TEMPLATE_THEME"] = preg_replace("/[^a-zA-Z0-9_\-\(\)\!]/", "", $arParams["TEMPLATE_THEME"]);
    if ("site" == $arParams["TEMPLATE_THEME"]) {
        $templateId = (string)Main\Config\Option::get("main", "wizard_template_id", "eshop_bootstrap", SITE_ID);
        $templateId = (preg_match("/^eshop_adapt/", $templateId)) ? "eshop_adapt" : $templateId;
        $arParams["TEMPLATE_THEME"] = (string)Main\Config\Option::get("main", "wizard_" . $templateId . "_theme_id", "blue", SITE_ID);
    }
    if ("" != $arParams["TEMPLATE_THEME"]) {
        if (!is_file($_SERVER["DOCUMENT_ROOT"] . $this->GetFolder() . "/themes/" . $arParams["TEMPLATE_THEME"] . "/style.css"))
            $arParams["TEMPLATE_THEME"] = "";
    }
}
if ("" == $arParams["TEMPLATE_THEME"])
    $arParams["TEMPLATE_THEME"] = "blue";


\CModule::IncludeModule("catalog");
\CModule::IncludeModule("iblock");

if (is_array($arResult["GRID"]["ROWS"]) && count($arResult["GRID"]["ROWS"])) {
    foreach ($arResult["GRID"]["ROWS"] as &$item) {
        $mxResult = \CCatalogSku::GetProductInfo($item["PRODUCT_ID"]);

        if (is_array($mxResult)) {
            $res = \CIBlockElement::GetByID($mxResult["ID"]);
            if ($ar_res = $res->GetNext()) {
                $item["NAME"] = $ar_res["NAME"];
            }

            $arSelect = ["ID", "IBLOCK_ID", "NAME", "SORT", "DATE_ACTIVE_FROM", "PROPERTY_*"];
            $arFilter = [
                "IBLOCK_ID" => COLORS_ID,
                "ACTIVE_DATE" => "Y",
                "ACTIVE" => "Y",
                "PROPERTY_PARENT" => $mxResult["ID"],
                "PROPERTY_COLOR" => $item["PROPERTY_COLOR_VALUE"],
            ];
            $res = CIBlockElement::GetList([], $arFilter, false, array("nPageSize" => 1), $arSelect);
            if ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetProperties();
                $images = $arFields["IMAGES"]["VALUE"];

                if ($images && is_array($images) && count($images) > 0) {
                    $arPhotoSmall = CFile::ResizeImageGet($images[0], ["width" => 124, "height" => 184], BX_RESIZE_IMAGE_EXACT, false, false, false, 75);
                    $item["DETAIL_PICTURE_SRC"] = $arPhotoSmall["src"];
                    $item["PREVIEW_PICTURE_SRC"] = $arPhotoSmall["src"];
                }
            }
        }
    }
    unset($item);
}


if ($USER->GetId() && $USER->IsAdmin()) {

    $arResult["DOMESTIC_ACCOUNT"] = CSaleUserAccount::GetByUserID($USER->GetId(), "RUB");
    if ($arResult["DOMESTIC_ACCOUNT"]) {
        $arResult["DOMESTIC_ACCOUNT_CURRENT_BUDGET_FORMATED"] = number_format($arResult["DOMESTIC_ACCOUNT"]["CURRENT_BUDGET"], 0, "", " ") . " руб.";
    }
}

$basketIDhL = intval($_GET["b"]);
if ($basketIDhL > 0) {
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/classes/CartSharing.php");
    $BasketSharing = new BasketSharing();
    $BasketSharing->updateCurrentBasketUser($basketIDhL);
}

if ($GLOBALS["LANG"] === "EN") {
    foreach ($arResult["BASKET_ITEM_RENDER_DATA"] as $key => $item) {
        $arResult["BASKET_ITEM_RENDER_DATA"][$key]["NAME"] = $item["NAME_EN"];
    }
}
