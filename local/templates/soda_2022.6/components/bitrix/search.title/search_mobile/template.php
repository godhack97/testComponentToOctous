<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

$this->setFrameMode(true); ?>
<?
$INPUT_ID = trim($arParams["~INPUT_ID"]);
if (strlen($INPUT_ID) <= 0)
	$INPUT_ID = "title-search-input-mobile";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if (strlen($CONTAINER_ID) <= 0)
	$CONTAINER_ID = "title-search-mobile";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

if ($arParams["SHOW_INPUT"] !== "N") : ?>

	<div class="wrapper" id="<? echo $CONTAINER_ID ?>">
		<div class="search-wrapper">
			<div class="search-logo-block">
				<a href="<?= SITE_DIR ?>" class="search-logo">
					<img class="hover" src="<?= SITE_TEMPLATE_PATH ?>/images/new-header-logo.svg" alt="Logo SODAMODA" />
				</a>
			</div>

			<form action="<?= $arResult["FORM_ACTION"] ?>" method="get">
				<input id="<? echo $INPUT_ID ?>" class="field-text" type="text" name="q" placeholder="<?= Loc::getMessage("SEARCH_FIELD") ?>" value="" autocomplete="off" />
				<input class="send hover" type="submit" name="send" value="<?= Loc::getMessage("SEARCH_BUTTON") ?>">
			</form>
		</div>
	</div>

<? endif ?>

<? /*
<script>

  //BX.ready(function(){
  new JCTitleSearch({
    'AJAX_PAGE' : '<?echo CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
    'CONTAINER_ID': '<?echo $CONTAINER_ID?>',
    'INPUT_ID': '<?echo $INPUT_ID?>',
    'MIN_QUERY_LEN': 2
  });

  $(".popup-search input[name=q]").on("keyup", function () {
    if($(this).val().length >= 2) {
      JCTitleSearchGlobal.onChange();


    }
  });
  //});
</script>
*/ ?>