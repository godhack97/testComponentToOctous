<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */

use Bitrix\Main\Loader,
    Bitrix\Highloadblock\HighloadBlockTable;

global ${$arParams["FILTER_NAME"]};
if(${$arParams["FILTER_NAME"]}["OFFERS"]["=PROPERTY_78"]){
    $sizes=${$arParams["FILTER_NAME"]}["OFFERS"]["=PROPERTY_78"];
    if(is_array($sizes) && count($sizes)>0){
        foreach($arResult['ITEMS'] as &$item){
            foreach($item["OFFERS"] as $key=>$val){
                if(!in_array($val["DISPLAY_PROPERTIES"]["SIZES"]["VALUE_ENUM_ID"],$sizes))
                    unset($item["OFFERS"][$key]);
            }
        }unset($item);
    }
}


Loader::includeModule("highloadblock");

$hlblock = HighloadBlockTable::getById(2)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$PsuTaskControl = $entity->getDataClass();

foreach ($arResult['ITEMS'] as &$arItem){
    if ($arItem['OFFERS'] && is_array($arItem['OFFERS']))
    {
        foreach ($arItem['OFFERS'] as &$offer)
        {
            if($offer["PROPERTIES"]["COLOR"]["VALUE"]){
                $offer["PROPERTIES"]["COLOR"]["VALUE_ENUM_ID"]=$offer["PROPERTIES"]["COLOR"]["VALUE"];
                $offer["PROPERTIES"]["COLOR"]["VALUE_XML_ID"]=$offer["PROPERTIES"]["COLOR"]["VALUE"];
                $reslist=$PsuTaskControl::getList([
                    'filter'=>[
                        "UF_XML_ID"=>$offer["PROPERTIES"]["COLOR"]["VALUE"],
                    ],
                ]);
                if ($el=$reslist->fetch())
                {
                    $offer["PROPERTIES"]["COLOR"]["VALUE"]=$el["UF_NAME"];
                }
            }
        }
        unset($offer);
    }
}unset($arItem);

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();


//if($USER->IsAdmin()) {
foreach ($arResult['ITEMS'] as $arItem)
{
    $arAllProductsArts[] = $arItem['ID'];

//  if($arItem['ID'] == 193)
//    dbgz($arItem);
}

if (!empty($arAllProductsArts))
{
    $arSelect = Array("ID", "IBLOCK_ID", "NAME", "SORT", "DATE_ACTIVE_FROM", "PROPERTY_*");
    $arFilter = Array(
        "IBLOCK_ID" => COLORS_ID,
        //"ACTIVE_DATE" => "Y",
        //"ACTIVE" => "Y",
        'PROPERTY_PARENT' => $arAllProductsArts,
        "!PROPERTY_IMAGES" => false);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 300), $arSelect);
    while ($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $arFields = array_merge($arProps, $arFields);
        $arPhotos[$arFields['PARENT']['VALUE']][$arFields['COLOR']['VALUE']] = $arFields;
    }
}
//}



$arItemsNew=[];

foreach ($arResult['ITEMS'] as $arItem)
{

    $arOffers = array();

    if (!empty($arItem['OFFERS']))
    {

        $arSetPhoto = array();

        foreach ($arItem['OFFERS'] as $offer)
        {
            $offer["PROPERTIES"]["MORE_PHOTO"]['VALUE']=false;

            if ($arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']] && !$arSetPhoto[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']])
            {
                $offer['PROPERTIES']['FULL']=$arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['FULL'];
                $offer['PROPERTIES']['FULL_IMAGE']=$arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['FULL_IMAGE'];
                $arSetPhoto[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']] = $offer['ID'];
                $offerFindedPhotos = $arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['IMAGES']['VALUE'];
                $offer['SORT'] = $arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['SORT'];

                if (!empty($offerFindedPhotos))
                {
                    $offer["PROPERTIES"]["MORE_PHOTO"]['VALUE'] = $offerFindedPhotos;
                }
            }

            if (!empty($offer["PROPERTIES"]["MORE_PHOTO"]['VALUE']))
            {
                $isUse = false;
                foreach ($arOffers as $arOffer)
                {
                    if ($arOffer["PROPERTIES"]['COLOR']['VALUE_XML_ID'] == $offer["PROPERTIES"]['COLOR']['VALUE_XML_ID'] &&
                        $arOffer["PROPERTIES"]['CML2_LINK']['VALUE'] == $offer["PROPERTIES"]['CML2_LINK']['VALUE'])
                        $isUse = true;
                }
                if (!$isUse)
                    $arOffers[] = $offer;
            }

        }

        if (count($arOffers) < 1)
        {
            $arItemsNew[] = $arItem;
        }
        else
        {
            foreach ($arOffers as $arOffer)
            {

                if (!empty($arResult['ORIGINAL_PARAMETERS']['GLOBAL_FILTER']['OFFERS']['=PROPERTY_72']) && !in_array($arOffer["PROPERTIES"]["COLOR"]['VALUE_ENUM_ID'], $arResult['ORIGINAL_PARAMETERS']['GLOBAL_FILTER']['OFFERS']['=PROPERTY_72']))
                {
                    continue;
                }

                $arOfferInfo = Array(
                    'SORT' => $arOffer['SORT'],
                    'ARTICLE' => $arItem["PROPERTIES"]['CML2_ARTICLE']['VALUE'],
                    'NAME' => trim($arItem['NAME']) . ', ' . $arOffer["PROPERTIES"]["COLOR"]['VALUE'],
                    'EDIT_LINK' => $arItem['EDIT_LINK'],
                    'DELETE_LINK' => $arItem['DELETE_LINK'],
                    'CODE' => $arItem['CODE'],
                    'DETAIL_PICTURE' => $arOffer["PROPERTIES"]["MORE_PHOTO"]['VALUE'][0],
                    'DETAIL_PAGE_URL' => $arItem['DETAIL_PAGE_URL'] . '?setColor=' . $arOffer['PROPERTIES']['COLOR']['VALUE_ENUM_ID'],
                );

                $arItemsNew[] = array_merge($arOffer, $arOfferInfo);

            }
        }

    }
    else
    {
        $arItemsNew[] = $arItem;
    }
}

uasort($arItemsNew, function ($a, $b)
{
    //return $a['SORT'] - $b['SORT'];
    if ($a['SORT'] == $b)
    {
        return 0;
    }

    return ($a['SORT'] > $b['SORT']) ? -1 : 1;
});

$arResult['ITEMS'] = $arItemsNew;
