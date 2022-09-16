<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<div class="section-auth">
  <div class="col">
    <h1><?=GetMessage("SYSTEM_AUTH_FORGOT_LOGIN")?></h1>
    <br>
    <div class="form">
      
      <?if(!empty($arParams["~AUTH_RESULT"])):?>
        <div class="message-result<?if($arParams["~AUTH_RESULT"]['TYPE'] == 'ERROR'):?> error<?endif?><?if($arParams["~AUTH_RESULT"]['TYPE'] == 'OK'):?> success<?endif?>"><?=$arParams["~AUTH_RESULT"]['MESSAGE']?></div>
        <br>
      <?endif?>

      <form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
        
        <?if(strlen($arResult["BACKURL"]) > 0):?>
          <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
        <?endif?>

        <input type="hidden" name="AUTH_FORM" value="Y">
        <input type="hidden" name="TYPE" value="SEND_PWD">
        <input type="hidden" name="USER_EMAIL" />

        <div class="description"><?echo GetMessage("sys_forgot_pass_label")?></div>

        <?if($arResult["PHONE_REGISTRATION"]):?>

          <div class="field">
            <input class="field-style js-mask-init" type="text" placeholder=" " size="40" name="USER_PHONE_NUMBER" value="" id="field_user_phone" aria-required="false" >
            <label class="placeholder" for="field_user_phone"><?=GetMessage("sys_forgot_pass_phone")?></label>
            <div class="field-description"><?echo GetMessage("sys_forgot_pass_note_phone")?></div>
          </div>
          <div class="field"><?echo GetMessage("sys_forgot_split")?></div>

        <?endif?>

        <div class="field">
          <input class="field-style" type="text" placeholder=" " size="40" name="USER_LOGIN" value="<?=$arResult["LAST_LOGIN"]?>" id="field_user_login" aria-required="false" >
          <label class="placeholder" for="field_user_login"><?=GetMessage("sys_forgot_pass_email")?></label>
          <div class="field-description"><?echo GetMessage("sys_forgot_pass_note_email")?></div>
        </div>

        <div class="wrap-buttons-between flex-end field">
          <a class="black" href="/personal/"><?=GetMessage("AUTH_AUTH")?></a>
          <button class="ibutton" type="submit" name="send_account_info"><?=GetMessage("AUTH_SEND")?></button>
        </div>


        <?if($arResult["USE_CAPTCHA"]):?>
          <div class="field">
            <div>
              <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
              <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
            </div>
            <div><?echo GetMessage("system_auth_captcha")?></div>
            <div><input type="text" name="captcha_word" maxlength="50" value="" /></div>
          </div>
        <?endif?>
            
      </form>


    </div>
  </div>
</div>

<script type="text/javascript">
  document.bform.onsubmit = function(){
    document.bform.USER_EMAIL.value = document.bform.USER_LOGIN.value;
  };
  document.bform.USER_PHONE_NUMBER.focus();
  
  $(function(){
    $('.js-mask-init').inputmasks(maskPhoneOpts).addClass('js-mask-inited');
  })
</script>