<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}
/** @var array $arParams */
/** @var array $arResult */

use Bitrix\Main\Localization\Loc;

$arSizeCropPreload = array('width' => 192, 'height' => 288);
$arSizeCropMobile2 = array('width' => 414, 'height' => 621);
$arSizeCropMobile = array('width' => 620, 'height' => 930);
$arSizeCropTablet = array('width' => 768, 'height' => 1152);
?>

<div class="news-trend-list">
  <?foreach ($arResult['ITEMS'] as $item):?>
    <div class="news-trend-card">
    <div class="image-container">
      <div class="image">
        <a href="<?= $item['LINK']; ?>">
          <?
          $arImagePreload = CFile::ResizeImageGet($item['IMAGE_ID'], $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT)['src'];
          $arImageMobile2 = CFile::ResizeImageGet($item['IMAGE_ID'], $arSizeCropMobile2, BX_RESIZE_IMAGE_EXACT)['src'];
          $arImageMobile = CFile::ResizeImageGet($item['IMAGE_ID'], $arSizeCropMobile, BX_RESIZE_IMAGE_EXACT)['src'];
          $arImageTablet = CFile::ResizeImageGet($item['IMAGE_ID'], $arSizeCropTablet, BX_RESIZE_IMAGE_EXACT)['src'];
          ?>

          <picture>
            <source srcset="<?= $arImagePreload ?>" data-src="<?= $arImageMobile2 ?>" media="(max-width: 768px)" />
            <source srcset="<?= $arImagePreload ?>" data-src="<?= $arImageMobile ?>" media="(max-width: 1024px)" />
            <img src="<?= $arImagePreload ?>" data-src="<?= $arImageTablet ?>" itemprop="image" />
          </picture>
        </a>
      </div>
    </div>
    <div class="description">
      <span class="subtitle">New in</span>
      <a href="<?= $item['LINK']; ?>" class="name hover">
        <?= Loc::getCurrentLang() == 'ru' ? $item['NAME'] :  $item['ENG_NAME']; ?>
      </a>
    </div>
    <div class="price-block">
      <div class="price-wrap">
        <span class="price price-current"><?= FormatCurrency($item['PRICE']['PRICE'], $item['PRICE']['CURRENCY']); ?></span>
      </div>
      <div class="colors-block">
        <div class="color hover active" style="background-color: #000000"></div>
        <div class="color hover" style="background-color: #D0BB92"></div>
      </div>
    </div>
  </div>
  <?endforeach?>
</div>