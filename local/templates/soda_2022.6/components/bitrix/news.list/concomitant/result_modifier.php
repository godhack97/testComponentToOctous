<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
  Bitrix\Highloadblock\HighloadBlockTable;

Loader::includeModule("highloadblock");

$hlblock = HighloadBlockTable::getById(2)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$PsuTaskControl = $entity->getDataClass();
if(isset($arParams['PARENT_SECTION']) && !empty($arParams['PARENT_SECTION'])){
	if(is_array($arResult['ITEMS']) && count($arResult['ITEMS'])){
		foreach ($arResult['ITEMS'] as &$arItem) {
			$productID = $arItem['ID'];
			if ($productID > 0) {
				$res = CCatalogSKU::getOffersList($productID, 0, ['ACTIVE' => 'Y']);
				$count = 0;
				
				if(isset($res[$productID]) && count($res[$productID])){
					foreach ($res[$productID] as $val) {
						if ($count > 0) break;
						
						$arOffer = CCatalogProduct::GetByIDEx($val['ID']);
						// dbg($arOffer);
						$colorName = $PsuTaskControl::getList(['filter' => ['UF_XML_ID' => $arOffer['PROPERTIES']['COLOR']['VALUE']]])->fetch()['UF_NAME'];
						
						if($arOffer['PRODUCT']['AVAILABLE'] !== 'N'){
							$colorName = $PsuTaskControl::getList(['filter' => ["UF_XML_ID" => $arOffer['PROPERTIES']['COLOR']['VALUE']]])->fetch()["UF_NAME"];
							
							$arResult['ADDITIONAL_DATA'][$productID] = [
								'ID' => $arOffer['ID'],
								'DETAIL_PAGE_URL' => $arOffer['DETAIL_PAGE_URL'] . '?setColor=' . $arOffer['PROPERTIES']['COLOR']['VALUE'],
								'PRICE' => CurrencyFormat($arOffer['PRICES'][1]['PRICE'], $arOffer['PRICES'][1]['CURRENCY']),
								'COLOR' => $colorName
							];
							
							$count++;
						}
						if(empty($arItem['PROPERTIES']['PARENT']['VALUE'])){
							$arSelect = ["ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*"];
							$arFilter = ["IBLOCK_ID" => 11, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", 'PROPERTY_PARENT' => $productID, 'PROPERTY_COLOR' => $arOffer['PROPERTIES']['COLOR']['VALUE'], "!PROPERTY_IMAGES" => false];
							$res = CIBlockElement::GetList([], $arFilter, false, ["nPageSize" => 300], $arSelect);
							while ($ob = $res->GetNextElement())
							{
								$arProps = $ob->GetProperties();
								$arItem['PROPERTIES']['IMAGES'] = $arProps['IMAGES'];
								$arItem['PROPERTIES']['PARENT'] = $arProps['PARENT'];
							}
						}
					}
				}
			}
		}
	}
}
else{
	if(is_array($arResult['ITEMS']) && count($arResult['ITEMS'])){
		foreach ($arResult['ITEMS'] as $arItem) {
			$productID = $arItem['PROPERTIES']['PARENT']['VALUE'];
			if ($productID > 0) {
				$res = CCatalogSKU::getOffersList($productID, 0, ['PROPERTY_COLOR' => $arItem['PROPERTIES']['COLOR']['VALUE'], 'ACTIVE' => 'Y']);
				
				$count = 0;
				if(isset($res[$productID]) && count($res[$productID])){
					foreach ($res[$productID] as $val) {
						if ($count > 0) break;
						
						$arOffer = CCatalogProduct::GetByIDEx($val['ID']);
						// dbg($arOffer);
						
						$colorName = $PsuTaskControl::getList(['filter' => ['UF_XML_ID' => $arItem['PROPERTIES']['COLOR']['VALUE']]])->fetch()['UF_NAME'];
						
						if($arOffer['PRODUCT']['AVAILABLE'] !== 'N'){
							$colorName = $PsuTaskControl::getList(['filter' => ["UF_XML_ID" => $arItem['PROPERTIES']['COLOR']['VALUE']]])->fetch()["UF_NAME"];
							
							$arResult['ADDITIONAL_DATA'][$productID] = [
								'ID' => $arOffer['ID'],
								'DETAIL_PAGE_URL' => $arOffer['DETAIL_PAGE_URL'] . '?setColor=' . $arItem['PROPERTIES']['COLOR']['VALUE'],
								'PRICE' => CurrencyFormat($arOffer['PRICES'][1]['PRICE'], $arOffer['PRICES'][1]['CURRENCY']),
								'COLOR' => $colorName
							];
							
							$count++;
						}
					}
				}
			}
		}
	}
}
