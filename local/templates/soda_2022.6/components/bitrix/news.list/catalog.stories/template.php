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
?>

<?if(count($arResult["ITEMS"])>0):?>

  <div class="products-style-history color-<?=$arParams['COLOR_KEY']?>" data-color="<?=$arParams['COLOR_KEY']?>">
    <div id="cc_stories_<?=$arParams['COLOR_KEY']?>" class="cc_stories_list"<? /* if($arParams['COLOR_DISPLAY'] == 'N'):?> style="display:none;"<?endif */ ?>>

      <div class="main-history">
        <div class="items initial-end-">
          <div class="items-init">
            <?foreach($arResult["ITEMS"] as $arItem):?>
              <?
                $productID = $arItem['PROPERTIES']['PARENT']['VALUE'];
                $image = $arItem['PROPERTIES']['IMAGES']['VALUE'][0];
                if(empty($image))
                  continue;

                $link = $arResult['ADDITIONAL_DATA'][$productID]["DETAIL_PAGE_URL"];
                $arImageCrop = CFile::ResizeImageGet($image, Array('width' => 130, 'height' => 130), BX_RESIZE_IMAGE_EXACT); // BX_RESIZE_IMAGE_EXACT
                $arItem["NAME"] = $arItem["NAME"] .', '. $arResult['ADDITIONAL_DATA'][$productID]["COLOR"];
              ?>
              <div class="item hover">
                <a href="<?=$link?>"><div class="image" style="background-image: url(<?=$arImageCrop['src']?>)"></div></a>
                <a href="<?=$link?>"><div class="name"><?=$arItem['NAME']?></div></a>
              </div>
            <?endforeach;?>
          </div>
        </div>
      </div>

    </div>
  </div>

<?endif?>