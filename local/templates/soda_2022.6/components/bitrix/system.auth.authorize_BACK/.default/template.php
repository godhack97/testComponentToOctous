<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Localization\Loc;?>

<?
//ShowMessage($arParams["~AUTH_RESULT"]);
//ShowMessage($arResult['ERROR_MESSAGE']);
?>

<div class="section-auth">
  <div class="col">
    <h1><?=Loc::getMessage('SYSTEM_AUTH_LOGIN')?></h1>
    <div class="form">
      
      <?if($arResult["AUTH_SERVICES"] && FALSE):?>
        <?$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
          Array(
            "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
            "CURRENT_SERVICE" => $arResult["CURRENT_SERVICE"],
            "AUTH_URL" => $arResult["AUTH_URL"],
            "POST" => $arResult["POST"],
            "SHOW_TITLES" => $arResult["FOR_INTRANET"]?'N':'Y',
            "FOR_SPLIT" => $arResult["FOR_INTRANET"]?'Y':'N',
            "AUTH_LINE" => $arResult["FOR_INTRANET"]?'N':'Y',
          ),
          $component,
          array("HIDE_ICONS"=>"Y")
        );?>
        <? /*
        <div class="socials">
          <a class="ibutton hollow soc-ins"></a>
          <a class="ibutton hollow soc-vk"></a>
          <a class="ibutton hollow soc-fb"></a>
          <a class="ibutton hollow soc-phone">По номеру телефона</a>
        </div>
        <div class="split-or">или</div>
        */ ?>
      <?else:?>
        <br>
      <?endif?>
        
      <div class="description"><?=Loc::getMessage('SYSTEM_AUTH_AUTH_INFO')?></div>
      <form action="" name="auth">
        <div class="message-result error"></div>
        <input type="hidden" name="mode" value="authorize">
        <div class="field">
          <input type="text" placeholder="<?=Loc::getMessage('SYSTEM_AUTH_AUTH_EMAIL')?>" size="40" name="USER_LOGIN" value="" aria-required="true" required="" oninvalid="this.setCustomValidity('<?=Loc::getMessage('SYSTEM_AUTH_JS_ERROR_EMPTY_FIELD')?>')" oninput="setCustomValidity('')">
        </div>
        <div class="field">
          <input type="password" placeholder="<?=Loc::getMessage('SYSTEM_AUTH_AUTH_PASSWORD')?>" size="40" name="USER_PASSWORD" value="" aria-required="true" required="" oninvalid="this.setCustomValidity('<?=Loc::getMessage('SYSTEM_AUTH_JS_ERROR_EMPTY_FIELD')?>')" oninput="setCustomValidity('')">
        </div>
        <div class="wrap-buttons-between flex-end">
          <a class="black" href="<?=SITE_DIR?>personal/?forgot_password=yes"><?=Loc::getMessage('SYSTEM_AUTH_AUTH_FORGOT_PASSWORD')?></a>
          <button class="ibutton" type="submit" name=""><?=Loc::getMessage('SYSTEM_AUTH_AUTH_LOGIN')?></button>
        </div>
      </form>
    </div>
  </div>
  <div class="col">
    <h1><?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION')?></h1>
    <br>
    <div class="form">
      <div class="description"><?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_INFO')?></div>
      <form action="" name="register">
        <div class="message-result error"></div>
        <input type="hidden" name="mode" value="register">
        <div class="field">
          <input type="text" placeholder="<?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_EMAIL')?>" size="40" name="USER_EMAIL" value="" aria-required="true" required="" oninvalid="this.setCustomValidity('<?=Loc::getMessage('SYSTEM_AUTH_JS_ERROR_EMPTY_FIELD')?>')" oninput="setCustomValidity('')">
        </div>
        <div class="field">
          <input type="password" placeholder="<?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_PASSWORD')?>" name="USER_PASSWORD" value="" aria-required="true" required="" oninvalid="this.setCustomValidity('<?=Loc::getMessage('SYSTEM_AUTH_JS_ERROR_EMPTY_FIELD')?>')" oninput="setCustomValidity('')">
        </div>
        <div class="field">
          <input type="password" placeholder="<?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_CONFIRM_PASSWORD')?>" name="USER_CONFIRM_PASSWORD" value="" aria-required="true" required="" oninvalid="this.setCustomValidity('<?=Loc::getMessage('SYSTEM_AUTH_JS_ERROR_EMPTY_FIELD')?>')" oninput="setCustomValidity('')">
        </div>
        <div class="field">
          <input type="text" placeholder="<?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_NAME')?>" size="40" name="USER_NAME" value="" aria-required="true" required="" oninvalid="this.setCustomValidity('<?=Loc::getMessage('SYSTEM_AUTH_JS_ERROR_EMPTY_FIELD')?>')" oninput="setCustomValidity('')">
        </div>
        <div class="field">
          <input type="text" placeholder="<?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_LAST_NAME')?>" size="40" name="USER_LAST_NAME" value="">
        </div>
        <div class="field">
          <input type="text" placeholder="<?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_PHONE')?>" size="40" name="USER_PHONE" value="" aria-required="true" required="" oninvalid="this.setCustomValidity('<?=Loc::getMessage('SYSTEM_AUTH_JS_ERROR_EMPTY_FIELD')?>')" oninput="setCustomValidity('')">
        </div>
        <div class="field field-checkbox">
          <label>
            <input type="checkbox" value="Y" name="sender">
            <div class="label"><?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_RECIVE_PERSONAL')?></div>
          </label>
        </div>
        <div class="field field-checkbox">
          <label> 
            <input type="checkbox" value="Y" name="rule" required="required" oninvalid="this.setCustomValidity('<?=Loc::getMessage('SYSTEM_AUTH_JS_ERROR_EMPTY_FIELD')?>')" oninput="setCustomValidity('')">
            <div class="label"><?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_AGREE')?> <a href="<?=SITE_DIR?>rule/" target="_blank" class="black"><?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_AGREE_PRIVACY')?></a></div>
          </label>
        </div>
        <div class="wrap-buttons-between flex-end">
          <span class="false">.</span>
          <button class="ibutton" type="submit" name=""><?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_REGISTRATION')?></button>
        </div>
      </form>
    </div>
  </div>
</div>
