<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */

/** @var CBitrixComponent $component */

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$arSizeCropPreload = array('width' => 192, 'height' => 288);
$arSizeCropMobile = array('width' => 620, 'height' => 930);
$arSizeCropTablet = array('width' => 768, 'height' => 1152);
$arSizeCrop = array('width' => 980, 'height' => 1470);
?>

<div class="news-detail">
  <div class="news-product">
    <div class="news-detail-head">
      <h2 class="font-caption"><?= $arResult["NAME"] ?></h2>
      <? if ($arResult["PREVIEW_TEXT"]): ?>
        <p class="subtitle">
          <?= $arResult["PREVIEW_TEXT"]; ?>
        </p>
      <? endif; ?>
    </div>

    <? if (!empty($arResult['PROPERTIES']['IMAGES']['VALUE'])): ?>
      <?
      $imagesSize = count($arResult['PROPERTIES']['IMAGES']['VALUE']);

      if ($imagesSize > 6)
        $imagesSize = 5;
      ?>
      <div class="images-wrap images-size-<?= $imagesSize ?>">
        <? foreach ($arResult['PROPERTIES']['IMAGES']['VALUE'] as $key => $imageID): ?>
          <?
          $arImagePreload = CFile::ResizeImageGet($imageID, $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT);
          $arImageMobile = CFile::ResizeImageGet($imageID, $arSizeCropMobile, BX_RESIZE_IMAGE_EXACT);
          $arImageTablet = CFile::ResizeImageGet($imageID, $arSizeCropTablet, BX_RESIZE_IMAGE_EXACT);
          $arImage = CFile::ResizeImageGet($imageID, $arSizeCrop, BX_RESIZE_IMAGE_EXACT);
          ?>

          <div class="image-item">
            <a href="<?= $arResult['PROPERTIES']['BTN_LINK']['VALUE'] ?>">
              <picture>
                <source srcset="<?= $arImagePreload['src'] ?>" media="(max-width: 425px)"
                        data-src="<?= $arImageMobile['src'] ?>"/>
                <source srcset="<?= $arImagePreload['src'] ?>" media="(max-width: 1024px)"
                        data-src="<?= $arImageTablet['src'] ?>"/>
                <img src="<?= $arImagePreload['src'] ?>" alt="articles img" data-src="<?= $arImage['src'] ?>">
              </picture>
            </a>
          </div>
        <? endforeach ?>
      </div>
    <? else: ?>
      <?
      $arImagePreload = CFile::ResizeImageGet($arResult['PREVIEW_PICTURE']['ID'], $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT);
      $arImageMobile = CFile::ResizeImageGet($arResult['PREVIEW_PICTURE']['ID'], $arSizeCropMobile, BX_RESIZE_IMAGE_EXACT);
      $arImageTablet = CFile::ResizeImageGet($arResult['PREVIEW_PICTURE']['ID'], $arSizeCropTablet, BX_RESIZE_IMAGE_EXACT);
      $arImage = CFile::ResizeImageGet($arResult['PREVIEW_PICTURE']['ID'], $arSizeCrop, BX_RESIZE_IMAGE_EXACT);
      ?>
      <div class="images-wrap images-size-1">
        <div class="image-item">
          <a href="<?= $arResult['PROPERTIES']['BTN_LINK']['VALUE'] ?>">
            <picture>
              <source srcset="<?= $arImagePreload['src'] ?>" media="(max-width: 425px)"
                      data-src="<?= $arImageMobile['src'] ?>"/>
              <source srcset="<?= $arImagePreload['src'] ?>" media="(max-width: 1024px)"
                      data-src="<?= $arImageTablet['src'] ?>"/>
              <img src="<?= $arImagePreload['src'] ?>" alt="articles img" data-src="<?= $arImage['src'] ?>">
            </picture>
          </a>
        </div>
      </div>
    <? endif ?>

    <div class="info">
      <a href="<?= SITE_DIR ?>articles/" class="news-back hover">
        <div class="news-back-arrow">
          <img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/news-arrow.svg" alt="arrow back">
        </div>
        <p><?= Loc::getMessage("NEWS_DETAIL_BACK_LINK") ?></p>
      </a>
      <div class="description-wrap">
        <? if ($arResult['DETAIL_TEXT']): ?>
          <p class="description">
            <?= $arResult['DETAIL_TEXT']; ?>
          </p>
        <? endif; ?>

        <? if ($arResult['PROPERTIES']['BTN_NAME'] && $arResult['PROPERTIES']['BTN_LINK']): ?>
          <a href="<?= $arResult['PROPERTIES']['BTN_LINK']['VALUE'] ?>" class="ibutton big hollow">
            <?= $arResult['PROPERTIES']['BTN_NAME']['VALUE'] ?>
          </a>
        <? endif; ?>
      </div>
    </div>
  </div>

  <div class="news-trend">
    <p class="font-caption"><?= Loc::getMessage("NEWS_DETAIL_STYLED_WIDTH") ?></p>
    <? $APPLICATION->IncludeComponent(
        "arlix:home.premiere_carousel",
        "news",
        [
            "IBLOCK_ID" => 28,
            "COUNT" => 4,
        ],
        false
    ); ?>
  </div>
</div>