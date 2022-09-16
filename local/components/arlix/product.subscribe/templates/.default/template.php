<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use \Bitrix\Main\Localization\Loc;
$this->setFrameMode(true);
?>
<?if($arParams['AUTH'] == 'Y'):?>

<div class="subscribe-button subscribe-user cs-1" data-product-id="<?=$arParams['PRODUCT_ID']?>" <?if($arResult['IS_SUBSCRIBE']):?>style="display: none"<?endif;?>><span class="label-desktop"><?=Loc::getMessage("APS_SUBSCRIBE_BUTTON")?></span><span class="label-mobile"><?=Loc::getMessage("APS_SUBSCRIBE_BUTTON_SHORT")?></span></div>
  <div class="subscribe-button unsubscribe-user cs-2" data-product-id="<?=$arParams['PRODUCT_ID']?>" <?if(!$arResult['IS_SUBSCRIBE']):?>style="display: none"<?endif;?>><?=Loc::getMessage("APS_UNSUBSCRIBE_BUTTON")?></div>

<?else:?>

  <div class="subscribe-button subscribe-not-auth cs-3" data-product-id="<?=$arParams['PRODUCT_ID']?>" rel="nofollow" <?if($arResult['IS_SUBSCRIBE']):?>style="display: none"<?endif;?>><span class="label-desktop"><?=Loc::getMessage("APS_SUBSCRIBE_BUTTON")?></span><span class="label-mobile"><?=Loc::getMessage("APS_SUBSCRIBE_BUTTON_SHORT")?></span></div>
  <div class="subscribe-button unsubscribe-not-auth cs-4" data-product-id="<?=$arParams['PRODUCT_ID']?>" rel="nofollow" <?if(!$arResult['IS_SUBSCRIBE']):?>style="display: none"<?endif;?>><?=Loc::getMessage("APS_UNSUBSCRIBE_BUTTON")?></div>

<?endif?>

<a class="ibutton hollow js-open-learn-receipt" data-product-id="<?=$arParams['PRODUCT_ID']?>" data-product-article="<?=$arParams['ARTICLE']?>" rel="nofollow" style="display: none"><?=Loc::getMessage("APS_POPUP_BUTTON_RECEIPT")?></a>

<?if(!$arParams['IS_MOBILE']):?>
  <div id="content-popup-product-subscribe-<?=$arParams['PRODUCT_ID']?>" class="content-popup-product-subscribe">
    <p><?=Loc::getMessage("APS_POPUP_DESCRIPTION")?></p>
    <form name="product-subscribe" method="post" class="form">
      <input type="hidden" name="productId" value="<?=$arParams['PRODUCT_ID']?>">

      <div class="field">
        <input oninvalid="setCustomValidity('Phone')" type="text" class="field-style" name="phone" value="" placeholder=" " id="popup-form-field-phone">
        <label class="placeholder" for="popup-form-field-phone"><?=Loc::getMessage("APS_POPUP_FIELD_PHONE")?></label>
      </div>
      <div class="field">
        <input oninvalid="setCustomValidity('Email')" type="text" class="field-style" name="email" value="" placeholder=" " id="popup-form-field-email">
        <label class="placeholder" for="popup-form-field-phone"><?=Loc::getMessage("APS_POPUP_FIELD_EMAIL")?></label>
      </div>

      <input type="submit" class="ibutton" value="<?=Loc::getMessage("APS_POPUP_SUBMIT")?>" />
    </form>
  </div>
<?endif?>

<script>
  BX.message({
    APS_AJAX_URL: '<?=$templateFolder?>/ajax.php',
  });
  
  //  pattern="^[a-zA-Z][a-zA-Z0-9-_\.]{1,20}$"
</script>
