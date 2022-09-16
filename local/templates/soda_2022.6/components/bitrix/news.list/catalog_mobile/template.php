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
if (count($arResult["ITEMS"]) > 0) {?>
  <p class="look-title"><?=Loc::getMessage('NEWS_LIST_PROFUCT_MORE_CAPTION_MOBILE')?></p>
  <div class="look-filter">
    <div class="look-wrap" data-template="news.list:catalog_mobile">
      <?foreach ($arResult["ITEMS"] as $arItem):?>
        <?
          $productID = $arItem['PROPERTIES']['PARENT']['VALUE'];
          $image = $arItem['PROPERTIES']['IMAGES']['VALUE'][0];
          if (empty($image))
            continue;

          $arSizeCropTablet = Array('width' => 768, 'height' => 1152);
          $arImageTablet = CFile::ResizeImageGet($image, $arSizeCropTablet, BX_RESIZE_IMAGE_EXACT);

          $arSizeCropTabletTmp = Array('width' => 192, 'height' => 288);
          $arImageTabletTmp = CFile::ResizeImageGet($image, $arSizeCropTabletTmp, BX_RESIZE_IMAGE_EXACT);
        ?>
        <?if (isset($arResult['ADDITIONAL_DATA'][$productID])) {?>
          <a href="<?=$arResult['ADDITIONAL_DATA'][$productID]["DETAIL_PAGE_URL"]?>" class="look-item">
            <img src="<?=$arImageTabletTmp['src']?>" alt="product look">
          </a>
        <?}?>
      <?endforeach;?>

    </div>
  </div>
<?}?>