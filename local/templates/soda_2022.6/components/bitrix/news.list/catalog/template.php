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
if (count($arResult["ITEMS"]) > 0 && isset($arResult['ADDITIONAL_DATA'])) { ?>
  <h2 class="look-title skeleton"><?= Loc::getMessage('NEWS_LIST_PROFUCT_MORE_CAPTION') ?></h2>
  <div class="look-filter">
    <div class="product-item-container look-slider-init" data-template="news.list:catalog">
      <? foreach ($arResult["ITEMS"] as $arItem): ?>
        <?

        $productID = $arItem['PROPERTIES']['PARENT']['VALUE'];
        $image = $arItem['PROPERTIES']['IMAGES']['VALUE'][0];
        if (empty($image))
          continue;

        $arSizeCropPreload = array('width' => 192, 'height' => 288);
        $arImagePreload = CFile::ResizeImageGet($image, $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT);

        $arSizeCropMobile = array('width' => 620, 'height' => 930);
        $arImageMobile = CFile::ResizeImageGet($image, $arSizeCropMobile, BX_RESIZE_IMAGE_EXACT);

        $arSizeCropTablet = array('width' => 768, 'height' => 1152);
        $arImageTablet = CFile::ResizeImageGet($image, $arSizeCropTablet, BX_RESIZE_IMAGE_EXACT);
        ?>

        <? if (isset($arResult['ADDITIONAL_DATA'][$productID])) { ?>
          <div class="item">
            <div class="item-wrap">
              <div class="image-container skeleton">
                <div class="image">
                  <a href="<?= $arResult['ADDITIONAL_DATA'][$productID]["DETAIL_PAGE_URL"] ?>">
                    <picture>
                      <source srcset="<?= $arImagePreload['src'] ?>" media="(max-width: 1024px)"
                              data-src="<?= $arImageMobile['src'] ?>"/>
                      <img src="<?= $arImagePreload['src'] ?>" alt="product look"
                           data-src="<?= $arImageTablet['src'] ?>">
                    </picture>
                  </a>
                </div>
                <div class="favorite-block">
                  <a class="favorite js-favorite-button"
                     data-id="<?= $arResult['ADDITIONAL_DATA'][$productID]["ID"] ?>">
                    <img src="<?= SITE_TEMPLATE_PATH ?>/images/product/favorite.svg" alt="favorite">
                  </a>
                </div>
              </div>

              <div class="description">
                <span class="subtitle skeleton">New Season</span>

                <div class="name-block" itemprop="name">
                  <a class="name hover skeleton" href="<?= $arResult['ADDITIONAL_DATA'][$productID]["DETAIL_PAGE_URL"] ?>"
                     itemprop="url">
                    <?= $arItem["NAME"] ?>, <?= $arResult['ADDITIONAL_DATA'][$productID]["COLOR"] ?>
                  </a>
                  <div class="colors-block">
                    <div class="color hover active" style="background-color: #EBE6E6"></div>
                    <div class="color hover" style="background-color: #751520"></div>
                    <div class="color hover" style="background-color: #BF9B75"></div>
                  </div>
                </div>
              </div>

              <div class="price-block" itemprop="price">
                <span class="price skeleton"><?= $arResult['ADDITIONAL_DATA'][$productID]["PRICE"] ?></span>
              </div>
            </div>
          </div>
        <? } ?>
      <? endforeach; ?>
    </div>
  </div>
<? } ?>