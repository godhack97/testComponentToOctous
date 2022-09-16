<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
if (count($arResult["ITEMS"]) > 0 && isset($arResult['ADDITIONAL_DATA'])) {?>
  <h2 class="title skeleton"><?=Loc::getMessage('NEWS_LIST_PROFUCT_MORE_DRAGGABLE_CAPTION')?></h2>
  <div class="items-list" data-template="news.list:catalog_draggable">
    <?foreach ($arResult["ITEMS"] as $arItem):?>
      <?
        $productID = $arItem['PROPERTIES']['PARENT']['VALUE'];
        $image = $arItem['PROPERTIES']['IMAGES']['VALUE'][0];
        if (empty($image))
          continue;

        $arSizeCropPreload = Array('width' => 192, 'height' => 288);
        $arImagePreload = CFile::ResizeImageGet($image, $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT);

        $arSizeCropMobile2 = Array('width' => 414, 'height' => 621);
        $arImageMobile = CFile::ResizeImageGet($image, $arSizeCropMobile2, BX_RESIZE_IMAGE_EXACT);

        $arSizeCropTablet = Array('width' => 768, 'height' => 1152);
        $arImageTablet = CFile::ResizeImageGet($image, $arSizeCropTablet, BX_RESIZE_IMAGE_EXACT);
      ?>
      <?if (isset($arResult['ADDITIONAL_DATA'][$productID])) {?>
        <div class="item">
          <div class="item-wrap">
            <div class="image skeleton">
              <a href="<?=$arResult['ADDITIONAL_DATA'][$productID]["DETAIL_PAGE_URL"]?>">
                <picture>
                  <source srcset="<?=$arImagePreload['src']?>" data-src="<?=$arImageMobile['src']?>" media="(max-width: 600px)" />
                  <img src="<?=$arImagePreload['src']?>" data-src="<?=$arImageTablet['src']?>" alt="product look">
                </picture>
              </a>
            </div>

            <div class="description">
              <div class="favorite-block">
                <a class="favorite hover js-favorite-button" data-id="<?=$arResult['ADDITIONAL_DATA'][$productID]["ID"]?>">
                  <img class="icon" src="<?= SITE_TEMPLATE_PATH ?>/images/product/favorite-mobile.svg" alt="favorite">
                  <img class="icon add" src="<?= SITE_TEMPLATE_PATH ?>/images/product/favorite.svg" alt="favorite">
                </a>
              </div>

              <span class="subtitle skeleton">Новинка</span>

              <div class="name-block" itemprop="name">
                <a class="name hover skeleton" href="<?=$arResult['ADDITIONAL_DATA'][$productID]["DETAIL_PAGE_URL"]?>" itemprop="url">
                  <?=$arItem["NAME"]?>, <?=$arResult['ADDITIONAL_DATA'][$productID]["COLOR"]?>
                </a>

              </div>
            </div>

            <div class="price-block" itemprop="price">
              <span class="price skeleton"><?=$arResult['ADDITIONAL_DATA'][$productID]["PRICE"]?></span>
              <div class="colors-block">
                <div class="color hover active" style="background-color: #EBE6E6"></div>
                <div class="color hover" style="background-color: #751520"></div>
                <div class="color hover" style="background-color: #BF9B75"></div>
              </div>
            </div>
          </div>
        </div>
      <?}?>
    <?endforeach;?>
  </div>
<?}?>