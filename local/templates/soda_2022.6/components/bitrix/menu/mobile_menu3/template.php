<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Localization\Loc;
$this->setFrameMode(true);

// CUtil::InitJSCore();
?>

<!-- <div class="popups popup-new-menu">
  <div class="scroll-wrapper">
    <div class="wrapper">
      <div class="langs-change">
        <div class="langs-change-wrap lang">
          <p class="langs-change-title">Language</p>
          <div class="langs-change-list">
            <div class="item<? if ($dataLangCurrency['lang'] == 'EN') : ?> active<? endif ?>"><a href="?language=EN">English</a></div>
            <div class="item<? if ($dataLangCurrency['lang'] == 'RU') : ?> active<? endif ?>"><a href="?language=RU">Русский</a></div>
          </div>
        </div>
        <div class="langs-change-wrap currency">
          <p class="langs-change-title">Currency</p>
          <div class="langs-change-list">
            <div class="item<? if ($dataLangCurrency['currency'] == 'usd') : ?> active<? endif ?>"><a href="?currency=usd">USD</a></div>
            <div class="item<? if ($dataLangCurrency['currency'] == 'aed') : ?> active<? endif ?>"><a href="?currency=aed">AED</a></div>
            <div class="item<? if ($dataLangCurrency['currency'] == 'rub') : ?> active<? endif ?>"><a href="?currency=rub">RUB</a></div>
          </div>
        </div>
      </div>
      <nav class="mobile-menu">
        <ul class="menu-list">
          <li class="menu-item">
            <div class="menu-name">
              <span>New in</span>
              <div class="menu-arrow">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/menu-arrow-back.svg" alt="arrow-back">
              </div>
            </div>
            <div class="menu-content">
              <div class="menu-content-back">
                <div class="menu-arrow arrow-back">
                  <img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/menu-arrow-back.svg" alt="arrow-back">
                </div>
                <span>Назад</span>
              </div>
              <div class="menu-content-name">
                <span>Новинки</span>
              </div>
              <ul class="menu-content-list">
                <li class="menu-content-item"><span>Джинсы</span></li>
                <li class="menu-content-item"><span>Обувь</span></li>
                <li class="menu-content-item"><span>Белье</span></li>
                <li class="menu-content-item"><span>Платья</span></li>
                <li class="menu-content-item"><span>Костюмы</span></li>
              </ul>
            </div>
          </li>
          <li class="menu-item">
            <div class="menu-name">
              <span>Clothing</span>
              <div class="menu-arrow">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/menu-arrow-back.svg" alt="arrow-back">
              </div>
            </div>
            <div class="menu-content">
              <div class="menu-content-back">
                <div class="menu-arrow arrow-back">
                  <img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/menu-arrow-back.svg" alt="arrow-back">
                </div>
                <span>Назад</span>
              </div>
              <div class="menu-content-name">
                <span>Одежда</span>
              </div>
              <ul class="menu-content-list">
                <li class="menu-content-item"><span>Джинсы</span></li>
                <li class="menu-content-item"><span>Обувь</span></li>
                <li class="menu-content-item"><span>Белье</span></li>
                <li class="menu-content-item"><span>Платья</span></li>
                <li class="menu-content-item"><span>Костюмы</span></li>
              </ul>
            </div>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</div> -->

<?if (!empty($arResult)):?>
  <div class="menu-left-top" itemscope="" itemtype="http://schema.org/SiteNavigationElement">
    <div class="item allotted">
      <a href='/catalog/new/' class="link-parent hover"><?=Loc::getMessage("MENU_NEW_COLLECTION")?></a>
      <div class="subs display" itemprop="about" itemscope="" itemtype="http://schema.org/ItemList">
        <?foreach($arResult['CATALOG_MENU'] as $arItem):
            if($arItem['DEPTH_LEVEL'] > 1) continue;
            ?>
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
        <?endforeach?>
      </div>
    </div>
  </div>

  <div class="menu-left-bottom" itemscope="" itemtype="http://schema.org/SiteNavigationElement">
    <div class="toggle hover show js-menu-left-toggle"><?=Loc::getMessage("MENU_INFO")?></div>
    <div class="items <?= (CSite::InDir('/company/') || CSite::InDir('/contacts/')) ? 'display' : ''; ?>" itemprop="about" itemscope="" itemtype="http://schema.org/ItemList">
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