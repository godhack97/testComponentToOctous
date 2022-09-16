<?
$siteId = '';
if (isset($_REQUEST['site_id']) && is_string($_REQUEST['site_id']))
  $siteId = substr(preg_replace('/[^a-z0-9_]/i', '', $_REQUEST['site_id']), 0, 2);

if ($siteId) {
  define('SITE_ID', $siteId);
}
?>
<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php'); ?>
<?

use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader,
  Bitrix\Highloadblock\HighloadBlockTable;

global $USER;

Loader::includeModule('highloadblock');

$arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(HL_COLOR_ID)->fetch();
$obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
$strEntityDataClass = $obEntity->getDataClass();

// Получим GET параметр поиска
$searchData = trim(htmlspecialcharsbx($_GET['article']));

$userID = $USER->GetID();

// Собираем фильтр
$arFilter = [];
$arFilter['IBLOCK_ID'] = CATALOG_ID;

if ($searchData != '') {
  // Если в searchData только цифры - это артикул, ИНАЧЕ - это название
  // if(preg_match('/^[0-9]*$/', $searchData))
  // $arFilter['PROPERTY_CML2_ARTICLE'] = $searchData;
  // else
  // $arFilter['NAME'] = '%'.$searchData.'%';

  // Получаем выборку
  $arFilter['=PROPERTY_CML2_ARTICLE'] = $searchData;
  $productsObj = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_CML2_ARTICLE', 'PROPERTY_PRODUCT_OWNER']);
  
  if ($productsObj->result->num_rows == 0) {
    unset($arFilter['PROPERTY_CML2_ARTICLE']);
    $arFilter['NAME'] = '%' . $searchData . '%';
    $productsObj = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_CML2_ARTICLE', 'PROPERTY_PRODUCT_OWNER']);
  }
}
// Собираем нужные данные
$arOffersCS = [];
while ($arItem = $productsObj->GetNext()) {
  $productID = $arItem['ID'];

  $arOffersCS[$productID]['NAME'] = $arItem['NAME'];
  $arOffersCS[$productID]['ARTICLE'] = $arItem['PROPERTY_CML2_ARTICLE_VALUE'];
}

echo json_encode(['status' => count($arOffersCS) > 0, 'list' => $arOffersCS]);
?>