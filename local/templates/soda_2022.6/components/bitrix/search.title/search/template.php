<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);?>
<?
$INPUT_ID = trim($arParams["~INPUT_ID"]);
if(strlen($INPUT_ID) <= 0)
	$INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if(strlen($CONTAINER_ID) <= 0)
	$CONTAINER_ID = "title-search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

$QUERY = false;
if(!empty($arParams['QUERY']))
  $QUERY = $arParams['QUERY'];


if($arParams["SHOW_INPUT"] !== "N"):?>
  <div class="search <?if($QUERY):?>set-search<?else:?>no-active<?endif?>">
    <div class="name"><?=Loc::getMessage("SEARCH_NAME")?></div>
    <div class="field" id="<?echo $CONTAINER_ID?>">
      <form action="<?=$arResult["FORM_ACTION"]?>">
        <input id="<?echo $INPUT_ID?>" type="text" name="q" value="<?=$QUERY?>" class="query" size="15" maxlength="50" autocomplete="off" />
        <input name="s" class="send" type="submit" value="<?=Loc::getMessage("SEARCH_SUBMIT")?>" />
      </form>
    </div>
		<?//<div class="overlap js-set-search"></div>?>
    <div class="overlap js-mobile-open-search"></div>
  </div>
<?endif?>
<? /*
<script>
	BX.ready(function(){
    new JCTitleSearch({
      'AJAX_PAGE' : '<?echo CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
      'CONTAINER_ID': '<?echo $CONTAINER_ID?>',
      'INPUT_ID': '<?echo $INPUT_ID?>',
      'MIN_QUERY_LEN': 2
    });
  });
</script>
*/ ?>