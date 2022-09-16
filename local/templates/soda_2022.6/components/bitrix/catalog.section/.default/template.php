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


//if (!empty($arResult['NAV_RESULT']))
//{
//	$navParams =  array(
//		'NavPageCount' => $arResult['NAV_RESULT']->NavPageCount,
//		'NavPageNomer' => $arResult['NAV_RESULT']->NavPageNomer,
//		'NavNum' => $arResult['NAV_RESULT']->NavNum
//	);
//}
//else
//{
//	$navParams = array(
//		'NavPageCount' => 1,
//		'NavPageNomer' => 1,
//		'NavNum' => $this->randString()
//	);
//}

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
$viewMode = 'type-2';
//if(isset($_COOKIE['viewMode'])){
//  switch ($_COOKIE['viewMode']){
//    case 1:
//       $viewMode = 'type-1';
//      break;
//     case 2:
//      $viewMode = 'type-2';
//      break;
//    case 3:
//      $viewMode = 'type-3';
//      break;
//  }
//}

if ($_REQUEST['ajax'] == 'Y') {
  $APPLICATION->RestartBuffer();
}
global $USER;

?>


  <div class="public-message">
    Сроки обработки заказов в период распродажи увеличены. Приносим свои извинения
  </div>


  <div class="wrapper-back-mobile<?=!empty($_GET['q']) ? ' active': '';?>"">
  <div class="products ajax_load_items <?=$viewMode?>" data-pages="<?= $arResult['PAGES']; ?>"
       data-page="<?= $arResult['CURRENT_PAGE']; ?>" data-ajax-url="<?= $APPLICATION->GetCurPageParam('', ['page', 'ajax']); ?>">
    <?php
    if (!empty($_GET['q'])) {
      $obSearch = new CSearch;
      if(is_array($arParams['SEARCH_ELEMENTS']) && count($arParams['SEARCH_ELEMENTS'])){
        $obSearch->Search([
            'QUERY' => $_GET['q'],
            'MODULE_ID' => 'main',
            'ITEM_ID' => array_values($arParams['SEARCH_ELEMENTS']),
        ]);
      }
      else{
        $obSearch->Search([
            'QUERY' => $_GET['q'],
            'MODULE_ID' => 'main',
        ]);
      }

      if ($obSearch->errorno != 0) {
        ?>
        <div class="wrapper">
          <div class="products-empty"><?= Loc::getMessage('CATALOG_SECTION_NOT_FOUND'); ?></div>
        </div>
        <?php
      }
    }

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

        if ($item['PROPERTIES']['FULL']['VALUE_XML_ID'] == 'Y') {
          $classItem = 'full';
          $count = 0;
        }

        if (!empty($item['PROPERTIES']['FULL_IMAGE']['VALUE'])) {
          $classItem = 'full huge';
          $count = 0;
        }

        //        if(count($arResult['ITEMS']) == 1) {
        //          $classItem = 'full';
        //
        //          if(!empty($item['PROPERTIES']['FULL_IMAGE']['VALUE'])) {
        //            $classItem = 'full huge';
        //          }
        //        }

        if (count($arKitElements) > 0)
          $classItem .= ' exist-kit';
        ?>

        <div class="item <?= $classItem ?><?= ($itemIndex > $countStartLoadImg) ? ' item__hide' : '' ?>"
             id="<?= $areaIds[$item['ID']] ?>">
          <?php
          $APPLICATION->IncludeComponent(
              'bitrix:catalog.item',
              'catalog',
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
        </div>
        <?php
      }
      unset($generalParams, $rowItems);
      ?>
      <!-- items-container -->
      <?php
    } elseif ($arParams['FILTER_NAME'] == 'searchFilter' && empty($arParams['SEARCH_ELEMENTS'])) {
      ?>
      <div class="wrapper">
        <div class="products-empty"><?= Loc::getMessage('CATALOG_SECTION_NOT_FOUND') ?></div>
      </div>
      <?
    } elseif (empty($arParams['SEARCH_ELEMENTS'])) {
      ?>
      <div class="wrapper">
        <div class="products-empty"><?= Loc::getMessage('CATALOG_SECTION_EMPTY') ?></div>
      </div>
      <?
    }
    ?>
  </div>
</div>

<?php
if ($_REQUEST['ajax'] == 'Y') {
  die();
}
?>

  <div class="lazyload-items-spinner">
    <div class="preloader loading">
      <div class="preloader__loader">
        <svg viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle class="preloader__base" cx="25" cy="25" r="20"></circle>
          <circle class="preloader__indicator" cx="25" cy="25" r="20"></circle>
        </svg>
      </div>
    </div>
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
    var <?=$obName?> = new JCCatalogSectionComponent({
      siteId: '<?=CUtil::JSEscape($component->getSiteId())?>',
      componentPath: '<?=CUtil::JSEscape($componentPath)?>',
      navParams: <?=CUtil::PhpToJSObject($navParams)?>,
      deferredLoad: false, // enable it for deferred load
      initiallyShowHeader: '<?=!empty($arResult['ITEM_ROWS'])?>',
      bigData: <?=CUtil::PhpToJSObject($arResult['BIG_DATA'])?>,
      lazyLoad: !!'<?=$showLazyLoad?>',
      loadOnScroll: !!'<?=($arParams['LOAD_ON_SCROLL'] === 'Y')?>',
      template: '<?=CUtil::JSEscape($signedTemplate)?>',
      ajaxId: '<?=CUtil::JSEscape($arParams['AJAX_ID'])?>',
      parameters: '<?=CUtil::JSEscape($signedParams)?>',
      container: '<?=$containerName?>'
    });
  </script>
<?php
  $frame->end();
  if(!isset($_REQUEST['q'])){
    ?>
    <script>
        var arItems = {};
        var lastScrollTop = 0;
        var lastItemsContainer = 0;
        var offsetWrap = 0;
        var items = 0;
        var allPage = $('.ajax_load_items').attr('data-pages');
        //var url = new URLSearchParams($('.ajax_load_items').attr('data-ajax-url'));
        var url = $('.ajax_load_items').attr('data-ajax-url');
        window.load_ajax = false;
        var params = {
            allPage: parseInt($('.ajax_load_items').attr('data-pages')),
            currentPage: parseInt($('.ajax_load_items').attr('data-page')) + 1,
            startPage: parseInt($('.ajax_load_items').attr('data-page')) + 1,
            lastItemsContainer: 0,
            lastScrollTop: 0,
        }

        var sliderParameters = {
          // lazyLoad: 'ondemand',
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: false,
          draggable: true,
          touchMove: true,
          swipe: true,
          easing: 'ease',
          touchThreshold: 20,
          dots: false,
          arrows: false,
          responsive: [
            {
              breakpoint: 1024,
              settings: {
                dots: true,
                infinite: true,
              },
            },
          ],
        }

        function insertParam(url, key, value) {
          var queryString = url.split('?');
          if(queryString.length > 1){
            return url+'&'+key+'='+value;
          }
          else{
            return url+'?'+key+'='+value;
          }
        }

        function loadItems(session = false){
            var targetContainer = $('.ajax_load_items');
            window.load_ajax = true;
            if(session){
                let lastPageSession = parseInt(sessionStorage.getItem('lastPageItems'));
                if(params.currentPage < lastPageSession){
                  for (; params.currentPage <= lastPageSession; params.currentPage++) {
                    $('.lazyload-items-spinner').show();
                    $.ajax({
                      type: 'GET',
                      url: insertParam(url, 'page', params.currentPage),
                      dataType: 'html',
                      async : false,
                      success: function (data) {
                        $('.lazyload-items-spinner').hide();

                        var elements = $(data).find('.item');

                        targetContainer.attr('data-page', $(data).find('.ajax_load_items').attr('data-page'));
                        targetContainer.append(elements);
                        params.lastItemsContainer = targetContainer.height();

                        if(params.currentPage === parseInt(sessionStorage.getItem('lastPageItems'))){
                          setTimeout(function () {
                            window.scrollTo(0, sessionStorage.getItem('lastScrollTop'));
                            window.load_ajax = false;
                          }, 500);
                        }

                        $('.images-wrapper-new').not('.slick-initialized').slick(sliderParameters);

                        elements.find('.image-container').each(function () {
                          let imageContainer = $(this);

                          imageContainer.find('img').on("load", function(e) {
                            imageContainer.closest('.item-wrapper').find('.skeleton:not(.image-container)').removeClass('skeleton');
                            imageContainer.removeClass('skeleton');
                          });
                        });
                        loadNextSlide();
                        //sessionStorage.setItem('lastPageItems', $(data).find('.ajax_load_items').attr('data-page'));
                      },
                    })
                  }
                }
                else{
                  window.load_ajax = false;
                }
            }
            else {
                if(params.currentPage === params.startPage){
                    $('.lazyload-items-spinner').show();

                    $.ajax({
                        type: 'GET',
                        url: insertParam(url, 'page', params.startPage),
                        dataType: 'html',
                        success: function (data) {
                            $('.lazyload-items-spinner').hide();

                            var elements = $(data).find('.item');

                            targetContainer.attr('data-page', params.currentPage);
                            targetContainer.append(elements);
                            params.lastItemsContainer = targetContainer.height();
                            params.currentPage++;
                            window.load_ajax = false;

                            $('.images-wrapper-new').not('.slick-initialized').slick(sliderParameters);

                            elements.find('.image-container').each(function () {
                              let imageContainer = $(this);

                              imageContainer.find('img').on("load", function(e) {
                                imageContainer.closest('.item-wrapper').find('.skeleton:not(.image-container)').removeClass('skeleton');
                                imageContainer.removeClass('skeleton');
                              });
                            });
                            loadNextSlide();
		                        window.toKitFavs.reinit(window.paramsFans);
                            //sessionStorage.setItem('lastPageItems', $(data).find('.ajax_load_items').attr('data-page'));
                        },
                    })
                }
                else{
                    $('.lazyload-items-spinner').show();

                    $.ajax({
                        type: 'GET',
                        url: insertParam(url, 'page', params.currentPage),
                        dataType: 'html',
                        success: function (data) {
                            $('.lazyload-items-spinner').hide();

                            var elements = $(data).find('.item');

                            targetContainer.attr('data-page', params.currentPage);
                            targetContainer.append(elements);
                            params.lastItemsContainer = targetContainer.height();

                            params.currentPage++;
                            window.load_ajax = false;

                            $('.images-wrapper-new').not('.slick-initialized').slick(sliderParameters);

                            elements.find('.image-container').each(function () {
                              let imageContainer = $(this);

                              imageContainer.find('img').on("load", function(e) {
                                imageContainer.closest('.item-wrapper').find('.skeleton:not(.image-container)').removeClass('skeleton');
                                imageContainer.removeClass('skeleton');
                              });
                            });
                            loadNextSlide();
	                          window.toKitFavs.reinit(window.paramsFans);
                            //sessionStorage.setItem('lastPageItems', $(data).find('.ajax_load_items').attr('data-page'));
                        },
                    })
                }
            }
        }

        $(document).ready(function () {
	        console.log(window.paramsFans);
          localStorage.setItem('referrerCatalog', location.pathname);
          window.scrollTo(0, 0);

          let lastPageSession = parseInt(sessionStorage.getItem('lastPageItems'));

          if($('.ajax_load_items').hasClass('type-3')){
              let timerId = setTimeout(function tick() {
                  offsetWrap = $('.blur-wrap').height() + $('.blur-wrap').offset().top;
                  let startLoad = offsetWrap - ($('.item-wrapper').height() * 2);
                  console.log('startLoad = '+startLoad +' ___ .products .item heightOuter() = '+$('.products .item').outerHeight(true) +' ___ scrollTop = '+ ($(window).scrollTop() + $(window).height()) );
                  if ($(window).scrollTop() + $(window).height() >= startLoad && window.load_ajax !== true){
                      loadItems();
                      if(params.allPage >= params.currentPage){
                          timerId = setTimeout(tick, 1000); // (*)
                      }
                  }
              }, 1000);
              //do {
              //loadItems();
              //}
              //while ($(window).scrollTop() + $(window).height() >= startLoad)
          }
          if(lastPageSession){
              if(lastPageSession !== params.currentPage || lastPageSession > params.currentPage){
                  loadItems(true);
              }
          }

          $(document).on('click', 'a', function (e) {
              if (!$(this).hasClass('.item')) {
                  sessionStorage.removeItem('lastPageItems');
                  sessionStorage.removeItem('lastScrollTop');
              }
          });

          $(document).on('click', '.ajax_load_items .item', function (e) {
              sessionStorage.setItem('lastPageItems', $('.ajax_load_items').attr('data-page'));
              sessionStorage.setItem('lastScrollTop', $(window).scrollTop());
          });

          $(document).on('change', '.ajax_load_items', function (){
            loadItems();
          });

          $('.ajax_load_items').find('.image-container').each(function () {
            let imageContainer = $(this);

            if (imageContainer.hasClass('slick-active') || imageContainer.hasClass('video-wrapper')) {
                imageContainer.closest('.item-wrapper').find('.skeleton:not(.image-container)').removeClass('skeleton');
                imageContainer.removeClass('skeleton');
            }


            imageContainer.find('img').on("load", function(e) {
              imageContainer.closest('.item-wrapper').find('.skeleton:not(.image-container)').removeClass('skeleton');
              imageContainer.removeClass('skeleton');
            });
          });
        });

        $(window).scroll(function() {
          var st = $(this).scrollTop();
          let allPage = params.allPage + 1;
          offsetWrap = $('.blur-wrap').height() + $('.blur-wrap').offset().top;
          let startLoad;
          if (window.matchMedia('(max-width: 768px)').matches) {
            startLoad = offsetWrap - ($('.item-wrapper').height() * 4);
          } else {
            startLoad = offsetWrap - ($('.item-wrapper').height() * 2);
          }

          if ($(window).scrollTop() + $(window).height() >= startLoad && window.load_ajax !== true) {

            if (st >= lastScrollTop && items < $('.ajax_load_items').find('.item').length) {
              if(allPage > params.currentPage){
                loadItems();
              }
            }
          }

          lastScrollTop = st;
        });

        function loadNextSlide() {
          let nextSlide = $('.image-container__next:not(.slick-cloned)'),
              srcSlide = nextSlide.find('img[data-swipe]'),
              srcsetSlide = nextSlide.find('source[data-mobile-swipe]');

          if (srcSlide.length > 0) {
            srcSlide.each(function () {
              $(this).attr('src', $(this).attr('data-swipe'));
              $(this).removeAttr('data-swipe');
            });
          }

          if (srcsetSlide.length > 0) {
            srcsetSlide.each(function () {
              $(this).attr('srcset', $(this).attr('data-mobile-swipe'));
              $(this).removeAttr('data-mobile-swipe');
            });
          }

          nextSlide.removeClass('skeleton');
        }

        $(window).on('load', function () {
          loadNextSlide();
        });

        $('.images-wrapper-new').slick(sliderParameters);

        $(document).on('mouseenter', '.images-mouse-tab', function () {
          let currentSlider = $(this).closest('.images-slider').find('.images-wrapper-new');

          $(this).closest('.images-slider').find('[data-index-line='+ $(this).data('indexTab') +']').addClass('active');

          currentSlider.slick('slickGoTo', $(this).data('indexTab'), true);

          $(document).on('afterChange', '.images-wrapper-new', function(event, slick, direction){
            let srcSlide = $(this).find('img[data-swipe]'),
                srcsetSlide = $(this).find('source[data-mobile-swipe]');

            if (srcSlide.length > 0) {
              srcSlide.each(function () {
                $(this).attr('src', $(this).attr('data-swipe'));
                $(this).removeAttr('data-swipe');
              });
            }

            if (srcsetSlide.length > 0) {
              srcsetSlide.each(function () {
                $(this).attr('srcset', $(this).attr('data-mobile-swipe'));
                $(this).removeAttr('data-mobile-swipe');
              });
            }
          });

        });

        $(document).on('mouseleave', '.images-mouse-tab', function () {
          $(this).closest('.images-slider').find('[data-index-line='+ $(this).data('indexTab') +']').removeClass('active');
        });

    </script>
    <?
    } else {?>
    <script>
      function loadNextSlide() {
        let nextSlide = $('.image-container__next:not(.slick-cloned)'),
            srcSlide = nextSlide.find('img[data-swipe]'),
            srcsetSlide = nextSlide.find('source[data-mobile-swipe]');

        if (srcSlide.length > 0) {
          srcSlide.each(function () {
            $(this).attr('src', $(this).attr('data-swipe'));
            $(this).removeAttr('data-swipe');
          });
        }

        if (srcsetSlide.length > 0) {
          srcsetSlide.each(function () {
            $(this).attr('srcset', $(this).attr('data-mobile-swipe'));
            $(this).removeAttr('data-mobile-swipe');
          });
        }

        nextSlide.removeClass('skeleton');
      }

      let sliderParameters = {
        // lazyLoad: 'ondemand',
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: false,
        draggable: true,
        touchMove: true,
        swipe: true,
        easing: 'ease',
        touchThreshold: 20,
        dots: false,
        arrows: false,
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              dots: true,
              infinite: true,
            },
          },
        ],
      }
      $('.images-wrapper-new').slick(sliderParameters);

      $(document).ready(function () {
        $('.ajax_load_items').find('.image-container').each(function () {
          let imageContainer = $(this);

          if (imageContainer.hasClass('slick-active') || imageContainer.hasClass('video-wrapper')) {
            imageContainer.closest('.item-wrapper').find('.skeleton:not(.image-container)').removeClass('skeleton');
            imageContainer.removeClass('skeleton');
          }

          imageContainer.find('img').on("load", function(e) {
            imageContainer.closest('.item-wrapper').find('.skeleton:not(.image-container)').removeClass('skeleton');
            imageContainer.removeClass('skeleton');
          });
        });
      });

      $(window).on('load', function () {
        loadNextSlide();
      });

      $(document).on('mouseenter', '.images-mouse-tab', function () {
        let currentSlider = $(this).closest('.images-slider').find('.images-wrapper-new');

        $(this).closest('.images-slider').find('[data-index-line='+ $(this).data('indexTab') +']').addClass('active');

        currentSlider.slick('slickGoTo', $(this).data('indexTab'), true);

        $(document).on('afterChange', '.images-wrapper-new', function(event, slick, direction){
          let srcSlide = $(this).find('img[data-swipe]'),
              srcsetSlide = $(this).find('source[data-mobile-swipe]');

          if (srcSlide.length > 0) {
            srcSlide.each(function () {
              $(this).attr('src', $(this).attr('data-swipe'));
              $(this).removeAttr('data-swipe');
            });
          }

          if (srcsetSlide.length > 0) {
            srcsetSlide.each(function () {
              $(this).attr('srcset', $(this).attr('data-mobile-swipe'));
              $(this).removeAttr('data-mobile-swipe');
            });
          }
        });

      });

      $(document).on('mouseleave', '.images-mouse-tab', function () {
        $(this).closest('.images-slider').find('[data-index-line='+ $(this).data('indexTab') +']').removeClass('active');
      });
    </script>
  <?}
?>