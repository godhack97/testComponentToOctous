<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$this->__component->SetResultCacheKeys(array("CACHED_TPL"));
use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Highloadblock\HighloadBlockTable;

Loader::includeModule("highloadblock");
$hlBlockId = HL_COLOR_ID;
$hlblock = HighloadBlockTable::getById($hlBlockId)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$PsuTaskControl = $entity->getDataClass();

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
  
  // Двумя циклами собираем цвета с их размерами
  foreach ($arResult["OFFERS"] as $offer)
  {
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
              'IS_DISCOUNT' => $offer['ITEM_PRICES'][0]['BASE_PRICE'] !== $offer['ITEM_PRICES'][0]['PRICE'],
            ],
        ];
      }
    }
  }

  foreach ($arResult["OFFERS"] as $offer)
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