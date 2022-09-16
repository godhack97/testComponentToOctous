<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Conversion\Internals\MobileDetect;

$this->setFrameMode(true);
ob_start();
$detect = new MobileDetect;

$templateLibrary = array('popup', 'fx');
$currencyList = '';

$isRuleModerator = false;
if(CSite::InGroup(array(1, 8, 9, 15, 16))) // || htmlspecialchars($_GET['admin']) == 'Y')
  $isRuleModerator = true;


if (!empty($arResult['CURRENCIES']))
{
  $templateLibrary[] = 'currency';
  $currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}

$templateData = array(
    'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
    'TEMPLATE_LIBRARY' => $templateLibrary,
    'CURRENCIES' => $currencyList,
    'ITEM' => array(
        'ID' => $arResult['ID'],
        'IBLOCK_ID' => $arResult['IBLOCK_ID'],
        'OFFERS_SELECTED' => $arResult['OFFERS_SELECTED'],
        'JS_OFFERS' => $arResult['JS_OFFERS']
    )
);
unset($currencyList, $templateLibrary);

$mainId = $this->GetEditAreaId($arResult['ID']);
$itemIds = array(
    'ID' => $mainId,
    'DISCOUNT_PERCENT_ID' => $mainId.'_dsc_pict',
    'STICKER_ID' => $mainId.'_sticker',
    'BIG_SLIDER_ID' => $mainId.'_big_slider',
    'BIG_IMG_CONT_ID' => $mainId.'_bigimg_cont',
    'SLIDER_CONT_ID' => $mainId.'_slider_cont',
    'OLD_PRICE_ID' => $mainId.'_old_price',
    'PRICE_ID' => $mainId.'_price',
    'DISCOUNT_PRICE_ID' => $mainId.'_price_discount',
    'PRICE_TOTAL' => $mainId.'_price_total',
    'SLIDER_CONT_OF_ID' => $mainId.'_slider_cont_',
    'QUANTITY_ID' => $mainId.'_quantity',
    'QUANTITY_DOWN_ID' => $mainId.'_quant_down',
    'QUANTITY_UP_ID' => $mainId.'_quant_up',
    'QUANTITY_MEASURE' => $mainId.'_quant_measure',
    'QUANTITY_LIMIT' => $mainId.'_quant_limit',
    'BUY_LINK' => $mainId.'_buy_link',
    'ADD_BASKET_LINK' => $mainId.'_add_basket_link',
    'BASKET_ACTIONS_ID' => $mainId.'_basket_actions',
    'NOT_AVAILABLE_MESS' => $mainId.'_not_avail',
    'COMPARE_LINK' => $mainId.'_compare_link',
    'TREE_ID' => $mainId.'_skudiv',
    'DISPLAY_PROP_DIV' => $mainId.'_sku_prop',
    'DISPLAY_MAIN_PROP_DIV' => $mainId.'_main_sku_prop',
    'OFFER_GROUP' => $mainId.'_set_group_',
    'BASKET_PROP_DIV' => $mainId.'_basket_prop',
    'SUBSCRIBE_LINK' => $mainId.'_subscribe',
    'TABS_ID' => $mainId.'_tabs',
    'TAB_CONTAINERS_ID' => $mainId.'_tab_containers',
    'SMALL_CARD_PANEL_ID' => $mainId.'_small_card_panel',
    'TABS_PANEL_ID' => $mainId.'_tabs_panel'
);
$obName = $templateData['JS_OBJ'] = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);
$name = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
    : $arResult['NAME'];
$title = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
    : $arResult['NAME'];
$alt = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
    : $arResult['NAME'];


$haveOffers = !empty($arResult['OFFERS']);
if ($haveOffers)
{
  $actualItem = isset($arResult['OFFERS'][$arResult['OFFERS_SELECTED']])
      ? $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]
      : reset($arResult['OFFERS']);
  $showSliderControls = false;

  foreach ($arResult['OFFERS'] as $offer)
  {
    if ($offer['MORE_PHOTO_COUNT'] > 1)
    {
      $showSliderControls = true;
      break;
    }
  }
}
else
{
  $actualItem = $arResult;
  $showSliderControls = $arResult['MORE_PHOTO_COUNT'] > 1;
}


$skuProps = array();
$price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
$measureRatio = $actualItem['ITEM_MEASURE_RATIOS'][$actualItem['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
$showDiscount = $price['PERCENT'] > 0;

$showDescription = !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
$showBuyBtn = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']);
$buyButtonClassName = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showAddBtn = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']);
$showButtonClassName = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && ($arResult['CATALOG_SUBSCRIBE'] === 'Y' || $haveOffers);

$arParams['MESS_BTN_BUY'] = $arParams['MESS_BTN_BUY'] ?: Loc::getMessage('CT_BCE_CATALOG_BUY');
$arParams['MESS_BTN_ADD_TO_BASKET'] = $arParams['MESS_BTN_ADD_TO_BASKET'] ?: Loc::getMessage('CT_BCE_CATALOG_ADD');
$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE'] ?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE');
$arParams['MESS_BTN_COMPARE'] = $arParams['MESS_BTN_COMPARE'] ?: Loc::getMessage('CT_BCE_CATALOG_COMPARE');
$arParams['MESS_PRICE_RANGES_TITLE'] = $arParams['MESS_PRICE_RANGES_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_PRICE_RANGES_TITLE');
$arParams['MESS_DESCRIPTION_TAB'] = $arParams['MESS_DESCRIPTION_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_DESCRIPTION_TAB');
$arParams['MESS_PROPERTIES_TAB'] = $arParams['MESS_PROPERTIES_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_PROPERTIES_TAB');
$arParams['MESS_COMMENTS_TAB'] = $arParams['MESS_COMMENTS_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_COMMENTS_TAB');
$arParams['MESS_SHOW_MAX_QUANTITY'] = $arParams['MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_BCE_CATALOG_SHOW_MAX_QUANTITY');
$arParams['MESS_RELATIVE_QUANTITY_MANY'] = $arParams['MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['MESS_RELATIVE_QUANTITY_FEW'] = $arParams['MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_FEW');


$SET_COLOR_ID = false;
if(htmlspecialcharsbx($_REQUEST['setColor']))
{
  if($arResult['PHOTO_ID'][$_REQUEST['setColor']]) {
    $SET_COLOR_ID = $arResult['PHOTO_ID'][$_REQUEST['setColor']];
  }
  elseif($arResult['PHOTO_OFFER_ID'][$_REQUEST['setColor']]) {
    $SET_COLOR_ID = $arResult['PHOTO_OFFER_ID'][$_REQUEST['setColor']];
  }
  else {
    $SET_COLOR_ID = htmlspecialcharsbx($_REQUEST['setColor']);
  }
}


$arSizeCrop = Array('width' => 980, 'height' => 1470);
$arSizeCropTablet = Array('width' => 768, 'height' => 1152);
$arSizeCropMobile = Array('width' => 620, 'height' => 930); //Array('width' => 414, 'height' => 621);
$arSizeCropPreload = Array('width' => 192, 'height' => 288);

if($_REQUEST["new"]=="y") {
  //echo "<pre>";print_r($arResult['OFFERS'][0]['PROPERTIES']['COLOR']);echo "</pre>";
}
// $arParams['IS_MOBILE'] == 'Y'
//$arResult['IS_AVAILABLE'] = false;
?>
<?if($arParams['AJAX'] != 'Y'):?>

  <script>
    $(function(){
      function fix100vh() {
        var winHeight = $(window).height();
        if ( isMobile() ) {
          $('.100vh-fix').css('height', winHeight);
        }
      }; fix100vh();
    })
  </script>


  <?
  $sectionUrl = $arResult['SECTION']['SECTION_PAGE_URL'];
  if(htmlspecialcharsbx($_GET['offer']))
    $sectionUrl .= '#offer'. htmlspecialcharsbx($_GET['offer']);
  ?>

  <a class="mobile-arrow-back" data-section-url="<?=$sectionUrl?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/arrow-back.svg"></a>
  
  <div data-is-partners="<?=$arParams['IS_PARTNERS']?>" class="wrapper-products products-drag" data-section="<?=$arResult['SECTION']['ID']?>">

<?endif?>

  <div class="product-detail product-<?=$arResult['ID']?><?if($arParams['AJAX'] != 'Y'):?> current<?endif?>" data-id="<?=$arResult['ID']?>" id="<?=$itemIds['ID']?>" itemscope itemtype="http://schema.org/Product">

    <meta itemprop="name" content="<?=$name?>" />
    <meta itemprop="category" content="<?=$arResult['CATEGORY_PATH']?>" />

    <?if($_GET['dbg'] == 'Y'):?>
      <input type="hidden" id="dbg" value="Y">
    <?endif?>

    <div class="images">
      <div class="">
        <div class="product-detail-slider<?if($detect->isMobile() && !empty($arResult['OFFERS_COLOR_LIST'])):?> mobile-colors<?endif?>">

          <?$frame = $this->createFrame()->begin("");?>
          <?if($detect->isMobile() && !empty($arResult['OFFERS_COLOR_LIST'])):?>

            <? // Если моб. устройство и есть разные цвета, то слайдеры с иной обёрткой ?>

            <? $count = 0; ?>
            <?foreach($arResult['OFFERS_COLOR_LIST'] as $keyColor => $arImagesColor):?>
              <div class="image-slider-color color-<?=$keyColor?>" data-color="<?=$keyColor?>" data-dbg="1">
                <?foreach($arImagesColor['IMAGES'] as $index => $imageID):?>
                  <? $arImage = CFile::ResizeImageGet($imageID, $arSizeCrop, BX_RESIZE_IMAGE_EXACT); ?>
                  <? $arImageTablet = CFile::ResizeImageGet($imageID, $arSizeCropTablet, BX_RESIZE_IMAGE_EXACT); ?>
                  <? $arImageMobile = CFile::ResizeImageGet($imageID, $arSizeCropMobile, BX_RESIZE_IMAGE_EXACT); ?>
                  <? $arImagePreload = CFile::ResizeImageGet($imageID, $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT); ?>
                  <div class="image" data-index="<?=$index?>">
                    <a data-fancybox="gallery" href="<?=$arImage['src']?>">
                      <picture>
                        <?if(empty($index)):?>
                          <source srcset="<?=$arImage['src']?>" media="(min-width: 769px)" />
                          <source srcset="<?=$arImageTablet['src']?>" media="(min-width: 415px)" />
                          <img src="<?=$arImageMobile['src']?>" alt="<?=$name?>">
                        <?else:?>
                          <source srcset="<?=$arImagePreload['src']?>" data-srcset-replace-onload-time="<?=$arImage['src']?>?replace" media="(min-width: 769px)" />
                          <source srcset="<?=$arImagePreload['src']?>" data-srcset-replace-onload-time="<?=$arImageTablet['src']?>?replace" media="(min-width: 415px)" />
                          <img src="<?=$arImagePreload['src']?>" data-src-replace-onload-time="<?=$arImage['src']?>?replace" alt="<?=$name?>">
                        <?endif?>
                      </picture>
                    </a>
                  </div>
                <?endforeach?>
                <? $count++; ?>
              </div>
            <?endforeach?>

          <?else:?>

            <? // Иначе всё по адаптивной логике ?>

            <div class="image-slider image-slider-<?=$arResult['ID']?><?if($arParams['AJAX'] == 'Y'):?> active<?endif?>" data-dbg="2">

              <?if(!empty($arResult['OFFERS_COLOR_LIST'])):?>

                <? $count = 0; ?>
                <?foreach($arResult['OFFERS_COLOR_LIST'] as $keyColor => $arImagesColor):?>

                  <?foreach($arImagesColor['IMAGES'] as $index => $imageID):?>
                    <?
                      $arImage = CFile::ResizeImageGet($imageID, $arSizeCrop, BX_RESIZE_IMAGE_EXACT);

                      $arImagePreload = CFile::ResizeImageGet($imageID, $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT);

                      $isDisplay = false;
                      if($SET_COLOR_ID)
                      {
                        if($SET_COLOR_ID == $keyColor)
                          $isDisplay = true;
                      }
                      elseif($count == 0 && !$detect->isMobile())
                        $isDisplay = true;
                    ?>
                    <div class="image color-<?=$keyColor?><?if(!$isDisplay):?> display-none<?endif?>" data-color-name="<?=$arResult['OFFERS_COLOR_LIST'][$keyColor]['NAME']?>">
                      <a data-fancybox="gallery" href="<?=$arImage['src']?>">
                        <?if(empty($index)):?>
                          <img src="<?=$arImage['src']?>" data-dbg="images-1" data-index="<?=$index?>" />
                        <?else:?>
                          <img src="<?=$arImagePreload['src']?>" data-src-replace-scroll="<?=$arImage['src']?>" data-dbg="images-1" data-index="<?=$index?>" />
                        <?endif?>
                      </a>
                    </div>
                  <?endforeach?>

                  <? $count++; ?>
                <?endforeach?>

              <?else:?>

                <?if(!empty($arResult['PROPERTIES']['VIDEO_IMAGE']['VALUE']) && !empty($arResult['PROPERTIES']['VIDEO_FILES']['VALUE'])):?>
                  <div class="image">
                    <?$arVideoImage = CFile::ResizeImageGet($arResult['PROPERTIES']['VIDEO_IMAGE']['VALUE'], $arSizeCrop, BX_RESIZE_IMAGE_EXACT); ?>
                    <video autoplay="autoplay" class="product-media _videoLoaded _videoPlaying" poster="<?=$arVideoImage['src']?>" playsinline="" loop="" muted="">
                      <?foreach($arResult['PROPERTIES']['VIDEO_FILES']['VALUE'] as $videoID):?>
                        <?$arFile = CFile::GetFileArray($videoID);?>
                        <source src="<?=$arFile['SRC']?>" type="<?=$arFile['CONTENT_TYPE']?>">
                      <?endforeach;?>
                      <img class="_imageLoaded _imgImpressions" src="<?=$arVideoImage['src']?>">
                    </video>
                  </div>
                <?endif?>

                <?if(!empty($arResult['DETAIL_PICTURE']['SRC'])):?>
                  <?$arDetailImage = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'], $arSizeCrop, BX_RESIZE_IMAGE_EXACT); ?>
                  <div class="image color-default">
                    <? /* <a data-fancybox="gallery" href="<?=$arDetailImage['src']?>"> */ ?>
                      <img src="<?=$arDetailImage['src']?>" data-dbg="images-3" />
                    <? /* </a> */ ?>
                  </div>
                <?else:?>
                  <div class="image color-default">
                    <img src="<?=SITE_TEMPLATE_PATH?>/images/default.jpg" data-dbg="images-default" />
                  </div>
                <?endif?>

                <?if(!empty($arResult['PROPERTIES']['IMAGES']['VALUE'])):?>
                  <?foreach($arResult['PROPERTIES']['IMAGES']['VALUE'] as $imageID):?>
                    <?$arImage = CFile::ResizeImageGet($imageID, $arSizeCrop, BX_RESIZE_IMAGE_EXACT); ?>
                    <div class="image color-default">
                      <? /* <a data-fancybox="gallery" href="<?=$arImage['src']?>"> */ ?>
                        <img src="<?=$arImage['src']?>" data-dbg="images-2" />
                      <? /* </a> */ ?>
                    </div>
                  <?endforeach?>
                <?endif?>

              <?endif?>

            </div>
          <?endif?>
          <?$frame->end();?>

        </div>
      </div>
    </div>
    <div class="info js-sticky-kit-go">

      <? //if($isRuleModerator):?>

        <?
          $indexStories = 0;

          foreach($arResult['COLOR_SIZES'] as $key => $color)
          {
            $isDisplay = false;
            if($SET_COLOR_ID)
            {
              if($SET_COLOR_ID == $key)
                $isDisplay = true;
            }
            elseif($indexStories == 0)
              $isDisplay = true;

            if($arResult['COLOR_CONCOMITANT'][$key])
            {
              global $FILTER_NAME_COLOR;
              $FILTER_NAME_COLOR["ID"] = $arResult['COLOR_CONCOMITANT'][$key];
              ?>
              <?$APPLICATION->IncludeComponent(
                  "bitrix:news.list",
                  "catalog.stories",
                  array(
                    "COLOR_KEY" => $key,
                    "COLOR_DISPLAY" => ($isDisplay ? 'Y' : 'N'),
                    "ACTIVE_DATE_FORMAT" => "d.m.Y",
                    "ADD_SECTIONS_CHAIN" => "N",
                    "AJAX_MODE" => "N",
                    "AJAX_OPTION_ADDITIONAL" => "",
                    "AJAX_OPTION_HISTORY" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "N",
                    "CACHE_FILTER" => "N",
                    "CACHE_GROUPS" => "Y",
                    "CACHE_TIME" => "36000000",
                    "CACHE_TYPE" => "A",
                    "CHECK_DATES" => "Y",
                    "DETAIL_URL" => "/catalog/#SECTION_CODE#/#ELEMENT_CODE#/",
                    "DISPLAY_BOTTOM_PAGER" => "N",
                    "DISPLAY_DATE" => "N",
                    "DISPLAY_NAME" => "N",
                    "DISPLAY_PICTURE" => "N",
                    "DISPLAY_PREVIEW_TEXT" => "N",
                    "DISPLAY_TOP_PAGER" => "N",
                    "FIELD_CODE" => array(
                        0 => "CODE",
                        1 => "NAME",
                        2 => "IBLOCK_ID",
                        3 => "IBLOCK_CODE",
                        4 => "IBLOCK_EXTERNAL_ID",
                        5 => "",
                    ),
                    "FILTER_NAME" => "FILTER_NAME_COLOR",
                    "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                    "IBLOCK_ID" => "11",
                    "IBLOCK_TYPE" => "catalogs",
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                    "INCLUDE_SUBSECTIONS" => "N",
                    "MESSAGE_404" => "",
                    "NEWS_COUNT" => "7",
                    "PAGER_BASE_LINK_ENABLE" => "N",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL" => "N",
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_TEMPLATE" => ".default",
                    "PAGER_TITLE" => Loc::getMessage("CATALOG_ELEMENT_NEWS_LIST"),
                    "PARENT_SECTION" => "",
                    "PARENT_SECTION_CODE" => "",
                    "PREVIEW_TRUNCATE_LEN" => "",
                    "PROPERTY_CODE" => array(
                        0 => "",
                        1 => "IMAGES",
                        2 => "",
                    ),
                    "SET_BROWSER_TITLE" => "N",
                    "SET_LAST_MODIFIED" => "N",
                    "SET_META_DESCRIPTION" => "N",
                    "SET_META_KEYWORDS" => "N",
                    "SET_STATUS_404" => "N",
                    "SET_TITLE" => "N",
                    "SHOW_404" => "N",
                    "SORT_BY1" => "ACTIVE_FROM",
                    "SORT_BY2" => "SORT",
                    "SORT_ORDER1" => "DESC",
                    "SORT_ORDER2" => "ASC",
                    "STRICT_SECTION_CHECK" => "N",
                    "COMPONENT_TEMPLATE" => "catalog"
                  ),
                  false
              );
            }
            $indexStories ++;
          }
        ?>
      <? // endif?>

      <div class="wrapper">

        <? /*if($USER->IsAdmin()):?>
          <div class="header-product">
            <div class="indent-flex">
              <?$APPLICATION->IncludeComponent("bitrix:search.title", "search", Array(
                "CATEGORY_0" => array(	// Ограничение области поиска
                  0 => "iblock_catalogs",
                ),
                "CATEGORY_0_TITLE" => "",	// Название категории
                "CATEGORY_0_forum" => array(
                  0 => "all",
                ),
                "CATEGORY_0_iblock_catalogs" => array(	// Искать в информационных блоках типа "iblock_catalogs"
                  0 => "2",
                ),
                "CATEGORY_0_main" => "",
                "CHECK_DATES" => "N",	// Искать только в активных по дате документах
                "CONTAINER_ID" => "title-search",	// ID контейнера, по ширине которого будут выводиться результаты
                "INPUT_ID" => "title-search-input",	// ID строки ввода поискового запроса
                "NUM_CATEGORIES" => "1",	// Количество категорий поиска
                "ORDER" => "date",	// Сортировка результатов
                "PAGE" => SITE_DIR."catalog/",	// Страница выдачи результатов поиска (доступен макрос #SITE_DIR#)
                "SHOW_INPUT" => "Y",	// Показывать форму ввода поискового запроса
                "SHOW_OTHERS" => "N",	// Показывать категорию "прочее"
                "TOP_COUNT" => "5",	// Количество результатов в каждой категории
                "USE_LANGUAGE_GUESS" => "N",	// Включить автоопределение раскладки клавиатуры
                "COMPONENT_TEMPLATE" => ".default"
              ),
                false
              );?>

              <div class="info-right">
                <div class="menu">
                  <?$APPLICATION->IncludeComponent(
                    "bitrix:system.auth.form",
                    "",
                    Array(
                      "FORGOT_PASSWORD_URL" => SITE_DIR."personal/index.php?forgotpassword=yes",
                      "PROFILE_URL" => SITE_DIR."personal/profile/",
                      "REGISTER_URL" => SITE_DIR."personal/profile/",
                      "SHOW_ERRORS" => "N"
                    )
                  );?>

                  <?if(Loc::getCurrentLang() == 'ru'):?>
                    <div class="item hover js-open-chat"><?=Loc::getMessage("HEADER_INFO_MENU_CHAT")?></div>
                  <?endif;?>
                </div>
                <div id="basket-mini-wr" class="basket js-basket-display">
                  <?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "basket_line", Array(
                      "HIDE_ON_BASKET_PAGES" => "Y",	// Не показывать на страницах корзины и оформления заказа
                      "PATH_TO_AUTHORIZE" => "",	// Страница авторизации
                      "PATH_TO_BASKET" => SITE_DIR."basket/",	// Страница корзины
                      "PATH_TO_ORDER" => SITE_DIR."order/",	// Страница оформления заказа
                      "PATH_TO_PERSONAL" => SITE_DIR."personal/",	// Страница персонального раздела
                      "PATH_TO_PROFILE" => SITE_DIR."personal/",	// Страница профиля
                      "PATH_TO_REGISTER" => SITE_DIR."login/",	// Страница регистрации
                      "POSITION_FIXED" => "N",	// Отображать корзину поверх шаблона
                      "SHOW_AUTHOR" => "N",	// Добавить возможность авторизации
                      "SHOW_EMPTY_VALUES" => "Y",	// Выводить нулевые значения в пустой корзине
                      "SHOW_NUM_PRODUCTS" => "Y",	// Показывать количество товаров
                      "SHOW_PERSONAL_LINK" => "Y",	// Отображать персональный раздел
                      "SHOW_PRODUCTS" => "Y",	// Показывать список товаров
                      "SHOW_REGISTRATION" => "N",	// Добавить возможность регистрации
                      "SHOW_TOTAL_PRICE" => "Y",	// Показывать общую сумму по товарам
                      "COMPONENT_TEMPLATE" => ".default",
                      "SHOW_DELAY" => "N",	// Показывать отложенные товары
                      "SHOW_NOTAVAIL" => "N",	// Показывать товары, недоступные для покупки
                      "SHOW_IMAGE" => "Y",	// Выводить картинку товара
                      "SHOW_PRICE" => "Y",	// Выводить цену товара
                      "SHOW_SUMMARY" => "Y",	// Выводить подытог по строке
                  ),
                      false
                  );?>
                </div>
              </div>


            </div>
          </div>
        <?endif */ ?>

        <div class="mobile-shutter-close js-shutter-close"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/times.svg"></div>
        <div class="mobile-touch-line"></div>
        <div class="mobile-message-added">
          <div class="wrapper">
            <div class="text"><?=Loc::getMessage("CATALOG_ELEMENT_INFO_SIZE")?></div>
            <a class="link hover" href="/basket/"><?=Loc::getMessage("CATALOG_ELEMENT_INFO_SHOW")?></a>
          </div>
          <div class="wrapper preorder">
            <div class="text"><?=Loc::getMessage("CATALOG_ELEMENT_INFO_PREORDER")?></div>
          </div>
        </div>

        <div class="indent">

          <h1 class="name">
            <?if(SITE_ID=="s1"):?><?=$arResult['NAME']?><?else:?><?if($arResult['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE']):?><?=$arResult['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE']?><?else:?><?=$arResult['NAME']?><?endif?><?endif?>

            <div class="icons-save">
<!--                <img class="icon icon-share share-mobile" id="share-mobile-2442" data-id="2442" data-name="Жакет, БЕЖЕВЫЙ" data-url="/catalog/kostyumy/zhaket/?setColor=a193d970-675a-11e9-8425-b88303f1ae99&amp;offer=2442" src="/local/templates/sodamoda/images/icons/icon-share-ios.png">-->
              <img id="share-mobile" class="icon share-mobile" src="<?=SITE_TEMPLATE_PATH?>/images/icons/icon-share-ios.png" />

              <div class="favorites-list mode-mobile">
                <?if(count($arResult['COLOR_SIZES']) > 0):?>
                  <? $index = 0; ?>
                  <?foreach($arResult['COLOR_SIZES'] as $key => $item):?>
                    <?
                      $isDisplay = false;
                      if($SET_COLOR_ID)
                      {
                        if($SET_COLOR_ID == $key)
                          $isDisplay = true;
                      }
                      elseif($index == 0)
                        $isDisplay = true;
                    ?>
<!--                      <img class="icon icon-like js-favorite-button<?if($isDisplay):?> active<?endif?>" data-id="<?=$item['OFFER_ID']?>" data-color-key="<?=$key?>" src="/local/templates/sodamoda/images/icons/icon-like.svg">-->

                    <div class="favorite-item<?if($isDisplay):?> active<?endif?>" data-color-key="<?=$key?>">
                      <a class="indent-item js-favorite-button" data-id="<?=$item['OFFER_ID']?>">
                        <img class="icon icon-like" src="<?=SITE_TEMPLATE_PATH?>/images/icons/icon-like.svg">
                        <img class="icon icon-like added" src="<?=SITE_TEMPLATE_PATH?>/images/icons/icon-like-black.svg">
                      </a>
                    </div>
                    <? $index++; ?>
                  <?endforeach?>
                <?else:?>
                  <img class="icon icon-like" src="/local/templates/sodamoda/images/icons/icon-like.svg">
                <?endif?>
              </div>
            </div>
          </h1>
          <div class="price">
            <span id='price-old' <?=$arResult['COLOR_SIZES'][$SET_COLOR_ID]['PRICES']['IS_DISCOUNT'] ? 'class="price-old"' : '';?>><?= $arResult['COLOR_SIZES'][$SET_COLOR_ID]['PRICES']['IS_DISCOUNT'] ? $arResult['COLOR_SIZES'][$SET_COLOR_ID]['PRICES']['OLD'] : ''; ?></span><span id="price-current"><?=$arResult['COLOR_SIZES'][$SET_COLOR_ID]['PRICES']['CURRENT']?></span>
          </div>
          <?if($arResult["CAN_BUY"]):?>
            <div class="mobile-buy mobile-display" data-can-buy="1">
              <!-- js-addtobasket -->
              <a class="ibutton js-addtobasket-go" data-id="<?=$arResult['ID']?>" data-size="" rel="nofollow"><?=Loc::getMessage("CATALOG_ADD_TO_BASKET")?></a>
              <div class="size-module" data-num="1">

                  <?if(!empty($arResult['PROPERTIES']['SIZES']['VALUE'])):?>
                  <div class="size">
                    <?foreach($arResult['PROPERTIES']['SIZES']['VALUE'] as $keyitemSizes=>$itemSizes):?>
                      <div class="item"><a class="js-addtobasket-go-item hover"
                                            data-id="<?=$arResult['ID']?>"
                                            data-elurl="<?=$arResult['DETAIL_PAGE_URL']?>"
                                            data-propid="<?=$arResult['PROPERTIES']['SIZES']['VALUE_ENUM_ID'][$keyitemSizes]?>"
                                            data-elid="<?=$arResult['ID']?>"
                                            data-name="<?=$arResult['PROPERTIES']['SIZES']['NAME']?>"
                                           data-code="<?=$arResult['PROPERTIES']['SIZES']['CODE']?>"

                              <?if(!empty($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'])):?>
                                  data-name-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['NAME']?>"
                                  data-code-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['CODE']?>"
                                  data-value-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?>"
                              <?endif?>

                                  <?if(!empty($arResult['PROPERTIES']['COLOR']['VALUE'])):?>
                                  data-name-color="<?=$arResult['PROPERTIES']['COLOR']['NAME']?>"
                                  data-code-color="<?=$arResult['PROPERTIES']['COLOR']['CODE']?>"
                                  <?if(is_array($arResult['PROPERTIES']['COLOR']['VALUE'])):?>
                                      data-value-color="<?=implode(', ', $arResult['PROPERTIES']['COLOR']['VALUE']);?>"
                                  <?else:?>
                                      data-value-color="<?=$arResult['PROPERTIES']['COLOR']['VALUE']?>"
                                  <?endif?>
                              <?endif?>

                                            data-value="<?=$itemSizes?>"><?=$itemSizes?></a></div>
                    <?endforeach?>
                  </div>
                <?endif?>

                <div class="table-size hover">
                  <div class="caption js-open-popup-table-sizes"><?=Loc::getMessage("CATALOG_ELEMENT_TABLE_SIZE_TITLE")?></div>
                  <div class="icon js-open-popup-table-sizes"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/hanger.svg"></div>
                </div>
                <div class="close hover js-popup-close"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/close.svg"></div>
              </div>
            </div>

          <?else:?>

            <div class="mobile-buy mobile-display" data-can-buy="0">
              <!-- js-addtobasket -->

              <? /*if($isRuleModerator):?>
                <? $arResult['IS_AVAILABLE'] = false; ?>
              <?endif?>

              <?if($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'] == 1247):?>
                <? $arResult['IS_AVAILABLE'] = false; ?>
              <?endif */ ?>


              <? // if($arResult['IS_AVAILABLE']):?>


                <?if(!empty($arResult['OFFERS_COLOR_LIST']) && count($arResult['OFFERS_COLOR_LIST']) > 1):?>
                  <div class="choose-colors-wrap">
                    <a class="ibutton js-addtobasket-go" data-id="<?=$arResult['ID']?>" data-size="" rel="nofollow"><?=Loc::getMessage("CATALOG_ADD_TO_BASKET")?></a>
                    <div class="choose-colors">
                      <?foreach($arResult['OFFERS_COLOR_LIST'] as $keyColor => $arItemColor):?>
                        <div class="item value">
                          <a class="js-change-color js-change-history item-color<?if($SET_COLOR_ID == $keyColor):?> active<?endif?>" data-preorder="<?=$arItemColor['PREORDER'] ? 'true' : 'false';?>" data-available="<?=$arItemColor['AVAILABLE'] ? 'true' : 'false';?>" data-offer-color data-key="<?=$keyColor?>" style="background-color:<?=$arItemColor['HEX']?>" title="<?=$arItemColor['NAME']?>"></a>
                        </div>
                      <?endforeach?>
                    </div>
                  </div>
                <?else:?>
                  <a class="ibutton js-addtobasket-go" data-id="<?=$arResult['ID']?>" data-size="" rel="nofollow"><?=Loc::getMessage("CATALOG_ADD_TO_BASKET")?></a>
                <?endif?>


              <? /* else:?>
                #APS_MOBILE#
              <?endif */
              ?>


              <div class="size-module" data-num="2">

                <? /*   if(count($arResult['COLOR_SIZES'])>0){?>
                  <div class="item">
                    <span class="name">Цвет:</span>
                    <?$i=0;?>
                    <?foreach($arResult['COLOR_SIZES'] as $key => $item){?>
                      <span class="value"><a data-offer-color class="js-change-color black<?if($i==0){?> active<?}?>" data-key="<?=$key?>"><?=$item["VALUE"]?></a><?if($i + 1 < count($arResult['COLOR_SIZES'])):?>,<?endif?></span>
                      <?$i++;?>
                    <?}?>
                  </div>
                <?} */ ?>

                <?if(count($arResult['COLOR_SIZES']) > 0):?>
                  <? $index = 0; ?>
                  <?foreach($arResult['COLOR_SIZES'] as $key => $item):?>

                    <?
                      $isDisplay = false;
                      if($SET_COLOR_ID)
                      {
                        if($SET_COLOR_ID == $key)
                          $isDisplay = true;
                      }
                      elseif($index == 0)
                        $isDisplay = true;
                    ?>

                    <div data-offer-color-sizes="<?=$key?>" class="size<?if($isDisplay):?> active<?endif?> <?=$isPreorderAvailable ? 'is-preorder-available' : '';?>" data-offer-color-sizes-photo="<?=$arResult['PHOTO_ID'][$key]?>" data-key="<?=$key?>">
                      <?foreach($item['SIZES'] as $keySize => $itemSize):?>
                        <div class="item">
                            <a class="js-set-size-offer hover <?if($itemSize['STORE_DATA']['AVAILABLE']):?>available<?else:?>disabled<?endif?>"
                                data-offer-color-sizes-select
                                data-js-addtobasket-go-item
                                data-elid="<?=$itemSize['OFFER_ID']?>"
                                data-name-size="<?=$itemSize['NAME']?>"
                                data-code-size="<?=$itemSize['CODE']?>"
                                data-value-size="<?=$itemSize['VALUE']?>"
                                data-name-color="<?=$item['NAME']?>"
                                data-code-color="<?=$item['CODE']?>"
                                data-value-color="<?=$item['VALUE']?>"
                                
                                data-preorder="<?=$arResult['OFFERS_COLOR_LIST'][$key]['PREORDER'] && !$itemSize['STORE_DATA']['AVAILABLE'] ? 'true' : 'false'?>"
                                data-available="<?=$itemSize['STORE_DATA']['AVAILABLE'] ? 'true' : 'false'?>"
                                data-amount="<?=$itemSize['STORE_DATA']['AMOUNT']?>"
                                
                                data-name-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['NAME']?>"
                                data-code-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['CODE']?>"
                                data-value-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?>"><?=$itemSize["VALUE"]?></a>
                          <?if($itemSize['STORE_DATA']['AMOUNT'] < 1 && $itemSize['PREORDER']):?>
                            <div class="buttons-more">
                                  <div class="preorder-button"
                                    data-js-addtobasket-go-item
                                    data-elid="<?=$itemSize['OFFER_ID']?>"
                                    data-name-size="<?=$itemSize['NAME']?>"
                                    data-code-size="<?=$itemSize['CODE']?>"
                                    data-value-size="<?=$itemSize['VALUE']?>"
                                    data-name-color="<?=$item['NAME']?>"
                                    data-code-color="<?=$item['CODE']?>"
                                    data-value-color="<?=$item['VALUE']?>"

                                    data-preorder="<?=$arResult['OFFERS_COLOR_LIST'][$key]['PREORDER'] && !$itemSize['STORE_DATA']['AVAILABLE'] ? 'true' : 'false'?>"
                                    data-available="<?=$itemSize['STORE_DATA']['AVAILABLE'] ? 'true' : 'false'?>"
                                    data-amount="<?=$itemSize['STORE_DATA']['AMOUNT']?>"

                                    data-name-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['NAME']?>"
                                    data-code-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['CODE']?>"
                                    data-value-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?>"
                                  ><?=Loc::getMessage("CATALOG_TO_PREORDER_BUTTON")?></div>
                                  <div class='subscribe_form'><?= '#APS_DESKTOP_' . $itemSize['OFFER_ID'] . '#'; ?></div>
                            </div>
                          <?endif?>
                        </div>
                        <? $index++; ?>
                      <?endforeach?>
                    </div>
                  <?endforeach?>
                <?endif?>
                
                <div class="table-size hover">
                  <div class="caption js-open-popup-table-sizes"><?=Loc::getMessage("CATALOG_ELEMENT_TABLE_SIZE_TITLE")?></div>
                  <div class="icon js-open-popup-table-sizes"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/hanger.svg"></div>
                </div>
                <div class="close hover js-popup-close"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/close.svg"></div>
              </div>
                  
            </div>

          <?endif?>

          <div class="props">

            <?if(!empty($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'])):?>
              <div class="item">
                <span class="name"><?=Loc::getMessage("CATALOG_ELEMENT_PROPS_ARTICUL")?></span>
                <span class="value"><?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?></span>
                <?if($isRuleModerator):?>
                  <span class="value remains"><a class="black" href="/personal/store/?query=<?=$arResult['ID'];?>&color=<?=$_REQUEST['setColor'];?>" target="_blank" id="js-store-checked" data-base-url="/personal/store-page/?query=<?=$arResult['ID'];?>">Остатки</a></span>
                <?endif?>
              </div>
            <?endif?>

            <?/*if(!$detect->isMobile() && count($arResult['OFFERS_COLOR_LIST']) > 0):?>
              <div class="item" data-dbg="desktop offers color">
                <span class="name">Цвет:</span>
                <? $i = 0; ?>
                <?foreach($arResult['OFFERS_COLOR_LIST'] as $keyColor => $item):?>
                  <?
                    $isDisplay = false;
                    if($SET_COLOR_ID)
                    {
                      if($SET_COLOR_ID == $keyColor)
                        $isDisplay = true;
                    }
                    elseif($i == 0)
                      $isDisplay = true;
                  ?>
                  <span class="value"><a data-offer-color class="js-change-color black<?if($isDisplay):?> active<?endif?>" data-key="<?=$keyColor?>"><?=trim($item['NAME'])?></a><?if($i + 1 < count($arResult['OFFERS_COLOR_LIST'])):?>,<?endif?></span>
                  <? $i++; ?>
                <?endforeach?>
              </div>
            <?endif*/?>
            <?if(!$detect->isMobile() && count($arResult['COLOR_SIZES']) > 0):?>

              <div class="item" data-dbg="desktop offers color">
                <span class="name"><?=Loc::getMessage("CATALOG_ELEMENT_PROPS_COLOR")?></span>
                <? $index = 0; ?>
                <?foreach($arResult['COLOR_SIZES'] as $key => $item):?>
                  <?
                    $isDisplay = false;
                    if($SET_COLOR_ID)
                    {
                      if($SET_COLOR_ID == $key)
                        $isDisplay = true;
                    }
                    elseif($index == 0)
                      $isDisplay = true;
                  ?>
                  <span class="value"><a data-offer-color="" data-discount="<?=$item['PRICES']['IS_DISCOUNT'];?>" data-old-price="<?=$item['PRICES']['OLD'];?>" data-current-price="<?=$item['PRICES']['CURRENT'];?>" data-preorder="<?=$arResult['OFFERS_COLOR_LIST'][$key]['PREORDER'] ? 'true' : 'false';?>" data-available="<?=$item['AVAILABLE'] ? 'true' : 'false';?>" data-offer-color-sizes-photo="<?=$arResult['PHOTO_ID'][$key]?>" class="js-change-color black<?if($isDisplay):?> active<?endif?>" data-key="<?=$key?>"><?=trim($item['VALUE'])?></a><?if($index + 1 < count($arResult['COLOR_SIZES'])):?>,<?endif?></span>
                  <? $index++; ?>
                <?endforeach?>
              </div>

            <?elseif(!empty($arResult['PROPERTIES']['COLOR']['VALUE'])):?>

              <div class="item">
                <span class="name"><?=Loc::getMessage("CATALOG_ELEMENT_PROPS_COLOR")?></span>
                <?if(is_array($arResult['PROPERTIES']['COLOR']['VALUE'])):?>
                  <span class="value"><a class="js-change-color black active"><?=implode('</a>, <a class="js-change-color black">', $arResult['PROPERTIES']['COLOR']['VALUE']);?></a></span>
                <?else:?>
                  <span class="value"><?=$arResult['PROPERTIES']['COLOR']['VALUE']?></span>
                <?endif?>
              </div>

            <?endif?>


            <?if(!empty($arResult['PROPERTIES']['FABRIC']['VALUE'])):?>
              <div class="item">
                <span class="name"><?=Loc::getMessage("CATALOG_ELEMENT_PROPS_CLOTH")?></span>
                <?if(is_array($arResult['PROPERTIES']['FABRIC']['VALUE'])):?>
                  <span class="value"><?=implode(', ', $arResult['PROPERTIES']['FABRIC']['VALUE']);?></span>
                <?else:?>
                  <span class="value"><?=$arResult['PROPERTIES']['FABRIC']['VALUE']?></span>
                <?endif?>
              </div>
            <?endif?>

          </div>

          <? /* if($detect->isMobile() && !empty($arResult['OFFERS_COLOR_LIST'])):?>
            <div class="mobile-colors-selected">
              <div class="items-wrapper">
                <div class="items owl-carousel">
                  <?foreach($arResult['OFFERS_COLOR_LIST'] as $keyColor => $arImage):?>
                    <? $arImageCrop = CFile::ResizeImageGet($arImage['IMAGES'][0], Array('width' => 260, 'height' => 390), BX_RESIZE_IMAGE_EXACT); ?>
                    <div class="item">
                      <a data-offer-color class="js-change-color" data-key="<?=$keyColor?>">
                        <img class="image" src="<?=$arImageCrop['src']?>" />
                        <div class="name"><?=$arImage['NAME']?></div>
                      </a>
                    </div>
                  <?endforeach?>
                </div>
              </div>
            </div>
          <?endif */ ?>
          <?if(!empty($arResult['DETAIL_TEXT']) || $arResult['PROPERTIES']['ENG_DESCRIPTION']['VALUE']):?>
          <p class="description">
            <?if(SITE_ID=="s1"):?>
              <?=$arResult['DETAIL_TEXT']?>
            <?else:?>
              <?if($arResult['PROPERTIES']['ENG_DESCRIPTION']['VALUE']):?>
                <?=$arResult['PROPERTIES']['ENG_DESCRIPTION']['VALUE']['TEXT']?>
              <?else:?>
                <?=$arResult['DETAIL_TEXT']?>
              <?endif?>
            <?endif?>
          </p>
          <?endif?>

          <div class="desktop-display">
            <div class="size-module" data-num="0">

              <?if(count($arResult['COLOR_SIZES']) > 0):?>
                <? $index = 0; ?>
                <?foreach($arResult['COLOR_SIZES'] as $key => $item):?>
                  <?
                    $isDisplay = false;
                    if($SET_COLOR_ID)
                    {
                      if($SET_COLOR_ID == $key)
                        $isDisplay = true;
                    }
                    elseif($index == 0)
                      $isDisplay = true;
                  ?>

                  <div class="size<?if($isDisplay):?> active<?endif?>" data-preorder="<?=$arResult['OFFERS_COLOR_LIST'][$key]['PREORDER'] ? 'true' : 'false';?>" data-offer-color-sizes-photo="<?=$arResult['PHOTO_ID'][$key]?>" data-offer-color-sizes="<?=$key?>" data-key="<?=$key?>">
                    <?foreach($item['SIZES'] as $keySize => $itemSize):
                      ?>
                      <div class="item<?if($itemSize['PREORDER']):?> preorder<?endif?><?if(count($item['SIZES']) == 1):?> active<?endif?>">
                        <a class="js-set-size-offer hover <?if($itemSize['STORE_DATA']['AVAILABLE']):?>available<?else:?>disabled<?endif?>"
                            data-offer-color-sizes-select
                            data-elid="<?=$itemSize['OFFER_ID']?>"
                            data-photoid="<?=$itemSize['PHOTO_ID']?>"
                            data-name-size="<?=$itemSize['NAME']?>"
                            data-code-size="<?=$itemSize['CODE']?>"
                            data-value-size="<?=$itemSize['VALUE']?>"
                            data-name-color="<?=$item['NAME']?>"
                            data-code-color="<?=$item['CODE']?>"
                            data-value-color="<?=$item['VALUE']?>"
                            data-available="<?=$itemSize['STORE_DATA']['AVAILABLE'] ? 'true' : 'false';?>"
                            data-preorder="<?=$itemSize['PREORDER'] && !$itemSize['STORE_DATA']['AVAILABLE'] ? 'true' : 'false';?>"
                            data-amount="<?=$itemSize['STORE_DATA']['AMOUNT']?>"
                            data-name-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['NAME']?>"
                            data-code-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['CODE']?>"
                            data-value-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?>">
                              <?=$itemSize["VALUE"]?>
                        </a>
                        <?if($itemSize['STORE_DATA']['AMOUNT'] < 1 && $itemSize['PREORDER']):?>
                        <div class="buttons-more">
                            <div class="preorder-button"><?=Loc::getMessage("CATALOG_TO_PREORDER_BUTTON")?><div class="tip">Данная модель доступна к предзаказу, сроки изготовления в среднем составляют 1-4 недели</div></div>
                            <div class="subscribe_form"><?= '#APS_DESKTOP_' . $itemSize['OFFER_ID'] . '#'; ?></div>
                        </div>
                        <?endif?>
                      </div>
                      <? $index++; ?>
                    <?endforeach?>
                  </div>
                <?endforeach?>
              <?endif?>

              <?if(!empty($arResult['PROPERTIES']['SIZES']['VALUE'])):?>
                <div class="size">
                  <?foreach($arResult['PROPERTIES']['SIZES']['VALUE'] as $keyitemSizes => $itemSizes):?>
                      <div class="item"><a
                                  data-elurl="<?=$arResult['DETAIL_PAGE_URL']?>"
                                  data-propid="<?=$arResult['PROPERTIES']['SIZES']['VALUE_ENUM_ID'][$keyitemSizes]?>"
                                  data-elid="<?=$arResult['ID']?>"
                                  data-name="<?=$arResult['PROPERTIES']['SIZES']['NAME']?>"
                                  data-code="<?=$arResult['PROPERTIES']['SIZES']['CODE']?>"

                              <?if(!empty($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'])):?>
                                  data-name-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['NAME']?>"
                                  data-code-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['CODE']?>"
                                  data-value-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?>"
                              <?endif?>


                              <?if(!empty($arResult['PROPERTIES']['COLOR']['VALUE'])):?>
                                  data-name-color="<?=$arResult['PROPERTIES']['COLOR']['NAME']?>"
                                  data-code-color="<?=$arResult['PROPERTIES']['COLOR']['CODE']?>"
                                  <?if(is_array($arResult['PROPERTIES']['COLOR']['VALUE'])):?>
                                      data-value-color="<?=implode(', ', $arResult['PROPERTIES']['COLOR']['VALUE']);?>"
                                  <?else:?>
                                      data-value-color="<?=$arResult['PROPERTIES']['COLOR']['VALUE']?>"
                                  <?endif?>
                              <?endif?>
                              data-value="<?=$itemSizes?>" class="js-set-size hover"><?=$itemSizes?></a></div>
                  <?endforeach;?>
                </div>
              <?endif?>
            </div>

            <div class="table-size-indent">

                <div class="favorites-list">

                  <? /*
                  <a class="wishlist-item o-fav-btn" data-id="<?$item['OFFER_IBLOCK_ID']?>">
                    <div class="mess">В избранное</div>
                    <img class="icon" src="<?=SITE_TEMPLATE_PATH?>/images/icons/icon-like.svg">
                  </a>

                  <a class="wishlist-item js-wishlist" data-wishiblock="2<? // $item['OFFER_IBLOCK_ID']?>" data-wishid="1259<? //$item['OFFER_ID']?>">
                    <div class="mess">В избранное</div>
                    <img class="icon" src="<?=SITE_TEMPLATE_PATH?>/images/icons/icon-like.svg">
                  </a> */ ?>

                  <?if(count($arResult['COLOR_SIZES']) > 0):?>
                    <? $index = 0; ?>
                    <?foreach($arResult['COLOR_SIZES'] as $key => $item):?>
                      <?
                        $isDisplay = false;
                        if($SET_COLOR_ID)
                        {
                          if($SET_COLOR_ID == $key)
                            $isDisplay = true;
                        }
                        elseif($index == 0)
                          $isDisplay = true;
                      ?>
                      <div class="favorite-item<?if($isDisplay):?> active<?endif?>" data-color-key="<?=$key?>">
                        <a class="indent-item js-favorite-button" data-id="<?=$item['OFFER_ID']?>">
                          <div class="mess"><?=Loc::getMessage("CATALOG_ELEMENT_FAVORITE_ADD")?></div>
                          <div class="mess added"><?=Loc::getMessage("CATALOG_ELEMENT_FAVORITE_ADDED")?></div>
                          <img class="icon" src="<?=SITE_TEMPLATE_PATH?>/images/icons/icon-like.svg">
                          <img class="icon added" src="<?=SITE_TEMPLATE_PATH?>/images/icons/icon-like-black.svg">
                        </a>
                      </div>
                      <? $index++; ?>
                    <?endforeach?>
                  <?endif?>
                </div>

              <div class="table-size hover">
                <div class="caption js-open-popup-table-sizes"><?=Loc::getMessage("CATALOG_ELEMENT_TABLE_SIZE_BUTTON")?></div>
                <div class="icon js-open-popup-table-sizes"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/hanger.svg"></div>
              </div>
            </div>

            <?if($arResult["CAN_BUY"]):?>
            
              <a class='ibutton js-addtobasket' data-can-buy='1'
                <? if (!empty($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'])): ?>
                  data-name-article="<?= $arResult['PROPERTIES']['CML2_ARTICLE']['NAME'] ?>"
                  data-code-article="<?= $arResult['PROPERTIES']['CML2_ARTICLE']['CODE'] ?>"
                  data-value-article="<?= $arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'] ?>"
                <?endif ?>
                <? if (!empty($arResult['PROPERTIES']['COLOR']['VALUE'])): ?>
                  data-name-color="<?= $arResult['PROPERTIES']['COLOR']['NAME'] ?>"
                  data-code-color="<?= $arResult['PROPERTIES']['COLOR']['CODE'] ?>"
                  <? if (is_array($arResult['PROPERTIES']['COLOR']['VALUE'])): ?>
                    data-value-color="<?= implode(', ', $arResult['PROPERTIES']['COLOR']['VALUE']); ?>"
                  <? else: ?>
                    data-value-color="<?= $arResult['PROPERTIES']['COLOR']['VALUE'] ?>"
                  <?endif ?>
                <?endif ?>
                 data-id="<?= $arResult['ID'] ?>" data-size=""
                 rel="nofollow"><?= Loc::getMessage('CATALOG_ADD_TO_BASKET') ?></a>

            <?elseif(count($arResult['COLOR_SIZES']) > 0):?>

              <? /*if($isRuleModerator):?>
                <? $arResult['IS_AVAILABLE'] = false; ?>
              <?endif?>

              <?if($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'] == 1376):?>
                <? $arResult['IS_AVAILABLE'] = false; ?>
              <?endif */ ?>
              <div class="is_available">
                <a class="ibutton js-metrika-addtobasket" data-can-buy="0" data-addtobasket-offer data-size="" rel="nofollow"><?=Loc::getMessage("CATALOG_ADD_TO_BASKET")?></a>
              </div>
              <?//
              //
              //<div class="is_preorder" style="display: none">
              //  <a class="ibutton js-metrika-addtobasket" data-can-buy="0" data-addtobasket-offer data-size="" rel="nofollow"><?=Loc::getMessage("CATALOG_TO_ORDER_BUTTON")? ></a>
          //</div>
            //<div class="not_available" style="display: none">#APS_DESKTOP#</div>
              //if($arResult['IS_AVAILABLE']):?>
                <? /* <a class="ibutton js-metrika-addtobasket" data-can-buy="0" data-addtobasket-offer data-size="" rel="nofollow"><?=Loc::getMessage("CATALOG_ADD_TO_BASKET")?></a>

              <? // if($arResult['IS_AVAILABLE']):?>
                <a class="ibutton js-metrika-addtobasket" data-can-buy="0" data-addtobasket-offer data-size="" rel="nofollow"><?=Loc::getMessage("CATALOG_ADD_TO_BASKET")?></a>
                <? /* if($isRuleModerator):?>
                <a class="ibutton hollow js-open-fitting">Записаться на примерку</a>
                <?endif */ ?>
              <? /* else:?>
                #APS_DESKTOP#
              <?endif */ ?>
            <?endif?>
          </div>

          <div class="links">
            <?if(Loc::getCurrentLang() == 'ru'):?>
              <div class="item">
                <a class="link hover js-open-fitting">Подбор образа со стилистом</a>
              </div>
            <?endif?>
            <?if(!empty($arResult['PROPERTIES']['COMPOSITION']['VALUE']) || !empty($arResult['PROPERTIES']['CARE']['VALUE'])):?>
              <div class="item">
                <a class="link hover js-open-popup-cares"><?=Loc::getMessage("CATALOG_ELEMENT_LINKS_TITLE")?></a>
                <div class="info-cares">

                  <?if(!empty($arResult['PROPERTIES']['COMPOSITION']['VALUE'])):?>
                    <div class="caption"><?=Loc::getMessage("CATALOG_ELEMENT_LINKS_COMPOSITION")?></div>
                    <?foreach($arResult['PROPERTIES']['COMPOSITION']['VALUE'] as $index => $value):?>
                      <?=$value?>
                      <br><br>
                    <?endforeach?>
                    <?
                    //if ($isRuleModerator) {
                    if(is_array($arResult['COMPOSITION_LIST']) && count($arResult['COMPOSITION_LIST'])){
                      foreach($arResult['COMPOSITION_LIST'] as $arVal) {
                        echo '<span>'. $arVal['MATERIAL'] .'</span> '. $arVal['VALUE'] .'<br>';
                      }
                    }
                    echo '<br>';
                    //}
                    ?>
                    <?/*foreach($arResult['PROPERTIES']['COMPOSITION']['VALUE'] as $index => $row):?>
                      <div class="caption"><?=$row?></div>
                      <?=$arResult['PROPERTIES']['COMPOSITION']['DESCRIPTION'][ $index ]?>
                      <br>
                    <?endforeach*/?>
                  <?endif?>

                  <?if(!empty($arResult['PROPERTIES']['CARE']['VALUE'])):?>
                    <div class="caption"><?=Loc::getMessage("CATALOG_ELEMENT_LINKS_CARE")?></div>
                    <div class="cares">
                      <?
                        $arFilter = Array("IBLOCK_ID" => $arResult['PROPERTIES']['CARE']['LINK_IBLOCK_ID'], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "ID" => $arResult['PROPERTIES']['CARE']['VALUE']);
                        $rsCare = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>0), Array("ID", "NAME", "PROPERTY_NAME_EN", "PROPERTY_ICON"));
                        $careNameLabel = (SITE_ID == 's2' ? 'PROPERTY_NAME_EN_VALUE' : 'NAME');
                        while($arCare = $rsCare->Fetch()) {
                          ?>
                          <div class="care-item">
                            <img src="<?=CFile::GetPath($arCare['PROPERTY_ICON_VALUE'])?>" alt="<?=$arCare['NAME']?>">
                            <div class="tip"><?=$arCare[$careNameLabel]?></div>
                          </div>
                          <?
                        }
                      ?>
                    </div>
                  <?endif?>

                </div>
              </div>
            <?endif?>
            <? /*div class="item">
              <a class="link hover js-open-popup-delivery">Доставка, обмен и возврат</a>
            </div*/ ?>
            <div class="item">
              <a class="link hover" href="https://api.whatsapp.com/send?phone=79688700070" target="_blank" title="<?=Loc::getMessage("CATALOG_ELEMENT_WATSAPP_TITLE")?>"><?=Loc::getMessage("CATALOG_ELEMENT_WATSAPP_LINK_TITLE")?></a>
            </div>
            <div class="item">
              <a class="link hover" href="tel:+78003505670"><?=Loc::getMessage("CATALOG_ELEMENT_CALL_LINK_TITLE")?> 8 800 350-5670</a>
            </div>
            <? /*div class="item">
              <a class="link hover js-open-chat">Написать в чат</a>
            </div */ ?>
            <div class="item item-socials">
              <a class="link hover js-share-display"><?=Loc::getMessage("CATALOG_ELEMENT_SHARED")?></a>
              <div class="wrap-share">
                <!-- div class="ya-share2" data-services="vkontakte,facebook,whatsapp,viber,twitter,skype,telegram,linkedin" data-size="s"></div -->
                <div class="ya-share2" data-curtain data-shape="round" data-color-scheme="whiteblack" data-services="vkontakte,facebook,telegram,whatsapp"></div>
              </div>
            </div>
          </div>


          <div class="section-path">
            <?
            $arSectionPath[0] = ['NAME' => 'SODAMODA', 'URL' => '/'];
            if(SITE_ID == 's2') $arSectionPath[0]['URL'] = '/en/';
            foreach($arResult['SECTION']['PATH'] as $arPath)
              $arSectionPath[] = ['NAME' => $arPath['NAME'], 'URL' => $arPath['SECTION_PAGE_URL']];
            ?>
            <?if(count($arSectionPath) > 1):?>
              <?foreach($arSectionPath as $index => $arPath):?>
                <a class="hover" href="<?=$arPath['URL']?>"><?=$arPath['NAME']?></a><?if($index != count($arSectionPath) - 1):?> / <?endif?>
              <?endforeach?>
            <?endif?>
          </div>

          <?if(FALSE && $detect->isMobile()):?>

            <div class="products-more" data="dbg-template">
              <div class="caption">Дополнить образ</div>
              <div class="products-more-slider-wrapper">
                <div class="products-more_items owl-carousel">
                  <div class="item">
                      <div class="image">
                        <a href="">
                          <img src="http://sm-tpl.arlix.ru/images/product2.jpg" />
                        </a>
                      </div>
                      <div class="name"><a class="hover" href="">Куртка с эффектом потёртости</a></div>
                      <div class="price">2 399 руб.</div>
                      <!-- div class="form">
                        <select name="size">
                          <option selected="selected" disabled="disabled">Выберите размер</option>
                          <option>XXS</option>
                          <option>XS</option>
                          <option>S</option>
                          <option>M</option>
                          <option>L</option>
                          <option>XL</option>
                          <option>XXL</option>
                        </select>
                        <a class="ibutton hollow">Добавить</a>
                      </div -->
                  </div>
                  <div class="item">
                      <div class="image">
                        <a href="">
                          <img src="http://sm-tpl.arlix.ru/images/product2.jpg" />
                        </a>
                      </div>
                      <div class="name"><a class="hover" href="">Куртка с эффектом потёртости</a></div>
                      <div class="price">2 399 руб.</div>
                  </div>
                </div>
              </div>
            </div>

          <?endif?>

        </div>
      </div>
    </div>

  </div>

<?if($arParams['AJAX'] != 'Y'):?>

</div>
<?
  //dbgz($SET_COLOR_ID);
  //dbgz($arResult['COLOR_SIZES']);
  $index = 0;

  foreach($arResult['COLOR_SIZES'] as $key => $color)
  {
    $isDisplay = false;
    if($SET_COLOR_ID)
    {
      if($SET_COLOR_ID == $key)
        $isDisplay = true;
    }
    elseif($index == 0)
      $isDisplay = true;

    if($arResult['COLOR_CONCOMITANT'][$key])
    {
      ?>
      <div id="cc_<?=$key?>" class="cc_list"<?if(!$isDisplay):?> style="display:none;"<?endif?>>

        <?
          global $FILTER_NAME_COLOR;
          $FILTER_NAME_COLOR["ID"] = $arResult['COLOR_CONCOMITANT'][$key];
        ?>
        <?$APPLICATION->IncludeComponent(
            "bitrix:news.list",
            "catalog",
            array(
              "ACTIVE_DATE_FORMAT" => "d.m.Y",
              "ADD_SECTIONS_CHAIN" => "N",
              "AJAX_MODE" => "N",
              "AJAX_OPTION_ADDITIONAL" => "",
              "AJAX_OPTION_HISTORY" => "N",
              "AJAX_OPTION_JUMP" => "N",
              "AJAX_OPTION_STYLE" => "N",
              "CACHE_FILTER" => "N",
              "CACHE_GROUPS" => "Y",
              "CACHE_TIME" => "36000000",
              "CACHE_TYPE" => "A",
              "CHECK_DATES" => "Y",
              "DETAIL_URL" => "/catalog/#SECTION_CODE#/#ELEMENT_CODE#/",
              "DISPLAY_BOTTOM_PAGER" => "N",
              "DISPLAY_DATE" => "N",
              "DISPLAY_NAME" => "N",
              "DISPLAY_PICTURE" => "N",
              "DISPLAY_PREVIEW_TEXT" => "N",
              "DISPLAY_TOP_PAGER" => "N",
              "FIELD_CODE" => array(
                  0 => "CODE",
                  1 => "NAME",
                  2 => "IBLOCK_ID",
                  3 => "IBLOCK_CODE",
                  4 => "IBLOCK_EXTERNAL_ID",
                  5 => "",
              ),
              "FILTER_NAME" => "FILTER_NAME_COLOR",
              "HIDE_LINK_WHEN_NO_DETAIL" => "N",
              "IBLOCK_ID" => "11",
              "IBLOCK_TYPE" => "catalogs",
              "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
              "INCLUDE_SUBSECTIONS" => "N",
              "MESSAGE_404" => "",
              "NEWS_COUNT" => "7",
              "PAGER_BASE_LINK_ENABLE" => "N",
              "PAGER_DESC_NUMBERING" => "N",
              "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
              "PAGER_SHOW_ALL" => "N",
              "PAGER_SHOW_ALWAYS" => "N",
              "PAGER_TEMPLATE" => ".default",
              "PAGER_TITLE" => Loc::getMessage("CATALOG_ELEMENT_NEWS_LIST"),
              "PARENT_SECTION" => "",
              "PARENT_SECTION_CODE" => "",
              "PREVIEW_TRUNCATE_LEN" => "",
              "PROPERTY_CODE" => array(
                  0 => "",
                  1 => "IMAGES",
                  2 => "",
              ),
              "SET_BROWSER_TITLE" => "N",
              "SET_LAST_MODIFIED" => "N",
              "SET_META_DESCRIPTION" => "N",
              "SET_META_KEYWORDS" => "N",
              "SET_STATUS_404" => "N",
              "SET_TITLE" => "N",
              "SHOW_404" => "N",
              "SORT_BY1" => "ACTIVE_FROM",
              "SORT_BY2" => "SORT",
              "SORT_ORDER1" => "DESC",
              "SORT_ORDER2" => "ASC",
              "STRICT_SECTION_CHECK" => "N",
              "COMPONENT_TEMPLATE" => "catalog"
            ),
            false
        );
        ?>

      </div>
      <?
    }
    $index ++;
  }
?>


<?
$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
if ($arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !$emptyProductProperties)
{
    ?>
    <div id="<?=$itemIds['BASKET_PROP_DIV']?>" style="display: none;">
        <?
        if (!empty($arResult['PRODUCT_PROPERTIES_FILL']))
        {
            foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propId => $propInfo)
            {
                ?>
                <input type="hidden" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]" value="<?=htmlspecialcharsbx($propInfo['ID'])?>">
                <?
                unset($arResult['PRODUCT_PROPERTIES'][$propId]);
            }
        }

        $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
        if (!$emptyProductProperties)
        {
            ?>
            <table>
                <?
                foreach ($arResult['PRODUCT_PROPERTIES'] as $propId => $propInfo)
                {
                    ?>
                    <tr>
                        <td><?=$arResult['PROPERTIES'][$propId]['NAME']?></td>
                        <td>
                            <?
                            if (
                                $arResult['PROPERTIES'][$propId]['PROPERTY_TYPE'] === 'L'
                                && $arResult['PROPERTIES'][$propId]['LIST_TYPE'] === 'C'
                            )
                            {
                                foreach ($propInfo['VALUES'] as $valueId => $value)
                                {
                                    ?>
                                    <label>
                                        <input type="radio" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]"
                                               value="<?=$valueId?>" <?=($valueId == $propInfo['SELECTED'] ? '"checked"' : '')?>>
                                        <?=$value?>
                                    </label>
                                    <br>
                                    <?
                                }
                            }
                            else
                            {
                                ?>
                                <select name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]">
                                    <?
                                    foreach ($propInfo['VALUES'] as $valueId => $value)
                                    {
                                        ?>
                                        <option value="<?=$valueId?>" <?=($valueId == $propInfo['SELECTED'] ? '"selected"' : '')?>>
                                            <?=$value?>
                                        </option>
                                        <?
                                    }
                                    ?>
                                </select>
                                <?
                            }
                            ?>
                        </td>
                    </tr>
                    <?
                }
                ?>
            </table>
            <?
        }
        ?>
    </div>

    <?
    $jsParams = array(
        'CONFIG' => array(
            'USE_CATALOG' => $arResult['CATALOG'],
            'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
            'SHOW_PRICE' => !empty($arResult['ITEM_PRICES']),
            'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
            'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
            'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
            'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
            'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
            'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
            'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
            'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
            'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
            'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
            'USE_STICKERS' => true,
            'USE_SUBSCRIBE' => $showSubscribe,
            'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
            'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
            'ALT' => $alt,
            'TITLE' => $title,
            'MAGNIFIER_ZOOM_PERCENT' => 200,
            'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
            'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
            'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
                ? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
                : null
        ),
        'VISUAL' => $itemIds,
        'PRODUCT_TYPE' => $arResult['CATALOG_TYPE'],
        'PRODUCT' => array(
            'ID' => $arResult['ID'],
            'ACTIVE' => $arResult['ACTIVE'],
            'PICT' => reset($arResult['MORE_PHOTO']),
            'NAME' => $arResult['~NAME'],
            'SUBSCRIPTION' => true,
            'ITEM_PRICE_MODE' => $arResult['ITEM_PRICE_MODE'],
            'ITEM_PRICES' => $arResult['ITEM_PRICES'],
            'ITEM_PRICE_SELECTED' => $arResult['ITEM_PRICE_SELECTED'],
            'ITEM_QUANTITY_RANGES' => $arResult['ITEM_QUANTITY_RANGES'],
            'ITEM_QUANTITY_RANGE_SELECTED' => $arResult['ITEM_QUANTITY_RANGE_SELECTED'],
            'ITEM_MEASURE_RATIOS' => $arResult['ITEM_MEASURE_RATIOS'],
            'ITEM_MEASURE_RATIO_SELECTED' => $arResult['ITEM_MEASURE_RATIO_SELECTED'],
            'SLIDER_COUNT' => $arResult['MORE_PHOTO_COUNT'],
            'SLIDER' => $arResult['MORE_PHOTO'],
            'CAN_BUY' => $arResult['CAN_BUY'],
            'CHECK_QUANTITY' => $arResult['CHECK_QUANTITY'],
            'QUANTITY_FLOAT' => is_float($arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']),
            'MAX_QUANTITY' => $arResult['CATALOG_QUANTITY'],
            'STEP_QUANTITY' => $arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'],
            'CATEGORY' => $arResult['CATEGORY_PATH']
        ),
        'BASKET' => array(
            'ADD_PROPS' => $arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y',
            'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
            'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
            'EMPTY_PROPS' => $emptyProductProperties,
            'BASKET_URL' => $arParams['BASKET_URL'],
            'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
            'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
        )
    );
    unset($emptyProductProperties);
}

if ($arParams['DISPLAY_COMPARE'])
{
    $jsParams['COMPARE'] = array(
        'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
        'COMPARE_DELETE_URL_TEMPLATE' => $arResult['~COMPARE_DELETE_URL_TEMPLATE'],
        'COMPARE_PATH' => $arParams['COMPARE_PATH']
    );
}
?>
    <script>
        BX.message({
            ECONOMY_INFO_MESSAGE: '<?=GetMessageJS('CT_BCE_CATALOG_ECONOMY_INFO2')?>',
            TITLE_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_ERROR')?>',
            TITLE_BASKET_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_BASKET_PROPS')?>',
            BASKET_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_BASKET_UNKNOWN_ERROR')?>',
            BTN_SEND_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_SEND_PROPS')?>',
            BTN_MESSAGE_BASKET_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_BASKET_REDIRECT')?>',
            BTN_MESSAGE_CLOSE: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE')?>',
            BTN_MESSAGE_CLOSE_POPUP: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE_POPUP')?>',
            TITLE_SUCCESSFUL: '<?=GetMessageJS('CT_BCE_CATALOG_ADD_TO_BASKET_OK')?>',
            COMPARE_MESSAGE_OK: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_OK')?>',
            COMPARE_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_UNKNOWN_ERROR')?>',
            COMPARE_TITLE: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_TITLE')?>',
            BTN_MESSAGE_COMPARE_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT')?>',
            PRODUCT_GIFT_LABEL: '<?=GetMessageJS('CT_BCE_CATALOG_PRODUCT_GIFT_LABEL')?>',
            PRICE_TOTAL_PREFIX: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_PRICE_TOTAL_PREFIX')?>',
            RELATIVE_QUANTITY_MANY: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY'])?>',
            RELATIVE_QUANTITY_FEW: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW'])?>',
            SITE_ID: '<?=CUtil::JSEscape($component->getSiteId())?>'
        });

        var <?=$obName?> = new JCCatalogElement(<?=CUtil::PhpToJSObject($jsParams, false, true)?>);
    </script>

<?endif?>
<?
unset($actualItem, $itemIds, $jsParams);
$this->__component->arResult["CACHED_TPL"] = @ob_get_contents();
ob_get_clean();
