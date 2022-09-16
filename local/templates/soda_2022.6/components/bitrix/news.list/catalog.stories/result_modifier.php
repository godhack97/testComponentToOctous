<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
  Bitrix\Highloadblock\HighloadBlockTable;

Loader::includeModule("highloadblock");

$hlblock = HighloadBlockTable::getById(2)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$PsuTaskControl = $entity->getDataClass();

foreach ($arResult["ITEMS"] as $arItem) {
  $productID = $arItem['PROPERTIES']['PARENT']['VALUE'];
  if($productID > 0) {
    $res = CCatalogSKU::getOffersList($productID, 0, ['PROPERTY_COLOR' => $arItem['PROPERTIES']['COLOR']['VALUE']]);

    $count = 0;
    foreach($res[$productID] as $val) {
      if($count > 0) break;

      $arOffer = CCatalogProduct::GetByIDEx($val['ID']);
	    $colorName = $PsuTaskControl::getList(['filter' => ["UF_XML_ID" => $arItem['PROPERTIES']['COLOR']['VALUE']]])->fetch()["UF_NAME"];
	
	    $arResult['ADDITIONAL_DATA'][$productID] = [
		    'DETAIL_PAGE_URL' => $arOffer['DETAIL_PAGE_URL'].'?setColor='.$arItem['PROPERTIES']['COLOR']['VALUE'],
		    'PRICE' => CurrencyFormat($arOffer['PRICES'][1]['PRICE'], $arOffer['PRICES'][1]['CURRENCY']),
		    'COLOR' => $colorName
	    ];
	    $count++;
    }
  }
}
