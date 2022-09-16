<?php
namespace Sodamoda\Core\Events;

use \Bitrix\Main\Loader;
use \Bitrix\Iblock\PropertyTable;
use \Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Diag\Debug;

class Iblock{

  public function OnBeforeIBlockElementAdd(&$arFields)
  {
    if ($arFields['TMP_ID'] && $arFields['IBLOCK_ID'] == CATALOG_ID) {
      $arFields['ACTIVE'] = 'N';
    }

    if (in_array($arFields['IBLOCK_ID'], [CATALOG_ID, COLORS_ID])) {
      imagesResizeOriginal($arFields);
    }
		
	  if ($arFields['IBLOCK_ID'] == CATALOG_OFFERS_ID) {
		  $arFields['NAME'] = str_replace('  ', ' ', trim($arFields['NAME']));
		  //Debug::writeToFile($arFields);
	  }
  }
  public function OnBeforeIBlockElementUpdate(&$arFields)
  {
    if ($_REQUEST['mode'] == 'import' && $arFields['IBLOCK_ID'] != CATALOG_OFFERS_ID)
    {
      unset($arFields['NAME']);
    }
    if ($arFields['TMP_ID'] && in_array($arFields['IBLOCK_ID'], array(CATALOG_ID, CATALOG_OFFERS_ID))) {
      unset($arFields['ACTIVE']);
    }

    if (in_array($arFields['IBLOCK_ID'], [CATALOG_ID, COLORS_ID])) {
      imagesResizeOriginal($arFields);
    }
	  if ($arFields['IBLOCK_ID'] == CATALOG_OFFERS_ID) {
		  $arFields['NAME'] = str_replace(['  ', ' ))', ' ,'], [' ', '))', ','], trim($arFields['NAME']));
			if(empty($arFields['NAME'])){
				$name = '';
				$mxResult = \CCatalogSku::GetProductInfo($arFields['ID']);
				if (is_array($mxResult)){
					$res = \CIBlockElement::GetByID($mxResult["ID"]);
					if($ar_res = $res->GetNext()) {
						if(!empty($arFields['PROPERTY_VALUES'][78]['VALUE'])){
							$name .= $ar_res['NAME']. '('.trim($arFields['PROPERTY_VALUES'][78]['VALUE']);
						}
						elseif(!empty($arFields['PROPERTY_VALUES'][227]['VALUE'])){
							$name .= $ar_res['NAME']. '('.trim($arFields['PROPERTY_VALUES'][227]['VALUE']);
						}
						elseif(!empty($arFields['PROPERTY_VALUES'][232]['VALUE'])){
							$name .= $ar_res['NAME']. '('.trim($arFields['PROPERTY_VALUES'][232]['VALUE']).' ';
						}
						
						Loader::includeModule('highloadblock');
						
						$hlblock = HighloadBlockTable::getById(2)->fetch();
						$entity = HighloadBlockTable::compileEntity($hlblock);
						$PsuTaskControl = $entity->getDataClass();
						$reslist = $PsuTaskControl::getList([
							'filter' => [
								'UF_XML_ID' => $arFields['PROPERTIES'][72]['VALUE'],
							],
						]);
						if ($el = $reslist->fetch()) {
							$name .= $el['UF_NAME'].')';
						}
					}
					$arFields['NAME'] = $name;
				}
			}
		  //Debug::writeToFile($arFields);
	  }
    
    if($arFields['IBLOCK_ID'] == COLORS_ID && isset($arFields['PROPERTY_VALUES'][223])){
      $parentProduct = $arFields['PROPERTY_VALUES'][122][array_key_first($arFields['PROPERTY_VALUES'][122])]['VALUE'];
      $colorProductXML_ID = $arFields['PROPERTY_VALUES'][120][array_key_first($arFields['PROPERTY_VALUES'][120])]['VALUE'];
    
      $res = \CCatalogSKU::getOffersList($parentProduct, 0, ['PROPERTY_COLOR' => $colorProductXML_ID, 'ACTIVE' => 'Y']);
      foreach ($res[$parentProduct] as $offer){
        $updateProduct = ['QUANTITY_TRACE' => isset($arFields['PROPERTY_VALUES'][223][0]) ? 'N' : 'D'];
        \CCatalogProduct::Update($offer['ID'], $updateProduct);
      }
    }
  }
  public function OnBeforeIBlockPropertyAdd(&$arFields)
  {
    if ($_REQUEST['mode'] == 'import')
    {
      unset($arFields['NAME']);
    }
    if ($arFields['TMP_ID'] && in_array($arFields['IBLOCK_ID'], array(CATALOG_ID, CATALOG_OFFERS_ID))) {
      unset($arFields['ACTIVE']);
    }

    if (in_array($arFields['IBLOCK_ID'], [CATALOG_ID, COLORS_ID])) {
      imagesResizeOriginal($arFields);
    }
	  if ($arFields['IBLOCK_ID'] == CATALOG_OFFERS_ID) {
		  $arFields['NAME'] = trim($arFields['NAME']);
	  }
    
    if($arFields['IBLOCK_ID'] == COLORS_ID && isset($arFields['PROPERTY_VALUES'][223])){
      $parentProduct = $arFields['PROPERTY_VALUES'][122][array_key_first($arFields['PROPERTY_VALUES'][122])]['VALUE'];
      $colorProductXML_ID = $arFields['PROPERTY_VALUES'][120][array_key_first($arFields['PROPERTY_VALUES'][120])]['VALUE'];
    
      $res = \CCatalogSKU::getOffersList($parentProduct, 0, ['PROPERTY_COLOR' => $colorProductXML_ID, 'ACTIVE' => 'Y']);
      foreach ($res[$parentProduct] as $offer){
        $updateProduct = ['QUANTITY_TRACE' => isset($arFields['PROPERTY_VALUES'][223][0]) ? 'N' : 'D'];
        \CCatalogProduct::Update($offer['ID'], $updateProduct);
      }
    }
  }

  public function getUserTypeDescription()
  {
    return array(
      'USER_TYPE' => 'UserTypeElemColors',
      'PROPERTY_TYPE' => PropertyTable::TYPE_ELEMENT,
      'DESCRIPTION' => 'Элемент с цветом',
      'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
      //"VIEW_CALLBACK" => array(__CLASS__, 'GetPublicView'),
      //"EDIT_CALLBACK" => array(__CLASS__, 'GetPublicEdit'),
    );
  }
  public function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
  {

    $arItem = array(
      'ID' => 0,
      'IBLOCK_ID' => 0,
      'NAME' => ''
    );

    if (intval($value['VALUE']) > 0) {
      $arFilter = array(
        'ID' => intval($value['VALUE']),
        'IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'],
      );
      $arItem = \CIBlockElement::GetList(array(), $arFilter, false, false, array('ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_COLOR'))->Fetch();

      if ($arItem['PROPERTY_COLOR_VALUE']) {
        Loader::includeModule('highloadblock');

        $hlblock = HighloadBlockTable::getById(2)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlblock);
        $PsuTaskControl = $entity->getDataClass();

        $reslist = $PsuTaskControl::getList([
          'filter' => [
            'UF_XML_ID' => $arItem['PROPERTY_COLOR_VALUE'],
          ],
        ]);
        if ($el = $reslist->fetch()) {
          $arItem['PROPERTY_COLOR_VALUE'] = $el['UF_NAME'];
        }
      }


    }

    $html = '<input name="' . $strHTMLControlName['VALUE'] . '" id="' . $strHTMLControlName['VALUE'] . '" value="' . htmlspecialcharsex($value['VALUE']) . '" size="5" type="text">';
    $html .= '<input type="button" value="..." onclick="jsUtils.OpenWindow(\'/local/php_interface/iblock_element_search.php?lang=' . LANG . '&IBLOCK_ID=' . $arProperty['LINK_IBLOCK_ID'] . '&n=' . $strHTMLControlName['VALUE'] . '\', 600, 500);">';
    $html .= ' <span id="sp_' . $strHTMLControlName['VALUE'] . '">' . $arItem['NAME'] . ' - ' . $arItem['PROPERTY_COLOR_VALUE'] . '</span>';
    return $html;
  }
  public static function getUserTypeDescriptionСolor()
  {
    return array(
      'USER_TYPE' => 'UserTypeElemListColor',
      'PROPERTY_TYPE' => PropertyTable::TYPE_STRING,
      'DESCRIPTION' => 'Цвет товара',
      'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtmlColor'),
	    "ConvertToDB" => array(__CLASS__,"ConvertToDBColor"),
	    "ConvertFromDB" => array(__CLASS__,"ConvertFromDBColor"),
    );
  }
  public function GetPropertyFieldHtmlColor($arProperty, $value, $strHTMLControlName)
  {
		$id = $_REQUEST['ID'];
		$iblock = $_REQUEST['IBLOCK_ID'];
	  $arItemObj = \CIBlockElement::getList(['SORT' => 'ASC'], ['IBLOCK_ID' => $iblock, 'ACTIVE' => 'Y', '=ID' => $id], false, false, []);
	  while ($arItem = $arItemObj->GetNextElement()) {
		  $mainItem = $arItem->GetFields();
		  $mainItem['PROPERTIES'] = $arItem->GetProperties();
	  }
		$colorListID = [];
		if(!empty($mainItem['PROPERTIES']['SLIDE_PRODUCT']['VALUE'])){
			$res = \CCatalogSKU::getOffersList($mainItem['PROPERTIES']['SLIDE_PRODUCT']['VALUE'], 0, ['ACTIVE' => 'Y']);
			
			// Для каждого офера
			foreach ($res[$mainItem['PROPERTIES']['SLIDE_PRODUCT']['VALUE']] as $offerID => $val) {
				$arFilter = array(
					'ID' => intval($offerID),
					'IBLOCK_ID' => 8,
				);
				$arItem = \CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_COLOR'])->Fetch();
				
				if ($arItem['PROPERTY_COLOR_VALUE']) {
					Loader::includeModule('highloadblock');
					
					$hlblock = HighloadBlockTable::getById(2)->fetch();
					$entity = HighloadBlockTable::compileEntity($hlblock);
					$PsuTaskControl = $entity->getDataClass();
					
					$reslist = $PsuTaskControl::getList([
						'filter' => [
							'UF_XML_ID' => $arItem['PROPERTY_COLOR_VALUE'],
						],
					]);
					if ($el = $reslist->fetch()) {
						$colorListID[$arItem['PROPERTY_COLOR_VALUE']] = $el['UF_NAME'];
					}
				}
			}
		}
    $html = '<select name="' . $strHTMLControlName['VALUE'] . '" style="width:150px">';
	  $html .= '<option value="0">Не выбрано</option>';
		foreach ($colorListID as $id => $name) {
		  $html .= sprintf('<option value="%1$s" %2$s>%3$s</option>', htmlspecialchars($id), ($id == $value['VALUE'] ? 'selected' : ""), htmlspecialchars($name));
	  }
		$html .= '</select>';
    return $html;
  }
	public function ConvertToDBColor($arProperty, $value){ //Сохранение в БД
		return $value;
	}
	public function ConvertFromDBColor($arProperty, $value){ //Извлечение из БД
		return $value;
	}
}