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
<div class="main-slider initial-end" data-mobile="<?=$arParams['IS_MOBILE']?>" data-tablet="<?=$arParams['IS_TABLET']?>">
  <div class="main-slider-init">
    <?foreach($arResult["ITEMS"] as $arItem):?>
      <?
      $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
      $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

      if($arParams['IS_MOBILE'] == 'Y')
      {
        //$arItem['DETAIL_PICTURE']['SRC'] = CFile::GetPath($arItem['PROPERTIES']['IMAGE']['VALUE']);
        $arImageCrop = CFile::ResizeImageGet($arItem['PROPERTIES']['IMAGE']['VALUE'], Array('width' => 800, 'height' => 1200), BX_RESIZE_IMAGE_EXACT);
      }
      else
      {
        $arImageCrop = CFile::ResizeImageGet($arItem['DETAIL_PICTURE']['ID'], Array('width' => 1920, 'height' => 1275), BX_RESIZE_IMAGE_EXACT);
      }
      ?>
    
      <?if($arItem['ID'] == 1819 && Loc::getCurrentLang() == 'ru'):?>
    
        <div class="item<?if($arItem['PROPERTIES']['DARK']['VALUE_XML_ID'] == 'Y'):?> is-dark-mode<?endif?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
          <div class="item-center">
            <div class="item-content">
              <a href="/catalog/maski/maska-komplekt-3-shtuki/"><img src="/upload/iblock/627/IMG_5074.jpg"></a>
              <a class="name hover" href="/catalog/maski/maska-komplekt-3-shtuki/">Маска (комплект из 3-х штук)</a>
              <a href="/catalog/maski/maska-komplekt-3-shtuki/" class="ibutton hollow">Купить</a>
            </div>
          </div>
        </div>
    
      <?else:?>
    
        <?
          $arImageCrop = CFile::ResizeImageGet($arItem['DETAIL_PICTURE']['ID'], Array('width' => 1920, 'height' => 1275), BX_RESIZE_IMAGE_EXACT); 
          $arImageCropMobile = CFile::ResizeImageGet($arItem['PROPERTIES']['IMAGE']['VALUE'], Array('width' => 800, 'height' => 1200), BX_RESIZE_IMAGE_EXACT);
          
          $link = '';
          if(!empty($arItem['PROPERTIES']['LINK']['VALUE']))
            $link = $arItem['PROPERTIES']['LINK']['VALUE'];
          else if(!empty($arItem['PROPERTIES']['LINK_SECTION']['VALUE']))
          {
            $rsSection = CIBlockSection::GetByID($arItem['PROPERTIES']['LINK_SECTION']['VALUE']);
            if($arSection = $rsSection->GetNext())
              $link = $arSection['SECTION_PAGE_URL'];
          }
        ?>
    
        <div class="item<?if($arItem['PROPERTIES']['DARK']['VALUE_XML_ID'] == 'Y'):?> is-dark-mode<?endif?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
          <?if(!empty($link)):?>
            <a class="link" href="<?=$link?>">
              <div class="image image-mobile" style="background-image: url(<?=$arImageCropMobile['src']?>)"></div>
              <div class="image image-desktop" style="background-image: url(<?=$arImageCrop['src']?>)"></div>
              <!-- picture>
                <source srcset="<?=$arImageCrop['src']?>" media="(min-width: 769px)" />
                <img src="<?=$arImageCropMobile['src']?>" alt="<?=$productTitle?>">
              </picture -->
            </a>
          <?endif?>
        </div>
    
      <?endif?>
    
    <?endforeach?>
  </div>
  <?if(false): //$USER->IsAdmin()?>
    <a class="action-banner-main" href="<?if(Loc::getCurrentLang() == 'en'):?>/en<?endif?>/catalog/sale/">
      <div class="plank"><?=Loc::getMessage("ACTION_BANNER_TEXT")?></div>
    </a>
  <?endif?>
</div>