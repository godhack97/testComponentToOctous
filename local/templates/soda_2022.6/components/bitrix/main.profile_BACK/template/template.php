<?
/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use \Bitrix\Main\Localization\Loc;

if($arResult["SHOW_SMS_FIELD"] == true)
{
	CJSCore::Init('phone_auth');
}
?>

<div class="bx-auth-profile">

<?ShowError($arResult["strProfileError"]);?>
<?
if ($arResult['DATA_SAVED'] == 'Y')
	ShowNote(Loc::getMessage('PROFILE_DATA_SAVED'));
?>

<?if($arResult["SHOW_SMS_FIELD"] == true):?>

  <form method="post" action="<?=$arResult["FORM_TARGET"]?>">
    <?=$arResult["BX_SESSION_CHECK"]?>
    <input type="hidden" name="lang" value="<?=LANG?>" />
    <input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
    <input type="hidden" name="SIGNED_DATA" value="<?=htmlspecialcharsbx($arResult["SIGNED_DATA"])?>" />
    <table class="profile-table data-table">
      <tbody>
        <tr>
          <td><?echo Loc::getMessage("main_profile_code")?><span class="starrequired">*</span></td>
          <td><input size="30" type="text" name="SMS_CODE" value="<?=htmlspecialcharsbx($arResult["SMS_CODE"])?>" autocomplete="off" /></td>
        </tr>
      </tbody>
    </table>
    <p><input type="submit" name="code_submit_button" value="<?echo Loc::getMessage("main_profile_send")?>" /></p>
  </form>

  <script>
    new BX.PhoneAuth({
      containerId: 'bx_profile_resend',
      errorContainerId: 'bx_profile_error',
      interval: <?=$arResult["PHONE_CODE_RESEND_INTERVAL"]?>,
      data:
        <?=CUtil::PhpToJSObject([
          'signedData' => $arResult["SIGNED_DATA"],
        ])?>,
      onError:
        function(response)
        {
          var errorDiv = BX('bx_profile_error');
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

  <div id="bx_profile_error" style="display:none"><?ShowError("error")?></div>
  <div id="bx_profile_resend"></div>

  
<?else:?>

  <div class="section-left">
    <div class="col">
      
      <?if($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET']):?>
        <div class="domestic-points">
          <?=Loc::getMessage("MAIN_PROFILE_ON_YOUR_ACCOUNT")?> <?=number_format($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'], 0, '', ' ')?> <?=Loc::getMessage("MAIN_PROFILE_POINTS")?><br>
          <?=Loc::getMessage("MAIN_PROFILE_AVAILABLE_ORDER")?>
        </div>
      <?endif?>

      <form class="form" method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>" enctype="multipart/form-data">
        <?=$arResult["BX_SESSION_CHECK"]?>
        <input type="hidden" name="lang" value="<?=LANG?>" />
        <input type="hidden" name="ID" value=<?=$arResult["ID"]?> />

        <div class="field">
          <input type="text" placeholder="<?=Loc::getMessage("MAIN_PROFILE_NAME")?>" name="NAME" maxlength="50" value="<?=$arResult["arUser"]["NAME"]?>" aria-required="true" required="" />
        </div>
        <div class="field">
          <input type="text" placeholder="<?=Loc::getMessage("MAIN_PROFILE_LAST_NAME")?>" name="LAST_NAME" maxlength="50" value="<?=$arResult["arUser"]["LAST_NAME"]?>" />
        </div>    
        <div class="field">
          <input type="text" placeholder="<?=Loc::getMessage("MAIN_PROFILE_LOGIN")?>" name="LOGIN" maxlength="50" value="<? echo $arResult["arUser"]["LOGIN"]?>" />
        </div>
        <div class="field">
          <input type="text" placeholder="<?=Loc::getMessage("MAIN_PROFILE_EMAIL")?>" name="EMAIL" maxlength="50" value="<? echo $arResult["arUser"]["EMAIL"]?>" aria-required="true" required="" />
        </div>

        <?if($arResult["PHONE_REGISTRATION"]):?>
          <div class="field">
            <input type="text" placeholder="<?echo Loc::getMessage("main_profile_phone_number")?>" name="PHONE_NUMBER" value="<? echo $arResult["arUser"]["PHONE_NUMBER"]?>" />
          </div>
        <?endif?>

        <?if($arResult['CAN_EDIT_PASSWORD']):?>
          <div class="field">
            <input type="password" placeholder="<?=Loc::getMessage('NEW_PASSWORD_REQ')?>" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" class="bx-auth-input" />
            <?if($arResult["SECURE_AUTH"]):?>
              <span class="bx-auth-secure" id="bx_auth_secure" title="<?echo Loc::getMessage("AUTH_SECURE_NOTE")?>" style="display:none">
                <div class="bx-auth-secure-icon"></div>
              </span>
              <noscript>
                <span class="bx-auth-secure" title="<?echo Loc::getMessage("AUTH_NONSECURE_NOTE")?>">
                  <div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
                </span>
              </noscript>
              <script type="text/javascript">
                document.getElementById('bx_auth_secure').style.display = 'inline-block';
              </script>
            <?endif?>
          </div>
          <div class="field">
            <input placeholder="<?=Loc::getMessage('NEW_PASSWORD_CONFIRM')?>" type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" />
          </div>
        <?endif?>

        <div class="field">
          <input type="text" placeholder="<?=Loc::getMessage("MAIN_PROFILE_ADDRESS")?>" name="PERSONAL_STREET" maxlength="50" value="<? echo $arResult["arUser"]["PERSONAL_STREET"]?>" />
        </div>

        <?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
          <div id="user_div_user_properties" class="profile-block-<?=strpos($arResult["opened"], "user_properties") === false ? "hidden" : "shown"?>">
            <table class="data-table profile-table">
              <thead>
                <tr>
                  <td colspan="2">&nbsp;</td>
                </tr>
              </thead>
              <tbody>
              <?$first = true;?>
              <?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
              <tr><td class="field-name">
                <?if ($arUserField["MANDATORY"]=="Y"):?>
                  <span class="starrequired">*</span>
                <?endif;?>
                <?=$arUserField["EDIT_FORM_LABEL"]?>:</td><td class="field-value">
                  <?$APPLICATION->IncludeComponent(
                    "bitrix:system.field.edit",
                    $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                    array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField), null, array("HIDE_ICONS"=>"Y"));?></td></tr>
              <?endforeach;?>
              </tbody>
            </table>
          </div>
        <?endif;?>

        <div class="description"><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></div>

        <div class="wrap-buttons-between">
          <span class="false">.</span>
          <button class="ibutton" type="submit" name="save" value="<?=(($arResult["ID"]>0) ? Loc::getMessage("MAIN_SAVE") : Loc::getMessage("MAIN_ADD"))?>"><?=(($arResult["ID"]>0) ? Loc::getMessage("MAIN_SAVE") : Loc::getMessage("MAIN_ADD"))?></button>
        </div>

      </form>

      <?
      if($arResult["SOCSERV_ENABLED"] && FALSE)
      {
        $APPLICATION->IncludeComponent("bitrix:socserv.auth.split", ".default", array(
            "SHOW_PROFILES" => "Y",
            "ALLOW_DELETE" => "Y"
          ),
          false
        );
      }
      ?>
    </div>
  </div>
<?endif?>

</div>