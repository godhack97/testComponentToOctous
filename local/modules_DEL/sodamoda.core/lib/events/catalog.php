<?php
	namespace Sodamoda\Core\Events;
	
	use \Bitrix\Main\Loader;
	
	class Catalog{
		public function onStoreProductSave($id, $arFields) {
      /*
			$allAmountProduct = 0;
			$allAmount = $arFields["AMOUNT"];
			if (Loader::includeModule('catalog') & Loader::includeModule('iblock')) {
				// Получаем информацию о товаре, к которому привязано данное предложение,
				// вместе с этим провеяем не является ли это товаром
				$arProductInfo = \CCatalogSKU::GetProductInfo($arFields["PRODUCT_ID"]);
				if (is_array($arProductInfo)) {
					// Получаем данные об инфоблоке торговых предложений
					$arOffersInfo = \CCatalogSKU::GetInfoByProductIBlock($arProductInfo["IBLOCK_ID"]);
					$arFilter = [
						"IBLOCK_ID" => $arOffersInfo["IBLOCK_ID"],
						"PROPERTY_".$arOffersInfo["SKU_PROPERTY_ID"] => $arProductInfo["ID"],
						"!ID" => $arFields["PRODUCT_ID"]];
					// Получаем список торговых предложений
					$obOffersList = \CIBlockElement::GetList(["SORT" => "ASC"], $arFilter, false, false, ["IBLOCK_ID", "ID"]);
					$arOffers = [];
					while ($arOffers = $obOffersList->Fetch()) {
						// Получаем данные об остатках на складе для торгового предложения
						$obStoreOffer = \CCatalogStoreProduct::GetList([], ["STORE_ID"   => $arFields["STORE_ID"], "PRODUCT_ID" => $arOffers["ID"]], false, false, ["ID", "PRODUCT_ID", "AMOUNT"]);
						if ($arStore = $obStoreOffer->Fetch()) {
							$allAmount = $allAmount + $arStore["AMOUNT"];
						}
					}
					
					// Получаем данные о складе товара и формируем массив для обновления данных
					$arFieldsProduct = [
						"PRODUCT_ID" => $arProductInfo["ID"],
						"STORE_ID"   => $arFields["STORE_ID"],
						"AMOUNT"     => $allAmount,
					];
					
					$obStoreProduct = \CCatalogStoreProduct::GetList([], ["STORE_ID"   => $arFields["STORE_ID"], "PRODUCT_ID" => $arProductInfo["ID"]], false, false, ["ID", "PRODUCT_ID", "AMOUNT"]);
					$result = false;
					if ($arStoreProduct = $obStoreProduct->Fetch()) {
						$result = \CCatalogStoreProduct::Update($arStoreProduct["ID"], $arFieldsProduct);
					}
					else {
						$result = \CCatalogStoreProduct::Add($arFieldsProduct);
					}
					
					// Изменяем общее количество товара
					if ($result) {
						$obStoreProduct = \CCatalogStoreProduct::GetList([], ["PRODUCT_ID" => $arProductInfo["ID"]], false, false, ["ID", "PRODUCT_ID", "AMOUNT"]);
						while ($arStoreProduct = $obStoreProduct->Fetch()) {
							$allAmountProduct = $allAmountProduct + $arStoreProduct["AMOUNT"];
						}
						$arFieldsProduct = ["QUANTITY" => $allAmountProduct];
						\CCatalogProduct::Update($arProductInfo["ID"], $arFieldsProduct);
					}
				}
			}
      */
		}
		public function doIBlockAfterSave($arg1, $arg2 = false) {
      /*
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
						$rsOffers = \CIBlockElement::GetList([], ["ACTIVE" => "Y", "IBLOCK_ID" => $OFFERS_IBLOCK_ID, "PROPERTY_".$OFFERS_PROPERTY_ID => $ELEMENT_ID,], false, false, ["ID"]);
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
					$rsPrices = \CPrice::GetList([], ["PRODUCT_ID" => $arProductID,]);
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
						\CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, $IBLOCK_ID, ["OFFER_MIN_PRICE" => $minPrice, "OFFER_MAX_PRICE" => $maxPrice]);
					}
				}
			}
      */
		}
	}