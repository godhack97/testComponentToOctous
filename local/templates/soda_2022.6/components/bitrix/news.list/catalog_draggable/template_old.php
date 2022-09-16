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
if(count($arResult["ITEMS"])>0){
?>
<div class="products-more" data-template="news.list:catalog">
  <div class="caption"><?=Loc::getMessage('NEWS_LIST_PROFUCT_MORE_CAPTION')?></div>
  <div class="products-more-slider-wrapper">
    <div class="products-more_items owl-carousel">
      <?foreach ($arResult["ITEMS"] as $arItem):?>
        <?
        $productID = $arItem['PROPERTIES']['PARENT']['VALUE'];
        $image = $arItem['PROPERTIES']['IMAGES']['VALUE'][0];
        if(empty($image))
          continue;

        $arSizeCropTablet = Array('width' => 768, 'height' => 1152);
        $arImageTablet = CFile::ResizeImageGet($image, $arSizeCropTablet, BX_RESIZE_IMAGE_EXACT);

        $arSizeCropTabletTmp = Array('width' => 192, 'height' => 288);
        $arImageTabletTmp = CFile::ResizeImageGet($image, $arSizeCropTabletTmp, BX_RESIZE_IMAGE_EXACT);

	      if(isset($arResult['ADDITIONAL_DATA'][$productID])){
		      ?>
		      <div class="item">
			      <?if($image):?>
				      <div class="image">
					      <a href="<?=$arResult['ADDITIONAL_DATA'][$productID]["DETAIL_PAGE_URL"]?>">
						      <img src="<?=$arImageTabletTmp['src']?>" data-src-replace-scroll="<?=$arImageTablet['src']?>" />
					      </a>
				      </div>
			      <?endif?>

			      <div class="name">
				      <a class="hover" href="<?=$arResult['ADDITIONAL_DATA'][$productID]["DETAIL_PAGE_URL"]?>">
					      <?=$arItem["NAME"]?>, <?=$arResult['ADDITIONAL_DATA'][$productID]["COLOR"]?>
				      </a>
			      </div>
			      <div class="price"><?=$arResult['ADDITIONAL_DATA'][$productID]["PRICE"]?></div>
		      </div>
		      <?php
	      }
	      ?>
      <?endforeach;?>

    </div>
  </div>
</div>
<?}?>