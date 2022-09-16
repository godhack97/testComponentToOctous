<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Localization\Loc;
$this->setFrameMode(true);?>
<div class="indent js-sticky-kit-go" itemscope="" itemtype="http://schema.org/SiteNavigationElement">
  <?if (!empty($arResult)):?>
    <div class="menu-left-top" itemprop="about" itemscope="" itemtype="http://schema.org/ItemList">
      
      <div class="item allotted">
        <a href="<?if(Loc::getCurrentLang() == 'en'):?>/en<?endif?>/catalog/new/" class="link-parent hover"><?=Loc::getMessage("MENU_NEW_COLLECTION")?></a>
        <div class="subs display">
          <?foreach($arResult['CATALOG_MENU'] as $arItem):
              if($arItem['DEPTH_LEVEL'] > 1) continue;?>
            <?
              $isIndent = '';
              if(in_array($arItem["LINK"], ['/catalog/verkhnyaya-odezhda/', '/catalog/odezhda-dlya-kosmetologov/']))
                $isIndent = ' is-indent';
            ?>
            <?if($arItem["SELECTED"]):?>
              <div class="sub-item-wrap<?=$isIndent?> active" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ItemList">
                <a class="sub-item hover" href="<?=$arItem["LINK"]?>" itemprop="url">
                  <?=$arItem["TEXT"]?>
                  <meta itemprop="name" content="<?=$arItem["TEXT"]?>" />
                </a>
              </div>
            <?else:?>
              <div class="sub-item-wrap<?=$isIndent?>" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ItemList">
                <a class="sub-item hover" href="<?=$arItem["LINK"]?>" itemprop="url">
                  <?=$arItem["TEXT"]?>
                  <meta itemprop="name" content="<?=$arItem["TEXT"]?>" />
                </a>
              </div>
            <?endif?>
          <?endforeach;?>
        </div>
      </div>
      
      <!-- div class="item allotted">
        <a class="link-parent hover" href="/catalog/sale/"><?=Loc::getMessage("MENU_SALE")?></a>
      </div -->
      
    </div>
    <div class="menu-left-bottom" itemprop="about" itemscope="" itemtype="http://schema.org/ItemList">
      <div class="toggle hover show js-menu-left-toggle"><?=Loc::getMessage("MENU_INFO")?></div>
      <div class="items <?=(CSite::InDir("/company/") || CSite::InDir("/contacts/")) ? 'display' : '';?>">
        <?foreach($arResult['INFO_MENU'] as $arItem):?>
          <?if($arItem["SELECTED"]):?>
            <div class="item active" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ItemList">
              <a class="hover" href="<?=$arItem["LINK"]?>" itemprop="url">
                <?=$arItem["TEXT"]?>
                <meta itemprop="name" content="<?=$arItem["TEXT"]?>" />
              </a>
            </div>
          <?else:?>
            <div class="item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ItemList">
              <a class="hover" href="<?=$arItem["LINK"]?>" itemprop="url">
                <?=$arItem["TEXT"]?>
                <meta itemprop="name" content="<?=$arItem["TEXT"]?>" />
              </a>
            </div>
          <?endif?>
        <?endforeach;?>
      </div>
    </div>
  <?endif?>
</div>