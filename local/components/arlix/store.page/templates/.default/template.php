<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use \Bitrix\Main\Localization\Loc;?>
<?
// $templateFolder — /local/components/arlix/order.page/templates/.default
$this->addExternalJS(SITE_TEMPLATE_PATH . "/js/vue.js");
$this->addExternalJS($templateFolder . "/js/script.js");

?>
<div class="element-store-view display" id="preloading">
    <div class="element_block">
        <div class="element_block__text">
            <?=Loc::getMessage('ELEMENT_ADMIN_STORE_LOADING')?>
        </div>
    </div>
</div>
<div class="element-store-view first" style="opacity: 0;visibility: hidden;" data-id="<?=$arParams['QUERY'];?>" id="elem_store_view">
  <div class="element_block" id="app_view_store_element">
    <div class="element_block__text" v-if="loading">
      <?=Loc::getMessage('ELEMENT_ADMIN_STORE_LOADING')?>
    </div>
    <div class="element_block__text" v-if="!element && !loading">
      <?=Loc::getMessage('ELEMENT_ADMIN_STORE_SEARCH_INFO')?>
    </div>
    <div class="element_block__top" v-if="element && !loading">
      <div class="element_block__left"></div>
      <div class="element_block__right">
        <div class="element_info_cols">
          <div class="element_info_col_image">
            <img class="element_block__pict" v-if="item.ID==element.select_color_sizes"
                 v-for="item in element.offers_color_list" :style="item.SRC" :src="item.SRC" :href="item.SRC" data-fancybox="gallery" />
          </div>
          <div class="element_info_col_desc">
              <h1 class="element_block__title"><a :href="linkProduct">{{element.name}}</a></h1>

            <template v-for="item in element.color_sizes" v-if="item.VALUE_XML_ID==element.select_color_sizes">
              <div class="element_block__price" v-if="size.OFFER_ID==element.select_offer_id"
                   v-for="size in item.SIZES">{{formatNumber(size.price)}} руб.
              </div>
            </template>

            <div class="element_block__color" v-if="element.color_sizes.length">
              <ul>
                <li @click="setSelectColor(item.VALUE_XML_ID)" v-for="item in element.color_sizes"
                    :class="item.VALUE_XML_ID==element.select_color_sizes ? 'select' : ''">{{item.VALUE}}
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
              <td     @click="setSelectOfferAndStores(size.OFFER_ID,store.ID)"
                      :class="size.OFFER_ID==element.select_offer_id && store.ID==element.select_stores? 'select' : ''"
                      v-for="size in item.SIZES">{{getSizeStore(size.stores,index)}}
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>