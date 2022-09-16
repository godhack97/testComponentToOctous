<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use \Bitrix\Main\Localization\Loc;
?>
<?
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/vue.js");
$this->addExternalJS($templateFolder."/js/script.js");
?>

<div class="search set-search form">
  <div class="name"><?=Loc::getMessage("SEARCH_NAME")?></div>
  <div class="field" id="elementSearch">
    <div>
      <input @keyup.enter="search" v-model="query" id="input_id" type="text" name="q" value="" class="query" placeholder=" " size="15" maxlength="50" autocomplete="off" />
      <label class="placeholder" for="input_id">Ведите артикул для поиска</label>
      <input name="s" class="send hover" type="submit" value="<?=Loc::getMessage("SEARCH_BUTTON")?>" @click="search" />
    </div>
    
    <div class="search_result" v-if="!hide && list.length">
      <div class="search_result__item" v-for="item in list" @click="sendId(item.id)">
        <div class="pict" :style="item.pict"></div>
        <div class="body">
          <div class="body_name">{{item.name}}</div>
          <div class="price">{{formatNumber(item.price)}} <?=Loc::getMessage("SEARCH_CURRENCY")?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="overlap js-set-search"></div>
</div>