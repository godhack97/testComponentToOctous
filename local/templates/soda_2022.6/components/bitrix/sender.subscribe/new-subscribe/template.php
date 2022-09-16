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

$buttonId = $this->randString();
?>
<div class="subscribe">
  <div class="caption"><?=Loc::getMessage("SUBSCRIBE_NEWSLETTER")?></div>

  <?$frame = $this->createFrame("sender-subscribe")->begin("");?>
    <form id="bx_subscribe_subform_<?=$buttonId?>" role="form" method="post" action="<?=$arResult["FORM_ACTION"]?>" class="form">
      <?=bitrix_sessid_post()?>
      <input type="hidden" name="site_id" value="<?=SITE_ID?>">
      <input type="hidden" name="event" value="sender_subscription_add">

      <div class="field field-subscribe">
        <input class="email js-check-email-subscribe" type="text" name="SENDER_SUBSCRIBE_EMAIL" value="<?=$arResult["EMAIL"]?>" placeholder=" " id="field-footer-subscribe" />
        <label class="placeholder" for="field-footer-subscribe"><?=Loc::getMessage("SUBSCRIBE_FIELD_EMAIL")?></label>
				<button class="ibutton" name="subscribe_form"><img src="<?=SITE_TEMPLATE_PATH?>/images/subscribe-arrow.svg" alt=""></button>
      </div>

			<div class="subscribe-agreement">
				<input type="checkbox" id="toggle-button" class="toggle-button">
				<label for="toggle-button" class="agreement-text"><?=Loc::getMessage("SUBSCRIBE_TOGGLE_TEXT")?></label>
			</div>

    </form>
  <?$frame->end();?>
</div>