<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
use \Bitrix\Main\Localization\Loc;

global $USER;
if (Loc::getCurrentLang() == 'en') {
  $arNames = [];
  foreach ($arResult as $arItem) {
    $arNames[] = $arItem['TEXT'];
  }

  $countv = 1;
  $rsSections = CIBlockSection::GetList([], ['IBLOCK_ID' => CATALOG_ID, "NAME" => $arNames], false, ['NAME', 'UF_NAME_EN']);
  while($arSections = $rsSections->Fetch()) {
    foreach ($arResult as $key => $arItem) {
      if($arItem['TEXT'] == $arSections['NAME']) {
        $arResult[$key]['TEXT'] = $arSections['UF_NAME_EN'];
        continue 2;
      }
      $countv++;
    }
  }
}

foreach($arResult as $arItem) {
  
  if($arItem['LINK'] == '/catalog/sale/')
    continue;

  if($arItem['LINK'] == '/en/catalog/sale/')
    continue;

  if(!empty($arItem['PARAMS'])) {
    foreach($arItem['PARAMS'] as $val) {
      if($val == 'catalog') {
        $arResult['CATALOG_MENU'][] = $arItem;
        continue 2;
      }
    }
  }

  $arResult['INFO_MENU'][] = $arItem;
}
?>
