<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//Make all properties present in order
//to prevent html table corruption
foreach($arResult["ITEMS"] as $key => $arElement)
{
	$arRes = array();
	foreach($arParams["PROPERTY_CODE"] as $pid)
	{
		$arRes[$pid] = CIBlockFormatProperties::GetDisplayValue($arElement, $arElement["PROPERTIES"][$pid], "catalog_out");
	}
	$arResult["ITEMS"][$key]["DISPLAY_PROPERTIES"] = $arRes;
}



foreach($arResult["ITEMS"] as $key => $arElement)
{
  $arProduct = \CCatalogSku::GetProductInfo($arElement["ID"]);
  
  $arSelect = ["ID", "IBLOCK_ID", "NAME", "SORT", "DATE_ACTIVE_FROM", "PROPERTY_*"];
  $arFilter = [
      "IBLOCK_ID" => COLORS_ID,
      "ACTIVE_DATE" => "Y",
      "ACTIVE" => "Y",
      'PROPERTY_PARENT' => $arProduct['ID'],
      'PROPERTY_COLOR' => $arElement['PROPERTIES']['COLOR']['VALUE'],
  ];
  $res = CIBlockElement::GetList([], $arFilter, false, Array("nPageSize" => 1), $arSelect);
  if ($ob = $res->GetNextElement())
  {
    $arFields = $ob->GetProperties();
    $images = $arFields["IMAGES"]["VALUE"];
    if($images && is_array($images) && count($images) > 0)
    {
      $arPhotoSmall = CFile::ResizeImageGet($images[0], ['width' => 124, 'height' => 184], BX_RESIZE_IMAGE_EXACT);
      $arResult["ITEMS"][ $key ]['PICTURE']['SRC'] = $arPhotoSmall['src'];
    }
  }
  
  
  $db_props = CIBlockElement::GetProperty(CATALOG_ID, $arProduct['ID'], array("sort" => "asc"), Array("CODE"=>"CML2_ARTICLE"));
  $ar_props = $db_props->Fetch();
  if(!empty($ar_props['VALUE']))
    $arResult["ITEMS"][ $key ]['PROPERTIES']['CML2_ARTICLE']['VALUE'] = $ar_props['VALUE'];
  
}