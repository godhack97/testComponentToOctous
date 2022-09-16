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


//if($arParams['IS_MOBILE'] == 'Y')
//  $arImageCrop = Array('width' => 414, 'height' => 621);
//else
  $arImageCrop = Array('width' => 800, 'height' => 1200);

$arImageCropTablet = Array('width' => 768, 'height' => 1152);
$arImageCropMobile = Array('width' => 620, 'height' => 930); //Array('width' => 414, 'height' => 621);

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


$PRICE_DEALER = '900 руб.';

if(in_array($item['ARTICLE'], ['1146', '1239', '1114-627', '1120-703']))
  $PRICE_DEALER = '1 900 руб.';

if(in_array($item['ARTICLE'], ['1093-701']))
  $PRICE_DEALER = '2 500 руб.';

if(in_array($item['ARTICLE'], ['1092-302']))
  $PRICE_DEALER = '3 900 руб.';

?>

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
        
        <a>
          <picture>
            <source <?if($arParams['START_HIDE_SRC'] == 'Y'):?>data-img-<?endif?>srcset="<?=$arImage['src']?>" media="(min-width: 769px)" />
            <source <?if($arParams['START_HIDE_SRC'] == 'Y'):?>data-img-<?endif?>srcset="<?=$arImageTablet['src']?>" media="(min-width: 415px)" />
            <img <?if($arParams['START_HIDE_SRC'] == 'Y'):?>data-img-<?endif?>src="<?=$arImageMobile['src']?>" alt="<?=$productTitle?>" itemprop="image">
          </picture>
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
  <a><?=$productTitle?></a>
  <?if($USER->IsAdmin()):?><a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=<?=CATALOG_ID?>&type=catalogs&ID=<?=$arResult['ITEM']['LINK_ELEMENT_ID']?>&lang=ru" target="_blank" class="other-info">Сорт: <?=$arResult['ITEM']['SORT']?>, артикул: <?=$item['ARTICLE']?></a><?endif?>
</div>


<div itemscope itemprop="offers" itemtype="http://schema.org/Offer">
  <div class="price product-item-price-current" id="<?=$itemIds['PRICE']?>" itemprop="price">
    <?=$PRICE_DEALER?>
    <meta itemprop="priceCurrency" content="<?=$price['CURRENCY']?>">
    <meta itemprop="availability" content="http://schema.org/InStock" />
  </div>
</div>


<? 
/*
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
            if (!empty($price))
            {
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
} */
?>
    
<?if($arResult['ITEM']['PROPERTIES']['FULL']['VALUE_XML_ID'] == 'Y'):?>
  </div>
<?endif?>


