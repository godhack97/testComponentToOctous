<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
  Bitrix\Highloadblock\HighloadBlockTable;

Loader::includeModule("highloadblock");

$arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(HL_COLOR_ID)->fetch();
$obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
$strEntityDataClass = $obEntity->getDataClass();

//dbgz($arResult['ITEMS']);

$arOffersCS = [];
foreach($arResult['ITEMS'] as $key => $arItem)
{
	$productID = $arItem['ID'];
	
	// Получим все оферы товара
  $res = CCatalogSKU::getOffersList($productID, 0, ['ACTIVE' => 'Y']);
  foreach($res[$productID] as $val)
	{
		// Получим данные офера
		$arOffer = CCatalogProduct::GetByIDEx($val['ID'], true);
		
		// Собираем для цвета оффера его размеры
    if(!in_array($arOffer['PROPERTIES']['SIZES']['VALUE_ENUM'], $arOffersCS[$key][$arOffer['PROPERTIES']['COLOR']['VALUE']]['SIZES'])){
      $arOffersCS[$key][$arOffer['PROPERTIES']['COLOR']['VALUE']]['SIZES'][] = $arOffer['PROPERTIES']['SIZES']['VALUE_ENUM'];
    }
		
		// Найдём имя цвета (если ещё не было найдено)
		if(!isset($arOffersCS[$key][$arOffer['PROPERTIES']['COLOR']['VALUE']]['COLOR_NAME']))
		{
			$resData = $strEntityDataClass::getList(['select' => ['ID', 'UF_NAME'], 'filter' => ['UF_XML_ID' => $arOffer['PROPERTIES']['COLOR']['VALUE']]]);
			$arItemC = $resData->Fetch();
			$arOffersCS[$key][$arOffer['PROPERTIES']['COLOR']['VALUE']]['COLOR_NAME'] = $arItemC['UF_NAME'];
		}
  }
	
	// Добавим в ITEM Товара все его цвета и размеры, и прочее...
	$arResult['ITEMS'][$key]['COLORS_SIZES'] = $arOffersCS[$key];
 
 
	// Получим фото из инфоблока фотографий по ID товара (Родитель) и Цвету товара (если ещё не было найдено 1 фото)
	if(!isset($arResult['ITEMS'][$key]['PREVIEW_PHOTO']))
	{
		$arPhotoElObj = CIBlockElement::getList([], ['IBLOCK_ID' => COLORS_ID, 'PROPERTY_PARENT' => $productID], false, false, ['ID', 'IBLOCK_ID', 'PROPERTY_IMAGES']);
		
		// Возьмём 1 фото (без проверки на доступность файла - берём первое фото)
		$previewPhoto = $arPhotoElObj->GetNext()['PROPERTY_IMAGES_VALUE'];
		$arResult['ITEMS'][$key]['PREVIEW_PHOTO'] = CFile::ResizeImageGet($previewPhoto, ['width' => 124, 'height' => 184], BX_RESIZE_IMAGE_EXACT)['src'];
		
		// Возьмём 1 фото (с проверкой на доступность файла) ---------------------------------------------------------------------
		// $previewPhoto = false;
		// while($tmp = $arPhotoElObj->GetNext())
		// {
			// $previewPhoto = $tmp['PROPERTY_IMAGES_VALUE'];
			
			// if(file_exists($_SERVER['DOCUMENT_ROOT'].CFile::GetPath($tmp['PROPERTY_IMAGES_VALUE'])))
			// {
				// $arResult['ITEMS'][$key]['PREVIEW_PHOTO'] = CFile::GetPath($tmp['PROPERTY_IMAGES_VALUE']);
				// break;
			// }
		// }
		
		// // Если всё ещё не нашли - нет фото, кидаем ссылку на битое
		// if(!isset($arResult['ITEMS'][$key]['PREVIEW_PHOTO']))
			// $arResult['ITEMS'][$key]['PREVIEW_PHOTO'] = CFile::GetPath($previewPhoto);
		// если есть уверенность, что битых файлов фото нет - этот кусок закомментить, раскомментить кусок выше
		// -----------------------------------------------------------------------------------------------------------------------
	}
  $previewPhoto = CFile::ResizeImageGet($previewPhoto, ['width' => 124, 'height' => 184], BX_RESIZE_IMAGE_EXACT)['src'];
  if(empty($arResult['ITEMS'][$key]['PREVIEW_PHOTO'])){
    $previewPhoto = SITE_TEMPLATE_PATH.'/images/default.jpg';
  }
  
  
  $colorsList = [];
  $sizesList = [];
  foreach ($arResult['ITEMS'][$key]['COLORS_SIZES'] as $val) {
    if (!in_array($val['COLOR_NAME'], $colorsList)) {
      $colorsList[] = $val['COLOR_NAME'];
    }
    
    foreach ($val['SIZES'] as $size) {
      if (!in_array($size, $sizesList)) {
        $sizesList[] = trim($size);
      }
    }
  }
  
  $arResult['RESPONSE']['items'][] = [
    'id' => $productID,
    'name' => $arItem['NAME'],
    'article' => $arItem['PROPERTIES']['CML2_ARTICLE']['VALUE'],
    'preview_photo' => $previewPhoto,
    'colors' => implode(', ', $colorsList),
    'sizes' => implode(', ', $sizesList),
  ];
  $arResult['RESPONSE']['page'] = isset($_REQUEST['PAGEN_1']) ? intval($_REQUEST['PAGEN_1']) + 1 : 2;
}

//dbgz($arOffers);


