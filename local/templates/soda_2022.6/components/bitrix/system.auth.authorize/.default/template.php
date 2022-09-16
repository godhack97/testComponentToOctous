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
      
      <?if($arResult["AUTH_SERVICES"]):?>
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
          <input class="field-style" type="text" placeholder=" " size="40" name="USER_LOGIN" id="field_auth_user_login" value="" aria-required="true" required="" oninvalid="this.setCustomValidity('<?=Loc::getMessage('SYSTEM_AUTH_JS_ERROR_EMPTY_FIELD')?>')" oninput="setCustomValidity('')">
          <label class="placeholder" for="field_auth_user_login"><?=Loc::getMessage('SYSTEM_AUTH_AUTH_EMAIL')?></label>
        </div>
        <div class="field">
          <input class="field-style" type="password" placeholder=" " size="40" name="USER_PASSWORD" id="field_auth_user_pass" value="" aria-required="true" required="" oninvalid="this.setCustomValidity('<?=Loc::getMessage('SYSTEM_AUTH_JS_ERROR_EMPTY_FIELD')?>')" oninput="setCustomValidity('')">
          <label class="placeholder" for="field_auth_user_pass"><?=Loc::getMessage('SYSTEM_AUTH_AUTH_PASSWORD')?></label>
        </div>
        <div class="wrap-buttons-between">
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
      
      <form id="register_form" action="" name="register">
        <div class="message-result error"></div>
        <input type="hidden" name="mode" value="register">
        <div class="field">
          <input class="field-style" type="text" placeholder=" " size="40" name="USER_EMAIL" id="field_reg_user_email" value="" aria-required="true" required="" oninvalid="this.setCustomValidity('<?=Loc::getMessage('SYSTEM_AUTH_JS_ERROR_EMPTY_FIELD')?>')" oninput="setCustomValidity('')">
          <label class="placeholder" for="field_reg_user_email"><?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_EMAIL')?></label>
        </div>
        <div class="field">
          <input class="field-style" type="password" placeholder=" " name="USER_PASSWORD" id="field_reg_user_pass" value="" aria-required="true" required="" oninvalid="this.setCustomValidity('<?=Loc::getMessage('SYSTEM_AUTH_JS_ERROR_EMPTY_FIELD')?>')" oninput="setCustomValidity('')">
          <label class="placeholder" for="field_reg_user_pass"><?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_PASSWORD')?></label>
        </div>
        <div class="field">
          <input class="field-style" type="password" placeholder=" " name="USER_CONFIRM_PASSWORD" id="field_reg_user_conf_pass" value="" aria-required="true" required="" oninvalid="this.setCustomValidity('<?=Loc::getMessage('SYSTEM_AUTH_JS_ERROR_EMPTY_FIELD')?>')" oninput="setCustomValidity('')">
          <label class="placeholder" for="field_reg_user_conf_pass"><?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_CONFIRM_PASSWORD')?></label>
        </div>
        <div class="field">
          <input class="field-style" type="text" placeholder=" " size="40" name="USER_NAME" id="field_reg_user_name" value="" aria-required="true" required="" oninvalid="this.setCustomValidity('<?=Loc::getMessage('SYSTEM_AUTH_JS_ERROR_EMPTY_FIELD')?>')" oninput="setCustomValidity('')">
          <label class="placeholder" for="field_reg_user_name"><?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_NAME')?></label>
        </div>
        <div class="field">
          <input class="field-style" type="text" placeholder=" " size="40" name="USER_LAST_NAME" id="field_reg_user_last_name" value="">
          <label class="placeholder" for="field_reg_user_last_name"><?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_LAST_NAME')?></label>
        </div>
        <div class="field">
          <input class="field-style" type="text" placeholder=" " size="40" name="USER_PHONE" id="user_phone" value="" aria-required="true" required="" oninvalid="this.setCustomValidity('<?=Loc::getMessage('SYSTEM_AUTH_JS_ERROR_EMPTY_FIELD')?>')" oninput="setCustomValidity('')">
          <label class="placeholder" for="user_phone"><?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_PHONE')?></label>
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
        <div class="space-between-filds"></div>
        <div class="wrap-buttons-between flex-end">
          <button class="ibutton" type="submit" name=""><?=Loc::getMessage('SYSTEM_AUTH_REGISTRATION_REGISTRATION')?></button>
        </div>
      </form>
      
      
      <form method="post" id="form-register-check-phone" action="" style="display: none">
        <input type="hidden" name="mode" value="register-check-phone" />
        <input type="hidden" name="USER_PHONE" id="field-register-copy-phone" value="" />

        <div class="bx-authform-formgroup-container">
          <div class="field">
            <input class="field-style" type="text" placeholder=" " size="40" name="SMS_CODE" id="field_reg_sms_code" value="" aria-required="true" required="" oninvalid="this.setCustomValidity('<?=Loc::getMessage('SYSTEM_AUTH_JS_ERROR_EMPTY_FIELD')?>')" oninput="setCustomValidity('')">
            <label class="placeholder" for="field_reg_sms_code">Код подтверждения из СМС</label>
          </div>
        </div>

        <div class="wrap-buttons-between flex-end">
          <button type="submit" class="ibutton" name="code_submit_button">Отправить</button>
        </div>
      </form>
      
      
    </div>
  </div>
</div>
