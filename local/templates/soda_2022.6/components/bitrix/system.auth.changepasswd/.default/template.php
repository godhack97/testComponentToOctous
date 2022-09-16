<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($arResult["PHONE_REGISTRATION"])
{
	CJSCore::Init('phone_auth');
}
?>

<div class="section-auth">
  <div class="col">
    <h1>Смена пароля</h1>
    <br><br>
    <div class="form">

      <?if(!empty($arParams["~AUTH_RESULT"])):?>
        <div class="message-result<?if($arParams["~AUTH_RESULT"]['TYPE'] == 'ERROR'):?> error<?endif?><?if($arParams["~AUTH_RESULT"]['TYPE'] == 'OK'):?> success<?endif?>"><?=$arParams["~AUTH_RESULT"]['MESSAGE']?></div>
        <br>
      <?endif?>

      <?if($arResult["SHOW_FORM"]):?>

        <form method="post" action="<?=$arResult["AUTH_FORM"]?>" name="bform">
          <?if (strlen($arResult["BACKURL"]) > 0): ?>
            <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
          <? endif ?>
          <input type="hidden" name="AUTH_FORM" value="Y">
          <input type="hidden" name="TYPE" value="CHANGE_PWD">
          
          

          <?if($arResult["PHONE_REGISTRATION"]):?>
          
            <div class="field">
              <input type="hidden" name="USER_PHONE_NUMBER" value="<?=htmlspecialcharsbx($arResult["USER_PHONE_NUMBER"])?>" />
              <input class="field-style" type="text" placeholder=" " size="40" value="<?=htmlspecialcharsbx($arResult["USER_PHONE_NUMBER"])?>" id="FIELD_USER_CHECKWORD" disabled="disabled">
              <label class="placeholder" for="FIELD_USER_CHECKWORD"><?echo GetMessage("sys_auth_chpass_phone_number")?></label>
            </div>
          
            <div class="field">
              <input class="field-style" type="text" placeholder=" " name="USER_CHECKWORD" id="FIELD_USER_CHECKWORD" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>" autocomplete="off" required="" aria-required="true">
              <label class="placeholder" for="FIELD_USER_CHECKWORD"><?echo GetMessage("sys_auth_chpass_code")?></label>
            </div>

          <?else:?>
              
            <div class="field">
              <input class="field-style" type="text" placeholder=" " name="USER_LOGIN" id="FIELD_USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" required="" aria-required="true">
              <label class="placeholder" for="FIELD_USER_LOGIN"><?=GetMessage("AUTH_LOGIN_CUSTOM")?></label>
            </div>
            <div class="field">
              <input class="field-style" type="text" placeholder=" " name="USER_CHECKWORD" id="FIELD_USER_CHECKWORD" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>" autocomplete="off" required="" aria-required="true">
              <label class="placeholder" for="FIELD_USER_CHECKWORD"><?=GetMessage("AUTH_CHECKWORD")?></label>
            </div>
          
          <?endif?>
              
            <div class="field">
              <input type="password" placeholder=" " name="USER_PASSWORD" id="FIELD_USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" autocomplete="off" required="" aria-required="true">
              <label class="placeholder" for="FIELD_USER_PASSWORD"><?=GetMessage("AUTH_NEW_PASSWORD_REQ")?></label>
            </div>

            <?if($arResult["SECURE_AUTH"]):?>
              <span class="bx-auth-secure" id="bx_auth_secure" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
                <div class="bx-auth-secure-icon"></div>
              </span>
              <noscript>
              <span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
                <div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
              </span>
              </noscript>
              <script type="text/javascript">
              document.getElementById('bx_auth_secure').style.display = 'inline-block';
              </script>
            <?endif?>
                    
            <div class="field">
              <input type="password" placeholder=" " name="USER_CONFIRM_PASSWORD" id="FIELD_USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" autocomplete="off" required="" aria-required="true">
              <label class="placeholder" for="FIELD_USER_CONFIRM_PASSWORD"><?=GetMessage("AUTH_NEW_PASSWORD_CONFIRM")?></label>
            </div>
              

            <?if($arResult["USE_CAPTCHA"]):?>
              
              <div class="field">
                <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
                <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
              </div>
              
              <!-- span class="starrequired">*</span><?echo GetMessage("system_auth_captcha")?> -->
                  
              <!-- <input type="text" name="captcha_word" maxlength="50" value="" />-->
                  
              <div class="field">
                <input type="text" placeholder=" " name="captcha_word" id="FIELD_captcha_word" value="">
                <label class="placeholder" for="FIELD_captcha_word"><?=GetMessage("system_auth_captcha")?></label>
              </div>

            <?endif?>
              
            <div class="wrap-buttons-between flex-end field">
              <a class="black" href="/personal/"><?=GetMessage("AUTH_AUTH")?></a>
              <button class="ibutton" type="submit" name="change_pwd"><?=GetMessage("AUTH_CHANGE")?></button>
              <? /* <input type="submit" name="change_pwd" value="<?=GetMessage("AUTH_CHANGE")?>" /> */ ?>
            </div>
              
              
        </form>

        <div class="description"><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></div>

        <?if($arResult["PHONE_REGISTRATION"]):?>

          <script type="text/javascript">
          new BX.PhoneAuth({
            containerId: 'bx_chpass_resend',
            errorContainerId: 'bx_chpass_error',
            interval: <?=$arResult["PHONE_CODE_RESEND_INTERVAL"]?>,
            data:
              <?=CUtil::PhpToJSObject([
                'signedData' => $arResult["SIGNED_DATA"]
              ])?>,
            onError:
              function(response)
              {
                var errorDiv = BX('bx_chpass_error');
                var errorNode = BX.findChildByClassName(errorDiv, 'errortext');
                errorNode.innerHTML = '';
                for(var i = 0; i < response.errors.length; i++)
                {
                  errorNode.innerHTML = errorNode.innerHTML + BX.util.htmlspecialchars(response.errors[i].message) + '<br>';
                }
                errorDiv.style.display = '';
              }
          });
          </script>

          <div id="bx_chpass_error" class="description" style="display:none"><?ShowError("error")?></div>

          <div id="bx_chpass_resend" class="description"></div>

        <?endif?>

      <?endif?>

    </div>
  </div>
</div>