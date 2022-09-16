<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();
$arResult["ADD_URL"] = str_replace('#ID#', $arResult['ID'], $arResult["ADD_URL_TEMPLATE"]);


$arResult['OFFERS_LABEL'] = [];


if($arResult["OFFERS"] && is_array($arResult["OFFERS"]))
{
  $arColorsWithImages = [];

  // Собираем фотографии с торговых предложений в соответствии с цветом
  $arResult['OFFERS_IMAGES'] = [];
  foreach($arResult['OFFERS'] as $arOffer)
  {
    if(!empty($arOffer['PROPERTIES']['MORE_PHOTO']['VALUE']))
    {
      //dbgz($arOffer['PROPERTIES']);
      $arTmp = $arResult['OFFERS_IMAGES'][ $arOffer['PROPERTIES']['COLOR']['VALUE_ENUM_ID'] ];
      if(is_array($arTmp) && count($arTmp))
        $arResult['OFFERS_IMAGES'][ $arOffer['PROPERTIES']['COLOR']['VALUE_ENUM_ID'] ] = array_merge($arOffer['PROPERTIES']['MORE_PHOTO']['VALUE'], $arTmp);
      else
        $arResult['OFFERS_IMAGES'][ $arOffer['PROPERTIES']['COLOR']['VALUE_ENUM_ID'] ] = $arOffer['PROPERTIES']['MORE_PHOTO']['VALUE'];
    }
  }
  
  
  foreach ($arResult["OFFERS"] as $offer)
  {
//    if(array_key_exists($offer["PROPERTIES"]["COLOR"]["VALUE_ENUM_ID"], $arResult['OFFERS_IMAGES']))
//    {

      //dbgz($offer["PROPERTIES"]["COLOR"]['VALUE']);
      if($offer["PROPERTIES"]["COLOR"]['VALUE'])
      {
        $COLOR = $offer["PROPERTIES"]["COLOR"];

        if(!$arResult['OFFERS_LABEL'][ $COLOR["VALUE_ENUM_ID"] ])
        {
          //dbgz($offer["PROPERTIES"]["COLOR"]['VALUE']);
          $arResult['OFFERS_LABEL'][ $COLOR["VALUE_ENUM_ID"] ] = [
              'NAME' => $COLOR['NAME'],
              'SORT' => $COLOR['SORT'],
              'CODE' => $COLOR['CODE'],
              'XML_ID' => $COLOR['XML_ID'],
              'VALUE_ENUM' => $COLOR['VALUE_ENUM'],
              'VALUE_XML_ID' => $COLOR['VALUE_XML_ID'],
              'VALUE_SORT' => $COLOR['VALUE_SORT'],
              'VALUE' => $COLOR['VALUE'],
              'PROPERTY_VALUE_ID' => $COLOR['PROPERTY_VALUE_ID'],
              'VALUE_ENUM_ID' => $COLOR['VALUE_ENUM_ID'],
              'DESCRIPTION' => $COLOR['DESCRIPTION'],
              'SIZES' => [],
              'IMAGES' => $offer['PROPERTIES']['IMAGES']['VALUE'],
          ];
        }
      }
      
//    }
  }

  //dbgz($arResult['OFFERS_LABEL']);

  foreach ($arResult["OFFERS"] as $offer)
  {
//    if(array_key_exists($offer["PROPERTIES"]["COLOR"]["VALUE_ENUM_ID"], $arResult['OFFERS_IMAGES']))
//    {
      
      if($offer["PROPERTIES"]["COLOR"]['VALUE'] && $offer["PROPERTIES"]["SIZES"]['VALUE'])
      {
        $COLOR = $offer["PROPERTIES"]["COLOR"];
        $SIZES = $offer["PROPERTIES"]["SIZES"];

        if($arResult['OFFERS_LABEL'][ $COLOR["VALUE_ENUM_ID"] ])
        {
          //dbgz($COLOR["VALUE_ENUM_ID"]);
          $arResult['OFFERS_LABEL'][ $COLOR["VALUE_ENUM_ID"] ]['SIZES'][] = [
              'OFFER_ID' => $offer['ID'],
              'NAME' => $SIZES['NAME'],
              'SORT' => $SIZES['SORT'],
              'CODE' => $SIZES['CODE'],
              'XML_ID' => $SIZES['XML_ID'],
              'VALUE_ENUM' => $SIZES['VALUE_ENUM'],
              'VALUE_XML_ID' => $SIZES['VALUE_XML_ID'],
              'VALUE_SORT' => $SIZES['VALUE_SORT'],
              'VALUE' => $SIZES['VALUE'],
              'PROPERTY_VALUE_ID' => $SIZES['PROPERTY_VALUE_ID'],
              'VALUE_ENUM_ID' => $SIZES['VALUE_ENUM_ID'],
              'DESCRIPTION' => $SIZES['DESCRIPTION'],
          ];
        }
      }
      
//    }
  }

  
  // Массив цветов у которых есть картинки
  $arResult['OFFERS_COLOR_LIST'] = [];
  foreach($arResult['OFFERS_LABEL'] as $keyColor => $item)
  {
    if(array_key_exists($keyColor, $arResult['OFFERS_IMAGES']))
      $arResult['OFFERS_COLOR_LIST'][ $keyColor ] = ['NAME' => $item['VALUE'], 'IMAGES' => $arResult['OFFERS_IMAGES'][ $keyColor ]];
  }
  
  $SET_COLOR_ID = false;
  if(htmlspecialcharsbx($_REQUEST['setColor']))
    $SET_COLOR_ID = htmlspecialcharsbx($_REQUEST['setColor']);

  // Сортировка, выбранный цвет по ссылке идёт первым
  if($SET_COLOR_ID)
  {
    $arTmp1 = [];
    $arTmp2 = [];
    
    foreach($arResult['OFFERS_COLOR_LIST'] as $index => $item)
    {
      if($index == $SET_COLOR_ID)
        $arTmp1[$index] = $item;
      else
        $arTmp2[$index] = $item;
    }
    
    $arResult['OFFERS_COLOR_LIST'] = $arTmp1 + $arTmp2;   
  }
  
}

//dbgz($arResult['OFFERS_COLOR_LIST']);
//dbgz($arResult['OFFERS_LABEL']);