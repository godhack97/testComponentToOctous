<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<div class="products-more">
  <div class="caption">Дополнить модный образ</div>
  <div class="products-more-slider-wrapper">
    <div class="products-more_items">

      <?foreach ($arResult["ITEMS"] as $arItem):?>

        <?
          if(empty($arItem["DETAIL_PICTURE"]))
            continue;

          $arSizeCropTablet = Array('width' => 768, 'height' => 1152);
          $arImageTablet = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], $arSizeCropTablet, BX_RESIZE_IMAGE_EXACT);
        ?>

        <div class="item">

          <?if(is_array($arItem["DETAIL_PICTURE"])):?>
            <div class="image">
              <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
                <img src="<?=$arImageTablet['src']?>" />
              </a>
            </div>
          <?endif?>

          <div class="name"><a class="hover" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></div>

          <?foreach($arItem["ITEM_PRICES"] as $code => $arPrice):?>
            <div class="price"><?=$arPrice["PRINT_PRICE"]?></div>
          <?endforeach?>

          <? /*
          <div class="form">
            <?if($arItem["DISPLAY_PROPERTIES"]["SIZES"]["DISPLAY_VALUE"]):?>
              <select name="size">
                <option selected="selected" disabled="disabled">Выберите размер</option>
                <?foreach($arItem["DISPLAY_PROPERTIES"]["SIZES"]["DISPLAY_VALUE"] as $val):?>
                  <option><?=$val?></option>
                <?endforeach?>
              </select>
            <?endif?>
            <a class="ibutton hollow">Добавить</a>
          </div>
          */ ?>

          <!-- a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="ibutton hollow">Подробнее</a -->

        </div>
      <?endforeach?>

    </div>
  </div>
</div>