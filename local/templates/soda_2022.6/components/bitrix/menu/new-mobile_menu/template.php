<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Localization\Loc;
$this->setFrameMode(true);?>


<a href='/catalog/new/' class="menu-title new-title hover"><?=Loc::getMessage("MENU_NEW_COLLECTION")?></a>

<?if (!empty($arResult)):?>
  <div class="menu-top" itemscope="" itemtype="http://schema.org/SiteNavigationElement">
    <a href='/catalog/new/' class="menu-title clothes-title hover"><?=Loc::getMessage("MENU_CLOTHES")?></a>
    <nav class="menu-list" itemprop="about" itemscope="" itemtype="http://schema.org/ItemList">
      <?foreach($arResult['CATALOG_MENU'] as $arItem):?>
        <?if($arItem['DEPTH_LEVEL'] > 1) continue;?>

        <?
          $isIndent = '';
          if(in_array($arItem["LINK"], ['/catalog/verkhnyaya-odezhda/', '/catalog/odezhda-dlya-kosmetologov/']))
            $isIndent = ' is-indent';
        ?>

        <div class="menu-item-wrap<?if($arItem["SELECTED"]):?> active<?endif?><?=$isIndent?>" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ItemList">
          <a class="menu-item hover" href="<?=$arItem["LINK"]?>" itemprop="url">
            <?=$arItem["TEXT"]?>
            <meta itemprop="name" content="<?=$arItem["TEXT"]?>" />
          </a>
        </div>

      <?endforeach?>
    </nav>
  </div>

  <div class="menu-bottom" itemscope="" itemtype="http://schema.org/SiteNavigationElement">
    <p class="menu-title info-title menu-toggle-btn"><?=Loc::getMessage("MENU_INFO")?></p>
    <div class="menu-list menu-toggle-block" itemprop="about" itemscope="" itemtype="http://schema.org/ItemList">
      <?foreach($arResult['INFO_MENU'] as $arItem):?>
        <div class="menu-item-wrap<?if($arItem["SELECTED"]):?> active<?endif?>" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ItemList">
          <a class="menu-item hover" href="<?=$arItem["LINK"]?>" itemprop="url">
            <?=$arItem["TEXT"]?>
            <meta itemprop="name" content="<?=$arItem["TEXT"]?>" />
          </a>
        </div>
      <?endforeach;?>
    </div>
  </div>
<?endif?>

<a href='/personal/profile/' class="menu-title profile-title hover"><?=Loc::getMessage("MENU_PROFILE")?></a>

<a href="https://apps.apple.com/in/app/sodamoda/id1582756296" class="appstore-link hover" target="_blank">
  <div class="appstore-block">
    <img src="<?= SITE_TEMPLATE_PATH ?>/images/menu-appstore-img.png">
  </div>
</a>

<?/*
<div class="menu-products">
  <div class="menu-products-list">
    <a class="menu-products-item hover">
      <img src="<?= SITE_TEMPLATE_PATH ?>/images/three1.jpg" alt="product">
    </a>
    <a class="menu-products-item hover">
      <img src="<?= SITE_TEMPLATE_PATH ?>/images/three1.jpg" alt="product">
    </a>
    <a class="menu-products-item hover">
      <img src="<?= SITE_TEMPLATE_PATH ?>/images/three1.jpg" alt="product">
    </a>
    <a class="menu-products-item hover">
      <img src="<?= SITE_TEMPLATE_PATH ?>/images/three1.jpg" alt="product">
    </a>
    <a class="menu-products-item hover">
      <img src="<?= SITE_TEMPLATE_PATH ?>/images/three1.jpg" alt="product">
    </a>
  </div>
</div>
*/?>