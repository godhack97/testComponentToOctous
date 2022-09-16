<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
	
	use Bitrix\Main\Localization\Loc;
	use Bitrix\Sale\PriceMaths;
	
	use Bitrix\Main,
		Bitrix\Main\Context,
		Bitrix\Currency\CurrencyManager,
		Bitrix\Sale\Order,
		Bitrix\Sale,
		Bitrix\Sale\Basket,
		Bitrix\Sale\Delivery,
		Bitrix\Sale\PaySystem;
	
	
	/**
	 *
	 * This file modifies result for every request (including AJAX).
	 * Use it to edit output result for "{{ mustache }}" templates.
	 *
	 * @var array $result
	 */
	
	
	CBitrixComponent::includeComponentClass('arlix:product.subscribe');
	
	$mobileColumns = isset($this->arParams['COLUMNS_LIST_MOBILE'])
		? $this->arParams['COLUMNS_LIST_MOBILE']
		: $this->arParams['COLUMNS_LIST'];
	$mobileColumns = array_fill_keys($mobileColumns, true);
	
	$result['BASKET_ITEM_RENDER_DATA'] = array();
	
	foreach ($this->basketItems as $item) {
		if ($item['PRODUCT_ID']) {
			$arNoPhotoProductsIds[] = $item['PRODUCT_ID'];
		}
	}
	
	if (!empty($arNoPhotoProductsIds)) {
		$getForColorProductPhoto = getForColorProductPhoto($arNoPhotoProductsIds);
		$arNoPhotoProducts = $getForColorProductPhoto['NO_PHOTO_PRODUCTS'];
		$arWithPhotoProducts = $getForColorProductPhoto['WITH_PHOTO_PRODUCTS'];
	}
	
	function getAddCustomDiscount($price, $percent, $onlyPercentVal = false){
		if($onlyPercentVal){
			return ($price / 100 * $percent);
		}
		else{
			return ($price + ($price / 100 * $percent));
		}
	}
	
	
	$globalCurrencyUserCheck = (isset($_COOKIE["CATALOG_CURRENCY"]) && !empty($_COOKIE["CATALOG_CURRENCY"]));
	
	foreach ($this->basketItems as $row) {
		if (SITE_ID == 's2' && !empty($row["PROPERTY_ENG_NAIMENOVANIE_VALUE"])) {
			$row['NAME'] = $row['~NAME'] = $row["PROPERTY_ENG_NAIMENOVANIE_VALUE"];
		}
		
		$globalCurrencyUser = isset($_COOKIE["CATALOG_CURRENCY"]) && !empty($_COOKIE["CATALOG_CURRENCY"]) ? strtoupper($_COOKIE["CATALOG_CURRENCY"]) : $row['CURRENCY'];
		
		$mxResult = \CCatalogSku::GetProductInfo($row['PRODUCT_ID']);
    if(!$mxResult){
      $res = \CIBlockElement::GetByID($row['PRODUCT_ID']);
    }
    else {
      $res = \CIBlockElement::GetByID($mxResult['ID']);
    }
		$arFields = [];
		if ($ar_res = $res->GetNext()) {
			$itemProduct = $ar_res;
			$arFilter = [
				'IBLOCK_ID' => CATALOG_ID,
				'ACTIVE' => 'Y',
				'=ID' => $itemProduct,
			];
			$res = CIBlockElement::GetList([], $arFilter, false, array('nPageSize' => 1), []);
			if ($ob = $res->GetNextElement()) {
				$arFields = $ob->GetProperties();
			}
			if($globalCurrencyUser !== $row['CURRENCY']){
				if (isset($arFields['PROTSENT_IZMENENIYA_TSENY']) && !empty($arFields['PROTSENT_IZMENENIYA_TSENY']['VALUE'])) {
					$row['IS_CUSTOM_PRICE'] = true;
					$row['DISCOUNT_PRICE'] = getAddCustomDiscount(($row['FULL_PRICE'] * $row['QUANTITY']), $arFields['PROTSENT_IZMENENIYA_TSENY']['VALUE'], true);
					$row['FULL_PRICE'] = getAddCustomDiscount(($row['FULL_PRICE'] * $row['QUANTITY']), $arFields['PROTSENT_IZMENENIYA_TSENY']['VALUE']);
					$row['FULL_PRICE_FORMATED'] = CCurrencyLang::CurrencyFormat($row['FULL_PRICE'], $row['CURRENCY'], true);
					
					$row['PRICE'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($row['PRICE'], $row['CURRENCY'], $globalCurrencyUser), $globalCurrencyUser, true, true);
					$row['PRICE_FORMATED'] = html_entity_decode(SaleFormatCurrency($row['PRICE'], $globalCurrencyUser));
					
					$row['FULL_PRICE'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($row['FULL_PRICE'], $row['CURRENCY'], $globalCurrencyUser), $globalCurrencyUser, true, true);
					$row['FULL_PRICE_FORMATED'] = html_entity_decode(SaleFormatCurrency($row['FULL_PRICE'], $globalCurrencyUser));
					$row['DISCOUNT_PRICE'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($row['DISCOUNT_PRICE'], $row['CURRENCY'], $globalCurrencyUser), $globalCurrencyUser, true, true);
					$row['DISCOUNT_PRICE_FORMATED'] = html_entity_decode(SaleFormatCurrency($row['DISCOUNT_PRICE'], $globalCurrencyUser));
					$row['SUM_VALUE'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($row['SUM_VALUE'], $row['CURRENCY'], $globalCurrencyUser), $globalCurrencyUser, true, true);
					$row['SUM'] = html_entity_decode(SaleFormatCurrency($row['SUM_VALUE'], $globalCurrencyUser));
					$row['SUM_FULL_PRICE'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($row['SUM_FULL_PRICE'], $row['CURRENCY'], $globalCurrencyUser), $globalCurrencyUser, true, true);
					$row['SUM_FULL_PRICE_FORMATED'] = html_entity_decode(SaleFormatCurrency($row['SUM_FULL_PRICE'], $globalCurrencyUser));
					$row['SUM_DISCOUNT_PRICE'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($row['SUM_DISCOUNT_PRICE'], $row['CURRENCY'], $globalCurrencyUser), $globalCurrencyUser, true, true);
					$row['SUM_DISCOUNT_PRICE_FORMATED'] = html_entity_decode(SaleFormatCurrency(CCurrencyRates::ConvertCurrency($row['SUM_DISCOUNT_PRICE'], $row['CURRENCY'], $globalCurrencyUser), $globalCurrencyUser));
					
					$row['CURRENCY'] = $globalCurrencyUser;
				}
				else{
					$row['PRICE'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($row['PRICE'], $row['CURRENCY'], $globalCurrencyUser), $globalCurrencyUser, true, true);
					$row['PRICE_FORMATED'] = html_entity_decode(SaleFormatCurrency($row['PRICE'], $globalCurrencyUser));
					
					$row['FULL_PRICE'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($row['FULL_PRICE'] * $row['QUANTITY'], $row['CURRENCY'], $globalCurrencyUser), $globalCurrencyUser, true, true);
					$row['FULL_PRICE_FORMATED'] = html_entity_decode(SaleFormatCurrency($row['FULL_PRICE'], $globalCurrencyUser));
					$row['DISCOUNT_PRICE'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($row['DISCOUNT_PRICE'], $row['CURRENCY'], $globalCurrencyUser), $globalCurrencyUser, true, true);
					$row['DISCOUNT_PRICE_FORMATED'] = html_entity_decode(SaleFormatCurrency($row['DISCOUNT_PRICE'], $globalCurrencyUser));
					$row['SUM_VALUE'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($row['SUM_VALUE'], $row['CURRENCY'], $globalCurrencyUser), $globalCurrencyUser, true, true);
					$row['SUM'] = html_entity_decode(SaleFormatCurrency($row['SUM_VALUE'], $globalCurrencyUser));
					$row['SUM_FULL_PRICE'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($row['SUM_FULL_PRICE'], $row['CURRENCY'], $globalCurrencyUser), $globalCurrencyUser, true, true);
					$row['SUM_FULL_PRICE_FORMATED'] = html_entity_decode(SaleFormatCurrency($row['SUM_FULL_PRICE'], $globalCurrencyUser));
					$row['SUM_DISCOUNT_PRICE'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($row['SUM_DISCOUNT_PRICE'], $row['CURRENCY'], $globalCurrencyUser), $globalCurrencyUser, true, true);
					$row['SUM_DISCOUNT_PRICE_FORMATED'] = html_entity_decode(SaleFormatCurrency(CCurrencyRates::ConvertCurrency($row['SUM_DISCOUNT_PRICE'], $row['CURRENCY'], $globalCurrencyUser), $globalCurrencyUser));
					
					$row['CURRENCY'] = $globalCurrencyUser;
				}
			}
			else{
				if (isset($arFields['PROTSENT_IZMENENIYA_TSENY']) && !empty($arFields['PROTSENT_IZMENENIYA_TSENY']['VALUE'])) {
					$row['IS_CUSTOM_PRICE'] = true;
					$row['DISCOUNT_PRICE'] = getAddCustomDiscount(($row['FULL_PRICE'] * $row['QUANTITY']), $arFields['PROTSENT_IZMENENIYA_TSENY']['VALUE'], true);
					$row['FULL_PRICE'] = getAddCustomDiscount(($row['FULL_PRICE'] * $row['QUANTITY']), $arFields['PROTSENT_IZMENENIYA_TSENY']['VALUE']);
					$row['FULL_PRICE_FORMATED'] = CCurrencyLang::CurrencyFormat($row['FULL_PRICE'], $row['CURRENCY'], true);
				}
			}
		}
		$rowData = array(
			'ID' => $row['ID'],
			"PROPERTY_COLOR_VALUE" => $row["PROPERTY_COLOR_VALUE"],
			"NAME_EN" => $row["PROPERTY_ENG_NAIMENOVANIE_VALUE"],
			"SITE_ID" => SITE_ID,
			'PRODUCT_ID' => $row['PRODUCT_ID'],
			'NAME' => isset($row['~NAME']) ? $row['~NAME'] : $row['NAME'],
			'QUANTITY' => $row['QUANTITY'],
			'PROPS' => $row['PROPS'],
			'PROPS_ALL' => $row['PROPS_ALL'],
			'HASH' => $row['HASH'],
			'SORT' => $row['SORT'],
			'DETAIL_PAGE_URL' => $row['DETAIL_PAGE_URL'],
			'CURRENCY' => $row['CURRENCY'],
			'USE_EXTEND_CURRENCY' => isset($_COOKIE["CATALOG_CURRENCY"]) && !empty($_COOKIE["CATALOG_CURRENCY"]) && $_COOKIE["CATALOG_CURRENCY"] !== $row['CURRENCY'],
			'EXTEND_CURRENCY' => isset($_COOKIE["CATALOG_CURRENCY"]) && !empty($_COOKIE["CATALOG_CURRENCY"]) ? strtoupper($_COOKIE["CATALOG_CURRENCY"]) : $row['CURRENCY'],
			'DISCOUNT_PRICE_PERCENT' => $row['DISCOUNT_PRICE_PERCENT'],
			'DISCOUNT_PRICE_PERCENT_FORMATED' => $row['DISCOUNT_PRICE_PERCENT_FORMATED'],
			'SHOW_DISCOUNT_PRICE' => (float)$row['DISCOUNT_PRICE'] > 0,
			'PRICE' => $row['PRICE'],
			'PRICE_FORMATED' => $row['PRICE_FORMATED'],
			'FULL_PRICE' => $row['FULL_PRICE'],
			'FULL_PRICE_FORMATED' => $row['FULL_PRICE_FORMATED'],
			'DISCOUNT_PRICE' => $row['DISCOUNT_PRICE'],
			'DISCOUNT_PRICE_FORMATED' => $row['DISCOUNT_PRICE_FORMATED'],
			'SUM_PRICE' => $row['SUM_VALUE'],
			'SUM_PRICE_FORMATED' => $row['SUM'],
			'SUM_FULL_PRICE' => $row['SUM_FULL_PRICE'],
			'SUM_FULL_PRICE_FORMATED' => $row['SUM_FULL_PRICE_FORMATED'],
			'SUM_DISCOUNT_PRICE' => $row['SUM_DISCOUNT_PRICE'],
			'SUM_DISCOUNT_PRICE_FORMATED' => $row['SUM_DISCOUNT_PRICE_FORMATED'],
			'MEASURE_RATIO' => isset($row['MEASURE_RATIO']) ? $row['MEASURE_RATIO'] : 1,
			'MEASURE_TEXT' => $row['MEASURE_TEXT'],
			'AVAILABLE_QUANTITY' => $row['AVAILABLE_QUANTITY'],
			'CHECK_MAX_QUANTITY' => $row['CHECK_MAX_QUANTITY'],
			'MODULE' => $row['MODULE'],
			'PRODUCT_PROVIDER_CLASS' => $row['PRODUCT_PROVIDER_CLASS'],
			'NOT_AVAILABLE' => $row['NOT_AVAILABLE'] === true || $itemProduct['ACTIVE'] !== 'Y',
			'DELAYED' => $row['DELAY'] === 'Y',
			'SKU_BLOCK_LIST' => array(),
			'COLUMN_LIST' => array(),
			'SHOW_LABEL' => false,
			'LABEL_VALUES' => array(),
			'IS_CUSTOM_PRICE' => $row['IS_CUSTOM_PRICE'],
			'BRAND' => isset($row[$this->arParams['BRAND_PROPERTY'] . '_VALUE'])
				? $row[$this->arParams['BRAND_PROPERTY'] . '_VALUE']
				: '',
		);
		
		if ($arNoPhotoProducts[$row["PRODUCT_ID"]]) {
			$arWithPhotoProductThis = $arNoPhotoProducts[$row["PRODUCT_ID"]];
			$itemDetailPicture = $arWithPhotoProducts[$arWithPhotoProductThis['CML2_LINK']][$arWithPhotoProductThis['COLOR']]['DETAIL_PICTURE'];
		}
		if ($itemDetailPicture) {
			$itemDetailPictureResize = CFile::ResizeImageGet($itemDetailPicture, array('width' => 320, 'height' => 1000), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);
			$row['DETAIL_PICTURE_SRC'] = $itemDetailPictureResize['src'];
			$row['PREVIEW_PICTURE_SRC'] = $itemDetailPictureResize['src'];
		}
		
		// show price including ratio
		if ($rowData['MEASURE_RATIO'] != 1) {
			$price = PriceMaths::roundPrecision($rowData['PRICE'] * $rowData['MEASURE_RATIO']);
			if ($price != $rowData['PRICE']) {
				$rowData['PRICE'] = $price;
				$rowData['PRICE_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($price, $rowData['CURRENCY'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"], true, true);
				$rowData['PRICE_FORMATED'] = CCurrencyLang::CurrencyFormat($price, $rowData['CURRENCY'], true);
				$rowData['PRICE_FORMATED_EXTEND'] = html_entity_decode(SaleFormatCurrency(CCurrencyRates::ConvertCurrency($price, $rowData['CURRENCY'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]));
			}
			
			$fullPrice = PriceMaths::roundPrecision($rowData['FULL_PRICE'] * $rowData['MEASURE_RATIO']);
			if ($fullPrice != $rowData['FULL_PRICE']) {
				$rowData['FULL_PRICE'] = $fullPrice;
				$rowData['FULL_PRICE_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($fullPrice, $rowData['CURRENCY'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"], true, true);
				$rowData['FULL_PRICE_FORMATED'] = CCurrencyLang::CurrencyFormat($fullPrice, $rowData['CURRENCY'], true);
				$rowData['FULL_PRICE_FORMATED_EXTEND'] = html_entity_decode(SaleFormatCurrency(CCurrencyRates::ConvertCurrency($fullPrice, $rowData['CURRENCY'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]));
			}
			
			$discountPrice = PriceMaths::roundPrecision($rowData['DISCOUNT_PRICE'] * $rowData['MEASURE_RATIO']);
			if ($discountPrice != $rowData['DISCOUNT_PRICE']) {
				$rowData['DISCOUNT_PRICE'] = $discountPrice;
				$rowData['DISCOUNT_PRICE_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($discountPrice, $rowData['CURRENCY'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"], true, true);
				$rowData['DISCOUNT_PRICE_FORMATED'] = CCurrencyLang::CurrencyFormat($discountPrice, $rowData['CURRENCY'], true);
				$rowData['DISCOUNT_PRICE_FORMATED_EXTEND'] = html_entity_decode(SaleFormatCurrency(CCurrencyRates::ConvertCurrency($discountPrice, $rowData['CURRENCY'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]));
			}
		}
		
		$rowData['SHOW_PRICE_FOR'] = (float)$rowData['QUANTITY'] !== (float)$rowData['MEASURE_RATIO'];
		
		$hideDetailPicture = false;
		
		if (!empty($row['PREVIEW_PICTURE_SRC'])) {
			$rowData['IMAGE_URL'] = $row['PREVIEW_PICTURE_SRC'];
		} elseif (!empty($row['DETAIL_PICTURE_SRC'])) {
			$hideDetailPicture = true;
			$rowData['IMAGE_URL'] = $row['DETAIL_PICTURE_SRC'];
		}
		
		if (!empty($row['SKU_DATA'])) {
			$propMap = array();
			
			foreach ($row['PROPS'] as $prop) {
				$propMap[$prop['CODE']] = !empty($prop['~VALUE']) ? $prop['~VALUE'] : $prop['VALUE'];
			}
			
			$notSelectable = true;
			
			foreach ($row['SKU_DATA'] as $skuBlock) {
				$skuBlockData = array(
					'ID' => $skuBlock['ID'],
					'CODE' => $skuBlock['CODE'],
					'NAME' => $skuBlock['NAME']
				);
				
				$isSkuSelected = false;
				$isImageProperty = false;
				
				if (count($skuBlock['VALUES']) > 1) {
					$notSelectable = false;
				}
				
				foreach ($skuBlock['VALUES'] as $skuItem) {
					$skuValue = array(
						'ID' => $skuItem['ID'],
						'NAME' => $skuItem['NAME'],
						'SORT' => $skuItem['SORT'],
						'PICT' => !empty($skuItem['PICT']) ? $skuItem['PICT']['SRC'] : false,
						'XML_ID' => !empty($skuItem['XML_ID']) ? $skuItem['XML_ID'] : false,
						'VALUE_ID' => (
						$skuBlock['TYPE'] === 'S' && $skuBlock['USER_TYPE'] === 'directory'
							? $skuItem['XML_ID']
							: $skuItem['NAME']
						),
						'PROP_ID' => $skuBlock['ID'],
						'PROP_CODE' => $skuBlock['CODE']
					);
					
					if (
						!empty($propMap[$skuBlockData['CODE']])
						&& ($propMap[$skuBlockData['CODE']] == $skuItem['NAME'] || $propMap[$skuBlockData['CODE']] == $skuItem['XML_ID'])
					) {
						$skuValue['SELECTED'] = true;
						$isSkuSelected = true;
					}
					
					$skuBlockData['SKU_VALUES_LIST'][] = $skuValue;
					$isImageProperty = $isImageProperty || !empty($skuItem['PICT']);
				}
				
				if (!$isSkuSelected && !empty($skuBlockData['SKU_VALUES_LIST'][0])) {
					$skuBlockData['SKU_VALUES_LIST'][0]['SELECTED'] = true;
				}
				
				$skuBlockData['IS_IMAGE'] = $isImageProperty;
				
				$rowData['SKU_BLOCK_LIST'][] = $skuBlockData;
			}
		}
		
		if ($row['NOT_AVAILABLE']) {
			foreach ($rowData['SKU_BLOCK_LIST'] as $blockKey => $skuBlock) {
				if (!empty($skuBlock['SKU_VALUES_LIST'])) {
					if ($notSelectable) {
						foreach ($skuBlock['SKU_VALUES_LIST'] as $valueKey => $skuValue) {
							$rowData['SKU_BLOCK_LIST'][$blockKey]['SKU_VALUES_LIST'][0]['NOT_AVAILABLE_OFFER'] = true;
						}
					} elseif (!isset($rowData['SKU_BLOCK_LIST'][$blockKey + 1])) {
						foreach ($skuBlock['SKU_VALUES_LIST'] as $valueKey => $skuValue) {
							if ($skuValue['SELECTED']) {
								$rowData['SKU_BLOCK_LIST'][$blockKey]['SKU_VALUES_LIST'][$valueKey]['NOT_AVAILABLE_OFFER'] = true;
							}
						}
					}
				}
			}
		}
		
		if (!empty($result['GRID']['HEADERS']) && is_array($result['GRID']['HEADERS'])) {
			foreach ($result['GRID']['HEADERS'] as &$value) {
				if (
					$value['id'] === 'NAME' || $value['id'] === 'QUANTITY' || $value['id'] === 'PRICE'
					|| $value['id'] === 'PREVIEW_PICTURE' || ($value['id'] === 'DETAIL_PICTURE' & $hideDetailPicture)
					|| $value['id'] === 'SUM' || $value['id'] === 'PROPS' || $value['id'] === 'DELETE'
					|| $value['id'] === 'DELAY'
				) {
					continue;
				}
				
				$isPropSizeValue = $value['id'] === "PROPERTY_SIZES_VALUE" || $value['id'] === "PROPERTY_SIZES_JEANS_VALUE" || $value['id'] === "PROPERTY_SIZES_SHOES_VALUE";
				$isPropColorValue = $value['id'] === "PROPERTY_COLOR_VALUE";
				switch ($value['id']){
					case 'DETAIL_PICTURE':
						$value['name'] = Loc::getMessage('SBB_DETAIL_PICTURE_NAME');
						
						if (!empty($row['DETAIL_PICTURE_SRC']))
						{
							$rowData['COLUMN_LIST'][] = array(
								'CODE' => $value['id'],
								'NAME' => $value['name'],
								'VALUE' => array(
									array(
										'IMAGE_SRC' => $row['DETAIL_PICTURE_SRC'],
										'IMAGE_SRC_2X' => $row['DETAIL_PICTURE_SRC_2X'],
										'IMAGE_SRC_ORIGINAL' => $row['DETAIL_PICTURE_SRC_ORIGINAL'],
										'INDEX' => 0
									)
								),
								'IS_IMAGE' => true,
								'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
							);
						}
						break;
					case 'PREVIEW_TEXT':
						$value['name'] = Loc::getMessage('SBB_PREVIEW_TEXT_NAME');
						
						if ($row['PREVIEW_TEXT_TYPE'] === 'text' && !empty($row['PREVIEW_TEXT']))
						{
							$rowData['COLUMN_LIST'][] = array(
								'CODE' => $value['id'],
								'NAME' => $value['name'],
								'VALUE' => $row['PREVIEW_TEXT'],
								'IS_TEXT' => true,
								'IS_PROPERTY_SIZES_VALUE' => $value['id'] == "PROPERTY_SIZES_VALUE"?true:false,
								'IS_PROPERTY_COLOR_VALUE' => $value['id'] == "PROPERTY_COLOR_VALUE"?true:false,
								'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
							);
						}
						break;
					case 'TYPE':
						$value['name'] = Loc::getMessage('SBB_PRICE_TYPE_NAME');
						
						if (!empty($row['NOTES']))
						{
							$rowData['COLUMN_LIST'][] = array(
								'CODE' => $value['id'],
								'NAME' => $value['name'],
								'VALUE' => isset($row['~NOTES']) ? $row['~NOTES'] : $row['NOTES'],
								'IS_TEXT' => true,
								'IS_PROPERTY_SIZES_VALUE' => $isPropSizeValue,
								'IS_PROPERTY_COLOR_VALUE' => $isPropColorValue,
								'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
							);
						}
						break;
					case 'DISCOUNT':
						$value['name'] = Loc::getMessage('SBB_DISCOUNT_NAME');
						
						if ($row['DISCOUNT_PRICE_PERCENT'] > 0 && !empty($row['DISCOUNT_PRICE_PERCENT_FORMATED']))
						{
							$rowData['COLUMN_LIST'][] = array(
								'CODE' => $value['id'],
								'NAME' => $value['name'],
								'VALUE' => $row['DISCOUNT_PRICE_PERCENT_FORMATED'],
								'IS_TEXT' => true,
								'IS_PROPERTY_SIZES_VALUE' => $isPropSizeValue,
								'IS_PROPERTY_COLOR_VALUE' => $isPropColorValue,
								'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
							);
						}
						break;
					case 'WEIGHT':
						$value['name'] = Loc::getMessage('SBB_WEIGHT_NAME');
						
						if (!empty($row['WEIGHT_FORMATED']))
						{
							$rowData['COLUMN_LIST'][] = array(
								'CODE' => $value['id'],
								'NAME' => $value['name'],
								'VALUE' => $row['WEIGHT_FORMATED'],
								'IS_TEXT' => true,
								'IS_PROPERTY_SIZES_VALUE' => $isPropSizeValue,
								'IS_PROPERTY_COLOR_VALUE' => $isPropColorValue,
								'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
							);
						}
						break;
					default:
						if (!empty($row[$value['id'].'_SRC']))
						{
							$i = 0;
							
							foreach ($row[$value['id'].'_SRC'] as &$image)
							{
								$image['INDEX'] = $i++;
							}
							
							$rowData['COLUMN_LIST'][] = array(
								'CODE' => $value['id'],
								'NAME' => $value['name'],
								'VALUE' => $row[$value['id'].'_SRC'],
								'IS_IMAGE' => true,
								'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
							);
						}
						elseif (!empty($row[$value['id'].'_DISPLAY']))
						{
							$rowData['COLUMN_LIST'][] = array(
								'CODE' => $value['id'],
								'NAME' => $value['name'],
								'VALUE' => $row[$value['id'].'_DISPLAY'],
								'IS_TEXT' => true,
								'IS_PROPERTY_SIZES_VALUE' => $isPropSizeValue,
								'IS_PROPERTY_COLOR_VALUE' => $value['id'] == "PROPERTY_COLOR_VALUE"?true:false,
								'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
							);
						}
						elseif (!empty($row[$value['id'].'_LINK']))
						{
							$linkValues = array();
							
							foreach ($row[$value['id'].'_LINK'] as $index => $link)
							{
								$linkValues[] = array(
									'LINK' => $link,
									'IS_LAST' => !isset($row[$value['id'].'_LINK'][$index + 1])
								);
							}
							
							$rowData['COLUMN_LIST'][] = array(
								'CODE' => $value['id'],
								'NAME' => $value['name'],
								'VALUE' => $linkValues,
								'IS_LINK' => true,
								'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
							);
						}
						elseif (!empty($row[$value['id']]))
						{
							$rawValue = isset($row['~'.$value['id']]) ? $row['~'.$value['id']] : $row[$value['id']];
							$isHtml = !empty($row[$value['id'].'_HTML']);
							
							$rowData['COLUMN_LIST'][] = array(
								'CODE' => $value['id'],
								'NAME' => $value['name'],
								'VALUE' => $rawValue,
								'IS_TEXT' => !$isHtml,
								'IS_HTML' => $isHtml,
								'IS_PROPERTY_SIZES_VALUE' => $isPropSizeValue,
								'IS_PROPERTY_COLOR_VALUE' => $isPropColorValue,
								'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
							);
						}
						break;
				}
			}
		}
		
		if (!empty($row['LABEL_ARRAY_VALUE'])) {
			$labels = array();
			
			foreach ($row['LABEL_ARRAY_VALUE'] as $code => $value) {
				$labels[] = array(
					'NAME' => $value,
					'HIDE_MOBILE' => !isset($this->arParams['LABEL_PROP_MOBILE'][$code])
				);
			}
			
			$rowData['SHOW_LABEL'] = true;
			$rowData['LABEL_VALUES'] = $labels;
		}
		
		$result['BASKET_ITEM_RENDER_DATA'][] = $rowData;
	}
	
	if($globalCurrencyUserCheck !== $result['CURRENCY']){
		$result['allSum'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($result['allSum'], $result['CURRENCY'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"], true, true);
		$result['allSum_FORMATED'] = SaleFormatCurrency($result['allSum'], $_COOKIE["CATALOG_CURRENCY"]);
	}
	$totalData = array(
		'DISABLE_CHECKOUT' => (int)$result['ORDERABLE_BASKET_ITEMS_COUNT'] === 0,
		'PRICE' => $result['allSum'],
		//'PRICE_EXTEND' => SaleFormatCurrency(CCurrencyRates::ConvertCurrency($altSumNumber, $result['CURRENCY'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"], true, true),
		//'PRICE_FORMATED' => $result['allSum_FORMATED'],
		'PRICE_FORMATED' => html_entity_decode(SaleFormatCurrency($result['allSum'], $_COOKIE["CATALOG_CURRENCY"])),
		//'PRICE_WITHOUT_DISCOUNT_FORMATED' => $result['PRICE_WITHOUT_DISCOUNT'],
		'PRICE_WITHOUT_DISCOUNT_FORMATED' => html_entity_decode(SaleFormatCurrency($result['allSum'], $_COOKIE["CATALOG_CURRENCY"])),
		'CURRENCY' => $globalCurrencyUserCheck !== $result['CURRENCY'] ? $_COOKIE["CATALOG_CURRENCY"] : $result['CURRENCY'],
		'EXTEND_CURRENCY' => isset($_COOKIE["CATALOG_CURRENCY"]) && !empty($_COOKIE["CATALOG_CURRENCY"]) ? strtoupper($_COOKIE["CATALOG_CURRENCY"]) : $result['CURRENCY'],
		'USE_EXTEND_CURRENCY' => isset($_COOKIE["CATALOG_CURRENCY"]) && !empty($_COOKIE["CATALOG_CURRENCY"]) && $_COOKIE["CATALOG_CURRENCY"] !== $result['CURRENCY'],
		
		/* 'FULL_PRICE' => $result['FULL_PRICE'],
		 'FULL_PRICE_DELIVERY' => $result['FULL_PRICE_DELIVERY'],
		 'FULL_PRICE_FORMATED' => $result['FULL_PRICE_FORMATED'],
		 'FULL_PRICE_DELIVERY_FORMATED' => $result['FULL_PRICE_DELIVERY_FORMATED'],
	*/
	);
	
	
	if ($result['DISCOUNT_PRICE_ALL'] > 0) {
		$totalData['DISCOUNT_PRICE_FORMATED'] = $result['DISCOUNT_PRICE_FORMATED'];
		$totalData['DISCOUNT_PRICE_FORMATED_EXTEND'] = $result['DISCOUNT_PRICE_FORMATED'];
	}
	
	if ($result['allWeight'] > 0) {
		$totalData['WEIGHT_FORMATED'] = $result['allWeight_FORMATED'];
	}
	
	if ($this->priceVatShowValue === 'Y') {
		$totalData['SHOW_VAT'] = true;
		$totalData['VAT_SUM_FORMATED'] = $result['allVATSum_FORMATED'];
		$totalData['SUM_WITHOUT_VAT_FORMATED'] = $result['allSum_wVAT_FORMATED'];
	}
	
	if ($this->hideCoupon !== 'Y' && !empty($result['COUPON_LIST'])) {
		$totalData['COUPON_LIST'] = $result['COUPON_LIST'];
		
		foreach ($totalData['COUPON_LIST'] as &$coupon) {
			if ($coupon['JS_STATUS'] === 'ENTERED') {
				$coupon['CLASS'] = 'danger';
			} elseif ($coupon['JS_STATUS'] === 'APPLYED') {
				$coupon['CLASS'] = 'muted';
			} else {
				$coupon['CLASS'] = 'danger';
			}
		}
	}
	
	
	global $USER;
	
	
	Main\Loader::includeModule("sale");
	Main\Loader::includeModule("catalog");
	
	$siteId = Context::getCurrent()->getSite();
	$currencyCode = CurrencyManager::getBaseCurrency();
	
	$order = Order::create($siteId, $USER->isAuthorized() ? $USER->GetID() : null);
	$basket = Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
	$order->setBasket($basket);
	
	$shipmentCollection = $order->getShipmentCollection();
	$shipment = $shipmentCollection->createItem();
	
	$service = Delivery\Services\Manager::getActiveList();
	
	foreach ($service as $el) {
		$shipment->setFields(array(
			'DELIVERY_ID' => $el['ID'],
			'DELIVERY_NAME' => $el['NAME'],
		));
		$shipmentItemCollection = $shipment->getShipmentItemCollection();
		foreach ($order->getBasket() as $item) {
			$shipmentItem = $shipmentItemCollection->createItem($item);
			$shipmentItem->setQuantity($item->getQuantity());
		}
		break;
	}
	
	foreach ($result['BASKET_ITEM_RENDER_DATA'] as &$item) {
		foreach ($item['PROPS_ALL'] as &$props) {
			$props['IS_TEXT'] = true;
			$props['IS_PREORDER_PRODUCT_VALUE'] = $props['CODE'] == "PREORDER_PRODUCT" ? false : true;
			$props['IS_PROPERTY_SIZES_VALUE'] = $props['CODE'] == "SIZES" ? true : false;
			$props['IS_PROPERTY_COLOR_VALUE'] = $props['CODE'] == "COLOR" ? true : false;
			$props['IS_SHOW_LEFT'] = ($props['CODE'] != "SIZES" && $props['CODE'] != "PRODUCT.XML_ID" && $props['CODE'] != "CATALOG.XML_ID") ? true : false;
		}
		unset($props);
		$item['PROPS_ALL'] = array_values($item['PROPS_ALL']);
	}
	unset($item);
	
	$totalData['PRICE_WITHOUT_DISCOUNT_FORMATED'] = 0;
	foreach ($result['BASKET_ITEM_RENDER_DATA'] as $basket) {
		$totalData['PRICE_WITHOUT_DISCOUNT_FORMATED'] += ($basket['FULL_PRICE']);
	}
	
	if($globalCurrencyUserCheck !== $result['CURRENCY']){
		$totalData["FULL_PRICE"] = SaleFormatCurrency($result['allSum'], $_COOKIE["CATALOG_CURRENCY"], true, true);//$order->getPrice();
		$totalData["FULL_PRICE_DELIVERY"] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($order->getDeliveryPrice(), $result['CURRENCY'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"], true, true);
		$totalData["FULL_PRICE_FORMATED"] = html_entity_decode(SaleFormatCurrency($result['allSum'], $_COOKIE["CATALOG_CURRENCY"]));
		$totalData["FULL_PRICE_DELIVERY_FORMATED"] = html_entity_decode(SaleFormatCurrency($totalData["FULL_PRICE_DELIVERY"], $_COOKIE["CATALOG_CURRENCY"]));
		$totalData['PRICE_WITHOUT_DISCOUNT_FORMATED'] = html_entity_decode(SaleFormatCurrency($totalData['PRICE_WITHOUT_DISCOUNT_FORMATED'], $_COOKIE["CATALOG_CURRENCY"]));
		
	}
	else{
		$totalData["FULL_PRICE"] = $result['allSum'];//$order->getPrice();
		$totalData["FULL_PRICE_DELIVERY"] = $order->getDeliveryPrice();
		$totalData["FULL_PRICE_FORMATED"] = html_entity_decode(SaleFormatCurrency($totalData["FULL_PRICE"], $order->getCurrency()));
		$totalData["FULL_PRICE_DELIVERY_FORMATED"] = html_entity_decode(SaleFormatCurrency($totalData["FULL_PRICE_DELIVERY"], $order->getCurrency()));
		$totalData['PRICE_WITHOUT_DISCOUNT_FORMATED'] = html_entity_decode(SaleFormatCurrency($totalData['PRICE_WITHOUT_DISCOUNT_FORMATED'], $totalData['CURRENCY']));
		
	}
	$result['TOTAL_RENDER_DATA'] = $totalData;
	
	global $USER;
	
	if ($USER->IsAdmin()) {
		//var_dump(html_entity_decode(SaleFormatCurrency(CCurrencyRates::ConvertCurrency($altSumNumber, $result['CURRENCY'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"])));
	}
	\CModule::IncludeModule("catalog");
	\CModule::IncludeModule("iblock");
	if (is_array($result['GRID']['ROWS']) && count($result['GRID']['ROWS'])) {
		foreach ($result['GRID']['ROWS'] as &$item) {
			$mxResult = \CCatalogSku::GetProductInfo($item['PRODUCT_ID']);
			if (is_array($mxResult)) {
				$res = \CIBlockElement::GetByID($mxResult['ID']);
				if ($ar_res = $res->GetNext()) {
					$item['NAME'] = $ar_res['NAME'];
				}
			}
		}
		unset($item);
	}
	
	foreach ($result["BASKET_ITEM_RENDER_DATA"] as &$item) {
		$mxResult = \CCatalogSku::GetProductInfo($item["PRODUCT_ID"]);
		if (is_array($mxResult)) {
			$res = \CIBlockElement::GetByID($mxResult["ID"]);
			if ($ar_res = $res->GetNext()) {
				$item["NAME"] = $ar_res['~NAME'];
			}
			
			$arSelect = ["ID", "IBLOCK_ID", "NAME", "SORT", "DATE_ACTIVE_FROM", "PROPERTY_*"];
			$arFilter = [
				"IBLOCK_ID" => COLORS_ID,
				"ACTIVE_DATE" => "Y",
				"ACTIVE" => "Y",
				'PROPERTY_PARENT' => $mxResult["ID"],
				'PROPERTY_COLOR' => $item["PROPERTY_COLOR_VALUE"],
			];
			
			$res = CIBlockElement::GetList([], $arFilter, false, array("nPageSize" => 1), $arSelect);
			if ($ob = $res->GetNextElement()) {
				$arFields = $ob->GetProperties();
				
				$images = $arFields["IMAGES"]["VALUE"];
				if ($images && is_array($images) && count($images) > 0) {
					$arPhotoSmall = CFile::ResizeImageGet(
						$images[0],
						[
							'width' => 124,
							'height' => 184
						],
						BX_RESIZE_IMAGE_EXACT
					);
					$item["IMAGE_URL"] = $arPhotoSmall['src'];
				}
			}
		}
		
		foreach ($item['PROPS_ALL'] as $key => $arProps) {
			if (SITE_ID == 's2') {
				$item['PROPS_ALL'][$key]['NAME'] = Loc::getMessage("SBB_FIELD_" . $arProps['CODE']);
			}
		}
	}
	unset($item);