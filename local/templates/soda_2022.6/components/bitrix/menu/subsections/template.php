<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
  <div class="subsections-top mode-static" itemscope="" itemtype="http://schema.org/SiteNavigationElement">
    <?foreach($arResult as $arItem):?>
      <div class="sub-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ItemList">
        <a href="<?=$arItem['LINK']?>" class="black<?if($arItem['SELECTED']):?> active<?endif?>" itemprop="url">
          <?=$arItem['TEXT']?>
          <meta itemprop="name" content="<?=$arItem["TEXT"]?>" />
        </a>
      </div>
    <?endforeach?>
  </div>
<?endif?>