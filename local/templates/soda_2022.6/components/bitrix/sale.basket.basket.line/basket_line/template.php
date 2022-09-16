<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

if ($GLOBALS["LANG"] === "RU") {
    $size = "РАЗМЕР";
    $article = "АРТИКУЛ";
} else {
    $size = "SIZE";
    $article = "ARTICLE";
}
?>
<a href="<?= $arParams["PATH_TO_BASKET"] ?>" class="item-link wrap-icons hover">
  <img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/basket-mobile.svg" />
  <div class="count <? if ($arResult["PRODUCT_COUNT"] > 0) { ?>display<? } ?>"><?= $arResult["PRODUCT_COUNT"] ?></div>
</a>

<div class="basket-mini" data-dbg="line">
  <div class="indent">
    <div class="items">
      <?php if (is_array($arResult['CATEGORIES']['READY']) && count($arResult['CATEGORIES']['READY'])) { ?>
        <? foreach ($arResult["CATEGORIES"]["READY"] as $item) : ?>
          <div class="item">
            <a class="image hover" href="<?= $item["DETAIL_PAGE_URL"] ?>" style="background-image: url(<?= $item["PICTURE_SRC"] ?>)" target="_blank"></a>
            <div class="data">
              <div class="wrap-text">
                <div class="name"><a class="hover" target="_blank" href="<?= $item["DETAIL_PAGE_URL"] ?>"><?= $item["NAME"] ?></a></div>
                <div class="props">
                  <?php
                  foreach ($item['PROP'] as $prop) {
                    if ($prop['CODE'] == 'CML2_ARTICLE') {
                  ?>
                      <div class="prop">
                        <span class='title'><?= $article ?></span>
                        <span class="value"><?= $prop['VALUE'] ?></span>
                      </div>
                    <?php
                    }
                    if ($prop['CODE'] == 'SIZES') {
                    ?>
                      <div class="prop">
                        <span class='title'><?= $size ?></span>
                        <span class="value"><?= $prop['VALUE'] ?></span>
                      </div>
                    <?php
                    }
                  }
                  foreach ($item["PROP"] as $prop) {
                    if ($prop["CODE"] == "PREORDER_PRODUCT") {
                    ?>
                      <div class="prop">
                        <span class="value"><?= $prop["NAME"] ?></span>
                      </div>
                    <?php
                    }
                    if ($prop["CODE"] == "SIZES" || $prop["CODE"] == "PRODUCT.XML_ID" || $prop["CODE"] == "CATALOG.XML_ID" || $prop["CODE"] == "CML2_ARTICLE" || $prop["CODE"] == "PREORDER_PRODUCT")
                      continue;
                    ?>
                    <div class="prop">
                      <span class="value"><?= $prop["VALUE"] ?></span>
                    </div>
                  <?php
                  }
                  ?>
                </div>
              </div>
              <div class="price <?= $arResult['USE_EXTEND_CURRENCY'] ? 'alternative-currency' : '' ?>"><?= $item["SUM"] ?></div>
              <?php
              if ($arResult['USE_EXTEND_CURRENCY']) {
              ?>
                <div class="price"><?= $item["SUM_EXTEND"] ?></div>
              <?php
              }
              ?>
            </div>
          </div>
        <? endforeach ?>
      <?php } ?>
    </div>
    <div class="cost">
      <div class="col name"><?= Loc::getMessage("BASKET_LINE_PRICE") ?></div>
      <div class="col total <?= $arResult['USE_EXTEND_CURRENCY'] ? 'alternative-currency' : '' ?>"><?= $arResult["TOTAL_PRICE"] ?></div>
      <?php
      if ($arResult['USE_EXTEND_CURRENCY']) {
      ?>
        <div class="col total"><?= $arResult["TOTAL_PRICE_EXTEND"] ?></div>
      <?php
      }
      ?>
    </div>
    <a class="ibutton" href="<?= $arParams["PATH_TO_BASKET"] ?>"><?= Loc::getMessage("BASKET_LINE_SHOW_CART") ?></a>
  </div>
</div>