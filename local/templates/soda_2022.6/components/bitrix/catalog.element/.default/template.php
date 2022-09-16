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

$detect = new MobileDetect;

$templateLibrary = array('popup', 'fx');
$currencyList = '';

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
  $SET_COLOR_ID = htmlspecialcharsbx($_REQUEST['setColor']);

$arSizeCrop = Array('width' => 980, 'height' => 1470);
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

  <a class="mobile-arrow-back" href="<?=$arResult['SECTION']['SECTION_PAGE_URL']?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/arrow-back-black.svg"></a>
  <div data-is-partners="<?=$arParams['IS_PARTNERS']?>" class="wrapper-products products-drag" data-section="<?=$arResult['SECTION']['ID']?>">

<?endif?>

  <div class="product-detail product-<?=$arResult['ID']?> <?if($arParams['AJAX'] != 'Y'):?> current<?endif?>" data-id="<?=$arResult['ID']?>" id="<?=$itemIds['ID']?>" itemscope itemtype="http://schema.org/Product">

    <meta itemprop="name" content="<?=$name?>" />
    <meta itemprop="category" content="<?=$arResult['CATEGORY_PATH']?>" />

    <div class="images">
      <div class="">
        <div class="product-detail-slider<?if($detect->isMobile() && !empty($arResult['OFFERS_COLOR_LIST'])):?> mobile-colors<?endif?>">

          <?if($detect->isMobile() && !empty($arResult['OFFERS_COLOR_LIST'])):?>

            <? // Если моб. устройство и есть разные цвета, то слайдеры с иной обёрткой ?>

            <? $count = 0; ?>
            <?foreach($arResult['OFFERS_COLOR_LIST'] as $keyColor => $arImagesColor):?>
              <div class="image-slider-color color-<?=$keyColor?>" data-color="<?=$keyColor?>" data="dbg-1">
                <?foreach($arImagesColor['IMAGES'] as $imageID):?>
                  <? $arImage = CFile::ResizeImageGet($imageID, $arSizeCrop, BX_RESIZE_IMAGE_EXACT); ?>
                  <div class="image"><a data-fancybox="gallery" href="<?=$arImage['src']?>"><img src="<?=$arImage['src']?>" /></a></div>
                <?endforeach?>
                <? $count++; ?>
              </div>
            <?endforeach?>

          <?else:?>

            <? // Иначе всё по адаптивной логике ?>

            <div class="image-slider image-slider-<?=$arResult['ID']?><?if($arParams['AJAX'] == 'Y'):?> active<?endif?>" data="dbg-2">

              <?if(!empty($arResult['OFFERS_COLOR_LIST'])):?>

                <? $count = 0; ?>
                <?foreach($arResult['OFFERS_COLOR_LIST'] as $keyColor => $arImagesColor):?>

                  <?foreach($arImagesColor['IMAGES'] as $imageID):?>
                    <?
                      $arImage = CFile::ResizeImageGet($imageID, $arSizeCrop, BX_RESIZE_IMAGE_EXACT);

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
                        <img src="<?=$arImage['src']?>" />
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
                      <img src="<?=$arDetailImage['src']?>" />
                    <? /* </a> */ ?>
                  </div>
                <?else:?>
                  <div class="image color-default">
                    <img src="<?=SITE_TEMPLATE_PATH?>/images/default.jpg" />
                  </div>
                <?endif?>

                <?if(!empty($arResult['PROPERTIES']['IMAGES']['VALUE'])):?>
                  <?foreach($arResult['PROPERTIES']['IMAGES']['VALUE'] as $imageID):?>
                    <?$arImage = CFile::ResizeImageGet($imageID, $arSizeCrop, BX_RESIZE_IMAGE_EXACT); ?>
                    <div class="image color-default">
                      <? /* <a data-fancybox="gallery" href="<?=$arImage['src']?>"> */ ?>
                        <img src="<?=$arImage['src']?>" />
                      <? /* </a> */ ?>
                    </div>
                  <?endforeach?>
                <?endif?>

              <?endif?>

            </div>
          <?endif?>

        </div>
      </div>
    </div>
    <div class="info js-sticky-kit-go">
      <div class="wrapper">
        <div class="mobile-shutter-close js-shutter-close"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/times.svg"></div>
        <div class="mobile-touch-line"></div>
        <div class="mobile-message-added">
          <div class="wrapper">
            <div class="text">Размер <span class="insert-size">M (RU 44/46)</span> добавлен<br>в&nbsp;вашу корзину</div>
            <a class="link hover" href="/basket/">Посмотреть</a>
          </div>
        </div>

        <div class="indent">
          <h1><?=$arResult['NAME']?></h1>
          <div class="price"><?=$price['PRINT_BASE_PRICE']?></div>

          <?if($arResult["CAN_BUY"]){?>
            <div class="mobile-buy mobile-display" data-can-buy="1">
              <!-- js-addtobasket -->
              <a class="ibutton hollow js-addtobasket-go" data-id="<?=$arResult['ID']?>" data-size="" rel="nofollow"><?echo GetMessage("CATALOG_ADD_TO_BASKET")?></a>
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
                  <div class="caption js-open-popup-table-sizes">Какой у меня размер?</div>
                  <div class="icon js-open-popup-table-sizes"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/hanger.svg"></div>
                </div>
                <div class="close hover js-popup-close"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/close.svg"></div>
              </div>
            </div>
          <?}else{?>
              <div class="mobile-buy mobile-display" data-can-buy="0">
                  <!-- js-addtobasket -->
                  <a class="ibutton hollow js-addtobasket-go" data-id="<?=$arResult['ID']?>" data-size="" rel="nofollow"><?echo GetMessage("CATALOG_ADD_TO_BASKET")?></a>
                  <div class="size-module" data-num="2">

                    <? /*   if(count($arResult['OFFERS_LABEL'])>0){?>
                      <div class="item">
                        <span class="name">Цвет:</span>
                        <?$i=0;?>
                        <?foreach($arResult['OFFERS_LABEL'] as $key => $item){?>
                          <span class="value"><a data-offer-color class="js-change-color black<?if($i==0){?> active<?}?>" data-key="<?=$key?>"><?=$item["VALUE"]?></a><?if($i + 1 < count($arResult['OFFERS_LABEL'])):?>,<?endif?></span>
                          <?$i++;?>
                        <?}?>
                      </div>
                    <?} */ ?>

                    <?if(count($arResult['OFFERS_LABEL']) > 0):?>
                      <? $index = 0; ?>
                      <?foreach($arResult['OFFERS_LABEL'] as $key => $item):?>
                        <div data-offer-color-sizes="<?=$key?>" class="size<?if($index == 0):?> active<?endif?>" data-key="<?=$key?>">
                          <?foreach($item['SIZES'] as $keySize => $itemSize):?>
                            <div class="item">
                                <a  class="js-set-size-offer hover"
                                    data-offer-color-sizes-select
                                    data-js-addtobasket-go-item
                                    data-elid="<?=$itemSize['OFFER_ID']?>"
                                    data-name-size="<?=$itemSize['NAME']?>"
                                    data-code-size="<?=$itemSize['CODE']?>"
                                    data-value-size="<?=$itemSize['VALUE']?>"
                                    data-name-color="<?=$item['NAME']?>"
                                    data-code-color="<?=$item['CODE']?>"
                                    data-value-color="<?=$item['VALUE']?>"
                                    data-name-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['NAME']?>"
                                    data-code-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['CODE']?>"
                                    data-value-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?>"><?=$itemSize["VALUE"]?></a>
                            </div>
                            <? $index++; ?>
                          <?endforeach?>
                        </div>
                      <?endforeach?>
                    <?endif?>

                    <div class="table-size hover">
                      <div class="caption js-open-popup-table-sizes">Какой у меня размер?</div>
                      <div class="icon js-open-popup-table-sizes"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/hanger.svg"></div>
                    </div>
                    <div class="close hover js-popup-close"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/close.svg"></div>
                  </div>
              </div>
            <?}?>

          <div class="props">
            <?if(!empty($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'])):?>
              <div class="item">
                <span class="name">Артикул:</span>
                <span class="value"><?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?></span>
              </div>
            <?endif?>
            <?if(!empty($arResult['PROPERTIES']['COLOR']['VALUE'])):?>
              <div class="item">
                <span class="name">Цвет:</span>
                <?if(is_array($arResult['PROPERTIES']['COLOR']['VALUE'])):?>
                  <span class="value"><a class="js-change-color black active"><?=implode('</a>, <a class="js-change-color black">', $arResult['PROPERTIES']['COLOR']['VALUE']);?></a></span>
                <?else:?>
                  <span class="value"><?=$arResult['PROPERTIES']['COLOR']['VALUE']?></span>
                <?endif?>
              </div>
            <?endif?>

            <?if(!empty($arResult['PROPERTIES']['FABRIC']['VALUE'])):?>
              <div class="item">
                <span class="name">Ткань:</span>
                <?if(is_array($arResult['PROPERTIES']['FABRIC']['VALUE'])):?>
                  <span class="value"><?=implode(', ', $arResult['PROPERTIES']['FABRIC']['VALUE']);?></span>
                <?else:?>
                  <span class="value"><?=$arResult['PROPERTIES']['FABRIC']['VALUE']?></span>
                <?endif?>
              </div>
            <?endif?>

            <?if(!$detect->isMobile() && count($arResult['OFFERS_COLOR_LIST']) > 0):?>
              <div class="item" data-dbg="desktop color">
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
            <?endif?>
          </div>

          <?if($detect->isMobile() && !empty($arResult['OFFERS_COLOR_LIST'])):?>
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
          <?endif?>

          <?if(!empty($arResult['DETAIL_TEXT'])):?>
            <p class="description"><?=$arResult['DETAIL_TEXT']?></p>
          <?endif?>

          <div class="desktop-display">
            <div class="size-module" data-num="0">

              <?if(count($arResult['OFFERS_LABEL']) > 0):?>
                <? $index = 0; ?>
                <?foreach($arResult['OFFERS_LABEL'] as $key => $item):?>
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

                  <div class="size<?if($isDisplay):?> active<?endif?>" data-offer-color-sizes="<?=$key?>" data-key="<?=$key?>">
                    <?foreach($item['SIZES'] as $keySize=>$itemSize):?>
                      <div class="item">
                        <a  class="js-set-size-offer hover"
                            data-offer-color-sizes-select
                            data-elid="<?=$itemSize['OFFER_ID']?>"
                            data-name-size="<?=$itemSize['NAME']?>"
                            data-code-size="<?=$itemSize['CODE']?>"
                            data-value-size="<?=$itemSize['VALUE']?>"
                            data-name-color="<?=$item['NAME']?>"
                            data-code-color="<?=$item['CODE']?>"
                            data-value-color="<?=$item['VALUE']?>"
                            data-name-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['NAME']?>"
                            data-code-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['CODE']?>"
                            data-value-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?>"><?=$itemSize["VALUE"]?></a>
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

            <div class="table-size hover">
              <div class="caption js-open-popup-table-sizes">Таблица размеров</div>
              <div class="icon js-open-popup-table-sizes"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/hanger.svg"></div>
            </div>

            <?if($arResult["CAN_BUY"]){?>

              <a class="ibutton js-addtobasket" data-can-buy="1"
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
                data-id="<?=$arResult['ID']?>" data-size="" rel="nofollow"><?echo GetMessage("CATALOG_ADD_TO_BASKET")?></a>

            <?}else if(count($arResult['OFFERS_LABEL'])>0){?>

              <a class="ibutton js-metrika-addtobasket" data-can-buy="0" data-addtobasket-offer data-size="" rel="nofollow"><?echo GetMessage("CATALOG_ADD_TO_BASKET")?></a>

            <?}?>
          </div>

          <div class="links">
            <?if(!empty($arResult['PROPERTIES']['COMPOSITION']['VALUE']) || !empty($arResult['PROPERTIES']['CARE']['VALUE'])):?>
              <div class="item">
                <a class="link hover js-open-popup-cares">Состав и уход за изделием</a>
                <div class="info-cares">
                  <?if(!empty($arResult['PROPERTIES']['COMPOSITION']['VALUE'])):?>
                    <?foreach($arResult['PROPERTIES']['COMPOSITION']['VALUE'] as $index => $row):?>
                      <div class="caption"><?=$row?></div>
                      <?=$arResult['PROPERTIES']['COMPOSITION']['DESCRIPTION'][ $index ]?>
                      <br><br><br>
                    <?endforeach?>
                  <?endif?>
                  <?if(!empty($arResult['PROPERTIES']['CARE']['VALUE'])):?>
                    <div class="caption">Уход</div>
                    <div class="cares">
                      <?
                        $arFilter = Array("IBLOCK_ID" => 5, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "ID" => $arResult['PROPERTIES']['CARE']['VALUE']);
                        $rsCare = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>0), Array("ID", "NAME", "PROPERTY_ICON"));
                        while($arCare = $rsCare->Fetch()) {
                          ?><img src="<?=CFile::GetPath($arCare['PROPERTY_ICON_VALUE'])?>" title="<?=$arCare['NAME']?>"><?
                        }
                      ?>
                    </div>
                  <?endif?>
                </div>
              </div>
            <?endif?>
            <div class="item">
              <a class="link hover js-open-popup-delivery">Доставка, обмен и возврат</a>
            </div>
            <div class="item">
              <a class="link hover js-share-display">Поделиться</a>
              <div class="wrap-share">
                <div class="ya-share2" data-services="vkontakte,facebook,whatsapp,viber,twitter,skype,telegram,linkedin" data-size="s"></div>
              </div>
            </div>
          </div>

          <?if(FALSE && $detect->isMobile()):?>

            <div class="products-more">
              <div class="caption">Дополнить модный образ</div>
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
if( FALSE && $arResult["DISPLAY_PROPERTIES"]["CONCOMITANT"]["VALUE"]){

    global $FILTER_NAME_CONCOMITANT;
    $FILTER_NAME_CONCOMITANT["ID"]=$arResult["DISPLAY_PROPERTIES"]["CONCOMITANT"]["VALUE"];

    $APPLICATION->IncludeComponent(
        "bitrix:catalog.section",
        "concomitant",
        array(
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
            "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
            "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
            "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
            "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
            "PROPERTY_CODE_MOBILE" => $arParams["LIST_PROPERTY_CODE_MOBILE"],
            "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
            "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
            "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
            "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
            "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
            "BASKET_URL" => $arParams["BASKET_URL"],
            "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
            "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
            "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
            "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
            "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
            "FILTER_NAME" => "FILTER_NAME_CONCOMITANT",
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "CACHE_FILTER" => $arParams["CACHE_FILTER"],
            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
            "SET_TITLE" => $arParams["SET_TITLE"],
            "MESSAGE_404" => $arParams["~MESSAGE_404"],
            "SET_STATUS_404" => $arParams["SET_STATUS_404"],
            "SHOW_404" => $arParams["SHOW_404"],
            "FILE_404" => $arParams["FILE_404"],
            "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
            "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
            "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
            "PRICE_CODE" => $arParams["~PRICE_CODE"],
            "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
            "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

            "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
            "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
            "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
            "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
            "PRODUCT_PROPERTIES" => (isset($arParams["PRODUCT_PROPERTIES"]) ? $arParams["PRODUCT_PROPERTIES"] : []),

            "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
            "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
            "PAGER_TITLE" => $arParams["PAGER_TITLE"],
            "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
            "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
            "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
            "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
            "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
            "PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
            "PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
            "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
            "LAZY_LOAD" => $arParams["LAZY_LOAD"],
            "MESS_BTN_LAZY_LOAD" => $arParams["~MESS_BTN_LAZY_LOAD"],
            "LOAD_ON_SCROLL" => $arParams["LOAD_ON_SCROLL"],

            "OFFERS_CART_PROPERTIES" => (isset($arParams["OFFERS_CART_PROPERTIES"]) ? $arParams["OFFERS_CART_PROPERTIES"] : []),
            "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
            "OFFERS_PROPERTY_CODE" => (isset($arParams["LIST_OFFERS_PROPERTY_CODE"]) ? $arParams["LIST_OFFERS_PROPERTY_CODE"] : []),
            "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
            "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
            "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
            "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
            "OFFERS_LIMIT" => (isset($arParams["LIST_OFFERS_LIMIT"]) ? $arParams["LIST_OFFERS_LIMIT"] : 0),

            "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
            "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
            "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
            "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
            "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
            'CURRENCY_ID' => $arParams['CURRENCY_ID'],
            'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
            'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],

            'LABEL_PROP' => $arParams['LABEL_PROP'],
            'LABEL_PROP_MOBILE' => $arParams['LABEL_PROP_MOBILE'],
            'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'],
            'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
            'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
            'PRODUCT_BLOCKS_ORDER' => $arParams['LIST_PRODUCT_BLOCKS_ORDER'],
            'PRODUCT_ROW_VARIANTS' => $arParams['LIST_PRODUCT_ROW_VARIANTS'],
            'ENLARGE_PRODUCT' => $arParams['LIST_ENLARGE_PRODUCT'],
            'ENLARGE_PROP' => isset($arParams['LIST_ENLARGE_PROP']) ? $arParams['LIST_ENLARGE_PROP'] : '',
            'SHOW_SLIDER' => $arParams['LIST_SHOW_SLIDER'],
            'SLIDER_INTERVAL' => isset($arParams['LIST_SLIDER_INTERVAL']) ? $arParams['LIST_SLIDER_INTERVAL'] : '',
            'SLIDER_PROGRESS' => isset($arParams['LIST_SLIDER_PROGRESS']) ? $arParams['LIST_SLIDER_PROGRESS'] : '',

            'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
            'OFFER_TREE_PROPS' => (isset($arParams['OFFER_TREE_PROPS']) ? $arParams['OFFER_TREE_PROPS'] : []),
            'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
            'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
            'DISCOUNT_PERCENT_POSITION' => $arParams['DISCOUNT_PERCENT_POSITION'],
            'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
            'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
            'MESS_SHOW_MAX_QUANTITY' => (isset($arParams['~MESS_SHOW_MAX_QUANTITY']) ? $arParams['~MESS_SHOW_MAX_QUANTITY'] : ''),
            'RELATIVE_QUANTITY_FACTOR' => (isset($arParams['RELATIVE_QUANTITY_FACTOR']) ? $arParams['RELATIVE_QUANTITY_FACTOR'] : ''),
            'MESS_RELATIVE_QUANTITY_MANY' => (isset($arParams['~MESS_RELATIVE_QUANTITY_MANY']) ? $arParams['~MESS_RELATIVE_QUANTITY_MANY'] : ''),
            'MESS_RELATIVE_QUANTITY_FEW' => (isset($arParams['~MESS_RELATIVE_QUANTITY_FEW']) ? $arParams['~MESS_RELATIVE_QUANTITY_FEW'] : ''),
            'MESS_BTN_BUY' => (isset($arParams['~MESS_BTN_BUY']) ? $arParams['~MESS_BTN_BUY'] : ''),
            'MESS_BTN_ADD_TO_BASKET' => (isset($arParams['~MESS_BTN_ADD_TO_BASKET']) ? $arParams['~MESS_BTN_ADD_TO_BASKET'] : ''),
            'MESS_BTN_SUBSCRIBE' => (isset($arParams['~MESS_BTN_SUBSCRIBE']) ? $arParams['~MESS_BTN_SUBSCRIBE'] : ''),
            'MESS_BTN_DETAIL' => (isset($arParams['~MESS_BTN_DETAIL']) ? $arParams['~MESS_BTN_DETAIL'] : ''),
            'MESS_NOT_AVAILABLE' => (isset($arParams['~MESS_NOT_AVAILABLE']) ? $arParams['~MESS_NOT_AVAILABLE'] : ''),
            'MESS_BTN_COMPARE' => (isset($arParams['~MESS_BTN_COMPARE']) ? $arParams['~MESS_BTN_COMPARE'] : ''),

            'USE_ENHANCED_ECOMMERCE' => (isset($arParams['USE_ENHANCED_ECOMMERCE']) ? $arParams['USE_ENHANCED_ECOMMERCE'] : ''),
            'DATA_LAYER_NAME' => (isset($arParams['DATA_LAYER_NAME']) ? $arParams['DATA_LAYER_NAME'] : ''),
            'BRAND_PROPERTY' => (isset($arParams['BRAND_PROPERTY']) ? $arParams['BRAND_PROPERTY'] : ''),

            'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
            "ADD_SECTIONS_CHAIN" => "N",
            'ADD_TO_BASKET_ACTION' => $basketAction,
            'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
            'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
            'COMPARE_NAME' => $arParams['COMPARE_NAME'],
            'USE_COMPARE_LIST' => 'Y',
            'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
            'COMPATIBLE_MODE' => (isset($arParams['COMPATIBLE_MODE']) ? $arParams['COMPATIBLE_MODE'] : ''),
            'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : ''),
            'SHOW_ALL_WO_SECTION' => 'Y',
        ),
        $component
    );

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