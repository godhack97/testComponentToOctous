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
$this->setFrameMode(true);
?>
<div class="main-history product-history">
  <div class="items initial-end">
    <div class="items-init">
      <?foreach($arResult["ITEMS"] as $arItem):?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        
        $arImageCrop = CFile::ResizeImageGet($arItem['DETAIL_PICTURE']['ID'], Array('width' => 130, 'height' => 130), BX_RESIZE_IMAGE_EXACT);
        
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
        <div class="item hover" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
          <a href="<?=$link?>"><div class="image" style="background-image: url(<?=$arImageCrop['src']?>)"></div></a>
          <a href="<?=$link?>"><div class="name"><?=$arItem['NAME']?></div></a>
        </div>
      <?endforeach;?>
    </div>
  </div>
</div>




