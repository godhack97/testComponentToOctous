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
if (CSite::InGroup(array(1, 8, 9, 15, 16)))
    $isRuleModerator = true;


if (!empty($arResult['CURRENCIES'])) {
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
    'DISCOUNT_PERCENT_ID' => $mainId . '_dsc_pict',
    'STICKER_ID' => $mainId . '_sticker',
    'BIG_SLIDER_ID' => $mainId . '_big_slider',
    'BIG_IMG_CONT_ID' => $mainId . '_bigimg_cont',
    'SLIDER_CONT_ID' => $mainId . '_slider_cont',
    'OLD_PRICE_ID' => $mainId . '_old_price',
    'PRICE_ID' => $mainId . '_price',
    'DISCOUNT_PRICE_ID' => $mainId . '_price_discount',
    'PRICE_TOTAL' => $mainId . '_price_total',
    'SLIDER_CONT_OF_ID' => $mainId . '_slider_cont_',
    'QUANTITY_ID' => $mainId . '_quantity',
    'QUANTITY_DOWN_ID' => $mainId . '_quant_down',
    'QUANTITY_UP_ID' => $mainId . '_quant_up',
    'QUANTITY_MEASURE' => $mainId . '_quant_measure',
    'QUANTITY_LIMIT' => $mainId . '_quant_limit',
    'BUY_LINK' => $mainId . '_buy_link',
    'ADD_BASKET_LINK' => $mainId . '_add_basket_link',
    'BASKET_ACTIONS_ID' => $mainId . '_basket_actions',
    'NOT_AVAILABLE_MESS' => $mainId . '_not_avail',
    'COMPARE_LINK' => $mainId . '_compare_link',
    'TREE_ID' => $mainId . '_skudiv',
    'DISPLAY_PROP_DIV' => $mainId . '_sku_prop',
    'DISPLAY_MAIN_PROP_DIV' => $mainId . '_main_sku_prop',
    'OFFER_GROUP' => $mainId . '_set_group_',
    'BASKET_PROP_DIV' => $mainId . '_basket_prop',
    'SUBSCRIBE_LINK' => $mainId . '_subscribe',
    'TABS_ID' => $mainId . '_tabs',
    'TAB_CONTAINERS_ID' => $mainId . '_tab_containers',
    'SMALL_CARD_PANEL_ID' => $mainId . '_small_card_panel',
    'TABS_PANEL_ID' => $mainId . '_tabs_panel'
);
$obName = $templateData['JS_OBJ'] = 'ob' . preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);
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
if ($haveOffers) {
    $actualItem = isset($arResult['OFFERS'][$arResult['OFFERS_SELECTED']])
        ? $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]
        : reset($arResult['OFFERS']);
    $showSliderControls = false;

    foreach ($arResult['OFFERS'] as $offer) {
        if ($offer['MORE_PHOTO_COUNT'] > 1) {
            $showSliderControls = true;
            break;
        }
    }
} else {
    $actualItem = $arResult;
    $showSliderControls = $arResult['MORE_PHOTO_COUNT'] > 1;
}


$skuProps = [];
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
if (htmlspecialcharsbx($_REQUEST['setColor'])) {
    if ($arResult['PHOTO_ID'][$_REQUEST['setColor']]) {
        $SET_COLOR_ID = $arResult['PHOTO_ID'][$_REQUEST['setColor']];
    } elseif ($arResult['PHOTO_OFFER_ID'][$_REQUEST['setColor']]) {
        $SET_COLOR_ID = $arResult['PHOTO_OFFER_ID'][$_REQUEST['setColor']];
    } else {
        $SET_COLOR_ID = htmlspecialcharsbx($_REQUEST['setColor']);
    }
}

global $FILTER_NAME_CONCOMITANT;
$FILTER_NAME_CONCOMITANT["!=ID"] = $arResult["ID"];
$FILTER_NAME_CONCOMITANT["CATALOG_AVAILABLE"] =  'Y';
$FILTER_NAME_CONCOMITANT["SECTION_ID"] =  $arResult['IBLOCK_SECTION_ID'];

global $FILTER_NAME_COLOR;

$arSizeCrop = array('width' => 980, 'height' => 1470);
$arSizeCropFull = array('width' => 1366, 'height' => 2049);
$arSizeCropBigFull = Array('width' => 1920, 'height' => 2879);
$arSizeCropTablet = array('width' => 768, 'height' => 1152);
$arSizeCropMobile = array('width' => 620, 'height' => 930);
$arSizeCropMobile2 = array('width' => 414, 'height' => 621);
$arSizeCropPreload = array('width' => 192, 'height' => 288);
?>

<? if ($arParams['AJAX'] != 'Y') : ?>

    <? $sectionUrl = $arResult['SECTION']['SECTION_PAGE_URL'];
    if (htmlspecialcharsbx($_GET['offer']))
        $sectionUrl .= '#offer' . htmlspecialcharsbx($_GET['offer']);
    ?>

    <a class="mobile-arrow-back hover" data-section-url="<?= $sectionUrl ?>"><img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/arrow-back.svg"></a>

<? endif ?>

<div class="product-main product-<?=$arResult['ID']?>"
  data-id="<?=$arResult['ID']?>"
  id="<?=$itemIds['ID']?>"
  data-is-partners="<?=$arParams['IS_PARTNERS']?>"
  data-section="<?=$arResult['SECTION']['ID']?>"
  itemscope
  itemtype="http://schema.org/Product"
>

  <meta itemprop="name" content="<?=$name?>" />
  <meta itemprop="category" content="<?=$arResult['CATEGORY_PATH']?>" />

  <?if($_GET['dbg'] == 'Y'):?>
    <input type="hidden" id="dbg" value="Y">
  <?endif?>

	<div class="product-head">
		<div class="images-container">
      <?$frame = $this->createFrame()->begin("");?>
      <div class="images-filter">
        <?if(!empty($arResult['OFFERS_COLOR_LIST'])):?>
          <? $count = 0; ?>
          <?foreach($arResult['OFFERS_COLOR_LIST'] as $keyColor => $arImagesColor):?>
            <?
              $isDisplay = false;
              if($SET_COLOR_ID)
              {
                if($SET_COLOR_ID == $keyColor)
                  $isDisplay = true;
              }
              elseif($count == 0 && !$detect->isMobile())
                $isDisplay = true;

              $imagesSize = count($arImagesColor['IMAGES']);

              if ($imagesSize > 6)
                $imagesSize = 6;
            ?>
            <div class="images-wrap images-size-<?=$imagesSize?><?if($isDisplay):?> active<?endif?>" data-color-name="<?=$arResult['OFFERS_COLOR_LIST'][$keyColor]['NAME']?>"  data-filter="<?=$keyColor?>">

              <?foreach($arImagesColor['IMAGES'] as $index => $imageID):?>
                <?
                  if(
                      !empty($arImagesColor['VIDEO_IMAGE']['VALUE']) &&
                      !empty($arImagesColor['VIDEO_FILES']['VALUE']) &&
                      $index == 0 &&
                      $imagesSize > 1
                  )
                    continue;
                ?>

                <?
                  $arImageFull = CFile::ResizeImageGet($imageID, $arSizeCropFull, BX_RESIZE_IMAGE_EXACT, false, false, false, 60);
                  $arImageBigFull = CFile::ResizeImageGet($imageID, $arSizeCropBigFull, BX_RESIZE_IMAGE_EXACT, false, false, false, 60);
                  $arImagePreload = CFile::ResizeImageGet($imageID, $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT);

                  $arImage = CFile::ResizeImageGet($imageID, $arSizeCrop, BX_RESIZE_IMAGE_EXACT);

                  $arImageTablet = CFile::ResizeImageGet($imageID, $arSizeCropTablet, BX_RESIZE_IMAGE_EXACT);

                  $arImageMobile = CFile::ResizeImageGet($imageID, $arSizeCropMobile2, BX_RESIZE_IMAGE_EXACT);
                  $arImageMobileRetina = CFile::ResizeImageGet($imageID, $arSizeCropMobile, BX_RESIZE_IMAGE_EXACT);
                ?>

                <div class="image-item skeleton">
                  <picture>
                    <source srcset="<?=$arImageMobileRetina['src']?> 2x, <?=$arImageMobile['src']?> 1x" media="(max-width: 425px)" />
                    <source srcset="<?=$arImageTablet['src']?>" media="(max-width: 768px)" />

                    <?
                      $indexFlag = false;
                      if(
                        !empty($arImagesColor['VIDEO_IMAGE']['VALUE']) &&
                        !empty($arImagesColor['VIDEO_FILES']['VALUE']) &&
                        $index == 1
                      )
                        $indexFlag = true;

                    $src = $isDisplay ? $arImage['src'] : $arImagePreload['src'];
                    ?>

                    <img src="<?= $src?>"
                        alt="<?=$name?>"
                        data-src-bigfull="<?=$arImageBigFull['src']?>"
                        data-src-full="<?=$arImageFull['src']?>"
                        data-src-mini="<?=$arImagePreload['src']?>"
                        data-index="<?= $indexFlag ? 0 : $index?>"
                        <?if(!$isDisplay):?> data-src="<?=$arImage['src']?>"<?endif?>
                    />
                  </picture>
                </div>

                <?if(
                    !empty($arImagesColor['VIDEO_IMAGE']['VALUE']) &&
                    !empty($arImagesColor['VIDEO_FILES']['VALUE']) &&
                    $index == 1
                ):?>
                  <div class="image-item video-item skeleton">
                    <?
                      $arImageFull = CFile::ResizeImageGet($arImagesColor['VIDEO_IMAGE']['VALUE'], $arSizeCropFull, BX_RESIZE_IMAGE_EXACT, false, false, false, 60);
                      $arImageBigFull = CFile::ResizeImageGet($arImagesColor['VIDEO_IMAGE']['VALUE'], $arSizeCropBigFull, BX_RESIZE_IMAGE_EXACT, false, false, false, 60);

                      $arImagePreload = CFile::ResizeImageGet($arImagesColor['VIDEO_IMAGE']['VALUE'], $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT);
                      $arVideoImage = CFile::ResizeImageGet($arImagesColor['VIDEO_IMAGE']['VALUE'], $arSizeCrop, BX_RESIZE_IMAGE_EXACT);
                    ?>
                    <video autoplay="autoplay" class="video" poster="<?=$arVideoImage['src']?>" playsinline loop muted>
                      <?foreach($arImagesColor['VIDEO_FILES']['VALUE'] as $videoID):?>
                        <?$arFile = CFile::GetFileArray($videoID);?>
                        <source src="<?=$arFile['SRC']?>" type="<?=$arFile['CONTENT_TYPE']?>">
                      <?endforeach;?>
                      <img class="video-preview"
                           src="<?=$arVideoImage['src']?>"
                           data-src-bigfull="<?=$arImageBigFull['src']?>"
                           data-src-full="<?=$arImageFull['src']?>"
                           data-src-mini="<?=$arImagePreload['src']?>"
                           data-index="1">
                    </video>
                  </div>
                <?endif?>
              <?endforeach?>

            </div>
            <? $count++; ?>
          <?endforeach?>

        <?else:?>
          <div class="images-wrap images-size-1 active">
            <?if(!empty($arResult['DETAIL_PICTURE']['SRC'])):?>
              <?
              $arDetailImage = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'], $arSizeCrop, BX_RESIZE_IMAGE_EXACT);

              $arImageFull = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'], $arSizeCropFull, BX_RESIZE_IMAGE_EXACT,  false, false, false, 60);
              $arImageBigFull = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'], $arSizeCropBigFull, BX_RESIZE_IMAGE_EXACT, false, false, false, 60);
              $arImagePreload = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'], $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT);

              $arImageTablet = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'], $arSizeCropTablet, BX_RESIZE_IMAGE_EXACT);

              $arImageMobile = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'], $arSizeCropMobile2, BX_RESIZE_IMAGE_EXACT);
              $arImageMobileRetina = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'], $arSizeCropMobile, BX_RESIZE_IMAGE_EXACT);
              ?>
              <div class="image-item color-default">
                <picture>
                  <source srcset="<?=$arImageMobileRetina['src']?> 2x, <?=$arImageMobile['src']?> 1x" media="(max-width: 425px)" />
                  <source srcset="<?=$arImageTablet['src']?>" media="(max-width: 768px)" />
                  <img
                      src="<?=$arDetailImage['src']?>"
                      data-src-bigfull="<?=$arImageBigFull['src']?>"
                      data-src-full="<?=$arImageFull['src']?>"
                      data-src-mini="<?=$arImagePreload['src']?>"
                      data-index="0"
                  />
                </picture>
              </div>
            <?else:?>
              <div class="image-item color-default">
                <img
                    src="<?=SITE_TEMPLATE_PATH?>/images/default.jpg"
                    data-src-bigfull="<?=SITE_TEMPLATE_PATH?>/images/default.jpg"
                    data-src-full="<?=SITE_TEMPLATE_PATH?>/images/default.jpg"
                    data-src-mini="<?=SITE_TEMPLATE_PATH?>/images/default.jpg"
                    data-index="0"
                />
              </div>
            <?endif?>

            <?if(!empty($arResult['PROPERTIES']['IMAGES']['VALUE'])):?>
              <?foreach($arResult['PROPERTIES']['IMAGES']['VALUE'] as $imageID):?>
                <?$arImage = CFile::ResizeImageGet($imageID, $arSizeCrop, BX_RESIZE_IMAGE_EXACT); ?>
                <div class="image-item color-default">
                  <img src="<?=$arImage['src']?>" />
                </div>
              <?endforeach?>
            <?endif?>
          </div>
        <?endif?>
			</div>
      <?$frame->end();?>
		</div>

		<div class="info">
      <!-- js-sticky-kit-go -->
			<div class="info-wrap js-sticky-kit-go">
				<div class="info-breadcrumb">
					<div class="breadcrumb-list">
            <?//хлебные крошки?>
            <?
              $arSectionPath[0] = ['NAME' => 'SODAMODA', 'URL' => '/'];
              if(SITE_ID == 's2') $arSectionPath[0]['URL'] = '/en/';
              foreach($arResult['SECTION']['PATH'] as $arPath)
                $arSectionPath[] = ['NAME' => $arPath['NAME'], 'URL' => $arPath['SECTION_PAGE_URL']];
            ?>
            <?if(count($arSectionPath) > 1):?>
              <?foreach($arSectionPath as $index => $arPath):?>
                <?if ($index == 0) :?>
                  <a class="breadcrumb-item hover" href="<?=$arPath['URL']?>">
                    <?=Loc::getMessage("CT_BCE_CATALOG_BREADCRUMB")?>
                  </a>
                  <?if($index != count($arSectionPath) - 1):?><span>/</span><?endif?>
                <?else:?>
                  <a class="breadcrumb-item hover" href="<?=$arPath['URL']?>"><?=$arPath['NAME']?></a>
                  <?if($index != count($arSectionPath) - 1):?><span>/</span><?endif?>
                <?endif?>
              <?endforeach?>
            <?endif?>
					</div>
				</div>
				<div class="info-title">
          <div class="product-name-block">
            <h1 class="product-name">
              <?//название товара?>
              <?if(SITE_ID=="s1"):?>
                <?=$arResult['NAME']?>
              <?else:?>
                <?if($arResult['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE']):?>
                  <?=$arResult['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE']?>
                <?else:?><?=$arResult['NAME']?>
                <?endif?>
              <?endif?>
            </h1>
          </div>

					<div class="price-block">
            <div class="price-filter">
              <?//цена товара?>
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

                  <div class="price<?if($isDisplay):?> active<?endif?>" data-filter="<?=$key?>">
                    <span id="price-current" class="price-current"><?=$item['PRICES']['CURRENT']?></span>
                    <span id='price-old' <?=$item['PRICES']['IS_DISCOUNT'] ? 'class="price-old"' : '';?>>
                    <?= $item['PRICES']['IS_DISCOUNT'] ? $item['PRICES']['OLD'] : ''; ?>
                  </span>
                  </div>
                <?endforeach?>
              <?endif?>
              <?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("currency_element_".$item['ID'], "");?>
            </div>
            <div class="favorite-filter">
              <?//избранное?>
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

                  <div class="favorite-wrap<?if($isDisplay):?> active<?endif?>" data-filter="<?=$key?>">
                    <a class="share">
                      <img src="<?= SITE_TEMPLATE_PATH ?>/images/product/share.svg" alt="share">
                    </a>
                    <a class="favorite js-favorite-button" data-id="<?=$item['OFFER_ID']?>">
                      <img class="icon" src="<?= SITE_TEMPLATE_PATH ?>/images/product/favorites-new.svg" alt="favorite">
                      <img class="icon add" src="<?= SITE_TEMPLATE_PATH ?>/images/product/favorites-new-add.svg" alt="favorite">
                    </a>
                  </div>
                <?endforeach?>
              <?endif?>
            </div>
          </div>

          <div class="product-home">
            <p class="product-home-name">
              <?=(!empty($arResult["PROPERTIES"]["BRAND"]["VALUE"]) ? $arResult["PROPERTIES"]["BRAND"]["VALUE"] : 'SODAMODA')?>
            </p>

            <?//артикул?>
            <?if(!empty($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'])):?>
              <div class="article-block">
                <p><?=Loc::getMessage("CATALOG_ELEMENT_PROPS_ARTICUL")?></p>
                <span class="article"><?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?></span>
                <?if($isRuleModerator):?>
                  <span class="article remains"><a class="hover" href="/manager/store/?query=<?=$arResult['ID'];?>&color=<?=$_REQUEST['setColor'];?>" target="_blank" id="js-store-checked" data-base-url="/manager/store-page/?query=<?=$arResult['ID'];?>">Остатки</a></span>
                <?endif?>
              </div>
            <?endif?>
          </div>
				</div>

        <?if(count($arResult['COLOR_SIZES']) > 0):?>
				  <div class="color-options">
            <div class="color-chosen">
              <?//имя цвета?>
              <?if(!$detect->isMobile() && count($arResult['COLOR_SIZES']) > 0):?>
                <div class="color-chosen-wrap">
                  <p class="color-chosen-title"><?=Loc::getMessage("CATALOG_ELEMENT_PROPS_COLOR")?></p>
                  <div class="color-chosen-filter">
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
                      <span class="color-chosen-item<?if($isDisplay):?> active<?endif?>" data-filter="<?=$key?>"><?=trim($item['VALUE'])?></span>
                    <?endforeach?>
                  </div>
                </div>
              <?endif?>
            </div>
            <?//переключение цвета?>
            <?if(count($arResult['COLOR_SIZES']) > 1):?>
              <div class="color-list skeleton">
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

                  <label
                    class="color-item<?if($isDisplay):?> active<?endif?>"
                    data-filter="<?=$key?>"
                  >
                    <input type="radio" name="color" value="">
                    <span class="color-box" style="background-color: <?=$item['HEX'];?>;"></span>
                  </label>
                  <? $index++; ?>
                <?endforeach?>
              </div>
            <?endif?>
				  </div>
        <?endif?>

        <?if(count($arResult['COLOR_SIZES']) > 0):?>
          <div class="size-filter">
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
              <div class="size<?if($isDisplay):?> active<?endif?>"
                data-preorder="<?=$arResult['OFFERS_COLOR_LIST'][$key]['PREORDER'] ? 'true' : 'false';?>"
                data-offer-color-sizes="<?=$key?>"
                data-filter="<?=$key?>"
              >
                <div class="size-module">
                  <div class="size-head">
                    <span><?=Loc::getMessage("CATALOG_ELEMENT_PROPS_SIZE")?></span>
                    <p class="size-table hover js-open-popup-table-sizes"><?=Loc::getMessage("CATALOG_ELEMENT_TABLE_SIZE_BUTTON")?></p>
                  </div>
                  <div class="size-list">
                    <?foreach($item['SIZES'] as $keySize => $itemSize):?>
                      <label class="size-item skeleton js-set-size-offer<?if(count($item['SIZES']) == 1 && $itemSize['STORE_DATA']['AVAILABLE']):?> active<?endif?><?if($itemSize['PREORDER']):?> preorder<?endif?><?if($itemSize['STORE_DATA']['AVAILABLE']):?> available<?else:?> disabled<?endif?>"
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
                          data-value-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?>"
                      >
                        <input type="radio" name="size" value="">
                        <span class="size-text">
                          <?if($itemSize["ROST"]):?>
                            <?$newVal =  str_replace(['см', 'cm'], '', preg_replace('/\PL/u', '', $itemSize["VALUE"]));
                            echo $newVal;?>
                          <?else:?>
                            <?=$itemSize["VALUE"]?>
                          <?endif;?>
                        </span>
                        <?if($itemSize["ROST"]):?>
                          <span class="size-text rost">
                            <?=$itemSize["ROST"]?>
                            <?=Loc::getMessage("CATALOG_ELEMENT_PROPS_SIZE_SM")?>
                          </span>
                        <?endif;?>

                        <div class="sold-out">
                          <span>SOLD OUT</span>
                        </div>

                        <?if($itemSize['STORE_DATA']['AMOUNT'] < 1 && $itemSize['PREORDER']):?>
                          <div class="size-button-wrap">
                            <p class="size-button-text">
                              <?=Loc::getMessage("CATALOG_ELEMENT_INFO_PREORDER_MESS")?>
                            </p>
                            <div class="size-buttons">
                              <span class="size-button preorder-button hover">
                                <?=Loc::getMessage("CATALOG_TO_PREORDER_BUTTON")?>
                              </span>
                            </div>
                          </div>
                        <?endif?>

                      </label>
                    <? $index++; ?>
                    <?endforeach?>
                  </div>
                </div>

                <div class="size-add-basket">
                  <a class="ibutton js-metrika-addtobasket skeleton" data-can-buy="0" data-addtobasket-offer data-size="" rel="nofollow"><?=Loc::getMessage("CATALOG_ADD_TO_BASKET")?></a>
                </div>
              </div>
            <?endforeach?>
          </div>
        <?endif?>

				<div class="social-buttons">
					<a href="tel:+78003505670" class="social-button phone-btn hover">
						<img src="<?= SITE_TEMPLATE_PATH ?>/images/product/phone.svg" alt="phone">
						<span><?=Loc::getMessage("CATALOG_ELEMENT_CALL_LINK_TITLE")?></span>
					</a>
					<a href="https://api.whatsapp.com/send?phone=79688700070" target="_blank" class="social-button whatsapp-btn hover">
						<img src="<?= SITE_TEMPLATE_PATH ?>/images/product/whatsapp.svg" alt="whatsapp">
						<span><?=Loc::getMessage("CATALOG_ELEMENT_WHATSAPP_TITLE")?></span>
					</a>
					<a href="https://t.me/SODAMODA_CHAT" target="_blank" class="social-button telegram-btn hover">
						<img src="<?= SITE_TEMPLATE_PATH ?>/images/product/telegram.svg" alt="telegram">
						<span><?=Loc::getMessage("CATALOG_ELEMENT_TELEGRAM_TITLE")?></span>
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="product-mobile-modal modal-size hide">
		<div class="size-filter" data-num="2">
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
          <div class="size<?if($isDisplay):?> active<?endif?>"
            data-offer-color-sizes="<?=$key?>"
            data-filter="<?=$key?>"
          >
            <div class="size-module">
              <p class="size-head"><?=Loc::getMessage("CATALOG_ELEMENT_MOBILE_SELECT_SIZE")?></p>
              <div class="size-list">
                <?foreach($item['SIZES'] as $keySize => $itemSize):?>
                  <label class="size-item js-set-size-offer<?if($itemSize['STORE_DATA']['AVAILABLE']):?> available<?else:?> disabled<?endif?>"
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
                    data-value-article="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?>"
                  >
                    <div class="size-item-wrap">
                      <input type="radio" name="size" value="<?=$itemSize['VALUE']?>">
                      <span class="size-text"><?=$itemSize["VALUE"]?></span>
                      <?if($itemSize['STORE_DATA']['AMOUNT'] < 1 && $itemSize['PREORDER']):?>
                        <div class="size-buttons">
                          <div class="size-button preorder-button"
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
                          >
                            <span><?=Loc::getMessage("CATALOG_TO_PREORDER_BUTTON")?></span>
                          </div>
                          <div class="size-button subscribe-button" style="display: none;">
                            <span>сообщите мне</span>
                          </div>
                        </div>
                      <?endif?>
                    </div>
                  </label>
                  <? $index++; ?>
                <?endforeach?>
              </div>
            </div>
            <div class="size-table js-open-popup-table-sizes hover">
              <p><?=Loc::getMessage("CATALOG_ELEMENT_TABLE_SIZE_BUTTON")?></p>
              <div class="size-table-arrow">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/product/select-arrow.svg" alt="arrow">
              </div>
            </div>
          </div>
        <?endforeach?>
      <?endif?>
		</div>
	</div>

	<div class="product-mobile-modal modal-basket hide">
		<div class="basket-wrap">
			<div class="basket-head">
				<p class="basket-title"><?=Loc::getMessage("CT_BCE_CATALOG_BASKET_MODAL_TITLE")?></p>
				<a href="<?= SITE_DIR ?>basket/" class="basket-link"><?=Loc::getMessage("CT_BCE_CATALOG_BASKET_MODAL_LINK")?></a>
			</div>
			<p class="basket-message"><?=Loc::getMessage("CT_BCE_CATALOG_BASKET_MODAL_MESSAGE")?></p>
      <?//завершите образ?>
      <?if($arParams['AJAX'] != 'Y'):?>
        <?$index = 0;?>
        <?foreach ($arResult['COLOR_SIZES'] as $key => $color):?>
          <?
            $isDisplay = false;
            if ($SET_COLOR_ID)
            {
              if($SET_COLOR_ID == $key)
                $isDisplay = true;
            }
            elseif ($index == 0)
              $isDisplay = true;
          ?>
          <div class="product-mobile-look<?if($isDisplay):?> active<?endif?>" data-filter="<?=$key?>">
            <?if ($arResult['COLOR_CONCOMITANT'][$key]) {?>
              <?$FILTER_NAME_COLOR["ID"] = $arResult['COLOR_CONCOMITANT'][$key];?>

              <?$APPLICATION->IncludeComponent(
                  "bitrix:news.list",
                  "catalog_mobile",
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
                    "COMPONENT_TEMPLATE" => "catalog_mobile"
                  ),
                  false
              );
              ?>
            <?}?>
          </div>
        <?$index ++;
        endforeach?>
      <?endif?>
		</div>
	</div>

	<div class="product-mobile-modal modal-subscribe hide">
		<div class="subscribe-wrap">
			<div class="subscribe-close hover">
				<img src="<?= SITE_TEMPLATE_PATH ?>/images/product/modal-close.svg" alt="close">
			</div>
			<div class="subscribe-content">
				<p class="subscribe-title"><?=Loc::getMessage("CT_BCE_CATALOG_SUBSCRIBE_MODAL_TITLE")?></p>
				<p class="subscribe-subtitle"><?=Loc::getMessage("CT_BCE_CATALOG_SUBSCRIBE_MODAL_SUBTITLE")?></p>
				<form name="product-subscribe" method="post" class="form">
					<input type="hidden" name="productId" value="">
					<div class="field">
						<input oninvalid="setCustomValidity('Email')" type="text" class="field-style" name="email" value="" placeholder=" " id="popup-form-field-email">
						<label class="placeholder" for="popup-form-field-phone">e-mail</label>
					</div>
					<div class="subscribe-agreement">
						<input type="checkbox" id="toggle-button" class="toggle-button">
						<label for="toggle-button" class="agreement-text"><?=Loc::getMessage("CT_BCE_CATALOG_SUBSCRIBE_MODAL_MESSAGE")?></label>
					</div>
					<input type="submit" class="ibutton" value="ОК" />
				</form>
			</div>
		</div>
	</div>

	<div class="product-mobile-content">
		<div class="dragging-wrap">
			<div class="dragging-field"></div>
      <?//модалка переключения цвета?>
      <?if(!empty($arResult['OFFERS_COLOR_LIST']) && count($arResult['OFFERS_COLOR_LIST']) > 1):?>
        <div class="product-modal-color">
          <div class="modal-wrap">
            <div class="modal-content color-list">
              <?foreach($arResult['OFFERS_COLOR_LIST'] as $keyColor => $arItemColor):?>
                <label class="color-item<?if($SET_COLOR_ID == $keyColor):?> active<?endif?>"
                  data-preorder="<?=$arItemColor['PREORDER'] ? 'true' : 'false';?>" data-available="<?=$arItemColor['AVAILABLE'] ? 'true' : 'false';?>"
                  data-filter="<?=$keyColor?>"
                  title="<?=$arItemColor['NAME']?>"
                >
                  <input type="radio" name="color" value="" checked>
                  <span class="color-box" style="background-color: <?=$arItemColor['HEX']?>;"></span>
                </label>
              <?endforeach?>
            </div>
          </div>
        </div>
      <?endif?>
			<div class="product-mobile-container">
				<div class="product-mobile-head">
					<div class="info-wrap">
						<div class="info">
							<h2 class="product-name">
                <?//название товара?>
                <?if(SITE_ID=="s1"):?>
                  <?=$arResult['NAME']?>
                <?else:?>
                  <?if($arResult['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE']):?>
                    <?=$arResult['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE']?>
                  <?else:?><?=$arResult['NAME']?>
                  <?endif?>
                <?endif?>
              </h2>
							<div class="price-filter">
                <?//цена товара?>
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
                    <div class="price<?if($isDisplay):?> active<?endif?>" data-filter="<?=$key?>">
                      <span class="price-current"><?=$item['PRICES']['CURRENT']?></span>
                      <span <?=$item['PRICES']['IS_DISCOUNT'] ? 'class="price-old"' : '';?>>
                        <?= $item['PRICES']['IS_DISCOUNT'] ? $item['PRICES']['OLD'] : ''; ?>
                      </span>
                    </div>
                  <?endforeach?>
                <?endif?>
							</div>
              <?//артикул?>
              <?if(!empty($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'])):?>
                <div class="article-block">
                  <p><?=Loc::getMessage("CATALOG_ELEMENT_PROPS_ARTICUL")?></p>
                  <span class="article"><?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?></span>
                </div>
              <?endif?>
						</div>
						<div class="info-action">
							<div class="favorite-filter">
                <?//избранное?>
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
                    <div class="favorite-wrap<?if($isDisplay):?> active<?endif?>" data-filter="<?=$key?>">
                      <a class="share-mobile hover">
                        <img src="<?= SITE_TEMPLATE_PATH ?>/images/product/repost-mobile.svg" alt="share">
                      </a>
                      <a class="favorite hover js-favorite-button" data-id="<?=$item['OFFER_ID']?>">
                        <img class="icon" src="<?= SITE_TEMPLATE_PATH ?>/images/product/favorite-mobile.svg" alt="favorite">
                        <img class="icon add" src="<?= SITE_TEMPLATE_PATH ?>/images/product/favorite.svg" alt="favorite">
                      </a>
                    </div>
                  <?endforeach?>
                <?endif?>
							</div>
							<div class="color-options">
                <?//переключение цвета?>
                <?if(!empty($arResult['OFFERS_COLOR_LIST']) && count($arResult['OFFERS_COLOR_LIST']) > 1):?>
                  <div class="color-chosen">
                    <div class="color-chosen-filter">
                      <?foreach($arResult['OFFERS_COLOR_LIST'] as $keyColor => $arItemColor):?>
                        <span class="color-chosen-item<?if($SET_COLOR_ID == $keyColor):?> active<?endif?>"
                          data-preorder="<?=$arItemColor['PREORDER'] ? 'true' : 'false';?>" data-available="<?=$arItemColor['AVAILABLE'] ? 'true' : 'false';?>"
                          data-filter="<?=$keyColor?>"
                          style="background-color: <?=$arItemColor['HEX']?>;"
                          title="<?=$arItemColor['NAME']?>"
                        ></span>
                      <?endforeach?>
                    </div>
                    <div class="color-chosen-text"></div>
                  </div>
                  <div class="color-list">
                    <?foreach($arResult['OFFERS_COLOR_LIST'] as $keyColor => $arItemColor):?>
                      <label class="color-item<?if($SET_COLOR_ID == $keyColor):?> active<?endif?>"
                        data-preorder="<?=$arItemColor['PREORDER'] ? 'true' : 'false';?>" data-available="<?=$arItemColor['AVAILABLE'] ? 'true' : 'false';?>"
                        data-filter="<?=$keyColor?>"
                        title="<?=$arItemColor['NAME']?>"
                      >
                        <input type="radio" name="color" value="" checked>
                        <span class="color-box" style="background-color: <?=$arItemColor['HEX']?>;"></span>
                      </label>
                    <?endforeach?>
                  </div>
                <?endif?>
							</div>
						</div>
					</div>
          <?if(!empty($arResult['OFFERS_COLOR_LIST']) && count($arResult['OFFERS_COLOR_LIST']) > 1):?>
            <div class="basket-filter">
              <div class="basket-item active js-choice-size" data-filter="choice-black">
                <a class="ibutton" data-id="<?=$arResult['ID']?>" data-size="" rel="nofollow"><?=Loc::getMessage("CATALOG_ADD_TO_BASKET")?></a>
              </div>
            </div>
          <?else:?>
            <div class="basket-filter">
              <div class="basket-item active js-choice-size" data-filter="choice-black">
                <a class="ibutton" data-id="<?=$arResult['ID']?>" data-size="" rel="nofollow"><?=Loc::getMessage("CATALOG_ADD_TO_BASKET")?></a>
              </div>
            </div>
          <?endif?>
				</div>
				<div class="product-mobile-info">
					<div class="product-mobile-wrap">
						<div class="product-options">
              <?//доступные цвета?>
              <?if(!empty($arResult['OFFERS_COLOR_LIST']) && count($arResult['OFFERS_COLOR_LIST']) > 1):?>
                <div class="option-item available">
                  <p class="option-title"><?=Loc::getMessage("CATALOG_ELEMENT_ALSO_AVAILABLE")?></p>
                  <div class="available-images-filter">
                    <? $count = 0; ?>
                    <?foreach($arResult['OFFERS_COLOR_LIST'] as $keyColor => $arImagesColor):?>
                      <?
                        $isDisplay = false;
                        if($SET_COLOR_ID)
                        {
                          if($SET_COLOR_ID == $keyColor)
                            $isDisplay = true;
                        }
                        elseif($count == 0 && !$detect->isMobile())
                          $isDisplay = true;

                        $arImage = CFile::ResizeImageGet($arImagesColor['IMAGES'][0], $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT);
                      ?>
                      <div class="available-image-item skeleton hover<?if($isDisplay):?> active<?endif?>"
                          data-color-name="<?=$arResult['OFFERS_COLOR_LIST'][$keyColor]['NAME']?>"
                          data-filter="<?=$keyColor?>"
                        >
                          <img src="<?=$arImage['src']?>" alt="product"/>
                        </div>
                      <?if(count($arResult['OFFERS_COLOR_LIST']) == 2 && !empty($arImagesColor['IMAGES'][1])):?>
                        <?$arImage = CFile::ResizeImageGet($arImagesColor['IMAGES'][1], $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT);?>
                        <div class="available-image-item skeleton hover<?if($isDisplay):?> active<?endif?>"
                             data-color-name="<?=$arResult['OFFERS_COLOR_LIST'][$keyColor]['NAME']?>"
                             data-filter="<?=$keyColor?>"
                        >
                          <img src="<?=$arImage['src']?>" alt="product"/>
                        </div>
                      <?endif?>
                    <?endforeach?>
                  </div>
                </div>
              <?endif?>

              <?//описание?>
              <?if(!empty($arResult['DETAIL_TEXT']) || $arResult['PROPERTIES']['ENG_DESCRIPTION']['VALUE']):?>
                <div class="option-item description">
                  <p class="option-title"><?=Loc::getMessage("CT_BCE_CATALOG_DESCRIPTION_TAB")?></p>
                  <p class="description-text">
                    <?if(SITE_ID=="s1"):?>
                      <?=$arResult['DETAIL_TEXT']?>
                    <?else:?>
                      <?if($arResult['PROPERTIES']['ENG_DESCRIPTION']['VALUE']):?>
                        <?=$arResult['PROPERTIES']['ENG_DESCRIPTION']['VALUE']['TEXT']?>
                      <?endif?>
                    <?endif?>
                  </p>
                </div>
              <?endif?>

							<div class="option-item materials">
								<p class="option-title"><?=Loc::getMessage("CATALOG_ELEMENT_LINKS_TITLE")?></p>
                <?//материалы?>
                <?if(is_array($arResult['COMPOSITION_LIST']) && count($arResult['COMPOSITION_LIST'])){?>
                  <div class="materials-wrap composition">
                    <p class="materials-item-title"><?=Loc::getMessage("CATALOG_ELEMENT_LINKS_COMPOSITION")?></p>

                    <div class="materials-list">
                      <div class="composition-materials">
                        <h3 class="tab-subtitle"><?=Loc::getMessage("CATALOG_ELEMENT_LINKS_COMPOSITION")?></h3>
                        <div class="materials-list">
                          <?foreach($arResult['COMPOSITION_LIST'] as $arVal):?>
                            <span class="materials-item"><? echo $arVal['MATERIAL']. '&nbsp;' . $arVal['VALUE'] ?></span>
                          <?endforeach?>
                        </div>
                      </div>
                    </div>
                  </div>
                <?} elseif (!empty($arResult['PROPERTIES']['COMPOSITION']['VALUE'])){?>
                  <div class="materials-wrap composition">
                    <p class="materials-item-title"><?=Loc::getMessage("CATALOG_ELEMENT_LINKS_COMPOSITION")?></p>

                    <div class="materials-list">
                    <?if(empty($arResult["CompositionJSON"])) {
						foreach($arResult['PROPERTIES']['COMPOSITION']['VALUE'] as $index => $value) {?>
                        <span class="materials-item"><?=$value?></span>
	                    <?}?>
					<?} else { ?>
						<span class="materials-item"><?=$arResult["CompositionJSON"]?></span>
					<?}?>
                    </div>
                  </div>
                <?}?>

                <?//уход?>
                <?if(!empty($arResult['PROPERTIES']['CARE']['VALUE'])):?>
                  <div class="materials-wrap care">
                    <p class="materials-item-title"><?=Loc::getMessage("CATALOG_ELEMENT_LINKS_CARE")?></p>
                    <div class="materials-list">
                      <?
                        $arFilter = Array("IBLOCK_ID" => $arResult['PROPERTIES']['CARE']['LINK_IBLOCK_ID'], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "ID" => $arResult['PROPERTIES']['CARE']['VALUE']);
                        $rsCare = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>0), Array("ID", "NAME", "PROPERTY_NAME_EN", "PROPERTY_ICON"));
                        $careNameLabel = (SITE_ID == 's2' ? 'PROPERTY_NAME_EN_VALUE' : 'NAME');
                        while($arCare = $rsCare->Fetch()) {
                      ?>
                        <span class="materials-item"><?=$arCare[$careNameLabel]?></span>
                      <?}?>

                    </div>
                  </div>
                <?endif?>
							</div>

							<div class="option-item social">
								<p class="option-title"><?=Loc::getMessage("CATALOG_ELEMENT_MOBILE_SOCIAL_TITLE")?></p>
								<div class="social-buttons">
									<a href="tel:+78003505670" class="social-button phone-btn">
										<img src="<?= SITE_TEMPLATE_PATH ?>/images/product/phone.svg" alt="phone">
										<span><?=Loc::getMessage("CATALOG_ELEMENT_CALL_LINK_TITLE")?></span>
									</a>
									<a href="https://api.whatsapp.com/send?phone=79688700070" target="_blank" class="social-button whatsapp-btn">
										<img src="<?= SITE_TEMPLATE_PATH ?>/images/product/whatsapp.svg" alt="whatsapp">
										<span><?=Loc::getMessage("CATALOG_ELEMENT_WHATSAPP_TITLE")?></span>
									</a>
									<a href="https://t.me/SODAMODA_CHAT" target="_blank" class="social-button telegram-btn">
										<img src="<?= SITE_TEMPLATE_PATH ?>/images/product/telegram.svg" alt="telegram">
										<span><?=Loc::getMessage("CATALOG_ELEMENT_TELEGRAM_TITLE")?></span>
									</a>
								</div>
							</div>
						</div>
            <?//артикул?>
            <?if(!empty($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'])):?>
              <div class="article-block">
                <p><?=Loc::getMessage("CATALOG_ELEMENT_PROPS_ARTICUL")?></p>
                <span class="article"><?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?></span>
              </div>
            <?endif?>

            <?//завершите образ?>
            <?if($arParams['AJAX'] != 'Y'):?>
              <?$index = 0;?>
              <?foreach ($arResult['COLOR_SIZES'] as $key => $color):?>
                <?
                  $isDisplay = false;
                  if ($SET_COLOR_ID)
                  {
                    if($SET_COLOR_ID == $key)
                      $isDisplay = true;
                  }
                  elseif ($index == 0)
                    $isDisplay = true;
                ?>
                <div class="product-look mobile-images-container<?if($isDisplay):?> active<?endif?>" data-filter="<?=$key?>">
                    <?if ($arResult['COLOR_CONCOMITANT'][$key]) {?>
                      <?$FILTER_NAME_COLOR["ID"] = $arResult['COLOR_CONCOMITANT'][$key];?>

                      <?$APPLICATION->IncludeComponent(
                          "bitrix:news.list",
                          "catalog_draggable",
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
                            "COMPONENT_TEMPLATE" => "catalog_draggable"
                          ),
                          false
                      );
                      ?>
                    <?}?>
                </div>
              <?$index ++;
              endforeach?>
            <?endif?>

            <?//вам может понравится?>
						<div class="product-maylike mobile-images-container">
							<?php Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("maylike_mobile");?>
              <?php
                $APPLICATION->IncludeComponent(
                  'bitrix:catalog.section',
                  'concomitant_mobile',
                  [
                    "BY_LINK"=> "Y",
                    'SHOW_ALL_WO_SECTION' => 'Y',
                    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                    'ELEMENT_SORT_FIELD' => $arParams['ELEMENT_SORT_FIELD'],
                    'ELEMENT_SORT_ORDER' => 'ASC',
                    'ELEMENT_SORT_FIELD2' => $arParams['ELEMENT_SORT_FIELD2'],
                    'ELEMENT_SORT_ORDER2' => 'ASC',
                    'PROPERTY_CODE' => ['IMAGES'], // $arParams["LIST_PROPERTY_CODE"],
                    'PROPERTY_CODE_MOBILE' => $arParams['LIST_PROPERTY_CODE_MOBILE'],
                    'META_KEYWORDS' => $arParams['LIST_META_KEYWORDS'],
                    'META_DESCRIPTION' => $arParams['LIST_META_DESCRIPTION'],
                    'BROWSER_TITLE' => $arParams['LIST_BROWSER_TITLE'],
                    'SET_LAST_MODIFIED' => $arParams['SET_LAST_MODIFIED'],
                    'INCLUDE_SUBSECTIONS' => 'Y',//$arParams['INCLUDE_SUBSECTIONS'],
                    'BASKET_URL' => $arParams['BASKET_URL'],
                    'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
                    'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
                    'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],
                    'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                    'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
                    'FILTER_NAME' => 'FILTER_NAME_CONCOMITANT',
                    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                    'CACHE_TIME' => $arParams['CACHE_TIME'],
                    'CACHE_FILTER' => $arParams['CACHE_FILTER'],
                    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                    'SET_TITLE' => $arParams['SET_TITLE'],
                    'MESSAGE_404' => $arParams['~MESSAGE_404'],
                    'SET_STATUS_404' => $arParams['SET_STATUS_404'],
                    'SHOW_404' => $arParams['SHOW_404'],
                    'FILE_404' => $arParams['FILE_404'],
                    'DISPLAY_COMPARE' => $arParams['USE_COMPARE'],
                    'PAGE_ELEMENT_COUNT' => 10,
                    'CUSTOM_PAGE_ELEMENT_COUNT' => 10,
                    'LINE_ELEMENT_COUNT' => 10,
                    'PRICE_CODE' => $arParams['~PRICE_CODE'],
                    'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
                    'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],

                    'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
                    'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
                    'ADD_PROPERTIES_TO_BASKET' => (isset($arParams['ADD_PROPERTIES_TO_BASKET']) ? $arParams['ADD_PROPERTIES_TO_BASKET'] : ''),
                    'PARTIAL_PRODUCT_PROPERTIES' => (isset($arParams['PARTIAL_PRODUCT_PROPERTIES']) ? $arParams['PARTIAL_PRODUCT_PROPERTIES'] : ''),
                    'PRODUCT_PROPERTIES' => (isset($arParams['PRODUCT_PROPERTIES']) ? $arParams['PRODUCT_PROPERTIES'] : []),

                    'DISPLAY_TOP_PAGER' => $arParams['DISPLAY_TOP_PAGER'],
                    'DISPLAY_BOTTOM_PAGER' => $arParams['DISPLAY_BOTTOM_PAGER'],
                    'PAGER_TITLE' => $arParams['PAGER_TITLE'],
                    'PAGER_SHOW_ALWAYS' => $arParams['PAGER_SHOW_ALWAYS'],
                    'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
                    'PAGER_DESC_NUMBERING' => $arParams['PAGER_DESC_NUMBERING'],
                    'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
                    'PAGER_SHOW_ALL' => $arParams['PAGER_SHOW_ALL'],
                    'PAGER_BASE_LINK_ENABLE' => $arParams['PAGER_BASE_LINK_ENABLE'],
                    'PAGER_BASE_LINK' => $arParams['PAGER_BASE_LINK'],
                    'PAGER_PARAMS_NAME' => $arParams['PAGER_PARAMS_NAME'],
                    'LAZY_LOAD' => $arParams['LAZY_LOAD'],
                    'MESS_BTN_LAZY_LOAD' => $arParams['~MESS_BTN_LAZY_LOAD'],
                    'LOAD_ON_SCROLL' => $arParams['LOAD_ON_SCROLL'],

                    'OFFERS_CART_PROPERTIES' => (isset($arParams['OFFERS_CART_PROPERTIES']) ? $arParams['OFFERS_CART_PROPERTIES'] : []),
                    'OFFERS_FIELD_CODE' => $arParams['LIST_OFFERS_FIELD_CODE'],
                    'OFFERS_PROPERTY_CODE' => (isset($arParams['LIST_OFFERS_PROPERTY_CODE']) ? $arParams['LIST_OFFERS_PROPERTY_CODE'] : []),
                    'OFFERS_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],
                    'OFFERS_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],
                    'OFFERS_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],
                    'OFFERS_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],
                    'OFFERS_LIMIT' => 0,

                    'SECTION_ID' => $arResult['IBLOCK_SECTION_ID'],//$arResult['VARIABLES']['SECTION_ID'],
                    'SECTION_CODE' => '',//$arResult['VARIABLES']['SECTION_CODE'],
                    'SECTION_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['section'],
                    'DETAIL_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['element'],
                    'USE_MAIN_ELEMENT_SECTION' => $arParams['USE_MAIN_ELEMENT_SECTION'],
                    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                    'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
                    'HIDE_NOT_AVAILABLE_OFFERS' => $arParams['HIDE_NOT_AVAILABLE_OFFERS'],

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
                    'ADD_SECTIONS_CHAIN' => 'N',
                    'ADD_TO_BASKET_ACTION' => $arParams['DETAIL_ADD_TO_BASKET_ACTION'],
                    'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
                    'COMPARE_PATH' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['compare'],
                    'COMPARE_NAME' => $arParams['COMPARE_NAME'],
                    'USE_COMPARE_LIST' => 'Y',
                    'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
                    'COMPATIBLE_MODE' => (isset($arParams['COMPATIBLE_MODE']) ? $arParams['COMPATIBLE_MODE'] : ''),
                    'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : ''),
                    'AJAX_REQUEST' => $_REQUEST['ajax']
                  ],
	                $component
                );
              ?>
							<?php Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("maylike_mobile", "");?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="product-content">
		<div class="product-tabs">
			<ul class="tabs-list">
				<li class="tab-item active" data-text="<?=Loc::getMessage("CT_BCE_CATALOG_DESCRIPTION_TAB")?>">
          <?=Loc::getMessage("CT_BCE_CATALOG_DESCRIPTION_TAB")?>
        </li>
				<li class="tab-item tab-item-fitting" data-text="<?=Loc::getMessage("CT_BCE_CATALOG_FITTING_TAB")?>">
          <?=Loc::getMessage("CT_BCE_CATALOG_FITTING_TAB")?>
        </li>
				<li class="tab-item" data-text="<?=Loc::getMessage("CT_BCE_CATALOG_DELIVERY_TAB")?>">
          <?=Loc::getMessage("CT_BCE_CATALOG_DELIVERY_TAB")?>
        </li>
			</ul>

			<div class="tabs-wrap">
				<div class="tabs-container">
					<div class="tab-content tab-description active">
						<div class="description">
							<div class="description-text">
								<h2 class="tab-title">
                  <?//название товара?>
                  <?if(SITE_ID=="s1"):?>
                    <?=$arResult['NAME']?>
                  <?else:?>
                    <?if($arResult['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE']):?>
                      <?=$arResult['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE']?>
                    <?else:?><?=$arResult['NAME']?>
                    <?endif?>
                  <?endif?>
                </h2>
                <?//описание?>
                <?if(!empty($arResult['DETAIL_TEXT']) || $arResult['PROPERTIES']['ENG_DESCRIPTION']['VALUE']):?>
                  <p>
                    <?if(SITE_ID=="s1"):?>
                      <?=$arResult['DETAIL_TEXT']?>
                    <?else:?>
                      <?if($arResult['PROPERTIES']['ENG_DESCRIPTION']['VALUE']):?>
                        <?=$arResult['PROPERTIES']['ENG_DESCRIPTION']['VALUE']['TEXT']?>
                      <?endif?>
                    <?endif?>
                  </p>
                <?endif?>
							</div>
              <?/*
                <?//главное?>
                <?if(!empty($arResult['PROPERTIES']['MAIN_PROPERTIES']['VALUE'])):?>
                  <div class="description-main">
                    <h3 class="tab-subtitle"><?=Loc::getMessage("CATALOG_ELEMENT_PROPS_MAIN")?></h3>
                    <ul class="description-list">
                      <?foreach($arResult['PROPERTIES']['MAIN_PROPERTIES']['VALUE'] as $index => $value):?>
                        <li><?= $value?></li>
                      <?endforeach?>
                    </ul>
                  </div>
                <?endif?>
              */?>
						</div>
						<div class="composition">
              <?//артикул?>
              <?if(!empty($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'])):?>
                <div class="article-block">
                  <h3 class="tab-subtitle"><?=Loc::getMessage("CATALOG_ELEMENT_PROPS_ARTICUL")?></h3>
                  <span class="article"><?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?></span>
                </div>
              <?endif?>

              <?//материалы?>
              <?if(is_array($arResult['COMPOSITION_LIST']) && count($arResult['COMPOSITION_LIST'])){?>
                  <div class="composition-materials">
                    <h3 class="tab-subtitle"><?=Loc::getMessage("CATALOG_ELEMENT_LINKS_COMPOSITION")?></h3>
                    <div class="materials-list">
                      <?foreach($arResult['COMPOSITION_LIST'] as $arVal):?>
                        <span class="materials-item"><? echo $arVal['MATERIAL']. '&nbsp;' . $arVal['VALUE'] ?></span>
                      <?endforeach?>
                    </div>
                  </div>
              <?} elseif(!empty($arResult['PROPERTIES']['COMPOSITION']['VALUE'])){?>
                <div class="composition-materials">
                  <h3 class="tab-subtitle"><?=Loc::getMessage("CATALOG_ELEMENT_LINKS_COMPOSITION")?></h3>
                  <div class="materials-list">
                    <?foreach($arResult['PROPERTIES']['COMPOSITION']['VALUE'] as $index => $value):?>
                      <span class="materials-item"><?=$value?></span>
                    <?endforeach?>
                  </div>
                </div>
              <?}?>

              <?//уход?>
              <?if(!empty($arResult['PROPERTIES']['CARE']['VALUE'])):?>
                <div class="composition-materials care">
                  <h3 class="tab-subtitle"><?=Loc::getMessage("CATALOG_ELEMENT_LINKS_CARE")?></h3>
                  <div class="materials-list">
                    <?
                      $arFilter = Array("IBLOCK_ID" => $arResult['PROPERTIES']['CARE']['LINK_IBLOCK_ID'], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "ID" => $arResult['PROPERTIES']['CARE']['VALUE']);
                      $rsCare = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>0), Array("ID", "NAME", "PROPERTY_NAME_EN", "PROPERTY_ICON"));
                      $careNameLabel = (SITE_ID == 's2' ? 'PROPERTY_NAME_EN_VALUE' : 'NAME');
                      while($arCare = $rsCare->Fetch()) {
                    ?>
                      <span class="materials-item">
                        <img src="<?=CFile::GetPath($arCare['PROPERTY_ICON_VALUE'])?>" alt="<?=$arCare['NAME']?>">
                        <div class="tip"><?=$arCare[$careNameLabel]?></div>
                      </span>
                    <?}?>
                  </div>
                </div>
              <?endif?>
						</div>
					</div>

					<div class="tab-content tab-fitting">
						<h2 class="tab-title">Подбор со стилистом</h2>
						<div class="fitting-content">
							<p>Персональный подбор образов с нашим стилистом</p>
							<form name="fitting" method="post" class="form">
								<input type="hidden" name="mode" value="fitting" />
								<input type="hidden" name="page_name" value="<?= $APPLICATION->GetTitle() ?>" />
								<input type="hidden" name="page_url" value="<?= $APPLICATION->GetCurPageParam() ?>" />
								<div class="field">
									<input type="text" class="field-style" name="name" value="" placeholder=" " id="popup-fitting-form-field-name" required="required">
									<label class="placeholder" for="popup-fitting-form-field-name">Ваше имя</label>
								</div>
								<div class="field">
									<input type="text" class="field-style js-mask-phone" name="phone" value="" placeholder=" " id="popup-fitting-form-field-phone" required="required">
									<label class="placeholder" for="popup-fitting-form-field-phone">Телефон</label>
								</div>
								<div class="field">
									<input type="text" class="field-style" name="date" value="" placeholder=" " id="popup-fitting-form-field-date">
									<label class="placeholder" for="popup-fitting-form-field-date">Дата и время записи</label>
								</div>
								<div class="field">
									<select name="city" class="field-style" required="required">
										<option value="">Выберите город</option>
										<option value="MSC">Москва</option>
										<option value="SPB">Санкт-Петербург</option>
										<option value="RND">Ростов-на-Дону</option>
										<option value="KZN">Казань</option>
										<option value="KLR">Калининград</option>
									</select>
								</div>
								<input type="submit" class="ibutton js-fitting-send" value="Записаться" />
							</form>
						</div>
					</div>
					<div class="tab-content tab-delivery">
						<div class="delivery-text">
							<h2 class="tab-title"><?=Loc::getMessage("CT_BCE_CATALOG_DELIVERY_TAB_TITLE")?></h2>
                <?=Loc::getMessage("CT_BCE_CATALOG_DELIVERY_TAB_CONTENT")?>
						</div>
						<div class="delivery-image">
							<div class="delivery-image-item">
								<img src="<?= SITE_TEMPLATE_PATH ?>/images/product/delivery-1.jpg" alt="product">
							</div>
							<div class="delivery-image-item">
								<img src="<?= SITE_TEMPLATE_PATH ?>/images/product/delivery-2.jpg" alt="product">
							</div>
						</div>
					</div>
				</div>

        <?if(!empty($arResult['OFFERS_COLOR_LIST']) && count($arResult['OFFERS_COLOR_LIST']) > 1):?>
          <div class="tabs-available">
            <h3 class="tab-subtitle skeleton"><?=Loc::getMessage("CATALOG_ELEMENT_ALSO_AVAILABLE")?></h3>
            <div class="tabs-image-filter<?if(count($arResult['OFFERS_COLOR_LIST']) > 3):?> available-slider-init<?endif?>">
                <? $count = 0; ?>
                <?foreach($arResult['OFFERS_COLOR_LIST'] as $keyColor => $arImagesColor):?>
                  <?
                    $isDisplay = false;
                    if($SET_COLOR_ID)
                    {
                      if($SET_COLOR_ID == $keyColor)
                        $isDisplay = true;
                    }
                    elseif($count == 0 && !$detect->isMobile())
                      $isDisplay = true;

                    $arImage = CFile::ResizeImageGet($arImagesColor['IMAGES'][0], $arSizeCropMobile2, BX_RESIZE_IMAGE_EXACT);

                    $arImagePreload = CFile::ResizeImageGet($arImagesColor['IMAGES'][0], $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT);
                  ?>
                  <div class="available-image-item<?if($isDisplay):?> active<?endif?>" data-color-name="<?=$arResult['OFFERS_COLOR_LIST'][$keyColor]['NAME']?>" data-filter="<?=$keyColor?>">
                    <a class="skeleton">
                      <img src="<?=$arImagePreload['src']?>" alt="product" data-src="<?=$arImage['src']?>">
                    </a>
                  </div>

                  <?if(count($arResult['OFFERS_COLOR_LIST']) == 2 && !empty($arImagesColor['IMAGES'][1])):?>
                    <?
                    $arImage = CFile::ResizeImageGet($arImagesColor['IMAGES'][1], $arSizeCropMobile2, BX_RESIZE_IMAGE_EXACT);

                    $arImagePreload = CFile::ResizeImageGet($arImagesColor['IMAGES'][1], $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT);
                    ?>
                    <div class="available-image-item<?if($isDisplay):?> active<?endif?>" data-color-name="<?=$arResult['OFFERS_COLOR_LIST'][$keyColor]['NAME']?>" data-filter="<?=$keyColor?>">
                      <a class="skeleton">
                        <img src="<?=$arImagePreload['src']?>" alt="product" data-src="<?=$arImage['src']?>">
                      </a>
                    </div>
                  <?endif?>

                <?endforeach?>
            </div>
          </div>
        <?endif?>
			</div>
		</div>

    <?//завершите образ?>
    <?if($arParams['AJAX'] != 'Y'):?>
      <?$index = 0;?>

      <?foreach ($arResult['COLOR_SIZES'] as $key => $color):?>
        <?
          $isDisplay = false;
          if ($SET_COLOR_ID)
          {
            if($SET_COLOR_ID == $key)
              $isDisplay = true;
          }
          elseif ($index == 0)
            $isDisplay = true;
        ?>
        <div class="product-look<?if($isDisplay):?> active<?endif?>" data-filter="<?=$key?>">
            <?if ($arResult['COLOR_CONCOMITANT'][$key]) {?>
              <?$FILTER_NAME_COLOR["ID"] = $arResult['COLOR_CONCOMITANT'][$key];?>

              <?
                $APPLICATION->IncludeComponent(
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
            <?}?>
        </div>
      <?$index ++;
      endforeach?>
    <?endif?>
      <?php Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("maylike_desktop"); ?>
      <div class="product-maylike">
          <?php
          $APPLICATION->IncludeComponent(
              'bitrix:catalog.section',
              'concomitant_new',
              [
                  "BY_LINK" => "Y",
                  'SHOW_ALL_WO_SECTION' => 'Y',
                  'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                  'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                  'ELEMENT_SORT_FIELD' => $arParams['ELEMENT_SORT_FIELD'],
                  'ELEMENT_SORT_ORDER' => 'ASC',
                  'ELEMENT_SORT_FIELD2' => $arParams['ELEMENT_SORT_FIELD2'],
                  'ELEMENT_SORT_ORDER2' => 'ASC',
                  'PROPERTY_CODE' => ['IMAGES'], // $arParams["LIST_PROPERTY_CODE"],
                  'PROPERTY_CODE_MOBILE' => $arParams['LIST_PROPERTY_CODE_MOBILE'],
                  'META_KEYWORDS' => $arParams['LIST_META_KEYWORDS'],
                  'META_DESCRIPTION' => $arParams['LIST_META_DESCRIPTION'],
                  'BROWSER_TITLE' => $arParams['LIST_BROWSER_TITLE'],
                  'SET_LAST_MODIFIED' => $arParams['SET_LAST_MODIFIED'],
                  'INCLUDE_SUBSECTIONS' => 'Y', //$arParams['INCLUDE_SUBSECTIONS'],
                  'BASKET_URL' => $arParams['BASKET_URL'],
                  'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
                  'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
                  'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],
                  'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                  'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
                  'FILTER_NAME' => 'FILTER_NAME_CONCOMITANT',
                  'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                  'CACHE_TIME' => $arParams['CACHE_TIME'],
                  'CACHE_FILTER' => $arParams['CACHE_FILTER'],
                  'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                  'SET_TITLE' => $arParams['SET_TITLE'],
                  'MESSAGE_404' => $arParams['~MESSAGE_404'],
                  'SET_STATUS_404' => $arParams['SET_STATUS_404'],
                  'SHOW_404' => $arParams['SHOW_404'],
                  'FILE_404' => $arParams['FILE_404'],
                  'DISPLAY_COMPARE' => $arParams['USE_COMPARE'],
                  'PAGE_ELEMENT_COUNT' => 10,
                  'CUSTOM_PAGE_ELEMENT_COUNT' => 10,
                  'LINE_ELEMENT_COUNT' => 10,
                  'PRICE_CODE' => $arParams['PRICE_CODE'],
                  'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
                  'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],

                  'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
                  'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
                  'ADD_PROPERTIES_TO_BASKET' => (isset($arParams['ADD_PROPERTIES_TO_BASKET']) ? $arParams['ADD_PROPERTIES_TO_BASKET'] : ''),
                  'PARTIAL_PRODUCT_PROPERTIES' => (isset($arParams['PARTIAL_PRODUCT_PROPERTIES']) ? $arParams['PARTIAL_PRODUCT_PROPERTIES'] : ''),
                  'PRODUCT_PROPERTIES' => (isset($arParams['PRODUCT_PROPERTIES']) ? $arParams['PRODUCT_PROPERTIES'] : []),

                  'DISPLAY_TOP_PAGER' => $arParams['DISPLAY_TOP_PAGER'],
                  'DISPLAY_BOTTOM_PAGER' => $arParams['DISPLAY_BOTTOM_PAGER'],
                  'PAGER_TITLE' => $arParams['PAGER_TITLE'],
                  'PAGER_SHOW_ALWAYS' => $arParams['PAGER_SHOW_ALWAYS'],
                  'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
                  'PAGER_DESC_NUMBERING' => $arParams['PAGER_DESC_NUMBERING'],
                  'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
                  'PAGER_SHOW_ALL' => $arParams['PAGER_SHOW_ALL'],
                  'PAGER_BASE_LINK_ENABLE' => $arParams['PAGER_BASE_LINK_ENABLE'],
                  'PAGER_BASE_LINK' => $arParams['PAGER_BASE_LINK'],
                  'PAGER_PARAMS_NAME' => $arParams['PAGER_PARAMS_NAME'],
                  'LAZY_LOAD' => $arParams['LAZY_LOAD'],
                  'MESS_BTN_LAZY_LOAD' => $arParams['~MESS_BTN_LAZY_LOAD'],
                  'LOAD_ON_SCROLL' => $arParams['LOAD_ON_SCROLL'],

                  'OFFERS_CART_PROPERTIES' => (isset($arParams['OFFERS_CART_PROPERTIES']) ? $arParams['OFFERS_CART_PROPERTIES'] : []),
                  'OFFERS_FIELD_CODE' => $arParams['LIST_OFFERS_FIELD_CODE'],
                  'OFFERS_PROPERTY_CODE' => (isset($arParams['LIST_OFFERS_PROPERTY_CODE']) ? $arParams['LIST_OFFERS_PROPERTY_CODE'] : []),
                  'OFFERS_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],
                  'OFFERS_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],
                  'OFFERS_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],
                  'OFFERS_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],
                  'OFFERS_LIMIT' => 0,

                  'SECTION_ID' => $arResult['IBLOCK_SECTION_ID'], //$arResult['VARIABLES']['SECTION_ID'],
                  'SECTION_CODE' => '', //$arResult['VARIABLES']['SECTION_CODE'],
                  'SECTION_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['section'],
                  'DETAIL_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['element'],
                  'USE_MAIN_ELEMENT_SECTION' => $arParams['USE_MAIN_ELEMENT_SECTION'],
                  'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                  'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                  'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
                  'HIDE_NOT_AVAILABLE_OFFERS' => $arParams['HIDE_NOT_AVAILABLE_OFFERS'],

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
                  'ADD_SECTIONS_CHAIN' => 'N',
                  'ADD_TO_BASKET_ACTION' => $arParams['DETAIL_ADD_TO_BASKET_ACTION'],
                  'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
                  'COMPARE_PATH' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['compare'],
                  'COMPARE_NAME' => $arParams['COMPARE_NAME'],
                  'USE_COMPARE_LIST' => 'Y',
                  'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
                  'COMPATIBLE_MODE' => (isset($arParams['COMPATIBLE_MODE']) ? $arParams['COMPATIBLE_MODE'] : ''),
                  'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : ''),
                  'AJAX_REQUEST' => $_REQUEST['ajax']
              ],
              false
          );
          ?>
      </div>
      <?php Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("maylike_desktop", ""); ?>
    </div>

</div>

<div class="modalCatalog">
  <div class="modalCatalog-close">
    <img src="<?= SITE_TEMPLATE_PATH ?>/images/product/modal-close.svg" alt="close"/>
  </div>
  <div class="modalCatalog-items">
    <div class="modalCatalog-item-imgMini"></div>
    <div class="modalCatalog-item-img"></div>
  </div>
</div>

<?if($arParams['AJAX'] != 'Y'):?>
  <?
  $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
  if ($arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !$emptyProductProperties) { ?>
      <div id="<?=$itemIds['BASKET_PROP_DIV']?>" style="display: none;">
          <?if (!empty($arResult['PRODUCT_PROPERTIES_FILL'])) {
              foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propId => $propInfo) { ?>
                  <input type="hidden" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]" value="<?=htmlspecialcharsbx($propInfo['ID'])?>">
                  <? unset($arResult['PRODUCT_PROPERTIES'][$propId]);
              }
          }

          $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
          if (!$emptyProductProperties) { ?>
              <table>
                  <? foreach ($arResult['PRODUCT_PROPERTIES'] as $propId => $propInfo) { ?>
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