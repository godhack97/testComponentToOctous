<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @global string $componentPath
 * @global string $templateName
 * @var CBitrixComponentTemplate $this
 */
?>

<div class="wrap-icons hover">
  <img class="for-desktop" src="<?=SITE_TEMPLATE_PATH?>/images/icons/basket-mobile.svg" />
  <img class="for-mobile" src="<?=SITE_TEMPLATE_PATH?>/images/icons/basket-mobile.svg" />
  <div class="count <?if($arResult["PRODUCT_COUNT"]>0){?>display<?}?>"><?=$arResult["PRODUCT_COUNT"]?></div>
</div>

<div class="basket-mini" style="opacity:1; visibility: visible;">
  <div class="indent">
    <div class="items">
        <?foreach ($arResult["CATEGORIES"]["READY"] as $item){?>
            <div class="item">
                <a class="image hover" href="<?=$item["DETAIL_PAGE_URL"]?>" style="background-image: url(<?=$item["PICTURE_SRC"]?>)" target="_blank"></a>
                <div class="data">
                    <div class="wrap-text">
                        <div class="name"><a class="hover" target="_blank" href="<?=$item["DETAIL_PAGE_URL"]?>"><?=$item["NAME"]?></a></div>
                        <div class="props">
                            <?foreach ($item["PROP"] as $prop){?>
                                <?
                                if($prop["CODE"]=="PRODUCT.XML_ID" || $prop["CODE"]=="CATALOG.XML_ID" || $prop["CODE"]=="CML2_ARTICLE")
                                    continue;
                                ?>
                                <div class="prop">
                                    <?/*<span class="title"><?=$prop["NAME"]?>:</span>*/?>
                                    <span class="value"><?=$prop["VALUE"]?></span>
                                </div>
                            <?}?>
                        </div>
                    </div>
                    <div class="price"><?=$item["SUM"]?></div>
                </div>
            </div>
        <?}?>
    </div>
    <div class="cost">
        <div class="col name">Стоимость:</div>
        <div class="col total"><?=$arResult["TOTAL_PRICE"]?></div>
    </div>
    <a class="ibutton" href="<?=$arParams["PATH_TO_BASKET"]?>">Смотреть корзину</a>
  </div>
</div>