<?php
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
		die();
	}
	
	/** @global CMain $APPLICATION */
	/** @global CUser $USER */
	/** @global CDatabase $DB */
	/** @var CBitrixComponent $this */
	/** @var array $arParams */
	/** @var array $arResult */
	/** @var string $componentName */
	/** @var string $componentPath */
	/** @var string $componentTemplate */
	/** @var string $parentComponentName */
	/** @var string $parentComponentPath */
	/** @var string $parentComponentTemplate */
	$this->setFrameMode(false);
	
	use Bitrix\Main\Loader;
	use Bitrix\Highloadblock\HighloadBlockTable;
	
	
	Loader::includeModule('iblock');
	
	$arProductObj = CIBlockElement::getList(['SORT' => 'DESC'], ['IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y'], false, false, []);
	while ($arItem = $arProductObj->GetNextElement()) {
		$arProduct = $arItem->GetFields();
		$arProduct['PROPERTIES'] = $arItem->GetProperties();
		$arSizeCrop = ['width' => 1344, 'height' => 1344];
		$arSizeThumbsCrop = ['width' => 240, 'height' => 360];
		$arImage = CFile::ResizeImageGet($arProduct['DETAIL_PICTURE'], $arSizeCrop, BX_RESIZE_IMAGE_EXACT);
		$thumbs = [];
		
		global $USER;
		
		if(count($arProduct['PROPERTIES']['SLIDE_THUMBS']['VALUE']) > 0){
			foreach ($arProduct['PROPERTIES']['SLIDE_THUMBS']['VALUE'] as $thumb){
				$thumbs[] = CFile::ResizeImageGet($thumb, $arSizeCrop, BX_RESIZE_IMAGE_EXACT)['src'];
			}
		}
		$item = [
			'NAME' => $arProduct['NAME'],
			'ENG_NAME' => $arProduct['PROPERTIES']['NAME_ENG']['VALUE'],
			'DESCRIPTION' => $arProduct['PROPERTIES']['DESCRIPTION']['VALUE'],
			'ENG_DESCRIPTION' => $arProduct['PROPERTIES']['DESCRIPTION_ENG']['VALUE'],
			'THUMB' => $arImage['src'],
			'THUMBS' => $thumbs,
			'IS_THUMBS' => count($thumbs) === 6
		];
		if(!empty($arProduct['PROPERTIES']['LINK_SECTION']['VALUE'])){
			$sectionsObj = CIBlockSection::GetByID($arProduct['PROPERTIES']['LINK_SECTION']['VALUE']);
			if ($rsSection = $sectionsObj->GetNextElement()) {
				$selectSection = $rsSection->GetFields();
				$item['LINK'] = $selectSection['SECTION_PAGE_URL'];
			}
		}
		if(!empty($arProduct['PROPERTIES']['SLIDE_PRODUCT']['VALUE'])){
			//$item['LINK'] =$arProduct['PROPERTIES']['LINK_ELEMENT']['VALUE'];
			$productsObj = CIBlockElement::GetByID($arProduct['PROPERTIES']['SLIDE_PRODUCT']['VALUE']);
			if ($rsProduct = $productsObj->GetNextElement()) {
				$selectProduct = $rsProduct->GetFields();
				$selectProduct['PROPERTIES'] = $rsProduct->GetProperties();
				
				$rs = CPrice::GetList(["CATALOG_GROUP_ID" => "ASC"], ["=PRODUCT_ID" => $arProduct['PROPERTIES']['SLIDE_PRODUCT']['VALUE']]);
				while ($arPrice = $rs->Fetch()){
					$arPrice["PRICE"] = floatval($arPrice["PRICE"]);
					$selectProduct['PRICES'][] = $arPrice;
				}
				$item['PRICE'] = $selectProduct['PRICES'][0];
				
				$item['LINK'] = $selectProduct['DETAIL_PAGE_URL'].'?setColor='.$arProduct['PROPERTIES']['SLIDE_COLOR']['VALUE'];
			}
		}
		$arResult['ITEMS'][] = $item;
	}
	$this->includeComponentTemplate();
?>