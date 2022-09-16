<?php
	namespace Sodamoda\Core\Events;
	
	use \Bitrix\Main\Loader;
	
	class Iblock{
		static public $iblock_catalog = 1;
		static public $iblock_catalog_section_temp = 70;
		
		public function iblockSectionImport(&$arFields) {
			switch ($arFields["IBLOCK_ID"]){
				case self::$iblock_catalog:
					// Идет импорт из 1С
					if (@$_REQUEST["mode"] == "import"){
						if (empty($arFields["IBLOCK_SECTION_ID"])){
							$arFields["IBLOCK_SECTION_ID"] = self::$iblock_catalog_section_temp;
						}
						$arFields["ACTIVE"] = "N";
						
						return true;
					}
					
					break;
			}
		}
		public function OnStartIBlockElementAdd(&$arParams) {
			Loader::includeModule('iblock');
			
			switch ($arParams["IBLOCK_ID"]){
				case self::$iblock_catalog:
					// Идет импорт из 1С
					if (@$_REQUEST["mode"] == "import"){
						// Импорту из 1С запрещается создавать категории на сайте
						$arParams["IBLOCK_SECTION"] = array_filter($arParams["IBLOCK_SECTION"]);
						if (!empty($arParams["IBLOCK_SECTION"])) {
							$arNewIblockSection = [];
							foreach ($arParams["IBLOCK_SECTION"] as $sectionID) {
								$arFilter = [
									"IBLOCK_ID" => self::$iblock_catalog,
									"ID"        => $sectionID
								];
								$rsSect = \CIBlockSection::GetList([], $arFilter, false, [
									"IBLOCK_ID",
									"ID"
								], false);
								if ($arSect = $rsSect->Fetch()) {
									$arParentSects = [];
									$resNavChain = \CIBlockSection::GetNavChain($arSect["IBLOCK_ID"], $arSect["ID"]);
									while ($arSectionPath = $resNavChain->GetNext()) {
										$arParentSects[$arSectionPath["ID"]] = $arSectionPath;
									}
									
									// Не в разделе "Для синхронизации с 1С (не удалять!)"
									if (!in_array(self::$iblock_catalog_section_temp, array_keys($arParentSects))) {
										$arNewIblockSection[] = $arSect["ID"];
									}
								}
							}
							if (empty($arNewIblockSection)){
								$arNewIblockSection = [0 => 0];
							}
							$arParams["IBLOCK_SECTION"] = $arNewIblockSection;
						}
						
						unset($arParams["DETAIL_TEXT"]); // убираем анонс-описание
						unset($arParams["DETAIL_TEXT_TYPE"]);
						//unset($arParams["BASE_WEIGHT"]);
						//unset($arParams["CODE"]);
						//unset($arParams["SEARCHABLE_CONTENT"]);
						//unset($arFields["QUANTITY"]);
						//unset($arFields["PRODUCT_QUANTITY"]);
					}
					break;
			}
			return true;
		}
		
		public function OnStartIBlockElementUpdate(&$arParams) {
			Loader::includeModule('iblock');
			
			switch ($arParams["IBLOCK_ID"]){
				case self::$iblock_catalog:
					// Идет импорт из 1С
					if (@$_REQUEST["mode"] == "import"){
						$dbOldGroups = \CIBlockElement::GetElementGroups($arParams["ID"], true);
						$arOldGroups = [];
						while ($arGroup = $dbOldGroups->Fetch()){
							$arOldGroups[] = $arGroup["ID"];
						}
						
						// Импорту из 1С запрещается создавать категории на сайте
						$arParams["IBLOCK_SECTION"] = array_filter($arParams["IBLOCK_SECTION"]);
						if (!empty($arParams["IBLOCK_SECTION"])) {
							$arNewIblockSection = [];
							foreach ($arParams["IBLOCK_SECTION"] as $sectionID) {
								$arFilter = [
									"IBLOCK_ID" => self::$iblock_catalog,
									"ID"        => $sectionID
								];
								$rsSect = \CIBlockSection::GetList([], $arFilter, false, [
									"IBLOCK_ID",
									"ID"
								], false);
								if ($arSect = $rsSect->Fetch()) {
									$arParentSectsNew = [];
									$nav = \CIBlockSection::GetNavChain($arSect["IBLOCK_ID"], $arSect["ID"]);
									while ($arSectionPath = $nav->GetNext()) {
										$arParentSectsNew[$arSectionPath["ID"]] = $arSectionPath;
									}
									// Не в разделе "Для синхронизации с 1С (не удалять!)"
									if (!in_array(self::$iblock_catalog_section_temp, array_keys($arParentSectsNew))) {
										$arNewIblockSection[] = $arSect["ID"];
									}
								}
							}
							
							if (!empty($arNewIblockSection)) {
								$arParams["IBLOCK_SECTION"] = $arNewIblockSection;
							}
							else {
								$arParams["IBLOCK_SECTION"] = $arOldGroups;
							}
						}
						else {
							$arParams["IBLOCK_SECTION"] = $arOldGroups;
						}
						
						unset($arParams["DETAIL_TEXT"]); // убираем анонс-описание
						unset($arParams["DETAIL_TEXT_TYPE"]);
						//unset($arParams["BASE_WEIGHT"]);
						//unset($arParams["CODE"]);
						//unset($arParams["SEARCHABLE_CONTENT"]);
						//unset($arFields["QUANTITY"]);
						//unset($arFields["PRODUCT_QUANTITY"]);
					}
					break;
			}
			
			return true;
		}
		public function doIBlockAfterSave($arg1, $arg2 = false) {
			$ELEMENT_ID = false;
			$IBLOCK_ID = false;
			$OFFERS_IBLOCK_ID = false;
			$OFFERS_PROPERTY_ID = false;
			
			if (Loader::includeModule('currency')){
				$strDefaultCurrency = \CCurrency::GetBaseCurrency();
			}
			
			// Check for catalog event
			if (is_array($arg2) && $arg2["PRODUCT_ID"] > 0) {
				// Get iblock element
				$rsPriceElement = \CIBlockElement::GetList([], ["ID" => $arg2["PRODUCT_ID"],], false, false, ["ID", "IBLOCK_ID"]);
				if ($arPriceElement = $rsPriceElement->Fetch()) {
					$arCatalog = \CCatalog::GetByID($arPriceElement["IBLOCK_ID"]);
					if (is_array($arCatalog)) {
						// Check if it is offers iblock
						if ($arCatalog["OFFERS"] == "Y") {
							// Find product element
							$rsElement = \CIBlockElement::GetProperty($arPriceElement["IBLOCK_ID"], $arPriceElement["ID"], "sort", "asc", ["ID" => $arCatalog["SKU_PROPERTY_ID"]]);
							$arElement = $rsElement->Fetch();
							if ($arElement && $arElement["VALUE"] > 0) {
								$ELEMENT_ID = $arElement["VALUE"];
								$IBLOCK_ID = $arCatalog["PRODUCT_IBLOCK_ID"];
								$OFFERS_IBLOCK_ID = $arCatalog["IBLOCK_ID"];
								$OFFERS_PROPERTY_ID = $arCatalog["SKU_PROPERTY_ID"];
							}
							// or iblock which has offers
						}
						elseif ($arCatalog["OFFERS_IBLOCK_ID"] > 0) {
							$ELEMENT_ID = $arPriceElement["ID"];
							$IBLOCK_ID = $arPriceElement["IBLOCK_ID"];
							$OFFERS_IBLOCK_ID = $arCatalog["OFFERS_IBLOCK_ID"];
							$OFFERS_PROPERTY_ID = $arCatalog["OFFERS_PROPERTY_ID"];
							// or it's regular catalog
						}
						else {
							$ELEMENT_ID = $arPriceElement["ID"];
							$IBLOCK_ID = $arPriceElement["IBLOCK_ID"];
							$OFFERS_IBLOCK_ID = false;
							$OFFERS_PROPERTY_ID = false;
						}
					}
				}
				// Check for iblock event
			}
			elseif (is_array($arg1) && $arg1["ID"] > 0 && $arg1["IBLOCK_ID"] > 0) {
				// Check if iblock has offers
				$arOffers = \CIBlockPriceTools::GetOffersIBlock($arg1["IBLOCK_ID"]);
				if (is_array($arOffers)) {
					$ELEMENT_ID = $arg1["ID"];
					$IBLOCK_ID = $arg1["IBLOCK_ID"];
					$OFFERS_IBLOCK_ID = $arOffers["OFFERS_IBLOCK_ID"];
					$OFFERS_PROPERTY_ID = $arOffers["OFFERS_PROPERTY_ID"];
				}
			}
			
			if ($ELEMENT_ID) {
				static $arPropCache = [];
				
				if (!array_key_exists($IBLOCK_ID, $arPropCache)) {
					// Check for MINIMAL_PRICE property
					$rsProperty = \CIBlockProperty::GetByID("OFFER_MIN_PRICE", $IBLOCK_ID); //MINIMUM_PRICE
					$arProperty = $rsProperty->Fetch();
					if ($arProperty){
						$arPropCache[$IBLOCK_ID] = $arProperty["ID"];
					}
					else{
						$arPropCache[$IBLOCK_ID] = false;
					}
				}
				if ($arPropCache[$IBLOCK_ID]) {
					// Compose elements filter
					if ($OFFERS_IBLOCK_ID) {
						$rsOffers = \CIBlockElement::GetList([], ["ACTIVE" => "Y", "IBLOCK_ID" => $OFFERS_IBLOCK_ID, "PROPERTY_".$OFFERS_PROPERTY_ID => $ELEMENT_ID], false, false, ["ID"]);
						while ($arOffer = $rsOffers->Fetch()){
							$arProductID[] = $arOffer["ID"];
						}
						
						if (!is_array($arProductID)){
							$arProductID = [$ELEMENT_ID];
						}
					}
					else {
						$arProductID = [$ELEMENT_ID];
					}
					
					$minPrice = false;
					$maxPrice = false;
					// Get prices
					$rsPrices = \CPrice::GetList([], ["PRODUCT_ID" => $arProductID]);
					while ($arPrice = $rsPrices->Fetch()) {
						if ($strDefaultCurrency != $arPrice["CURRENCY"]){
							$arPrice["PRICE"] = \CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $arPrice["CURRENCY"], $strDefaultCurrency);
						}
						
						$PRICE = $arPrice["PRICE"];
						
						if ($minPrice === false || $minPrice > $PRICE){
							$minPrice = $PRICE;
						}
						if ($maxPrice === false || $maxPrice < $PRICE){
							$maxPrice = $PRICE;
						}
					}
					
					// Save found minimal price into property
					if ($minPrice !== false) {
						\CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, $IBLOCK_ID, ["OFFER_MIN_PRICE" => $minPrice, "OFFER_MAX_PRICE" => $maxPrice,]);
					}
				}
			}
		}
		
		public function onBeforeIBlockElementDelete($ID) {
			global $APPLICATION;
			
			$res = \CIBlockElement::GetByID($ID)->GetNext();
			if ($res['IBLOCK_ID'] == 27) {
				$APPLICATION->throwException("Нельзя удалить.");
				return false;
			}
			if ($res['IBLOCK_ID'] == 50) {
				$APPLICATION->throwException('Промокод удалять нельзя. Чтобы отключить промокод воспользуйтесь свойством "Активность"');
				return false;
			}
		}
	}