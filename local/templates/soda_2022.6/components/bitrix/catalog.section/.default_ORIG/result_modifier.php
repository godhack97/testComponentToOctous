<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
  
  /**
   * @var CBitrixComponentTemplate $this
   * @var CatalogSectionComponent $component
   * @var $arResult
   */
  
  use Bitrix\Main\Loader,
    Bitrix\Highloadblock\HighloadBlockTable,
    Bitrix\Main\Localization\Loc;
  use Bitrix\Main\Data\Cache;
  
  global $APPLICATION;
  
  $cache = Cache::createInstance();
  $cacheID = 'custom_'.str_replace('/', '_', $APPLICATION->GetCurDir());
  
  
  $component = $this->getComponent();
  $arParams = $component->applyTemplateModifications();
  
  $arItemsNew = [];
  if ($cache->initCache(900, $cacheID) && !isset($_GET['q']) && (!isset($_GET['set_filter']) && !isset($_GET['del_filter'])) && $APPLICATION->GetCurDir() == '/catalog/sale/') {
    $arItemsNew = $cache->getVars();
  }
  elseif ($cache->startDataCache() && !isset($_GET['q']) && (!isset($_GET['set_filter']) && !isset($_GET['del_filter'])) && $APPLICATION->GetCurDir() == '/catalog/sale/') {
    global ${$arParams['FILTER_NAME']};
    /*
     *
    if (${$arParams['FILTER_NAME']}['OFFERS']['=PROPERTY_78']) {
      $sizes = ${$arParams['FILTER_NAME']}['OFFERS']['=PROPERTY_78'];
      if (is_array($sizes) && count($sizes) > 0) {
        foreach ($arResult['ITEMS'] as &$item) {
          foreach ($item['OFFERS'] as $key => $val) {
            if (!in_array($val['DISPLAY_PROPERTIES']['SIZES']['VALUE_ENUM_ID'], $sizes)) {
              unset($item['OFFERS'][$key]);
            }
          }
        }
        unset($item);
      }
    }
    if (${$arParams['FILTER_NAME']}['OFFERS']['=PROPERTY_72']) {
      $colors = ${$arParams['FILTER_NAME']}['OFFERS']['=PROPERTY_72'];
      if (is_array($colors) && count($colors) > 0) {
        foreach ($arResult['ITEMS'] as &$item) {
          foreach ($item['OFFERS'] as $key => $val) {
            if (!in_array($val['DISPLAY_PROPERTIES']['COLOR']['~VALUE'], $colors)) {
              unset($item['OFFERS'][$key]);
            }
          }
        }
        unset($item);
      }
    }
    */
    
    Loader::includeModule('highloadblock');
    
    $hlblock = HighloadBlockTable::getById(2)->fetch();
    $entity = HighloadBlockTable::compileEntity($hlblock);
    $PsuTaskControl = $entity->getDataClass();
    foreach ($arResult['ITEMS'] as &$arItem) {
      if ($arItem['OFFERS'] && is_array($arItem['OFFERS'])) {
        foreach ($arItem['OFFERS'] as &$offer) {
          if ($offer['PROPERTIES']['COLOR']['VALUE']) {
            $offer['PROPERTIES']['COLOR']['VALUE_ENUM_ID'] = $offer['PROPERTIES']['COLOR']['VALUE'];
            $offer['PROPERTIES']['COLOR']['VALUE_XML_ID'] = $offer['PROPERTIES']['COLOR']['VALUE'];
            $reslist = $PsuTaskControl::getList([
              'filter' => [
                'UF_XML_ID' => $offer['PROPERTIES']['COLOR']['VALUE'],
              ],
            ]);
            if ($el = $reslist->fetch()) {
              $offer['PROPERTIES']['COLOR']['VALUE'] = (Loc::getCurrentLang() == 'en') ? $el['UF_NAME_EN'] : $el['UF_NAME'];
            }
          }
        }
        unset($offer);
      }
    }
    unset($arItem);
    
    foreach ($arResult['ITEMS'] as $arItem) {
      $arAllProductsArts[] = $arItem['ID'];
    }
    
    if (!empty($arAllProductsArts)) {
      $arSelect = array('ID', 'IBLOCK_ID', 'NAME', 'SORT', 'DATE_ACTIVE_FROM', 'PROPERTY_*');
      $arFilter = [
	      'IBLOCK_ID' => COLORS_ID,
	      'ACTIVE_DATE' => 'Y',
	      'ACTIVE' => 'Y',
	      'PROPERTY_PARENT' => $arAllProductsArts,
	      '!PROPERTY_IMAGES' => false
      ];
      $res = CIBlockElement::GetList(array(), $arFilter, false, array('nPageSize' => 500), $arSelect);
      while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $arFields = array_merge($arProps, $arFields);
        $arPhotos[$arFields['PARENT']['VALUE']][$arFields['COLOR']['VALUE']] = $arFields;
      }
    }
  
    foreach ($arResult['ITEMS'] as $arItem) {
      $arOffers = [];
    
      if (!empty($arItem['OFFERS'])) {
        $arSetPhoto = [];
      
        $arItemsNew[$arItem['ID']] = $arItem;
        foreach ($arItem['OFFERS'] as $offer) {
        
          $offer['PROPERTIES']['MORE_PHOTO']['VALUE'] = false;
        
          if ($arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']] && !$arSetPhoto[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]) {
            $offer['PROPERTIES']['VIDEO_FILES'] = $arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['VIDEO_FILES'];
            $offer['PROPERTIES']['VIDEO_IMAGE'] = $arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['VIDEO_IMAGE'];
          
            $offer['PROPERTIES']['FULL'] = $arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['FULL'];
            $offer['PROPERTIES']['FULL_IMAGE'] = $arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['FULL_IMAGE'];
            $arSetPhoto[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']] = $offer['ID'];
            $offerFindedPhotos = $arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['IMAGES']['VALUE'];
            $offer['SORT'] = $arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['SORT'];
            $offer['SORT_NEW'] = $arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['SORT_NEW']['VALUE'];
          
            if (!empty($offerFindedPhotos)) {
              $offer['PROPERTIES']['MORE_PHOTO']['VALUE'] = $offerFindedPhotos;
            }
          }
        
        
          if (!empty($offer['PROPERTIES']['MORE_PHOTO']['VALUE'])) {
            $isUse = false;
            foreach ($arOffers as $arOffer) {
              if ($arOffer['PROPERTIES']['COLOR']['VALUE_XML_ID'] == $offer['PROPERTIES']['COLOR']['VALUE_XML_ID'] &&
                $arOffer['PROPERTIES']['CML2_LINK']['VALUE'] == $offer['PROPERTIES']['CML2_LINK']['VALUE']) {
                $isUse = true;
              }
            }
            if (!$isUse) {
              $arOffers[] = $offer;
            }
          }
        
          //if (!empty($arResult['ORIGINAL_PARAMETERS']['GLOBAL_FILTER']['OFFERS']['=PROPERTY_72']) && !in_array($arOffer['PROPERTIES']['COLOR']['VALUE_ENUM_ID'], $arResult['ORIGINAL_PARAMETERS']['GLOBAL_FILTER']['OFFERS']['=PROPERTY_72'])) {
          //  continue;
          // }
        
          $nameOffer = (Loc::getCurrentLang() == 'en') ? $arItem['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE'] : $arItem['NAME'];
        
          $arOfferInfo = [
            'SORT' => $offer['SORT'],
            'SORT_NEW' => $offer['SORT_NEW'],
            'ARTICLE' => $arItem['PROPERTIES']['CML2_ARTICLE']['VALUE'],
            'NAME' => trim($nameOffer) . ', ' . $offer['PROPERTIES']['COLOR']['VALUE'],
            'EDIT_LINK' => $arItem['EDIT_LINK'],
            'DELETE_LINK' => $arItem['DELETE_LINK'],
            'CODE' => $arItem['CODE'],
            'DETAIL_PICTURE' => $offer['PROPERTIES']['MORE_PHOTO']['VALUE'][0],
            'DETAIL_PAGE_URL' => $arItem['DETAIL_PAGE_URL'] . '?setColor=' . $offer['PROPERTIES']['COLOR']['VALUE_ENUM_ID'] . '&offer=' . $offer['ID'],
            'IS_CUSTOM_DISCOUNT' => !empty($arItem['PROPERTIES']['PROTSENT_IZMENENIYA_TSENY']['VALUE']),
            'CUSTOM_DISCOUNT' => $arItem['PROPERTIES']['PROTSENT_IZMENENIYA_TSENY']['VALUE'],
          ];
        
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
          if($offer['ITEM_PRICES'][0]['DISCOUNT'] > 0){
            if($arOfferInfo['IS_CUSTOM_DISCOUNT']){
              $offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'] = ($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'] + ($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE']/100*$arItem['PROPERTIES']['PROTSENT_IZMENENIYA_TSENY']['VALUE']));
              $offer['ITEM_PRICES'][0]['PRINT_BASE_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
              $offer['ITEM_PRICES'][0]['PRINT_RATIO_BASE_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
              $offer['ITEM_PRICES'][0]['PRINT_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
              $offer['ITEM_PRICES'][0]['PRINT_RATIO_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
            }
          }
          else{
            if($arOfferInfo['IS_CUSTOM_DISCOUNT']){
              $offer['ITEM_PRICES'][0]['DISCOUNT'] = true;
              $offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'] = ($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'] + ($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE']/100*$arItem['PROPERTIES']['PROTSENT_IZMENENIYA_TSENY']['VALUE']));
              $offer['ITEM_PRICES'][0]['PRINT_BASE_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
              $offer['ITEM_PRICES'][0]['PRINT_RATIO_BASE_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
              $offer['ITEM_PRICES'][0]['PRINT_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
              $offer['ITEM_PRICES'][0]['PRINT_RATIO_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
            }
          }
        
          if(!isset($arItemsNew[$arItem['ID']]['colors'][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']])){
            $arItemsNew[$arItem['ID']]['colors'][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']] = [
              'id' => $offer['PROPERTIES']['COLOR']['VALUE_XML_ID'],
              'name' => $offer['PROPERTIES']['COLOR']['VALUE'],
              'hex' => $offer['PROPERTIES']['COLOR']['HEX'],
              'item' => array_merge($offer, $arOfferInfo),
            ];
          }
        }
        if(isset(${$arParams["FILTER_NAME"]}['OFFERS'])){
          foreach (${$arParams["FILTER_NAME"]}['OFFERS'] as $kFilterOffers =>$filterOffers){
            switch ($kFilterOffers){
              case '=PROPERTY_72':
                foreach ($filterOffers as $value){
                  foreach ($arItemsNew[$arItem['ID']]['colors'] as $kColor => $iColor){
                    if($kColor !== $value){
                      unset($arItemsNew[$arItem['ID']]['colors'][$kColor]);
                    }
                  }
                }
                if(count($arItemsNew[$arItem['ID']]['colors']) < 1){
                  //unset($arItemsNew[$arItem['ID']]);
                }
                break;
            }
          }
        }
      }
      else {
        $arItem['IS_CUSTOM_DISCOUNT'] = !empty($arItem['PROPERTIES']['PROTSENT_IZMENENIYA_TSENY']['VALUE']);
        $arItem['CUSTOM_DISCOUNT'] = $arItem['PROPERTIES']['PROTSENT_IZMENENIYA_TSENY']['VALUE'];
        $arItemsNew[] = $arItem;
      }
    }
    foreach ($arItemsNew as $iKey =>$item) {
      $arOffers = [];
      //print_r($item['colors']);
      foreach ($item['colors'] as $kColor => $color) {
        if(empty($color['item']['DETAIL_PICTURE'])){
          unset($arItemsNew[$iKey]['colors'][$kColor]);
        }
      }
    }
    foreach ($arItemsNew as $kItem => $vItem){
      if(isset($vItem['colors'])){
        foreach ($vItem['colors'] as $color){
          if(isset($color['item'])){
            $sort = 0;
            if($APPLICATION->GetCurDir() == '/catalog/new/' || $APPLICATION->GetCurDir() == '/en/catalog/new/'){
              if($sort < $color['item']['SORT_NEW']){
                $sort = $color['item']['SORT_NEW'];
              }
            }
            else {
              if($sort < $color['item']['SORT']){
                $sort = $color['item']['SORT'];
              }
            }
            $arItemsNew[$kItem]['SORT'] = $sort;
          }
        }
      }
      $arItemsNew[$kItem]['section'] = ['name' => $arResult['NAME'], 'url' => $arResult['SECTION_PAGE_URL']];
    }
    
    //if($APPLICATION->GetCurDir() == '/catalog/new/' || $APPLICATION->GetCurDir() == '/en/catalog/new/'){
    //  uasort($arItemsNew, function ($a, $b) {
    //    //return $a['SORT'] - $b['SORT'];
    //    if ($a['SORT_NEW'] == $b) {
    //      return 0;
    //    }
        
    //    return ($a['SORT_NEW'] > $b['SORT_NEW']) ? -1 : 1;
    //  });
    //}
    //else{
      uasort($arItemsNew, function ($a, $b) {
        //return $a['SORT'] - $b['SORT'];
        if ($a['SORT'] == $b) {
          return 0;
        }
        
        return ($a['SORT'] > $b['SORT']) ? -1 : 1;
      });
    //}
    
    $cache->endDataCache($arItemsNew); // записываем в кеш
  }
  else{
    global ${$arParams['FILTER_NAME']};
    /*
     *
    if (${$arParams['FILTER_NAME']}['OFFERS']['=PROPERTY_78']) {
      $sizes = ${$arParams['FILTER_NAME']}['OFFERS']['=PROPERTY_78'];
      if (is_array($sizes) && count($sizes) > 0) {
        foreach ($arResult['ITEMS'] as &$item) {
          foreach ($item['OFFERS'] as $key => $val) {
            if (!in_array($val['DISPLAY_PROPERTIES']['SIZES']['VALUE_ENUM_ID'], $sizes)) {
              unset($item['OFFERS'][$key]);
            }
          }
        }
        unset($item);
      }
    }
    if (${$arParams['FILTER_NAME']}['OFFERS']['=PROPERTY_72']) {
      $colors = ${$arParams['FILTER_NAME']}['OFFERS']['=PROPERTY_72'];
      if (is_array($colors) && count($colors) > 0) {
        foreach ($arResult['ITEMS'] as &$item) {
          foreach ($item['OFFERS'] as $key => $val) {
            if (!in_array($val['DISPLAY_PROPERTIES']['COLOR']['~VALUE'], $colors)) {
              unset($item['OFFERS'][$key]);
            }
          }
        }
        unset($item);
      }
    }
    */
    Loader::includeModule('highloadblock');
    
    $hlblock = HighloadBlockTable::getById(2)->fetch();
    $entity = HighloadBlockTable::compileEntity($hlblock);
    $PsuTaskControl = $entity->getDataClass();
    foreach ($arResult['ITEMS'] as &$arItem) {
      if ($arItem['OFFERS'] && is_array($arItem['OFFERS'])) {
        foreach ($arItem['OFFERS'] as &$offer) {
          if ($offer['PROPERTIES']['COLOR']['VALUE']) {
            $offer['PROPERTIES']['COLOR']['VALUE_ENUM_ID'] = $offer['PROPERTIES']['COLOR']['VALUE'];
            $offer['PROPERTIES']['COLOR']['VALUE_XML_ID'] = $offer['PROPERTIES']['COLOR']['VALUE'];
            $reslist = $PsuTaskControl::getList([
              'filter' => [
                'UF_XML_ID' => $offer['PROPERTIES']['COLOR']['VALUE'],
              ],
            ]);
            if ($el = $reslist->fetch()) {
              $offer['PROPERTIES']['COLOR']['VALUE'] = (Loc::getCurrentLang() == 'en') ? $el['UF_NAME_EN'] : $el['UF_NAME'];
              $offer['PROPERTIES']['COLOR']['HEX'] = $el['UF_HEX'];
            }
          }
        }
        unset($offer);
      }
    }
    unset($arItem);
    
    foreach ($arResult['ITEMS'] as $arItem) {
      $arAllProductsArts[] = $arItem['ID'];
    }
    
    if (!empty($arAllProductsArts)) {
      $arSelect = ['ID', 'IBLOCK_ID', 'NAME', 'SORT', 'DATE_ACTIVE_FROM', 'PROPERTY_*'];
      $arFilter = [
	      'IBLOCK_ID' => COLORS_ID,
	      'ACTIVE_DATE' => 'Y',
	      'ACTIVE' => 'Y',
	      'PROPERTY_PARENT' => $arAllProductsArts,
	      '!PROPERTY_IMAGES' => false
      ];
      $res = CIBlockElement::GetList(array(), $arFilter, false, array('nPageSize' => 500), $arSelect);
      while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $arFields = array_merge($arProps, $arFields);
        $arPhotos[$arFields['PARENT']['VALUE']][$arFields['COLOR']['VALUE']] = $arFields;
      }
    }
    
    foreach ($arResult['ITEMS'] as $arItem) {
      $arOffers = [];
			
      if (!empty($arItem['OFFERS'])) {
        $arSetPhoto = [];
        foreach ($arItem['OFFERS'] as $offer) {
					
          $offer['PROPERTIES']['MORE_PHOTO']['VALUE'] = false;
          
          if ($arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']] && !$arSetPhoto[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]) {
            $offer['PROPERTIES']['VIDEO_FILES'] = $arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['VIDEO_FILES'];
            $offer['PROPERTIES']['VIDEO_IMAGE'] = $arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['VIDEO_IMAGE'];
            
            $offer['PROPERTIES']['FULL'] = $arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['FULL'];
            $offer['PROPERTIES']['FULL_IMAGE'] = $arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['FULL_IMAGE'];
            $arSetPhoto[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']] = $offer['ID'];
            $offerFindedPhotos = $arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['IMAGES']['VALUE'];
            $offer['SORT'] = $arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['SORT'];
            $offer['SORT_NEW'] = $arPhotos[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['SORT_NEW']['VALUE'];
            
            if (!empty($offerFindedPhotos)) {
              $offer['PROPERTIES']['MORE_PHOTO']['VALUE'] = $offerFindedPhotos;
            }
          }
          
          if (!empty($offer['PROPERTIES']['MORE_PHOTO']['VALUE'])) {
            $isUse = false;
            foreach ($arOffers as $arOffer) {
              if ($arOffer['PROPERTIES']['COLOR']['VALUE_XML_ID'] == $offer['PROPERTIES']['COLOR']['VALUE_XML_ID'] &&
                $arOffer['PROPERTIES']['CML2_LINK']['VALUE'] == $offer['PROPERTIES']['CML2_LINK']['VALUE']) {
                $isUse = true;
              }
            }
            if (!$isUse) {
              $arOffers[] = $offer;
            }
          }
	
	        //if (!empty($arResult['ORIGINAL_PARAMETERS']['GLOBAL_FILTER']['OFFERS']['=PROPERTY_72']) && !in_array($arOffer['PROPERTIES']['COLOR']['VALUE_ENUM_ID'], $arResult['ORIGINAL_PARAMETERS']['GLOBAL_FILTER']['OFFERS']['=PROPERTY_72'])) {
		      //  continue;
	       // }
	
	        $nameOffer = (Loc::getCurrentLang() == 'en') ? $arItem['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE'] : $arItem['NAME'];
	
	        $arOfferInfo = [
		        'SORT' => $offer['SORT'],
		        'SORT_NEW' => $offer['SORT_NEW'],
		        'ARTICLE' => $arItem['PROPERTIES']['CML2_ARTICLE']['VALUE'],
		        'NAME' => trim($nameOffer) . ', ' . $offer['PROPERTIES']['COLOR']['VALUE'],
		        'EDIT_LINK' => $arItem['EDIT_LINK'],
		        'DELETE_LINK' => $arItem['DELETE_LINK'],
		        'CODE' => $arItem['CODE'],
		        'DETAIL_PICTURE' => $offer['PROPERTIES']['MORE_PHOTO']['VALUE'][0],
		        'DETAIL_PAGE_URL' => $arItem['DETAIL_PAGE_URL'] . '?setColor=' . $offer['PROPERTIES']['COLOR']['VALUE_ENUM_ID'] . '&offer=' . $offer['ID'],
		        'IS_CUSTOM_DISCOUNT' => !empty($arItem['PROPERTIES']['PROTSENT_IZMENENIYA_TSENY']['VALUE']),
		        'CUSTOM_DISCOUNT' => $arItem['PROPERTIES']['PROTSENT_IZMENENIYA_TSENY']['VALUE'],
	        ];
	
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
	        if($offer['ITEM_PRICES'][0]['DISCOUNT'] > 0){
		        if($arOfferInfo['IS_CUSTOM_DISCOUNT']){
              $customBasePrice = ($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'] + ($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE']/100*$arItem['PROPERTIES']['PROTSENT_IZMENENIYA_TSENY']['VALUE']));
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
			        $offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'] = $customBasePrice;
			        $offer['ITEM_PRICES'][0]['PRINT_BASE_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
			        $offer['ITEM_PRICES'][0]['PRINT_RATIO_BASE_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
              $offer['ITEM_PRICES'][0]['PRINT_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
              $offer['ITEM_PRICES'][0]['PRINT_RATIO_PRICE'] = SaleFormatCurrency($offer['ITEM_PRICES'][0]['RATIO_PRICE'], $offer['ITEM_PRICES'][0]["CURRENCY"]);
		        }
	        }
	        else{
		        if($arOfferInfo['IS_CUSTOM_DISCOUNT']){
              $customBasePrice = ($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'] + ($offer['ITEM_PRICES'][0]['RATIO_BASE_PRICE'] / 100 * $arItem['PROPERTIES']['PROTSENT_IZMENENIYA_TSENY']['VALUE']));
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
	        if(!isset($arItemsNew[$arItem['ID']]['colors'][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']])){
		        $arItemsNew[$arItem['ID']]['colors'][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']] = [
			        'id' => $offer['PROPERTIES']['COLOR']['VALUE_XML_ID'],
			        'name' => $offer['PROPERTIES']['COLOR']['VALUE'],
			        'hex' => $offer['PROPERTIES']['COLOR']['HEX'],
			        'item' => array_merge($offer, $arOfferInfo),
		        ];
	        }
          
        }
	      if(isset(${$arParams["FILTER_NAME"]}['OFFERS'])){
		      foreach (${$arParams["FILTER_NAME"]}['OFFERS'] as $kFilterOffers =>$filterOffers){
            switch ($kFilterOffers){
              case '=PROPERTY_72':
                foreach ($filterOffers as $value){
                  foreach ($arItemsNew[$arItem['ID']]['colors'] as $kColor => $iColor){
                    if($kColor !== $value){
                      unset($arItemsNew[$arItem['ID']]['colors'][$kColor]);
                    }
                  }
                }
                if(count($arItemsNew[$arItem['ID']]['colors']) < 1){
                  //unset($arItemsNew[$arItem['ID']]);
                }
                break;
						}
		      }
	      }
      }
      else {
	      $arItem['IS_CUSTOM_DISCOUNT'] = !empty($arItem['PROPERTIES']['PROTSENT_IZMENENIYA_TSENY']['VALUE']);
	      $arItem['CUSTOM_DISCOUNT'] = $arItem['PROPERTIES']['PROTSENT_IZMENENIYA_TSENY']['VALUE'];
	      $arItemsNew[] = $arItem;
      }
    }
	  foreach ($arItemsNew as $iKey =>$item) {
		  $arOffers = [];
		  //print_r($item['colors']);
		  foreach ($item['colors'] as $kColor => $color) {
				if(empty($color['item']['DETAIL_PICTURE'])){
					unset($arItemsNew[$iKey]['colors'][$kColor]);
				}
		  }
	  }
		foreach ($arItemsNew as $kItem => $vItem){
			if(isset($vItem['colors'])){
				foreach ($vItem['colors'] as $color){
					if(isset($color['item'])){
						$sort = 0;
						if($APPLICATION->GetCurDir() == '/catalog/new/' || $APPLICATION->GetCurDir() == '/en/catalog/new/'){
							if($sort < $color['item']['SORT_NEW']){
								$sort = $color['item']['SORT_NEW'];
							}
						}
						else {
							if($sort < $color['item']['SORT']){
								$sort = $color['item']['SORT'];
							}
						}
						$arItemsNew[$kItem]['SORT'] = $sort;
					}
				}
			}
			$arItemsNew[$kItem]['section'] = ['name' => $arResult['NAME'], 'url' => $arResult['SECTION_PAGE_URL']];
		}
    
    //if($APPLICATION->GetCurDir() == '/catalog/new/' || $APPLICATION->GetCurDir() == '/en/catalog/new/'){
    //  uasort($arItemsNew, function ($a, $b) {
    //    //return $a['SORT'] - $b['SORT'];
    //    if ($a['SORT_NEW'] == $b) {
   //      return 0;
    //    }
    //    return ($a['SORT_NEW'] > $b['SORT_NEW']) ? -1 : 1;
    //  });
    //}
    //else{
      uasort($arItemsNew, function ($a, $b) {
        //return $a['SORT'] - $b['SORT'];
        if ($a['SORT'] == $b) {
          return 0;
        }
        
        return ($a['SORT'] > $b['SORT']) ? -1 : 1;
      });
    //}
  }
	
	global $USER;
	
	if($USER->IsAdmin()){
		//print_r('<pre>');
		//print_r($arItemsNew);
		//print_r('</pre>');
	}
  $arResult['ITEMS'] = $arItemsNew;
  $arResult['CURRENT_ITEMS'] = $arItemsNew;
  
  $listChunk = array_chunk($arResult['ITEMS'], $arParams['CUSTOM_PAGE_ELEMENT_COUNT']);
  
  $arResult['PAGES'] = count($listChunk);
  //$listChunk = array_chunk($arResult['ITEMS'], $arParams['CUSTOM_PAGE_ELEMENT_COUNT']);
  $arResult['CURRENT_PAGE'] = ((int)$_REQUEST['page'] - 1);
  if (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) {
    $arResult['CURRENT_PAGE'] = $_REQUEST['page'];
    //$arResult['ITEMS'] = $listChunk[$arResult['CURRENT_PAGE']];
  }
  else {
    $arResult['CURRENT_PAGE'] = 1;
    //$arResult['ITEMS'] = $listChunk[0];
  }