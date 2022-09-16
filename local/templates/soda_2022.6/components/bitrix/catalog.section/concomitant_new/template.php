<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

  use \Bitrix\Main\Localization\Loc;

  /**
   * @global CMain $APPLICATION
   * @var array $arParams
   * @var array $arResult
   * @var CatalogSectionComponent $component
   * @var CBitrixComponentTemplate $this
   * @var string $templateName
   * @var string $componentPath
   */

  $this->setFrameMode(true);

  CModule::IncludeModule('highloadblock');

  $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(HL_COLOR_ID)->fetch();
  $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
  $strEntityDataClass = $obEntity->getDataClass();

  $showTopPager = false;
  $showBottomPager = false;
  $showLazyLoad = false;

  $countStartLoadImg = 4;


  if ($arParams['PAGE_ELEMENT_COUNT'] > 0 && $navParams['NavPageCount'] > 1) {
    $showTopPager = $arParams['DISPLAY_TOP_PAGER'];
    $showBottomPager = $arParams['DISPLAY_BOTTOM_PAGER'];
    $showLazyLoad = $arParams['LAZY_LOAD'] === 'Y' && $navParams['NavPageNomer'] != $navParams['NavPageCount'];
  }

  $templateLibrary = array('popup', 'ajax', 'fx');
  $currencyList = '';

  if (!empty($arResult['CURRENCIES'])) {
    $templateLibrary[] = 'currency';
    $currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
  }

  $templateData = array(
    'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
    'TEMPLATE_LIBRARY' => $templateLibrary,
    'CURRENCIES' => $currencyList
  );
  unset($currencyList, $templateLibrary);

  $elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
  $elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
  $elementDeleteParams = array('CONFIRM' => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));

  $positionClassMap = array(
    'left' => 'product-item-label-left',
    'center' => 'product-item-label-center',
    'right' => 'product-item-label-right',
    'bottom' => 'product-item-label-bottom',
    'middle' => 'product-item-label-middle',
    'top' => 'product-item-label-top'
  );

  $discountPositionClass = '';
  if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION'])) {
    foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos) {
      $discountPositionClass .= isset($positionClassMap[$pos]) ? ' ' . $positionClassMap[$pos] : '';
    }
  }

  $labelPositionClass = '';
  if (!empty($arParams['LABEL_PROP_POSITION'])) {
    foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos) {
      $labelPositionClass .= isset($positionClassMap[$pos]) ? ' ' . $positionClassMap[$pos] : '';
    }
  }

  $arParams['~MESS_BTN_BUY'] = $arParams['~MESS_BTN_BUY'] ?: Loc::getMessage('CT_BCS_TPL_MESS_BTN_BUY');
  $arParams['~MESS_BTN_DETAIL'] = $arParams['~MESS_BTN_DETAIL'] ?: Loc::getMessage('CT_BCS_TPL_MESS_BTN_DETAIL');
  $arParams['~MESS_BTN_COMPARE'] = $arParams['~MESS_BTN_COMPARE'] ?: Loc::getMessage('CT_BCS_TPL_MESS_BTN_COMPARE');
  $arParams['~MESS_BTN_SUBSCRIBE'] = $arParams['~MESS_BTN_SUBSCRIBE'] ?: Loc::getMessage('CT_BCS_TPL_MESS_BTN_SUBSCRIBE');
  $arParams['~MESS_BTN_ADD_TO_BASKET'] = $arParams['~MESS_BTN_ADD_TO_BASKET'] ?: Loc::getMessage('CT_BCS_TPL_MESS_BTN_ADD_TO_BASKET');
  $arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE'] ?: Loc::getMessage('CT_BCS_TPL_MESS_PRODUCT_NOT_AVAILABLE');
  $arParams['~MESS_SHOW_MAX_QUANTITY'] = $arParams['~MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_BCS_CATALOG_SHOW_MAX_QUANTITY');
  $arParams['~MESS_RELATIVE_QUANTITY_MANY'] = $arParams['~MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_BCS_CATALOG_RELATIVE_QUANTITY_MANY');
  $arParams['~MESS_RELATIVE_QUANTITY_FEW'] = $arParams['~MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_BCS_CATALOG_RELATIVE_QUANTITY_FEW');

  $arParams['MESS_BTN_LAZY_LOAD'] = $arParams['MESS_BTN_LAZY_LOAD'] ?: Loc::getMessage('CT_BCS_CATALOG_MESS_BTN_LAZY_LOAD');

  $generalParams = array(
    'IS_MOBILE' => $arParams['IS_MOBILE'],
    'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
    'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
    'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
    'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
    'MESS_SHOW_MAX_QUANTITY' => $arParams['~MESS_SHOW_MAX_QUANTITY'],
    'MESS_RELATIVE_QUANTITY_MANY' => $arParams['~MESS_RELATIVE_QUANTITY_MANY'],
    'MESS_RELATIVE_QUANTITY_FEW' => $arParams['~MESS_RELATIVE_QUANTITY_FEW'],
    'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
    'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
    'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
    'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
    'ADD_PROPERTIES_TO_BASKET' => $arParams['ADD_PROPERTIES_TO_BASKET'],
    'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
    'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'],
    'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
    'COMPARE_PATH' => $arParams['COMPARE_PATH'],
    'COMPARE_NAME' => $arParams['COMPARE_NAME'],
    'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
    'PRODUCT_BLOCKS_ORDER' => $arParams['PRODUCT_BLOCKS_ORDER'],
    'LABEL_POSITION_CLASS' => $labelPositionClass,
    'DISCOUNT_POSITION_CLASS' => $discountPositionClass,
    'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
    'SLIDER_PROGRESS' => $arParams['SLIDER_PROGRESS'],
    '~BASKET_URL' => $arParams['~BASKET_URL'],
    '~ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
    '~BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE'],
    '~COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
    '~COMPARE_DELETE_URL_TEMPLATE' => $arResult['~COMPARE_DELETE_URL_TEMPLATE'],
    'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
    'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
    'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
    'BRAND_PROPERTY' => $arParams['BRAND_PROPERTY'],
    'MESS_BTN_BUY' => $arParams['~MESS_BTN_BUY'],
    'MESS_BTN_DETAIL' => $arParams['~MESS_BTN_DETAIL'],
    'MESS_BTN_COMPARE' => $arParams['~MESS_BTN_COMPARE'],
    'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
    'MESS_BTN_ADD_TO_BASKET' => $arParams['~MESS_BTN_ADD_TO_BASKET'],
    'MESS_NOT_AVAILABLE' => $arParams['~MESS_NOT_AVAILABLE']
  );

	$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($navParams['NavNum']));
	$containerName = 'container-'.$navParams['NavNum'];

  if ($_REQUEST['ajax'] == 'Y') {
    $APPLICATION->RestartBuffer();
  }
  global $USER;

?>
	<h2 class="maylike-title skeleton"><?=Loc::getMessage('NEWS_LIST_PROFUCT_MORE_CAPTION_2')?></h2>
  <div class="product-item-container maylike-slider-init active" data-template="news.list:concomitant">
    <?php
      if (!empty($arResult['CURRENT_ITEMS']) && !empty($arResult['ITEM_ROWS'])) {
        $areaIds = array();
        foreach ($arResult['CURRENT_ITEMS'] as $item) {
          $uniqueId = $item['ID'] . '_' . md5($this->randString() . $component->getAction());
          $areaIds[$item['ID']] = $this->GetEditAreaId($uniqueId);
          $this->AddEditAction($uniqueId, $item['EDIT_LINK'], $elementEdit);
          $this->AddDeleteAction($uniqueId, $item['DELETE_LINK'], $elementDelete, $elementDeleteParams);
        }
        ?>
        <!-- items-container -->
        <?
        $count = 0;
        $allItems = $arResult['CURRENT_ITEMS'];
        //var_dump();
        //$listChunk = array_chunk($arResult['ITEMS'], $arParams['CUSTOM_PAGE_ELEMENT_COUNT']);
        $listChunk = array_slice($allItems, $arResult['CURRENT_PAGE'] === 1 ? 0 : ($arResult['CURRENT_PAGE'] - 1) * $arParams['CUSTOM_PAGE_ELEMENT_COUNT'], $arParams['CUSTOM_PAGE_ELEMENT_COUNT']);

        foreach ($listChunk as $item) {
          // ВЫТАСКИВАЕМ КОМПЛЕКТ ДЛЯ item
          // TODO - ещё надо проверять активность Цвета и активность Товара
          // У нас есть ID ТП и ID Товара

          $skuID = $item['ID'];
          $productID = $item['LINK_ELEMENT_ID'];

          // Мы можем по ID Товара в качестве Родителя получить из ИБ Фотографий элемент, у которого будет свойство KIT, указывающее на другие элементы ИБ Фотографий
          $photoElemID = 0;
          $arKitElements = [];
          $objPhotoElements = CIBlockElement::GetList([], ['IBLOCK_ID' => COLORS_ID, 'PROPERTY_PARENT' => $productID], false, false, ['ID', 'IBLOCK_ID', 'PROPERTY_COLOR', 'PROPERTY_KIT']);
          while ($tmp = $objPhotoElements->GetNext()) {
            // Здесь для каждого ID ИБ Фотографий, полученных из KIT, находим эти элементы - их первое фото и ID Родителя-товара
            // в соответствии с цветом и если что-то задано в KIT...
            if ($tmp['PROPERTY_COLOR_VALUE'] == $item['PROPERTIES']['COLOR']['VALUE_ENUM_ID'] && $tmp['PROPERTY_KIT_VALUE'] > 0) {
              $objKitElements = CIBlockElement::GetList([], ['IBLOCK_ID' => COLORS_ID, 'ID' => $tmp['PROPERTY_KIT_VALUE']], false, false, ['ID', 'IBLOCK_ID', 'ACTIVE', 'PROPERTY_PARENT', 'PROPERTY_IMAGES', 'PROPERTY_COLOR']);
              $tmpKit = $objKitElements->GetNext();

              // Если активен получаемый элемент ИБ Фотографий
              if ($tmpKit['ACTIVE'] == 'Y') {
                $arKit = [];
                $arKit['PARENT_PRODUCT_ID'] = $tmpKit['PROPERTY_PARENT_VALUE'];
                $arKit['PHOTO'] = $tmpKit['PROPERTY_IMAGES_VALUE'];
                $arKit['COLOR'] = $tmpKit['PROPERTY_COLOR_VALUE'];

                // Получим название цвета
                $resData = $strEntityDataClass::getList(['select' => ['ID', 'UF_NAME'], 'filter' => ['UF_XML_ID' => $tmpKit['PROPERTY_COLOR_VALUE']]]);
                $arItem = $resData->Fetch();
                $arKit['COLOR_NAME'] = $arItem['UF_NAME'];

                // Путь к файлу фото
                $arKit['PHOTO_LINK'] = CFile::GetPath($tmpKit['PROPERTY_IMAGES_VALUE']);

                // Ссылка на товар
                $kitProd = CCatalogProduct::GetByIDEx($tmpKit['PROPERTY_PARENT_VALUE']);
                $arKit['PARENT_PRODUCT_LINK'] = $kitProd['DETAIL_PAGE_URL'];
                $arKit['PARENT_PRODUCT_NAME'] = $kitProd['NAME'];

                // Если активен Товар
                if ($kitProd['ACTIVE'] == 'Y')
                  $arKitElements[] = $arKit;
              }
            }
          }

          $itemIndex++;

          $generalParams['START_HIDE_SRC'] = ($itemIndex > $countStartLoadImg) ? 'Y' : '';

          if ($count === 0) {
            $classItem = '';
            $count++;
          } else {
            $classItem = 'second';
            $count = 0;
          }

          if (count($arKitElements) > 0)
            $classItem .= ' exist-kit';
          ?>

          <?php
          $APPLICATION->IncludeComponent(
            'bitrix:catalog.item',
            'concomitant',
            [
              'RESULT' => [
                'ITEM' => $item,
                'AREA_ID' => $areaIds[$item['ID']],
                'KIT_ELEMENTS' => $arKitElements,    // Отправляем КОМПЛЕКТ в карточку товара
                'BIG_LABEL' => 'N',
                'TYPE' => 'CARD',
                'BIG_DISCOUNT_PERCENT' => 'N',
                'BIG_BUTTONS' => 'Y',
                'SCALABLE' => 'N',
              ],
              'PARAMS' => $generalParams
                + ['SKU_PROPS' => $arResult['SKU_PROPS'][$item['IBLOCK_ID']]]
            ],
            $component,
            array('HIDE_ICONS' => 'Y')
          );
          ?>
          <?php
        }
        unset($generalParams, $rowItems);
        ?>
        <!-- items-container -->
        <?php
      }
    ?>
  </div>

<?php
  if ($showLazyLoad) {
    ?>
    <div class="row bx-<?= $arParams['TEMPLATE_THEME'] ?>">
      <div class="btn btn-default btn-lg center-block" style="margin: 15px;"
           data-use="show-more-<?= $navParams['NavNum'] ?>">
        <?= $arParams['MESS_BTN_LAZY_LOAD'] ?>
      </div>
    </div>
    <?php
  }

  $signer = new \Bitrix\Main\Security\Sign\Signer;
  $signedTemplate = $signer->sign($templateName, 'catalog.section');
  $signedParams = $signer->sign(base64_encode(serialize($arResult['ORIGINAL_PARAMETERS'])), 'catalog.section');

  $frame = $this->createFrame()->begin(''); ?>
	<script>
	  BX.message({
	    BTN_MESSAGE_BASKET_REDIRECT: '<?=GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_BASKET_REDIRECT')?>',
	    BASKET_URL: '<?=$arParams['BASKET_URL']?>',
	    ADD_TO_BASKET_OK: '<?=GetMessageJS('ADD_TO_BASKET_OK')?>',
	    TITLE_ERROR: '<?=GetMessageJS('CT_BCS_CATALOG_TITLE_ERROR')?>',
	    TITLE_BASKET_PROPS: '<?=GetMessageJS('CT_BCS_CATALOG_TITLE_BASKET_PROPS')?>',
	    TITLE_SUCCESSFUL: '<?=GetMessageJS('ADD_TO_BASKET_OK')?>',
	    BASKET_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCS_CATALOG_BASKET_UNKNOWN_ERROR')?>',
	    BTN_MESSAGE_SEND_PROPS: '<?=GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_SEND_PROPS')?>',
	    BTN_MESSAGE_CLOSE: '<?=GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_CLOSE')?>',
	    BTN_MESSAGE_CLOSE_POPUP: '<?=GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_CLOSE_POPUP')?>',
	    COMPARE_MESSAGE_OK: '<?=GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_OK')?>',
	    COMPARE_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_UNKNOWN_ERROR')?>',
	    COMPARE_TITLE: '<?=GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_TITLE')?>',
	    PRICE_TOTAL_PREFIX: '<?=GetMessageJS('CT_BCS_CATALOG_PRICE_TOTAL_PREFIX')?>',
	    RELATIVE_QUANTITY_MANY: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY'])?>',
	    RELATIVE_QUANTITY_FEW: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW'])?>',
	    BTN_MESSAGE_COMPARE_REDIRECT: '<?=GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT')?>',
	    BTN_MESSAGE_LAZY_LOAD: '<?=CUtil::JSEscape($arParams['MESS_BTN_LAZY_LOAD'])?>',
	    BTN_MESSAGE_LAZY_LOAD_WAITER: '<?=GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_LAZY_LOAD_WAITER')?>',
	    SITE_ID: '<?=CUtil::JSEscape($component->getSiteId())?>'
	  });

	  // var <?=$obName?> = new JCCatalogSectionComponent({
	  //   siteId: '<?=CUtil::JSEscape($component->getSiteId())?>',
	  //   componentPath: '<?=CUtil::JSEscape($componentPath)?>',
	  //   navParams: <?=CUtil::PhpToJSObject($navParams)?>,
	  //   deferredLoad: false, // enable it for deferred load
	  //   initiallyShowHeader: '<?=!empty($arResult['ITEM_ROWS'])?>',
	  //   bigData: <?=CUtil::PhpToJSObject($arResult['BIG_DATA'])?>,
	  //   lazyLoad: !!'<?=$showLazyLoad?>',
	  //   loadOnScroll: !!'<?=($arParams['LOAD_ON_SCROLL'] === 'Y')?>',
	  //   template: '<?=CUtil::JSEscape($signedTemplate)?>',
	  //   ajaxId: '<?=CUtil::JSEscape($arParams['AJAX_ID'])?>',
	  //   parameters: '<?=CUtil::JSEscape($signedParams)?>',
	  //   container: '<?=$containerName?>'
	  // });
	</script>