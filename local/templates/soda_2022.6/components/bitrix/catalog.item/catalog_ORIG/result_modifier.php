<?php	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arCML2_LINK = $arResult['ITEM']['colors'][ array_key_first($arResult['ITEM']['colors']) ]['item']['PROPERTIES']["CML2_LINK"];

$arFilter = ["IBLOCK_ID" => $arCML2_LINK['LINK_IBLOCK_ID'], "ID" => $arCML2_LINK['VALUE'], "ACTIVE"=>"Y"];
$arSelect = Array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PROPERTY_CML2_ARTICLE");
$res4 = CIBlockElement::GetList(Array("SORT"=>'ASC'), $arFilter, false, false, $arSelect)->GetNext(1, 0);

$arResult['ITEM']['CML2_ARTICLE'] = $res4['PROPERTY_CML2_ARTICLE_VALUE'];
$sss = CIBlockSection::GetList([], ['ID' => $res4['IBLOCK_SECTION_ID'], 'IBLOCK_ID' => $res4['IBLOCK_ID']], false, ['NAME','UF_NAME_EN'])->Fetch();
$arResult['ITEM']['NAMES']['en'] = $sss['UF_NAME_EN'];
$arResult['ITEM']['NAMES']['ru'] = $sss['NAME'];
