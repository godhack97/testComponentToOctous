<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use \Bitrix\Main\Localization\Loc;?>
<?
// $templateFolder — /local/components/arlix/order.page/templates/.default
$this->addExternalJS(SITE_TEMPLATE_PATH . "/js/vue.js");
$this->addExternalJS($templateFolder . "/js/script.js");

//$APPLICATION->SetAdditionalCss(SITE_TEMPLATE_PATH."/css/jquery-ui.structure.css");
//$APPLICATION->SetAdditionalCss(SITE_TEMPLATE_PATH."/css/jquery-ui.css");
//
//$this->addExternalJS(SITE_TEMPLATE_PATH."/js/jquery-ui.min.js");
//$this->addExternalJS(SITE_TEMPLATE_PATH."/js/jquery.validate/jquery.validate.js");

?>
<div class="order-page first" style="opacity: 0;visibility: hidden;">
  <div class="element_block" id="appSearchPage">
    <h1>Поиск</h1>
    <br><br>
    <div class="element_block__text" v-if="!element">
      <?=Loc::getMessage('ORDER_PAGE_SEARCH_INFO')?>
    </div>
    <div class="element_block__top" v-if="element">
      <div class="element_block__left"></div>
      <div class="element_block__right">
        
        <div class="element_info_cols">
          <div class="element_info_col_image">
            <img class="element_block__pict" v-if="item.ID==element.select_color_sizes"
                 v-for="item in element.offers_color_list" :style="item.SRC" :src="item.SRC" :href="item.SRC" data-fancybox="gallery" />
          </div>
          <div class="element_info_col_desc">
            <h1 class="element_block__title">{{element.name}}</h1>

            <template v-for="item in element.color_sizes" v-if="item.VALUE_XML_ID==element.select_color_sizes">
              <div class="element_block__price" v-if="size.OFFER_ID==element.select_offer_id"
                   v-for="size in item.SIZES">{{formatNumber(size.price)}} руб.
              </div>
            </template>

            <div class="element_block__color" v-if="element.color_sizes.length">
              <ul>
                <li v-on:click="setSelectColor(item.VALUE_XML_ID)" v-for="item in element.color_sizes"
                    v-bind:class="item.VALUE_XML_ID==element.select_color_sizes ? 'select' : ''">{{item.VALUE}}
                </li>
              </ul>
            </div>
            
            <div class="element_block__prop" v-for="item in element.prop">{{item.name}}: {{item.value}}</div>

            <? /*<div class="element_block__text">Небольное описание данного товара, характеризуюшее его особенности и признаки</div>*/ ?>
          </div>
        </div>
        <div class="element_block__table" v-if="element.color_sizes.length">
          <table v-for="item in element.color_sizes" v-if="item.VALUE_XML_ID==element.select_color_sizes">
            <tr>
              <th>Склад</th>
              <th v-for="size in item.SIZES">{{size.VALUE}}</th>
            </tr>
            <tr v-for="(store, index) in element.stores">
              <td>{{store.TITLE}}</td>
              <td     v-on:click="setSelectOfferAndStores(size.OFFER_ID,store.ID)"
                      v-bind:class="size.OFFER_ID==element.select_offer_id && store.ID==element.select_stores? 'select' : ''"
                      v-for="size in item.SIZES">{{getSizeStore(size.stores,index)}}
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <div class="element_block__bottom" v-if="element">
      <div class="element_block__left"></div>
      <div class="element_block__right">
        <a @click.prevent="addBasket()" class="ibutton" data-can-buy="0" rel="nofollow"><?=Loc::getMessage('ORDER_PAGE_ADD')?></a>
      </div>
    </div>
  </div>
  <div class="order-cart" id="id-order-cart">
      <? $APPLICATION->IncludeComponent(
          "bitrix:sale.basket.basket",
          "create",
          array(
              "ACTION_VARIABLE" => "basketAction",
              "ADDITIONAL_PICT_PROP_2" => "-",
              "AUTO_CALCULATION" => "Y",
              "BASKET_IMAGES_SCALING" => "adaptive",
              "COLUMNS_LIST_EXT" => array(
                  0 => "PREVIEW_PICTURE",
                  1 => "DELETE",
                  2 => "SUM",
                  3 => "PROPERTY_SIZES",
                  4 => "PROPERTY_COLOR",
              ),
              "COLUMNS_LIST_MOBILE" => array(
                  0 => "PREVIEW_PICTURE",
                  1 => "DELETE",
                  2 => "SUM",
                  3 => "PROPERTY_SIZES",
                  4 => "PROPERTY_COLOR",
              ),
              "COMPATIBLE_MODE" => "N",
              "CORRECT_RATIO" => "Y",
              "DEFERRED_REFRESH" => "N",
              "DISCOUNT_PERCENT_POSITION" => "bottom-right",
              "DISPLAY_MODE" => "compact",
              "EMPTY_BASKET_HINT_PATH" => "/catalog/",
              "GIFTS_BLOCK_TITLE" => "Выберите один из подарков",
              "GIFTS_CONVERT_CURRENCY" => "N",
              "GIFTS_HIDE_BLOCK_TITLE" => "N",
              "GIFTS_HIDE_NOT_AVAILABLE" => "N",
              "GIFTS_MESS_BTN_BUY" => "Выбрать",
              "GIFTS_MESS_BTN_DETAIL" => "Подробнее",
              "GIFTS_PAGE_ELEMENT_COUNT" => "4",
              "GIFTS_PLACE" => "BOTTOM",
              "GIFTS_PRODUCT_PROPS_VARIABLE" => "prop",
              "GIFTS_PRODUCT_QUANTITY_VARIABLE" => "quantity",
              "GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
              "GIFTS_SHOW_OLD_PRICE" => "N",
              "GIFTS_TEXT_LABEL_GIFT" => "Подарок",
              "HIDE_COUPON" => "N",
              "LABEL_PROP" => array(),
              "PATH_TO_ORDER" => "/order/",
              "PRICE_DISPLAY_MODE" => "Y",
              "PRICE_VAT_SHOW_VALUE" => "N",
              "PRODUCT_BLOCKS_ORDER" => "props,sku,columns",
              "QUANTITY_FLOAT" => "N",
              "SET_TITLE" => "Y",
              "SHOW_DISCOUNT_PERCENT" => "Y",
              "SHOW_FILTER" => "N",
              "SHOW_RESTORE" => "N",
              "TEMPLATE_THEME" => "blue",
              "TOTAL_BLOCK_DISPLAY" => array(
                  0 => "bottom",
              ),
              "USE_DYNAMIC_SCROLL" => "Y",
              "USE_ENHANCED_ECOMMERCE" => "N",
              "USE_GIFTS" => "N",
              "USE_PREPAYMENT" => "N",
              "USE_PRICE_ANIMATION" => "Y",
              "COMPONENT_TEMPLATE" => "cart"
          ),
          false
      ); ?>
      <script>
          if(BX.Sale.BasketComponent.shownItems<=0){
              for (item in BX.Sale.BasketComponent.items) {
                  BX.Sale.BasketComponent.deleteBasketItem(item);
              }
              BX.Sale.BasketComponent.initializeBasketItems();
              BX.Sale.BasketComponent.sendRequest('refreshAjax', {fullRecalculation: 'Y'});
          }
      </script>
  </div>
</div>

<script>
  function reloadBasket(){
    $.ajax({
      type: "GET",
      url: '<?=$templateFolder?>/order-cart.php',
      success: function (data) {
        //$('#id-order-cart').html(data);
        /*$('#basket-mini-wr').html(data);
        waitCheck('close');
        $('.mobile-message-added').addClass('display');
        displayBasketAfterAdd();
        setTimeout(function(){
          $('.mobile-message-added').removeClass('display');
        }, 5000);*/
      }
    });
  }
</script>

<div class="order-order">
    <?
    global $APPLICATION;
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/libs/fias/js/core.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/libs/fias/js/fias.js");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/libs/fias/css/style.css");
    ?>
    <div id="order-form-wrap">
        <? $APPLICATION->IncludeComponent(
            "bitrix:sale.order.ajax",
            "cart",
            array(
                "USER_SELECTION" => "Y",
                "ACTION_VARIABLE" => "soa-action",
                "ADDITIONAL_PICT_PROP_2" => "-",
                "ALLOW_APPEND_ORDER" => "Y",
                "ALLOW_AUTO_REGISTER" => "Y",
                "ALLOW_NEW_PROFILE" => "N",
                "ALLOW_USER_PROFILES" => "N",
                "BASKET_IMAGES_SCALING" => "adaptive",
                "BASKET_POSITION" => "after",
                "COMPATIBLE_MODE" => "N",
                "DELIVERIES_PER_PAGE" => "9",
                "DELIVERY_FADE_EXTRA_SERVICES" => "N",
                "DELIVERY_NO_AJAX" => "H",
                "DELIVERY_NO_SESSION" => "Y",
                "DELIVERY_TO_PAYSYSTEM" => "p2d",
                "DISABLE_BASKET_REDIRECT" => "Y",
                "EMPTY_BASKET_HINT_PATH" => "/catalog/",
                "HIDE_ORDER_DESCRIPTION" => "N",
                "ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
                "PATH_TO_AUTH" => "/auth/",
                "PATH_TO_BASKET" => "/basket/",
                "PATH_TO_PAYMENT" => "/personal/order/payment/",
                "PATH_TO_PERSONAL" => "/personal/",
                "PAY_FROM_ACCOUNT" => "Y",
                "PAY_SYSTEMS_PER_PAGE" => "9",
                "PICKUPS_PER_PAGE" => "5",
                "PICKUP_MAP_TYPE" => "yandex",
                "PRODUCT_COLUMNS_HIDDEN" => array(),
                "PRODUCT_COLUMNS_VISIBLE" => array(
                    0 => "PREVIEW_PICTURE",
                    1 => "PROPS",
                ),
                "PROPS_FADE_LIST_1" => array(),
                "SEND_NEW_USER_NOTIFY" => "Y",
                "SERVICES_IMAGES_SCALING" => "adaptive",
                "SET_TITLE" => "N",
                "SHOW_BASKET_HEADERS" => "Y",
                "SHOW_COUPONS_BASKET" => "N",
                "SHOW_COUPONS_DELIVERY" => "N",
                "SHOW_COUPONS_PAY_SYSTEM" => "N",
                "SHOW_DELIVERY_INFO_NAME" => "Y",
                "SHOW_DELIVERY_LIST_NAMES" => "Y",
                "SHOW_DELIVERY_PARENT_NAMES" => "Y",
                "SHOW_MAP_IN_PROPS" => "N",
                "SHOW_NEAREST_PICKUP" => "N",
                "SHOW_NOT_CALCULATED_DELIVERIES" => "L",
                "SHOW_ORDER_BUTTON" => "always",
                "SHOW_PAY_SYSTEM_INFO_NAME" => "Y",
                "SHOW_PAY_SYSTEM_LIST_NAMES" => "Y",
                "SHOW_PICKUP_MAP" => "N",
                "SHOW_STORES_IMAGES" => "Y",
                "SHOW_TOTAL_ORDER_BUTTON" => "Y",
                "SHOW_VAT_PRICE" => "N",
                "SKIP_USELESS_BLOCK" => "Y",
                "SPOT_LOCATION_BY_GEOIP" => "Y",
                "TEMPLATE_LOCATION" => "popup",
                "TEMPLATE_THEME" => "blue",
                "USER_CONSENT" => "N",
                "USER_CONSENT_ID" => "1",
                "USER_CONSENT_IS_CHECKED" => "N",
                "USER_CONSENT_IS_LOADED" => "N",
                "USE_CUSTOM_ADDITIONAL_MESSAGES" => "N",
                "USE_CUSTOM_ERROR_MESSAGES" => "N",
                "USE_CUSTOM_MAIN_MESSAGES" => "N",
                "USE_ENHANCED_ECOMMERCE" => "N",
                "USE_PHONE_NORMALIZATION" => "Y",
                "USE_PRELOAD" => "N",
                "USE_PREPAYMENT" => "N",
                "USE_YM_GOALS" => "N",
                "COMPONENT_TEMPLATE" => ".default",
                "SHOW_COUPONS" => "Y",
                "ADDITIONAL_PICT_PROP_8" => "-",
                "COMPOSITE_FRAME_MODE" => "A",
                "COMPOSITE_FRAME_TYPE" => "AUTO"
            ),
            false
        ); ?>
    </div>
</div>