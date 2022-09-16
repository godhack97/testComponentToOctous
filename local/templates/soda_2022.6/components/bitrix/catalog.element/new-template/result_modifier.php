<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$this->__component->SetResultCacheKeys(array("CACHED_TPL"));
use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Application,
    Bitrix\Highloadblock\HighloadBlockTable;

Loader::includeModule("highloadblock");
$hlBlockId = HL_COLOR_ID;
$hlblock = HighloadBlockTable::getById($hlBlockId)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$PsuTaskControl = $entity->getDataClass();
$hlblockBrand = HighloadBlockTable::getById(9)->fetch();
$entityBrand = HighloadBlockTable::compileEntity($hlblockBrand);
$PsuTaskControlBrand = $entityBrand->getDataClass();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();
$arResult["ADD_URL"] = str_replace('#ID#', $arResult['ID'], $arResult["ADD_URL_TEMPLATE"]);

$res = CIBlockElement::GetList(
    [],
    [
        'IBLOCK_TYPE' => 'catalogs',
        'IBLOCK_ID' => IBLOCK_MEDIA,
        'PROPERTY_PARENT' => $arResult['ID'],
        'ACTIVE' => 'Y',
    ],
    false,
    false,
    [
        "IBLOCK_TYPE",
        "IBLOCK_ID",
        "NAME",
        "PROPERTY_VIDEO_IMAGE",
        "PROPERTY_VIDEO_FILES",
        "PROPERTY_COLOR",
        "PROPERTY_CONCOMITANT",
        "PROPERTY_PARENT",
        "ID",
    ]
);
while ($ar_res = $res->GetNext()){
    $arResult['COLOR_ID'][$ar_res["PROPERTY_COLOR_VALUE"]] = $ar_res["ID"];
    if($ar_res["PROPERTY_CONCOMITANT_VALUE"]){
        $arResult['COLOR_CONCOMITANT'][$ar_res["PROPERTY_COLOR_VALUE"]][] = $ar_res["PROPERTY_CONCOMITANT_VALUE"];
    }else{
        $arResult['COLOR_CONCOMITANT'][$ar_res["PROPERTY_COLOR_VALUE"]][] = -1;
    }
    $mas[] = $ar_res;
}


if ($arResult['OFFERS'] && is_array($arResult['OFFERS']))
{
    foreach ($arResult['OFFERS'] as &$offer)
    {
        if ($arResult['COLOR_ID'][$offer["PROPERTIES"]["COLOR"]['VALUE_XML_ID']])
        {
            $arResult['PHOTO_OFFER_ID'][$offer["ID"]] = $offer["PROPERTIES"]["COLOR"]['VALUE_ENUM_ID'];
            $arResult['PHOTO_ID'][$arResult['COLOR_ID'][$offer["PROPERTIES"]["COLOR"]['VALUE_XML_ID']]] = $offer["PROPERTIES"]["COLOR"]['VALUE_ENUM_ID'];
        }
    }
    unset($offer);
}


if ($USER->IsAdmin())
{

    $arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");
    $arFilter = Array("IBLOCK_ID" => 11, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", 'PROPERTY_PARENT' => $arResult['ID'], "!PROPERTY_IMAGES" => false);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 300), $arSelect);
    while ($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();
        //print_r($arFields);
        $arProps = $ob->GetProperties();
        //print_r($arProps);
        $arFields = array_merge($arProps, $arFields);
        $arPhotos[$arFields['PARENT']['VALUE']][$arFields['COLOR']['VALUE']] = $arFields;
    }

    $arSetPhoto = array();
    foreach ($arResult['OFFERS'] as &$offer)
    {
        if ($arPhotos[$arResult['ID']][$offer['PROPERTIES']['COLOR_REF']['VALUE']] && !$arSetPhoto[$arResult['ID']][$offer['PROPERTIES']['COLOR_REF']['VALUE']])
        {

            $arSetPhoto[$arResult['ID']][$offer['PROPERTIES']['COLOR_REF']['VALUE']] = $offer['ID'];

            $offerFindedPhotos = $arPhotos[$arResult['ID']][$offer['PROPERTIES']['COLOR_REF']['VALUE']]['IMAGES']['VALUE'];
            if (!empty($offerFindedPhotos))
            {
                //echo "<pre>"; print_r($offerFindedPhotos); echo "</pre>";
                $offer["PROPERTIES"]["MORE_PHOTO"]['VALUE'] = $offerFindedPhotos;
            }
            $offer["PROPERTIES"]["PREORDER"]['VALUE'] = $arPhotos[$arResult['ID']][$offer['PROPERTIES']['COLOR_REF']['VALUE']]['PREORDER']['VALUE_XML_ID'] === 'Y';
        }
    }

    unset($offer);

}


if(isset($arResult["PROPERTIES"]["BRAND"]["VALUE"]) && !empty($arResult["PROPERTIES"]["BRAND"]["VALUE"])){
  $rsList = $PsuTaskControlBrand::getList(['filter' => ["UF_XML_ID" => $arResult["PROPERTIES"]["BRAND"]["VALUE"]] ]);
  if ($el = $rsList->fetch()){
    $arResult["PROPERTIES"]["BRAND"]["VALUE"] = $el['UF_NAME'];
  }
}



// BEGIN COLOR -----------------------------------------------------------------
$arResult['COLOR_SIZES'] = [];
$arColorsWithoutImages = [];

if ($arResult["OFFERS"] && is_array($arResult["OFFERS"]))
{
  foreach ($arResult["OFFERS"] as &$offer)
  {
    if ($offer["PROPERTIES"]["COLOR"]["VALUE"])
    {
      $offer["PROPERTIES"]["COLOR"]["VALUE_ENUM_ID"] = $offer["PROPERTIES"]["COLOR"]["VALUE"];
      $offer["PROPERTIES"]["COLOR"]["VALUE_XML_ID"]  = $offer["PROPERTIES"]["COLOR"]["VALUE"];
      $rsList = $PsuTaskControl::getList(['filter' => ["UF_XML_ID" => $offer["PROPERTIES"]["COLOR"]["VALUE"]] ]);
      if ($el = $rsList->fetch())
      {
        $offer["PROPERTIES"]["COLOR"]["VALUE"] = $el["UF_NAME"];
        $offer["PROPERTIES"]["COLOR"]["HEX"] = empty($el["UF_HEX"]) ? '#777' : $el["UF_HEX"];

        if(SITE_ID == 's2' && !empty($el["UF_NAME_EN"])) {
          $offer["PROPERTIES"]["COLOR"]["VALUE"] = $el["UF_NAME_EN"];
        }
      }
    }
  }
  unset($offer);

  CBitrixComponent::includeComponentClass("arlix:product.subscribe");

	$arResult['IS_CUSTOM_DISCOUNT'] = !empty($arResult['PROPERTIES']['PROTSENT_IZMENENIYA_TSENY']['VALUE']);
	$arResult['CUSTOM_DISCOUNT'] = $arResult['PROPERTIES']['PROTSENT_IZMENENIYA_TSENY']['VALUE'];
  // Двумя циклами собираем цвета с их размерами
  foreach ($arResult["OFFERS"] as &$offer)
  {
	  $arPrices = \CCatalogProduct::GetByIDEx($offer['ID']);
		
	  $globalCurrencyUser = isset($_COOKIE["CATALOG_CURRENCY"]) && !empty($_COOKIE["CATALOG_CURRENCY"]) ? strtoupper($_COOKIE["CATALOG_CURRENCY"]) : $arParams['CURRENCY_ID'];
  
    switch ($globalCurrencyUser){
      case 'USD':
        $offer['ITEM_PRICES'][0]['BASE_PRICE'] = $arPrices['PRICES'][6]['PRICE'];
        $offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'] = $arPrices['PRICES'][6]['PRICE'];
        $offer['ITEM_PRICES'][0]['PRICE'] = $arPrices['PRICES'][6]['PRICE'];
        $offer['ITEM_PRICES'][0]['RATIO_PRICE'] = $arPrices['PRICES'][6]['PRICE'];
        $offer['ITEM_PRICES'][0]["CURRENCY"] = $arPrices['PRICES'][6]['CURRENCY'];
        $offer['ITEM_PRICES'][0]['PRINT_RATIO_PRICE'] = SaleFormatCurrency($arPrices['PRICES'][6]['PRICE'], $arPrices['PRICES'][6]["CURRENCY"]);
        break;
      case 'AED':
        $offer['ITEM_PRICES'][0]['BASE_PRICE'] = $arPrices['PRICES'][7]['PRICE'];
        $offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'] = $arPrices['PRICES'][7]['PRICE'];
        $offer['ITEM_PRICES'][0]['PRICE'] = $arPrices['PRICES'][7]['PRICE'];
        $offer['ITEM_PRICES'][0]['RATIO_PRICE'] = $arPrices['PRICES'][7]['PRICE'];
        $offer['ITEM_PRICES'][0]["CURRENCY"] = $arPrices['PRICES'][7]['CURRENCY'];
        $offer['ITEM_PRICES'][0]['PRINT_RATIO_PRICE'] = SaleFormatCurrency($arPrices['PRICES'][7]['PRICE'], $arPrices['PRICES'][7]["CURRENCY"]);
        break;
      case 'RUB':
      default:
        $offer['ITEM_PRICES'][0]['BASE_PRICE'] = $arPrices['PRICES'][1]['PRICE'];
        $offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'] = $arPrices['PRICES'][1]['PRICE'];
        $offer['ITEM_PRICES'][0]['PRICE'] = $arPrices['PRICES'][1]['PRICE'];
        $offer['ITEM_PRICES'][0]['RATIO_PRICE'] = $arPrices['PRICES'][1]['PRICE'];
        $offer['ITEM_PRICES'][0]["CURRENCY"] = $arPrices['PRICES'][1]['CURRENCY'];
        break;
    }
	  //if ($USER->IsAdmin()) {
    
    if($offer['ITEM_PRICES'][0]['DISCOUNT'] > 0){
      if($arResult['IS_CUSTOM_DISCOUNT']){
        $customBasePrice = ($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'] + ($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE']/100*$arResult['PROPERTIES']['PROTSENT_IZMENENIYA_TSENY']['VALUE']));
        switch ($globalCurrencyUser){
          case 'USD':
          case 'AED':
            $sourceBasePrice = $customBasePrice;
            $remainsCharPrice = substr($sourceBasePrice, -3);
            $remainsPrice = (float)$remainsCharPrice;
            if($remainsPrice > 9){
              $sourceBasePrice = round(str_replace($remainsCharPrice, floor($remainsPrice), $sourceBasePrice));
              $customBasePrice = $sourceBasePrice;
            }
            else {
              if($remainsPrice < 8){
                $remainsPrice = (float) 9;
              }
              $sourceBasePrice = round(str_replace($remainsCharPrice, ceil($remainsPrice), $sourceBasePrice));
              $customBasePrice = $sourceBasePrice;
            }
            break;
          case 'RUB':
            $sourceBasePrice = $customBasePrice / 100;
            $remainsCharPrice = substr($sourceBasePrice, -3);
            $remainsPrice = (float)$remainsCharPrice;
            if($remainsPrice > 9){
              $sourceBasePrice = round(str_replace($remainsCharPrice, floor($remainsPrice), $sourceBasePrice));
              $customBasePrice = $sourceBasePrice * 100;
            }
            else {
              if($remainsPrice < 8){
                $remainsPrice = (float) 9;
              }
              $sourceBasePrice = round(str_replace($remainsCharPrice, ceil($remainsPrice), $sourceBasePrice));
              $customBasePrice = $sourceBasePrice * 100;
            }
            break;
        }
        $offer['ITEM_PRICES'][0]['DISCOUNT'] = true;
        $offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'] = $customBasePrice;
        $offer['ITEM_PRICES'][0]['PRINT_BASE_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
        $offer['ITEM_PRICES'][0]['PRINT_RATIO_BASE_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
        $offer['ITEM_PRICES'][0]['PRINT_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
        $offer['ITEM_PRICES'][0]['PRINT_RATIO_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
      }
    }
    else{
      if($arResult['IS_CUSTOM_DISCOUNT']){
        $customBasePrice = ($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'] + ($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE']/100*$arResult['PROPERTIES']['PROTSENT_IZMENENIYA_TSENY']['VALUE']));
        switch ($globalCurrencyUser){
          case 'USD':
          case 'AED':
            $sourceBasePrice = $customBasePrice;
            $remainsCharPrice = substr($sourceBasePrice, -3);
            $remainsPrice = (float)$remainsCharPrice;
            if($remainsPrice > 9){
              $sourceBasePrice = round(str_replace($remainsCharPrice, floor($remainsPrice), $sourceBasePrice));
              $customBasePrice = $sourceBasePrice;
            }
            else {
              if($remainsPrice < 8){
                $remainsPrice = (float) 9;
              }
              $sourceBasePrice = round(str_replace($remainsCharPrice, ceil($remainsPrice), $sourceBasePrice));
              $customBasePrice = $sourceBasePrice;
            }
            break;
          case 'RUB':
            $sourceBasePrice = $customBasePrice / 100;
            $remainsCharPrice = substr($sourceBasePrice, -3);
            $remainsPrice = (float)$remainsCharPrice;
            if($remainsPrice > 9){
              $sourceBasePrice = round(str_replace($remainsCharPrice, floor($remainsPrice), $sourceBasePrice));
              $customBasePrice = $sourceBasePrice * 100;
            }
            else {
              if($remainsPrice < 8){
                $remainsPrice = (float) 9;
              }
              $sourceBasePrice = round(str_replace($remainsCharPrice, ceil($remainsPrice), $sourceBasePrice));
              $customBasePrice = $sourceBasePrice * 100;
            }
            break;
        }
        $offer['ITEM_PRICES'][0]['DISCOUNT'] = true;
        $offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'] = $customBasePrice;
        $offer['ITEM_PRICES'][0]['PRINT_BASE_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
        $offer['ITEM_PRICES'][0]['PRINT_RATIO_BASE_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
        $offer['ITEM_PRICES'][0]['PRINT_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
        $offer['ITEM_PRICES'][0]['PRINT_RATIO_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
      }
      else {
        $offer['ITEM_PRICES'][0]['PRINT_BASE_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
        $offer['ITEM_PRICES'][0]['PRINT_RATIO_BASE_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
        $offer['ITEM_PRICES'][0]['PRINT_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
        $offer['ITEM_PRICES'][0]['PRINT_RATIO_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
      }
    }
	  //}
    if ($offer["PROPERTIES"]["COLOR"]['VALUE'])
    {
      //dbgz($offer);
      $arColor = $offer["PROPERTIES"]["COLOR"];
      if (!$arResult['COLOR_SIZES'][ $arColor["VALUE_ENUM_ID"] ])
      {
        $arResult['COLOR_SIZES'][ $arColor["VALUE_ENUM_ID"] ] = [
            'ID' => $arColor['ID'],
            'OFFER_ID' => $offer['ID'],
            'OFFER_IBLOCK_ID' => $offer['IBLOCK_ID'],
            'NAME' => $arColor['NAME'],
            'SORT' => $arColor['SORT'],
            'CODE' => $arColor['CODE'],
            'HEX'  => $arColor['HEX'],
            'XML_ID' => $arColor['XML_ID'],
            'PHOTO_ID' => $arColor['PHOTO_ID'],
            'VALUE_ENUM' => $arColor['VALUE_ENUM'],
            'VALUE_XML_ID' => $arColor['VALUE_XML_ID'],
            'VALUE_SORT' => $arColor['VALUE_SORT'],
            'VALUE' => $arColor['VALUE'],
            'PROPERTY_VALUE_ID' => $arColor['PROPERTY_VALUE_ID'],
            'VALUE_ENUM_ID' => $arColor['VALUE_ENUM_ID'],
            'DESCRIPTION' => $arColor['DESCRIPTION'],
            'IMAGES' => $offer['PROPERTIES']['IMAGES']['VALUE'],
            'SIZES' => [],
            'PRICES' => [
              'OLD' => $offer['ITEM_PRICES'][0]['PRINT_RATIO_BASE_PRICE'],
              'CURRENT' => $offer['ITEM_PRICES'][0]['PRINT_PRICE'],
              'IS_DISCOUNT' => $offer['ITEM_PRICES'][0]['DISCOUNT'],
            ],
        ];
      }
    }
		
	  if ($USER->IsAdmin()) {
		  //print_r('<pre>');
		  //print_r($offer['ITEM_PRICES']);
		  //var_dump($offer['ITEM_PRICES'][0]['PRINT_PRICE']);
		  //print_r($arOfferInfo);
		  //print_r('$arOffer');
		  //print_r($arResult['IS_CUSTOM_DISCOUNT']);
		  //print_r($arResult["OFFERS"]);
		  //print_r('array_merge($arOffer, $arOfferInfo)');
		  //print_r(array_merge($arOffer, $arOfferInfo));
		  //print_r('</pre>');
	  }
  }

  foreach ($arResult["OFFERS"] as &$offer)
  {
    if ($offer["PROPERTIES"]["COLOR"]['VALUE'] && $offer["PROPERTIES"]["SIZES"]['VALUE'])
    {
      $colorEnumID = $offer["PROPERTIES"]["COLOR"]["VALUE_ENUM_ID"];
      $arSizes = $offer["PROPERTIES"]["SIZES"];

      if ($arResult['COLOR_SIZES'][ $colorEnumID ])
      {
        $arResult['COLOR_SIZES'][ $colorEnumID ]['SIZES'][] = [
            'OFFER_ID' => $offer['ID'],
            'NAME' => $arSizes['NAME'],
            'SORT' => $arSizes['SORT'],
            'CODE' => $arSizes['CODE'],
            'XML_ID' => $arSizes['XML_ID'],
            'PHOTO_ID' => $arColor['PHOTO_ID'],
            'VALUE_ENUM' => $arSizes['VALUE_ENUM'] . (empty($offer['PROPERTIES']['ROST']['VALUE']) ? '' : (' — '. $offer['PROPERTIES']['ROST']['VALUE'])),
            'VALUE_XML_ID' => $arSizes['VALUE_XML_ID'],
            'VALUE_SORT' => $arSizes['VALUE_SORT'],
            'VALUE' => $arSizes['VALUE'] . (empty($offer['PROPERTIES']['ROST']['VALUE']) ? '' : (' — '. $offer['PROPERTIES']['ROST']['VALUE'] .' '. Loc::getMessage("RSM_MEASURE_SCU"))),
            'PROPERTY_VALUE_ID' => $arSizes['PROPERTY_VALUE_ID'],
            'VALUE_ENUM_ID' => $arSizes['VALUE_ENUM_ID'],
            'DESCRIPTION' => $arSizes['DESCRIPTION'],
            'ROST' => $offer['PROPERTIES']['ROST']['VALUE'],
            'STORE_DATA' => LikesComponent::checkAvailableOffers($offer['ID']),
        ];
      }
    }
	  if ($offer["PROPERTIES"]["COLOR"]['VALUE'] && $offer["PROPERTIES"]["SIZES_JEANS"]['VALUE'])
	  {
		  $colorEnumID = $offer["PROPERTIES"]["COLOR"]["VALUE_ENUM_ID"];
		  $arSizes = $offer["PROPERTIES"]["SIZES_JEANS"];

		  if ($arResult['COLOR_SIZES'][ $colorEnumID ])
		  {
			  $arResult['COLOR_SIZES'][ $colorEnumID ]['SIZES'][] = [
				  'OFFER_ID' => $offer['ID'],
				  'NAME' => $arSizes['NAME'],
				  'SORT' => $arSizes['SORT'],
				  'CODE' => $arSizes['CODE'],
				  'XML_ID' => $arSizes['XML_ID'],
				  'PHOTO_ID' => $arColor['PHOTO_ID'],
				  'VALUE_ENUM' => $arSizes['VALUE_ENUM'] . (empty($offer['PROPERTIES']['ROST']['VALUE']) ? '' : (' — '. $offer['PROPERTIES']['ROST']['VALUE'])),
				  'VALUE_XML_ID' => $arSizes['VALUE_XML_ID'],
				  'VALUE_SORT' => $arSizes['VALUE_SORT'],
				  'VALUE' => $arSizes['VALUE'] . (empty($offer['PROPERTIES']['ROST']['VALUE']) ? '' : (' — '. $offer['PROPERTIES']['ROST']['VALUE'] .' '. Loc::getMessage("RSM_MEASURE_SCU"))),
				  'PROPERTY_VALUE_ID' => $arSizes['PROPERTY_VALUE_ID'],
				  'VALUE_ENUM_ID' => $arSizes['VALUE_ENUM_ID'],
				  'DESCRIPTION' => $arSizes['DESCRIPTION'],
				  'ROST' => $offer['PROPERTIES']['ROST']['VALUE'],
				  'STORE_DATA' => LikesComponent::checkAvailableOffers($offer['ID']),
			  ];
		  }
	  }
	  if ($offer["PROPERTIES"]["COLOR"]['VALUE'] && $offer["PROPERTIES"]["SIZES_SHOES"]['VALUE'])
	  {
		  $colorEnumID = $offer["PROPERTIES"]["COLOR"]["VALUE_ENUM_ID"];
		  $arSizes = $offer["PROPERTIES"]["SIZES_SHOES"];

		  if ($arResult['COLOR_SIZES'][ $colorEnumID ])
		  {
			  $arResult['COLOR_SIZES'][ $colorEnumID ]['SIZES'][] = [
				  'OFFER_ID' => $offer['ID'],
				  'NAME' => $arSizes['NAME'],
				  'SORT' => $arSizes['SORT'],
				  'CODE' => $arSizes['CODE'],
				  'XML_ID' => $arSizes['XML_ID'],
				  'PHOTO_ID' => $arColor['PHOTO_ID'],
				  'VALUE_ENUM' => $arSizes['VALUE_ENUM'] . (empty($offer['PROPERTIES']['ROST']['VALUE']) ? '' : (' — '. $offer['PROPERTIES']['ROST']['VALUE'])),
				  'VALUE_XML_ID' => $arSizes['VALUE_XML_ID'],
				  'VALUE_SORT' => $arSizes['VALUE_SORT'],
				  'VALUE' => $arSizes['VALUE'] . (empty($offer['PROPERTIES']['ROST']['VALUE']) ? '' : (' — '. $offer['PROPERTIES']['ROST']['VALUE'] .' '. Loc::getMessage("RSM_MEASURE_SCU"))),
				  'PROPERTY_VALUE_ID' => $arSizes['PROPERTY_VALUE_ID'],
				  'VALUE_ENUM_ID' => $arSizes['VALUE_ENUM_ID'],
				  'DESCRIPTION' => $arSizes['DESCRIPTION'],
				  'ROST' => $offer['PROPERTIES']['ROST']['VALUE'],
				  'STORE_DATA' => LikesComponent::checkAvailableOffers($offer['ID']),
			  ];
		  }
	  }
  }
  foreach ($arResult['COLOR_SIZES'] as $kColor => $vColor){
    $allAmount = 0;
    foreach ($vColor['SIZES'] as $size){
      $allAmount += $size['STORE_DATA']['AMOUNT'];
    }
    $arResult['COLOR_SIZES'][$kColor]['AVAILABLE'] = $allAmount > 0;
  }
  unset($allAmount);

  //dbgz($arResult['COLOR_SIZES']);
  //ROST


  // Собираем фотографии с торговых предложений в соответствии с цветом
  $arResult['OFFERS_IMAGES'] = [];
  foreach ($arResult['OFFERS'] as $arOffer)
  {
    if (!empty($arOffer['PROPERTIES']['MORE_PHOTO']['VALUE']))
    {
      $colorEnumID = $arOffer['PROPERTIES']['COLOR']['VALUE_ENUM_ID'];
      $arTmpImages = $arResult['OFFERS_IMAGES'][$colorEnumID];

      if (is_array($arTmpImages) && count($arTmpImages))
        $arResult['OFFERS_IMAGES'][$colorEnumID] = array_merge($arOffer['PROPERTIES']['MORE_PHOTO']['VALUE'], $arTmpImages);
      else
        $arResult['OFFERS_IMAGES'][$colorEnumID] = $arOffer['PROPERTIES']['MORE_PHOTO']['VALUE'];
    }
  }


  $arNewPhotos = [];
  $arSelect = Array("ID", "IBLOCK_ID", "NAME", "SORT", "DATE_ACTIVE_FROM", "PROPERTY_*");
  $arFilter = Array("IBLOCK_ID" => COLORS_ID, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", 'PROPERTY_PARENT' => $arResult['ID'], "!PROPERTY_IMAGES" => false);
  $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 300), $arSelect);
  while ($ob = $res->GetNextElement())
  {
    $arFields = $ob->GetFields();
    $arProps = $ob->GetProperties();
    $arFields = array_merge($arProps, $arFields);
    $arNewPhotos[$arFields['COLOR']['VALUE']] = $arFields;
  }


  // Массив цветов у которых есть картинки
  $arResult['OFFERS_COLOR_LIST'] = [];
  foreach ($arResult['COLOR_SIZES'] as $colorEnumID => $item)
  {
    if (array_key_exists($item["VALUE_XML_ID"], $arNewPhotos))
    {
      $arResult['OFFERS_COLOR_LIST'][$colorEnumID] = [
          'NAME' => $item['VALUE'],
          'HEX'  => $item['HEX'],
          'IMAGES' => $arNewPhotos[$item["VALUE_XML_ID"]]["IMAGES"]["VALUE"],
          'PREORDER' => $arNewPhotos[$item["VALUE_XML_ID"]]["PREORDER"]["VALUE_XML_ID"] === 'Y',
          'AVAILABLE' => $arResult['COLOR_SIZES'][$colorEnumID]['AVAILABLE'],
          'VIDEO_IMAGE' => $arNewPhotos[$item["VALUE_XML_ID"]]["VIDEO_IMAGE"],
          'VIDEO_FILES' => $arNewPhotos[$item["VALUE_XML_ID"]]["VIDEO_FILES"],
      ];
    }
    elseif (array_key_exists($colorEnumID, $arResult['OFFERS_IMAGES']))
    {
      $arResult['OFFERS_COLOR_LIST'][$colorEnumID] = [
          'NAME' => $item['VALUE'],
          'HEX'  => $item['HEX'],
          'IMAGES' => $arResult['OFFERS_IMAGES'][$colorEnumID],
          'PREORDER' => $arNewPhotos[$item["VALUE_XML_ID"]]["PREORDER"]["VALUE_XML_ID"] === 'Y',
          'AVAILABLE' => $arResult['COLOR_SIZES'][$colorEnumID]['AVAILABLE'],
          'VIDEO_IMAGE' => $arNewPhotos[$item["VALUE_XML_ID"]]["VIDEO_IMAGE"],
          'VIDEO_FILES' => $arNewPhotos[$item["VALUE_XML_ID"]]["VIDEO_FILES"],
      ];
    }
    else
      $arColorsWithoutImages[$colorEnumID] = $colorEnumID;

    foreach ($arResult['COLOR_SIZES'][ $colorEnumID ]['SIZES'] as $kSize => $vSize){
      $arResult['COLOR_SIZES'][ $colorEnumID ]['SIZES'][$kSize]['PREORDER'] = $arResult['OFFERS_COLOR_LIST'][$colorEnumID]['PREORDER'];
    }
  }
  if (count($arColorsWithoutImages))
  {
    foreach ($arResult['COLOR_SIZES'] as $colorEnumID => $item)
    {
      if (array_key_exists($colorEnumID, $arColorsWithoutImages))
        unset($arResult['COLOR_SIZES'][$colorEnumID]);
    }
  }

  $request = Application::getInstance()->getContext()->getRequest();
  $getColor = $request->get("setColor");
  $setColor[$getColor] = $getColor;
  $colorFlag = false;

  if (empty($setColor[$getColor])) {
    $url = $APPLICATION->GetCurPage().'?setColor='. array_key_first($arResult['COLOR_SIZES']);
    LocalRedirect($url);
  } else {

    foreach ($arResult['COLOR_SIZES'] as $colorEnumID => $item) {
      if (array_key_exists($colorEnumID, $setColor)) {
        $colorFlag = true;
        break;
      }
    }

    if (!$colorFlag)
      LocalRedirect($url);
  }

  // Удаляем ТП без картинок
  //  foreach($arResult['OFFERS_COLOR_LIST'] as $keyColor => $item)
  //  {
  //    if(empty($item['IMAGES']))
  //      unset($arResult['OFFERS_COLOR_LIST'][ $keyColor ]);
  //  }
  //
  //  dbgz($arResult['OFFERS_COLOR_LIST']);

  // Если у ТП нету картинок, то подгружаем для этого цвета дефолтные из товара
  //  $arDefaultImages = [];
  //  if(!empty($arResult['DETAIL_PICTURE']['ID']))
  //    $arDefaultImages[] = $arResult['DETAIL_PICTURE']['ID'];
  //
  //  if(!empty($arResult['PROPERTIES']['IMAGES']['VALUE']))
  //    $arDefaultImages = array_merge($arDefaultImages, $arResult['PROPERTIES']['IMAGES']['VALUE']);
  //
  //  foreach($arResult['OFFERS_COLOR_LIST'] as $keyColor => $item)
  //  {
  //    if(empty($item['IMAGES']))
  //      $arResult['OFFERS_COLOR_LIST'][ $keyColor ]['IMAGES'] = $arDefaultImages;
  //  }


  // Сортировка. Выбранный цвет по ссылке идёт первым
  $SET_COLOR_ID = false;
  if(htmlspecialcharsbx($_REQUEST['setColor']))
  {
    if($arResult['PHOTO_ID'][$_REQUEST['setColor']])
    {
      $SET_COLOR_ID = $arResult['PHOTO_ID'][$_REQUEST['setColor']];
    }
    elseif ($arResult['PHOTO_OFFER_ID'][$_REQUEST['setColor']])
    {
      $SET_COLOR_ID = $arResult['PHOTO_OFFER_ID'][$_REQUEST['setColor']];
    }
    else
    {
      $SET_COLOR_ID = htmlspecialcharsbx($_REQUEST['setColor']);
    }
  }

  if($SET_COLOR_ID)
  {
    $arTmp1 = [];
    $arTmp2 = [];

    foreach($arResult['OFFERS_COLOR_LIST'] as $index => $item)
    {
      if ($index == $SET_COLOR_ID)
        $arTmp1[$index] = $item;
      else
        $arTmp2[$index] = $item;
    }

    $arResult['OFFERS_COLOR_LIST'] = $arTmp1 + $arTmp2;
  }
}
// END COLOR -------------------------------------------------------------------





$ogImageID = false;
if(is_array($arResult['OFFERS_COLOR_LIST']) && count($arResult['OFFERS_COLOR_LIST'])){
  foreach($arResult['OFFERS_COLOR_LIST'] as $arImages)
  {
    foreach($arImages['IMAGES'] as $index => $imageID)
    {
      $ogImageID = $imageID;
      break;
    }
    break;
  }
}

if($ogImageID)
{
  // Создаем изображение для превью соц.сетей
  $image_social = CFile::ResizeImageGet($ogImageID, array('width'=>'500', 'height'=>'500'), BX_RESIZE_IMAGE_EXACT, true);
  $arResult['OG_IMAGE'] = $image_social["src"];

  // Передаем данные в результат после кеширования
  $this->__component->SetResultCacheKeys(array("OG_IMAGE"));
}


foreach($arResult['SECTION']['PATH'] as $key => $arPath) {
  if(SITE_ID == 's2')
    $arResult['SECTION']['PATH'][$key]['NAME'] = CIBlockSection::GetList([], ['ID' => $arPath['ID'], 'IBLOCK_ID' => $arPath['IBLOCK_ID']], false, ['UF_NAME_EN'])->Fetch()['UF_NAME_EN'];
}


if($USER->isAdmin())
{

  $res = CIBlockElement::GetList([], ['IBLOCK_ID' => MATERIALS_ID], false, false, ['ID', 'NAME', 'PROPERTY_NAME_EN']);
  while($arRes = $res->Fetch()) {
    $arResult['MATERIALS_LIST'][$arRes['ID']] = $arRes;
  }

  $arResult['COMPOSITION_LIST'] = [];
  $res = CIBlockElement::GetList([], ['PROPERTY_PARENT' => $arResult['ID'], "IBLOCK_ID" => COMPOSITION_ID], false, false, ['PROPERTY_MATERIAL', 'PROPERTY_VALUE']);
  while($arRes = $res->Fetch()) {
    $arResult['COMPOSITION_LIST'][] = [
      'VALUE' => $arRes['PROPERTY_VALUE_VALUE'],
      'MATERIAL' => (SITE_ID == 's2') ? $arResult['MATERIALS_LIST'][$arRes['PROPERTY_MATERIAL_VALUE']]['PROPERTY_NAME_EN_VALUE'] : $arResult['MATERIALS_LIST'][$arRes['PROPERTY_MATERIAL_VALUE']]['NAME'],
    ];
  }

}


$arResult['IS_AVAILABLE'] = false;

if(count($arResult['COLOR_SIZES']) > 0)
{

  $arOfferId = [];
  foreach($arResult['OFFERS'] as $arOffer) {
    $arOfferId[] = $arOffer['ID'];
  }

  if(!empty($arOfferId))
  {
    CBitrixComponent::includeComponentClass("arlix:product.subscribe");
    $arResult['IS_AVAILABLE'] = LikesComponent::isAvailable($arOfferId);
  }

}

if (is_object($this->__component)) {
  $keyName = 'OFFERS';
  $cp = $this->__component;
  $cp->arResult[$keyName] = $arResult['OFFERS'];
  $cp->SetResultCacheKeys(array($keyName));

  if (!isset($arResult[$keyName])) {
    $arResult[$keyName] = $cp->arResult[$keyName];
  }

  $keyName = 'IS_AUTH';
  $cp->arResult[$keyName] = $arParams['IS_AUTH'];
  $cp->SetResultCacheKeys(array($keyName));

  if (!isset($arResult[$keyName])) {
    $arResult[$keyName] = $cp->arResult[$keyName];
  }

  $keyName = 'ARTICLE';
  $cp->arResult[$keyName] = $arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'];
  $cp->SetResultCacheKeys(array($keyName));

  if (!isset($arResult[$keyName])) {
    $arResult[$keyName] = $cp->arResult[$keyName];
  }
}

$arResult["CompositionJSON"] = '';
if(($index = array_search("CompositionJSON", $arResult["PROPERTIES"]['CML2_TRAITS']["DESCRIPTION"])) !== false){

	$arCompositionJSON = json_decode($arResult["PROPERTIES"]['CML2_TRAITS']['~VALUE'][$index],1);

	$dataLangCurrency = App\Locale::getLangAndCurrency();

	if(count($arCompositionJSON)) {
		$hlBlockId16 = 16; //DetaliSostava
		$hlblock16 = HighloadBlockTable::getById($hlBlockId16)->fetch();
		$entity16 = HighloadBlockTable::compileEntity($hlblock16);
		$DetaliSostava = $entity16->getDataClass();

		$hlBlockId17 = 17; //MeterialySostava
		$hlblock17 = HighloadBlockTable::getById($hlBlockId17)->fetch();
		$entity17 = HighloadBlockTable::compileEntity($hlblock17);
		$MeterialySostava = $entity17->getDataClass();

		$arComposition = [];
		foreach($arCompositionJSON as $key=>$value){
			$arDetaliSostava = $DetaliSostava::getList(['filter' => ["UF_KOD" => $key ]])->fetch();

			if($dataLangCurrency['lang']=="RU"){
				$NAME = $arDetaliSostava['UF_NAME'];
			} else {
				$NAME = strlen($arDetaliSostava['UF_NAIMENOVANIEEN']) ?$arDetaliSostava['UF_NAIMENOVANIEEN'] : $arDetaliSostava['UF_NAME'];
			}
			if(is_array($value)){
				foreach($value as $key_val => $val){
					$arMeterialySostava = $MeterialySostava::getList(['filter' => ["UF_KOD" => $key_val ]])->fetch();

					if($dataLangCurrency['lang']=="RU"){
						$arComposition[$NAME][] = $arMeterialySostava['UF_NAME']."&nbsp;".$val."%";
					} else {
						$arMeterialySostava['UF_NAIMENOVANIEEN'] = strlen($arMeterialySostava['UF_NAIMENOVANIEEN']) ? $arMeterialySostava['UF_NAIMENOVANIEEN'] : $arMeterialySostava['UF_NAME'];
						$arComposition[$NAME][] = $arMeterialySostava['UF_NAIMENOVANIEEN']."&nbsp;".$val."%";
					}
				}
			}
			$arResult["CompositionJSON"] = '';
			$text = [];
			foreach($arComposition as $key=>$val) {
				$text[] = $key.":<br/>".implode(",&nbsp;",$val)."<br/>";
			}
			$arResult["CompositionJSON"] = implode("<br>",$text);
		}
	}
}
