<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

  use \Bitrix\Main\Localization\Loc;
  use \Bitrix\Main\Web\Json;

  $this->setFrameMode(true);

  /**
   * @global CMain $APPLICATION
   * @var array $arResult
   * @var array $arParams
   * @var array $item
   * @var array $actualItem
   * @var array $minOffer
   * @var array $itemIds
   * @var array $price
   * @var array $measureRatio
   * @var bool $haveOffers
   * @var bool $showSubscribe
   * @var array $morePhoto
   * @var bool $showSlider
   * @var bool $itemHasDetailUrl
   * @var string $imgTitle
   * @var string $productTitle
   * @var string $buttonSizeClass
   * @var CatalogSectionComponent $component
   */

  $isRuleModerator = false;
  if (CSite::InGroup(array(1, 8, 9, 15, 16))) // || htmlspecialchars($_GET['admin']) == 'Y')
    $isRuleModerator = true;

  //dbgz($USER->GetUserGroupArray());

  //if($arParams['IS_MOBILE'] == 'Y')
  //  $arImageCrop = Array('width' => 414, 'height' => 621);
  //else
  $arImageCrop = array('width' => 800, 'height' => 1200);

  $arImageCropTablet = array('width' => 768, 'height' => 1152);
  $arImageCropMobile = array('width' => 620, 'height' => 930); //Array('width' => 414, 'height' => 621);

  $arSrcsetCrop0 = array('width' => 124, 'height' => 184);
  //$arSrcsetCrop1 = Array('width' => 428, 'height' => 642);
  $arSrcsetCrop1 = array('width' => 768, 'height' => 1152);
  $arSrcsetCrop2 = array('width' => 768, 'height' => 1152);
  $arSrcsetCrop3 = array('width' => 1024, 'height' => 1536);
  $arSrcsetCrop4 = array('width' => 1366, 'height' => 2049);
  $arSrcsetCrop5 = array('width' => 1920, 'height' => 2880);

  $jsObject = [];
  $daysNewSeason = strtotime("-90 days");

  if (isset($item['colors'])) {
    $jsObject = [
      'id' => $item["ID"],
      'selectColor' => array_key_first($item["colors"]),
      'colors' => [],
    ];

    if (strtotime($item['DATE_CREATE']) > $daysNewSeason) {
      $newSection = Loc::getMessage('ITEM_NEW_SEASON');
    } else {
      if (isset($item['section']) && !empty($item['section'])) {

        if (Loc::getCurrentLang() == 'ru')
          $newSection = $item['section']['name'];
        else
          $newSection = Loc::getMessage('ITEM_NEW_SEASON');

      } else {
        $newSection = '&nbsp';
      }
    }

    foreach ($item['colors'] as $kColor => $vColor) {
      $videos = [];
      $fullImages = [];

      $itemColorData = [
        'id' => $vColor['item']['ID'],
        'name' => $vColor['item']['NAME'],
        'is_full' => $vColor['item']['PROPERTIES']['FULL']['VALUE_XML_ID'] == 'Y',
        'is_new_season' => strtotime($item['DATE_CREATE']) > $daysNewSeason,
        'new_season_text' => $item['NAMES'][LANGUAGE_ID], //$newSection,
        'exist_video' => !empty($vColor['item']['PROPERTIES']['VIDEO_IMAGE']['VALUE']) && !empty($vColor['item']['PROPERTIES']['VIDEO_FILES']['VALUE']),
        'video_thumb' => [
          'picture' => CFile::ResizeImageGet($vColor['item']['PROPERTIES']['VIDEO_IMAGE']['VALUE'], $arImageCrop, BX_RESIZE_IMAGE_EXACT)['src'],//$vColor['item']['PROPERTIES']['VIDEO_IMAGE']['VALUE'],
          'videos' => $videos,
        ],
        'exist_detail_picture' => !empty($vColor['item']['DETAIL_PICTURE']),
        'detail_picture' => [
          '428px' => CFile::ResizeImageGet($vColor['item']['DETAIL_PICTURE'], $arSrcsetCrop1, BX_RESIZE_IMAGE_EXACT, false, false, false, 75)['src'],// $vColor['item']['DETAIL_PICTURE'],
          '768px' => CFile::ResizeImageGet($vColor['item']['DETAIL_PICTURE'], $arSrcsetCrop2, BX_RESIZE_IMAGE_EXACT, false, false, false, 75)['src'],// $vColor['item']['DETAIL_PICTURE'],
          '1024px' => CFile::ResizeImageGet($vColor['item']['DETAIL_PICTURE'], $arSrcsetCrop3, BX_RESIZE_IMAGE_EXACT, false, false, false, 75)['src'],// $vColor['item']['DETAIL_PICTURE'],
          '1366px' => CFile::ResizeImageGet($vColor['item']['DETAIL_PICTURE'], $arSrcsetCrop4, BX_RESIZE_IMAGE_EXACT, false, false, false, 75)['src'],// $vColor['item']['DETAIL_PICTURE'],
          '1920w' => CFile::ResizeImageGet($vColor['item']['DETAIL_PICTURE'], $arSrcsetCrop5, BX_RESIZE_IMAGE_EXACT, false, false, false, 75)['src'],// $vColor['item']['DETAIL_PICTURE'],
          'full' => CFile::ResizeImageGet($vColor['item']['DETAIL_PICTURE'], $arImageCrop, BX_RESIZE_IMAGE_EXACT)['src'],// $vColor['item']['DETAIL_PICTURE'],
        ],
        'exist_full_image' => !empty($item['item']['PROPERTIES']['FULL_IMAGE']['VALUE']),
        'full_image' => CFile::ResizeImageGet($item['item']['PROPERTIES']['FULL_IMAGE']['VALUE'], ['width' => 1920, 'height' => 1920])['src'],
        'exist_full_images' => $vColor['item']['PROPERTIES']['FULL']['VALUE_XML_ID'] == 'Y' && !empty($vColor['item']['PROPERTIES']['IMAGES']['VALUE']),
        'full_images' => $fullImages,
        'url' => $vColor['item']['DETAIL_PAGE_URL'],
        'prices' => $vColor['item']['ITEM_PRICES'],
        'measure' => $vColor['item']['ITEM_MEASURE'],
      ];
      if ($itemColorData['exist_video']) {
        foreach ($vColor['item']['PROPERTIES']['VIDEO_FILES']['VALUE'] as $videoID) {
          $videos[] = CFile::GetFileArray($videoID);
        }
        $itemColorData['video_thumb']['videos'] = $videos;
      }
      if ($itemColorData['exist_full_images']) {
        foreach ($vColor['item']['PROPERTIES']['IMAGES']['VALUE'] as $imageID) {
          $fullImages[] = CFile::ResizeImageGet($imageID, $arImageCrop, BX_RESIZE_IMAGE_EXACT)['src'];
        }
        $itemColorData['full_images'] = $fullImages;
      }

      $jsObject['colors'][$kColor] = [
        'id' => $vColor['id'],
        'name' => $vColor['name'],
        'hex' => $vColor['hex'],
        'item' => $itemColorData,
        'default_picture' => SITE_TEMPLATE_PATH . '/images/default.jpg',
      ];
    }

    $currentItem = $jsObject['colors'][$jsObject['selectColor']]['item'];
  } else {
    $videos = [];
    $fullImages = [];
    $newSection = '';
    if (strtotime($item['DATE_CREATE']) > $daysNewSeason) {
      $newSection = Loc::getMessage('ITEM_NEW_SEASON');
    } else {
      if (isset($item['section']) && !empty($item['section'])) {
        $newSection = $item['section']['name'];
      } else {
        $newSection = '&nbsp';
      }
    }
    $currentItem = [
      'id' => $item['ID'],
      'name' => $item['NAME'],
      'is_full' => $item['PROPERTIES']['FULL']['VALUE_XML_ID'] == 'Y',
      'is_new_season' => strtotime($item['DATE_CREATE']) > $daysNewSeason,
      'new_season_text' => $item['NAMES'][LANGUAGE_ID], //$newSection,
      'exist_video' => !empty($item['PROPERTIES']['VIDEO_IMAGE']['VALUE']) && !empty($item['PROPERTIES']['VIDEO_FILES']['VALUE']),
      'video_thumb' => [
        'picture' => CFile::ResizeImageGet($item['PROPERTIES']['VIDEO_IMAGE']['VALUE'], $arSrcsetCrop2, BX_RESIZE_IMAGE_EXACT)['src'],//$vColor['item']['PROPERTIES']['VIDEO_IMAGE']['VALUE'],
        'videos' => $videos,
      ],
      'exist_detail_picture' => !empty($item['DETAIL_PICTURE']),
      'detail_picture' => [
        '428px' => CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arSrcsetCrop1, BX_RESIZE_IMAGE_EXACT, false, false, false, 75)['src'],// $vColor['item']['DETAIL_PICTURE'],
        '768px' => CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arSrcsetCrop2, BX_RESIZE_IMAGE_EXACT, false, false, false, 75)['src'],// $vColor['item']['DETAIL_PICTURE'],
        '1024px' => CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arSrcsetCrop3, BX_RESIZE_IMAGE_EXACT, false, false, false, 75)['src'],// $vColor['item']['DETAIL_PICTURE'],
        '1366px' => CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arSrcsetCrop4, BX_RESIZE_IMAGE_EXACT, false, false, false, 75)['src'],// $vColor['item']['DETAIL_PICTURE'],
        '1920w' => CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arSrcsetCrop5, BX_RESIZE_IMAGE_EXACT, false, false, false, 75)['src'],// $vColor['item']['DETAIL_PICTURE'],
        'full' => CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arImageCrop, BX_RESIZE_IMAGE_EXACT)['src'],// $vColor['item']['DETAIL_PICTURE'],
      ],
      'exist_full_image' => !empty($item['PROPERTIES']['FULL_IMAGE']['VALUE']),
      'full_image' => CFile::ResizeImageGet($item['PROPERTIES']['FULL_IMAGE']['VALUE'], ['width' => 1920, 'height' => 1920])['src'],
      'exist_full_images' => $item['PROPERTIES']['FULL']['VALUE_XML_ID'] == 'Y' && !empty($item['PROPERTIES']['IMAGES']['VALUE']),
      'full_images' => $fullImages,
      'url' => $item['DETAIL_PAGE_URL'],
      'prices' => $item['ITEM_PRICES'],
      'measure' => $item['ITEM_MEASURE'],
    ];
  }
?>
<div class="item-wrapper">
    <a id="offer<?= $currentItem['id']; ?>"></a>
  <? //=$currentItem['is_full'] ? '<div class="indent">' : ''; ?>

    <div class="image <?= $currentItem['exist_video'] ? 'active' : ''; ?> video-wrapper"
         id="video_block_<?= $item["ID"]; ?>">
        <a href="<?= $currentItem['url'] ?>">
            <video autoplay="autoplay" class="product-media _videoLoaded _videoPlaying"
                   poster="<?= $currentItem['video_thumb']['picture']; ?>" playsinline="" loop="" muted="">
              <?php
                if (count($currentItem['video_thumb']['videos']) > 0) {
                  foreach ($currentItem['video_thumb']['videos'] as $video) {
                    ?>
                      <source src="<?= $video['SRC'] ?>" type="<?= $video['CONTENT_TYPE'] ?>">
                    <?php
                  }
                } else {
                  ?>
                    <source/>
                  <?php
                }
              ?>
                <img class="_imageLoaded _imgImpressions" src="<?= $currentItem['video_thumb']['picture']; ?>">
            </video>
        </a>
    </div>
  <?php
    if ($currentItem['exist_detail_picture']) {
      if ($currentItem['exist_full_image']) {
        ?>
          <div class="image active" id="image_block_<?= $item["ID"]; ?>">
              <a href="<?= $currentItem['url'] ?>"><img src="<?= $currentItem['full_image'] ?>" itemprop="image"/></a>
          </div>
        <?php
      } elseif ($currentItem['exist_full_images']) {
        ?>
          <div class="slider-in-section" id="image_slider_block_<?= $item["ID"]; ?>">
              <div class="image active"><a href="<?= $currentItem['url']; ?>"><img
                              src="<?= $currentItem['full_image']; ?>"/></a></div>
            <?php
              foreach ($arResult['ITEM']['PROPERTIES']['IMAGES']['VALUE'] as $image) {
                ?>
                  <div class="image"><a href="<?= $currentItem['url']; ?>"><img src="<?= $image; ?>" itemprop="image"/></a>
                  </div>
                <?php
              }
            ?>
          </div>
        <?php
      } else {
        /*
<?php if(count($arColors)):?>

<div class="image" data-flag="else colors">
  <a href="<?=$item['DETAIL_PAGE_URL']?>">
    <?php foreach($arColors as $index => $color):?>
      <img data-color="<?=$index?>" src="<?=$color['SRC']?>"<?if($index):?> style="display:none"<?endif?> itemprop="image" />
    <?php endforeach?>
  </a>
  <div class="colors">
    <?php foreach($arColors as $index => $color):?>
      <div class="color hover js-switch-color<?if(!$index):?> active<?endif?>" data-color="<?=$index?>" style="background-color: <?=$color['HEX']?>" title="<?=$color['NAME']?>"></div>
    <?php endforeach?>
  </div>
</div>

<?php else:?>

<div class="image active" data-color="color_id_1" data-flag="else">
  <?php /* <a href="<?=$item['DETAIL_PAGE_URL']?>"><img <?if($arParams['START_HIDE_SRC'] == 'Y'):?>data-img-<?endif?>src="<?=$arImage['src']?>" /></a> * / ? >

<a href="<?=$item['DETAIL_PAGE_URL']? >">
  <?php / * if(!$isRuleModerator):?>

      <picture>
        <source <?if($arParams['START_HIDE_SRC'] == 'Y'):?>data-img-<?endif?>srcset="<?=$arImage['src']? >" media="(min-width: 769px)" />
        <source <?if($arParams['START_HIDE_SRC'] == 'Y'):?>data-img-<?endif?>srcset="<?=$arImageTablet['src']? >" media="(min-width: 415px)" />
        <img <?if($arParams['START_HIDE_SRC'] == 'Y'):?>data-img-<?endif?>src="<?=$arImageMobile['src']? >" alt="<?=$productTitle? >" itemprop="image">
      </picture>

      <? else: * /
  ?>
  <? //$arImageStep0 = CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arSrcsetCrop0, BX_RESIZE_IMAGE_EXACT, false, false, false, 75); ? >
  <? $arImageStep1 = CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arSrcsetCrop1, BX_RESIZE_IMAGE_EXACT, false, false, false, 75); ? >
  <? $arImageStep2 = CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arSrcsetCrop2, BX_RESIZE_IMAGE_EXACT, false, false, false, 75); ? >
  <? $arImageStep3 = CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arSrcsetCrop3, BX_RESIZE_IMAGE_EXACT, false, false, false, 75); ? >
  <? $arImageStep4 = CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arSrcsetCrop4, BX_RESIZE_IMAGE_EXACT, false, false, false, 75); ? >
  <? $arImageStep5 = CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arSrcsetCrop5, BX_RESIZE_IMAGE_EXACT, false, false, false, 75); ? >

  <?
    //  CModule::IncludeModule("delight.webpconverter");
    //  if($item['ID'] == 3235 && CModule::IncludeModule("delight.webpconverter"))
    //  {
    //    $OriginalFilePath = $_SERVER["DOCUMENT_ROOT"] . $arImageStep5['src'];
    //    $DestinationFilePath = $_SERVER["DOCUMENT_ROOT"] .'/webp'. $arImageStep5['src'] .'.webp';
    //    WebPConvert\WebPConvert::convert($OriginalFilePath, $DestinationFilePath);
    //  }
  ? >

  <picture data="webp" data-id="<?=$item['ID']? >" data-file-dest="<?=$DestinationFilePath? >">

    <? /* source type="image/webp" media="(max-width: 414px)" srcset="<?=$arImageStep0['src']? >" * / ?>
    <source type="image/webp" media="(max-width: 428px)" srcset="<?=$arImageStep1['src']? >">
    <source type="image/webp" media="(max-width: 768px)" srcset="<?=$arImageStep2['src']? >">
    <source type="image/webp" media="(max-width: 1024px)" srcset="<?=$arImageStep3['src']? >">
    <source type="image/webp" media="(max-width: 1366px)" srcset="<?=$arImageStep4['src']? >">
    <source type="image/webp" srcset="<?=$arImageStep5['src']? >">

    <?
      /* <source type="image/webp"
            <?if($arParams['START_HIDE_SRC'] == 'Y'):?>data-img-<?endif?>srcset="<?=$arImageStep1['src']? > 428w,
                    <?=$arImageStep2['src']? > 768w,
                    <?=$arImageStep3['src']? > 1024w,
                    <?=$arImageStep4['src']? > 1366w,
                    <?=$arImageStep5['src']? > 1920w"> * /

    ?>

    <img alt="" srcset="<? /* =$arImageStep0['src']?> 414w, * / ? >
                    <?=$arImageStep1['src']? > 428w,
                    <?=$arImageStep2['src']? > 768w,
                    <?=$arImageStep3['src']? > 1024w,
                    <?=$arImageStep4['src']? > 1366w,
                    <?=$arImageStep5['src']? > 1920w"
         src="<?=$arImage['src']? >" />
  </picture>

  <? // endif?>
</a>
</div>

<?endif?>
*/

        ?>
          <div class="image <?= $currentItem['exist_video'] ? '' : 'active'; ?>" id="image_block_<?= $item["ID"]; ?>">
              <a href="<?= $currentItem['url']; ?>"><img src="<?= $currentItem['detail_picture']['768px']; ?>"
                                                         itemprop="image"/></a>
          </div>
        <?php
      }
    } else {
      ?>
        <div class="image active" id="image_block_<?= $item["ID"]; ?>">
            <a href="<?= $currentItem['url']; ?>"><img src="<?= SITE_TEMPLATE_PATH ?>/images/default.jpg"
                                                       itemprop="image"/></a>
        </div>
      <?php
    }
  ?>

    <span class="subtitle-name" id="new_season_block_<?= $item["ID"]; ?>">
		<?= /*$currentItem['new_season_text']*/ $item['NAMES'][LANGUAGE_ID]; ?>
	</span>
    <meta itemprop="description" content="<?= $currentItem['name']; ?>" id="description_block_<?= $item["ID"]; ?>">
    <div class="name" itemprop="name" id="name_block_<?= $item["ID"]; ?>">
        <a class="name-link hover" href="<?= $currentItem['url']; ?>" itemprop="url"><?= $currentItem['name']; ?></a>
        <div class="actions-list">
            <a class="favorite js-favorite-button" data-id="<?= $currentItem['id']; ?>">
                <img class="icon" src="<?= SITE_TEMPLATE_PATH ?>/images/icons/catalog-favorite.svg">
                <img class="icon added" src="<?= SITE_TEMPLATE_PATH ?>/images/icons/catalog-favorite-black.svg">
            </a>
        </div>
      <?php if ($isRuleModerator): ?>
        <?php if (in_array($item['PRODUCT']['TYPE'], [1, 3])): ?>
              <a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=<?= CATALOG_ID ?>&type=catalogs&ID=<?= $currentItem['id']; ?>&lang=ru"
                 target="_blank"
                 class="other-info"><?= Loc::getMessage("CATALOG_ITEM_SORT") ?> <?= $arResult['ITEM']['SORT'] ?>
                  , <?= Loc::getMessage("CATALOG_ITEM_ARTICUL") ?> <?= $item['CML2_ARTICLE']?></a>
        <?php else: ?>
              <a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=<?= CATALOG_ID ?>&type=catalogs&ID=<?= $arResult['ITEM']['LINK_ELEMENT_ID'] ?>&lang=ru"
                 target="_blank"
                 class="other-info"><?= Loc::getMessage("CATALOG_ITEM_SORT") ?> <?= $arResult['ITEM']['SORT'] ?>
                  , <?= Loc::getMessage("CATALOG_ITEM_ARTICUL") ?> <?= $item['ARTICLE'].$item['CML2_ARTICLE'] ?></a>
        <?php endif ?>
      <?php endif ?>
    </div>

  <?php
    if (!empty($arParams['PRODUCT_BLOCKS_ORDER'])) {
      foreach ($arParams['PRODUCT_BLOCKS_ORDER'] as $blockName) {
        switch ($blockName) {
          case 'price': ?>
            <? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("currency_section_" . $item['ID']); ?>
              <div class="item-price-block" id="price_block_<?= $item["ID"]; ?>">
                  <div itemscope itemprop="offers" itemtype="http://schema.org/Offer">
                      <div class="price product-item-price-current" id="<?= $currentItem['prices'][0]['PRICE']; ?>"
                           itemprop="price">
                          <div id="price_count_<?= $item["ID"]; ?>">
                            <?php
                              if (!empty($currentItem['prices'][0])) {
                                if ($arParams['PRODUCT_DISPLAY_MODE'] === 'N' && $haveOffers) {
                                  echo '<span class="price-item">' . Loc::getMessage(
                                      'CT_BCI_TPL_MESS_PRICE_SIMPLE_MODE',
                                      array(
                                        '#PRICE#' => $currentItem['prices'][0]['PRINT_RATIO_PRICE'],
                                        '#VALUE#' => $measureRatio,
                                        '#UNIT#' => $currentItem['measure']['TITLE']
                                      )
                                    ) . '</span>';
                                } else {
                                  echo '<span class="price-item">' . $currentItem['prices'][0]['PRINT_RATIO_PRICE'] . '</span>';
                                }

                                if ($currentItem['prices'][0]['DISCOUNT']) {
                                  ?><span class="price-old"
                                          title="<?= $currentItem['prices'][0]['PRINT_RATIO_BASE_PRICE'] ?>"><?= $currentItem['prices'][0]['PRINT_RATIO_BASE_PRICE'] ?></span><?php
                                }
                              }
                            ?>
                              <meta itemprop="priceCurrency" content="<?= $currentItem['prices'][0]['CURRENCY'] ?>"
                                    id="meta_price_currency_<?= $item["ID"]; ?>">
                              <meta itemprop="availability" content="http://schema.org/InStock"/>
                          </div>

                        <?php //if(count($arColors)):?>
                          <div class="item-colors">
                            <?php
                              if (count($jsObject['colors']) > 1) {
                                foreach ($jsObject['colors'] as $color) {
                                  ?>
                                    <div class="color hover js-switch-color <?= $color['id'] === $jsObject['selectColor'] ? 'active' : ''; ?>"
                                         data-item="<?= $item["ID"]; ?>" data-color="<?= $color['id']; ?>"
                                         style="background-color: <?= $color['hex']; ?>" title=""></div>
                                  <?php
                                }
                              }
                            ?>
                          </div>
                        <?php //endif;?>

                      </div>
                  </div>
              </div>
            <? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("currency_section_" . $item['ID'], ""); ?>
            <?
            break;
        }
      }
    }
  ?>

  <?
    //dbgz($arResult['KIT_ELEMENTS']);

    if (count($arResult['KIT_ELEMENTS']) > 0) { ?>
        <div class="hidden-item <?= ((count($arResult['KIT_ELEMENTS']) < 5) ? 'item-4' : 'item-6') ?>">

          <? foreach ($arResult['KIT_ELEMENTS'] as $kit) {
            $arImageKit = CFile::ResizeImageGet($kit['PHOTO'], $arSrcsetCrop1, BX_RESIZE_IMAGE_EXACT, false, false, false, 75);
            ?>

              <a href="<?= $kit['PARENT_PRODUCT_LINK'] ?>?setColor=<?= $kit['COLOR'] ?><?= $arParams['IS_MOBILE'] !== 'Y' ? '' : '&is-desktop' ?>">
                  <img src="<?= $arImageKit['src'] ?>" alt="image">
                  <span><?= $kit['PARENT_PRODUCT_NAME'] ?>, <?= $kit['COLOR_NAME'] ?></span>
              </a>

          <? } ?>

        </div>
    <? } ?>

  <? //=$jsObject['colors'][$jsObject['selectColor']]['is_full'] ? '</div>' : ''; ?>

</div>
<script>
    //if(window.items){
    //	window.items[<?=$item["ID"];?>] = <?=Json::encode($jsObject)?>
    //}
    //else {
    //window.items = {}
    window.catalogItems['item_' + <?=$item["ID"];?>] = <?=Json::encode($jsObject)?>
    //}
</script>
