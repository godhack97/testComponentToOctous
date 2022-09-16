<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

/**
 * @global CMain $APPLICATION
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
if(CSite::InGroup(array(1, 8, 9, 15, 16))) // || htmlspecialchars($_GET['admin']) == 'Y')
  $isRuleModerator = true;

//dbgz($USER->GetUserGroupArray());

//if($arParams['IS_MOBILE'] == 'Y')
//  $arImageCrop = Array('width' => 414, 'height' => 621);
//else
  $arImageCrop = Array('width' => 800, 'height' => 1200);

$arImageCropTablet = Array('width' => 768, 'height' => 1152);
$arImageCropMobile = Array('width' => 620, 'height' => 930); //Array('width' => 414, 'height' => 621);

//$arSrcsetCrop1 = Array('width' => 428, 'height' => 642);
$arSrcsetCrop1 = Array('width' => 768, 'height' => 1152);
$arSrcsetCrop2 = Array('width' => 768, 'height' => 1152);
$arSrcsetCrop3 = Array('width' => 1024, 'height' => 1536);
$arSrcsetCrop4 = Array('width' => 1366, 'height' => 2049);
$arSrcsetCrop5 = Array('width' => 1920, 'height' => 2880);


$arColors = [];
if($item["OFFERS"] && is_array($item["OFFERS"]))
{
  $colorsCount = 0;
  foreach ($item["OFFERS"] as $offer)
  {
    if(!empty($offer["PROPERTIES"]["MORE_PHOTO"]['VALUE']))
    {
      $arColors[ trim($offer["PROPERTIES"]['COLOR']['VALUE']) ] = $offer["PROPERTIES"]['MORE_PHOTO']['VALUE'][0];
      $colorsCount++;
    }
  }
  
  if($colorsCount < 2)
    $arColors = [];
}

if(count($arColors))
{
  foreach($arColors as $color => $image)
  {
    $HEX = 'f0f0f0';
    $rsColor = CIBlockElement::GetList([], ['IBLOCK_ID' => '9', 'NAME' => $color], false, false, ['ID', 'IBLOCK_ID', 'NAME', 'CODE']);
    if($rsColor->SelectedRowsCount() > 0)
    {
      $arColor = $rsColor->Fetch();
      $HEX = $arColor['CODE'];
    }
    
    $arColorImage = CFile::ResizeImageGet($image, $arImageCrop, BX_RESIZE_IMAGE_EXACT);
    
    $arTmpColors[] = [
      'HEX' => '#'. $HEX,
      'SRC' => $arColorImage['src'],
      'NAME' => $color,
    ];
  }
  
  $arColors = $arTmpColors;
  unset($arTmpColors);
}
?>
<a id="offer<?=$item['ID']?>"></a>
<?if($arResult['ITEM']['PROPERTIES']['FULL']['VALUE_XML_ID'] == 'Y'):?>
  <div class="indent">
<?endif?>
    
<?if(!empty($item['PROPERTIES']['VIDEO_IMAGE']['VALUE']) && !empty($item['PROPERTIES']['VIDEO_FILES']['VALUE'])):?>
    
  <div class="image video-wrapper">
    <a href="<?=$item['DETAIL_PAGE_URL']?>">
      <?$arVideoImage = CFile::ResizeImageGet($item['PROPERTIES']['VIDEO_IMAGE']['VALUE'], $arImageCrop, BX_RESIZE_IMAGE_EXACT); ?>
      <video autoplay="autoplay" class="product-media _videoLoaded _videoPlaying" poster="<?=$arVideoImage['src']?>" playsinline="" loop="" muted="">
        <?foreach($item['PROPERTIES']['VIDEO_FILES']['VALUE'] as $videoID):?>
          <?$arFile = CFile::GetFileArray($videoID);?>
          <source src="<?=$arFile['SRC']?>" type="<?=$arFile['CONTENT_TYPE']?>">
        <?endforeach;?>
        <img class="_imageLoaded _imgImpressions" src="<?=$arVideoImage['src']?>">
      </video>
    </a>
  </div>
    
<?elseif(!empty($item['DETAIL_PICTURE'])):?>
    
  <? $arImage = CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arImageCrop, BX_RESIZE_IMAGE_EXACT); ?>
  <? $arImageTablet = CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arImageCropTablet, BX_RESIZE_IMAGE_EXACT); ?>
  <? $arImageMobile = CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arImageCropMobile, BX_RESIZE_IMAGE_EXACT); ?>
    
  <?if(!empty($item['PROPERTIES']['FULL_IMAGE']['VALUE'])):?>
    
    <? //$arImage['src'] = CFile::GetPath($item['PROPERTIES']['FULL_IMAGE']['VALUE']); ?>
    <? $arImage = CFile::ResizeImageGet($item['PROPERTIES']['FULL_IMAGE']['VALUE'], Array('width' => 1920, 'height' => 1920), BX_RESIZE_IMAGE_PROPORTIONAL); ?>
    
    <div class="image">
      <a href="<?=$item['DETAIL_PAGE_URL']?>"><img src="<?=$arImage['src']?>" itemprop="image" /></a>
    </div>
    
  <?elseif($arResult['ITEM']['PROPERTIES']['FULL']['VALUE_XML_ID'] == 'Y' && !empty($arResult['ITEM']['PROPERTIES']['IMAGES']['VALUE'])):?>
    
    <div class="slider-in-section">
      <div class="image"><a href="<?=$item['DETAIL_PAGE_URL']?>"><img src="<?=$arImage['src']?>" /></a></div>
      <?foreach($arResult['ITEM']['PROPERTIES']['IMAGES']['VALUE'] as $imageID):?>
        <? $arImagesMore = CFile::ResizeImageGet($imageID, $arImageCrop, BX_RESIZE_IMAGE_EXACT); ?>
        <div class="image"><a href="<?=$item['DETAIL_PAGE_URL']?>"><img src="<?=$arImagesMore['src']?>" itemprop="image" /></a></div>
      <?endforeach?>
    </div>
    
  <?else:?>
    
    
    <?if(count($arColors)):?>
    
      <div class="image" data-flag="else colors">
        <a href="<?=$item['DETAIL_PAGE_URL']?>">
          <?foreach($arColors as $index => $color):?>
            <img data-color="<?=$index?>" src="<?=$color['SRC']?>"<?if($index):?> style="display:none"<?endif?> itemprop="image" />
          <?endforeach?>
        </a>
        <div class="colors">
          <?foreach($arColors as $index => $color):?>
            <div class="color hover js-switch-color<?if(!$index):?> active<?endif?>" data-color="<?=$index?>" style="background-color: <?=$color['HEX']?>" title="<?=$color['NAME']?>"></div>
          <?endforeach?>
        </div>
      </div>
    
    <?else:?>
    
      <div class="image" data-flag="else">
        <? /* <a href="<?=$item['DETAIL_PAGE_URL']?>"><img <?if($arParams['START_HIDE_SRC'] == 'Y'):?>data-img-<?endif?>src="<?=$arImage['src']?>" /></a> */ ?>
        
        <a href="<?=$item['DETAIL_PAGE_URL']?>">
          
          <? /* if(!$isRuleModerator):?>
          
            <picture>
              <source <?if($arParams['START_HIDE_SRC'] == 'Y'):?>data-img-<?endif?>srcset="<?=$arImage['src']?>" media="(min-width: 769px)" />
              <source <?if($arParams['START_HIDE_SRC'] == 'Y'):?>data-img-<?endif?>srcset="<?=$arImageTablet['src']?>" media="(min-width: 415px)" />
              <img <?if($arParams['START_HIDE_SRC'] == 'Y'):?>data-img-<?endif?>src="<?=$arImageMobile['src']?>" alt="<?=$productTitle?>" itemprop="image">
            </picture>
          
          <? else: */ ?>
          
            <? $arImageStep1 = CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arSrcsetCrop1, BX_RESIZE_IMAGE_EXACT, false, false, false, 75); ?>
            <? $arImageStep2 = CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arSrcsetCrop2, BX_RESIZE_IMAGE_EXACT, false, false, false, 75); ?>
            <? $arImageStep3 = CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arSrcsetCrop3, BX_RESIZE_IMAGE_EXACT, false, false, false, 75); ?>
            <? $arImageStep4 = CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arSrcsetCrop4, BX_RESIZE_IMAGE_EXACT, false, false, false, 75); ?>
            <? $arImageStep5 = CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arSrcsetCrop5, BX_RESIZE_IMAGE_EXACT, false, false, false, 75); ?>
          
            <?
//              CModule::IncludeModule("delight.webpconverter");
//              if($item['ID'] == 3235 && CModule::IncludeModule("delight.webpconverter"))
//              {
//                $OriginalFilePath = $_SERVER["DOCUMENT_ROOT"] . $arImageStep5['src'];
//                $DestinationFilePath = $_SERVER["DOCUMENT_ROOT"] .'/webp'. $arImageStep5['src'] .'.webp';
//                WebPConvert\WebPConvert::convert($OriginalFilePath, $DestinationFilePath);
//              }
            ?>
          
            <picture data="webp" data-id="<?=$item['ID']?>" data-file-dest="<?=$DestinationFilePath?>">
              
              <source type="image/webp" media="(max-width: 428px)" srcset="<?=$arImageStep1['src']?>">
              <source type="image/webp" media="(max-width: 768px)" srcset="<?=$arImageStep2['src']?>">
              <source type="image/webp" media="(max-width: 1024px)" srcset="<?=$arImageStep3['src']?>">
              <source type="image/webp" media="(max-width: 1366px)" srcset="<?=$arImageStep4['src']?>">
              <source type="image/webp" srcset="<?=$arImageStep5['src']?>">

              <? /*
              <source type="image/webp"
                      <?if($arParams['START_HIDE_SRC'] == 'Y'):?>data-img-<?endif?>srcset="<?=$arImageStep1['src']?> 428w,
                              <?=$arImageStep2['src']?> 768w,
                              <?=$arImageStep3['src']?> 1024w,
                              <?=$arImageStep4['src']?> 1366w,
                              <?=$arImageStep5['src']?> 1920w">
              */ ?>
              
              <img alt="" 
                   <?if($arParams['START_HIDE_SRC'] == 'Y'):?>data-img-<?endif?>srcset="<?=$arImageStep1['src']?> 428w,
                           <?=$arImageStep2['src']?> 768w,
                           <?=$arImageStep3['src']?> 1024w,
                           <?=$arImageStep4['src']?> 1366w,
                           <?=$arImageStep5['src']?> 1920w"
                   <?if($arParams['START_HIDE_SRC'] == 'Y'):?>data-img-<?endif?>src="<?=$arImage['src']?>">
            </picture>

          <? // endif?>
          
        </a>

      </div>
    
    <?endif?>
    
    
  <?endif?>
    
    
<?else:?>
    
  <div class="image" >
    <a href="<?=$item['DETAIL_PAGE_URL']?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/default.jpg" itemprop="image" /></a>
  </div>
    
<?endif?>
<meta itemprop="description" content="<?=$productTitle?>">
<div class="name" itemprop="name">
  <a class="hover" href="<?=$item['DETAIL_PAGE_URL']?>" itemprop="url"><?=$productTitle?></a>
  <?if($isRuleModerator):?>
    <?if(in_array($item['PRODUCT']['TYPE'], [1, 3])):?>
      <a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=<?=CATALOG_ID?>&type=catalogs&ID=<?=$item['ID']?>&lang=ru" target="_blank" class="other-info"><?=Loc::getMessage("CATALOG_ITEM_SORT")?> <?=$arResult['ITEM']['SORT']?>, <?=Loc::getMessage("CATALOG_ITEM_ARTICUL")?> <?=$item['PROPERTIES']['CML2_ARTICLE']['VALUE']?></a>
    <?else:?>
      <a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=<?=CATALOG_ID?>&type=catalogs&ID=<?=$arResult['ITEM']['LINK_ELEMENT_ID']?>&lang=ru" target="_blank" class="other-info"><?=Loc::getMessage("CATALOG_ITEM_SORT")?> <?=$arResult['ITEM']['SORT']?>, <?=Loc::getMessage("CATALOG_ITEM_ARTICUL")?> <?=$item['ARTICLE']?></a>
    <?endif?>
  <?endif?>
</div>


<?
if (!empty($arParams['PRODUCT_BLOCKS_ORDER']))
{
  foreach ($arParams['PRODUCT_BLOCKS_ORDER'] as $blockName)
  {
    switch ($blockName)
    {
      case 'price': ?>
        <div itemscope itemprop="offers" itemtype="http://schema.org/Offer">
          <div class="price product-item-price-current" id="<?=$itemIds['PRICE']?>" itemprop="price">
            <?
            //dbgz($price);
            if (!empty($price))
            {
              if($price['DISCOUNT'])
              {
                ?><span class="price-old" title="<?=$price['PRINT_RATIO_BASE_PRICE']?>"><?=$price['PRINT_RATIO_BASE_PRICE']?></span><?
              }
              
              if ($arParams['PRODUCT_DISPLAY_MODE'] === 'N' && $haveOffers)
              {
                echo Loc::getMessage(
                  'CT_BCI_TPL_MESS_PRICE_SIMPLE_MODE',
                  array(
                    '#PRICE#' => $price['PRINT_RATIO_PRICE'],
                    '#VALUE#' => $measureRatio,
                    '#UNIT#' => $minOffer['ITEM_MEASURE']['TITLE']
                  )
                );
              }
              else
              {
                echo $price['PRINT_RATIO_PRICE'];
              }
            }
            ?>
            <meta itemprop="priceCurrency" content="<?=$price['CURRENCY']?>">
            <meta itemprop="availability" content="http://schema.org/InStock" />
          </div>
        </div>
        <?
        break;
    }
  }
}
?>
    
<?if($arResult['ITEM']['PROPERTIES']['FULL']['VALUE_XML_ID'] == 'Y'):?>
  </div>
<?endif?>


