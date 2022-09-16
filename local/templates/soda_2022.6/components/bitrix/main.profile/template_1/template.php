<?
/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use \Bitrix\Main\Localization\Loc;

if ($arResult["SHOW_SMS_FIELD"] == true) {
    CJSCore::Init('phone_auth');
}
?>

    <div class="bx-auth-profile profile_new">

        <? ShowError($arResult["strProfileError"]); ?>
        <?
        if ($arResult['DATA_SAVED'] == 'Y')
            ShowNote(Loc::getMessage('PROFILE_DATA_SAVED'));
        ?>


        <div class="section-left">
            <div class="col">

                <? if ($arResult['PHONE_CONFIRMED'] && $arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET']): ?>
                    <? //if($arResult['BONUS']):?>
                    <div class="domestic-points">
                        <?= Loc::getMessage("MAIN_PROFILE_ON_YOUR_ACCOUNT") ?> <?= number_format($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'], 0, '', ' ') ?> <?= Loc::getMessage("MAIN_PROFILE_POINTS") ?>
                        <br>
                        <?= Loc::getMessage("MAIN_PROFILE_AVAILABLE_ORDER") ?>
                    </div>
                <? endif ?>

                <form class="form" method="post" name="form1" action="<?= $arResult["FORM_TARGET"] ?>"
                      enctype="multipart/form-data">
                    <?= $arResult["BX_SESSION_CHECK"] ?>
                    <input type="hidden" name="lang" value="<?= LANG ?>" />
                    <input type="hidden" name="ID" value=<?= $arResult["ID"] ?> />

                    <div class="field">
                        <input class="field-style" type="text" placeholder=" " name="NAME" id="profile_name"
                               maxlength="50" value="<?= $arResult["arUser"]["NAME"] ?>" aria-required="true"
                               required="" />
                        <label class="placeholder"
                               for="profile_name"><?= Loc::getMessage('MAIN_PROFILE_NAME') ?></label>
                    </div>

                    <div class="field">
                        <input class="field-style" type="text" placeholder=" " name="LAST_NAME" id="profile_last_name"
                               maxlength="50" value="<?= $arResult["arUser"]["LAST_NAME"] ?>" />
                        <label class="placeholder"
                               for="profile_last_name"><?= Loc::getMessage('MAIN_PROFILE_LAST_NAME') ?></label>
                    </div>
                    <div class="field">
                        <input class="field-style" type="text" placeholder=" " name="LOGIN" id="profile_login"
                               maxlength="50" value="<? echo $arResult["arUser"]["LOGIN"] ?>" />
                        <label class="placeholder"
                               for="profile_login"><?= Loc::getMessage('MAIN_PROFILE_LOGIN') ?></label>
                    </div>
                    <div class="field">
                        <input class="field-style" type="text" placeholder=" " name="EMAIL" id="profile_email"
                               maxlength="50" value="<? echo $arResult["arUser"]["EMAIL"] ?>" aria-required="true"
                               required="" />
                        <label class="placeholder"
                               for="profile_email"><?= Loc::getMessage('MAIN_PROFILE_EMAIL') ?></label>
                    </div>

                    <? if ($arResult["PHONE_REGISTRATION"]): ?>
                        <div class="field">
                            <input class="field-style" type="text" placeholder=" " name="PHONE_NUMBER" id="PHONE"
                                   value="<? echo $arResult["arUser"]["PHONE_NUMBER"] ?>" />
                            <label class="placeholder"
                                   for="PHONE"><?= Loc::getMessage('main_profile_phone_number') ?></label>
                            <input type="hidden" id="action_phone" name="mode" value="profile-check-phone" />
                            <a href="javascript:void(0)" class="black button-confirm-phone"
                               style="<?= !$arResult['PHONE_CONFIRMED'] ? '' : 'display:none;'; ?>"
                               title="<?= Loc::getMessage('PROFILE_CONFIRM_PHONE_TITLE') ?>"><?= Loc::getMessage('PROFILE_CONFIRM_PHONE') ?></a>
                        </div>
                    <? endif ?>

                    <div class="field">
                        <input class="field-style" type="text" placeholder=" " name="PERSONAL_STREET"
                               id="profile_street" maxlength="50"
                               value="<? echo $arResult["arUser"]["PERSONAL_STREET"] ?>" />
                        <label class="placeholder"
                               for="profile_street"><?= Loc::getMessage('MAIN_PROFILE_ADDRESS') ?></label>
                    </div>

                    <? if ($arResult['CAN_EDIT_PASSWORD']): ?>

                        <div class="field">
                            <input class="field-style" type="password" placeholder=" " name="NEW_PASSWORD"
                                   id="profile_new_pass" maxlength="50" value="" autocomplete="off" />
                            <label class="placeholder"
                                   for="profile_new_pass"><?= Loc::getMessage('NEW_PASSWORD_REQ') ?></label>
                        </div>

                        <div class="field">
                            <input class="field-style" placeholder=" " type="password" name="NEW_PASSWORD_CONFIRM"
                                   id="profile_pass_confirm" maxlength="50" value="" autocomplete="off" />
                            <label class="placeholder"
                                   for="profile_pass_confirm"><?= Loc::getMessage('NEW_PASSWORD_CONFIRM') ?></label>
                        </div>

                    <? endif ?>

                    <? if ($arResult["USER_PROPERTIES"]["SHOW"] == "Y"): ?>
                        <div id="user_div_user_properties"
                             class="profile-block-<?= strpos($arResult["opened"], "user_properties") === false ? "hidden" : "shown" ?>">
                            <table class="data-table profile-table">
                                <thead>
                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                                </thead>
                                <tbody>
                                <? $first = true; ?>
                                <? foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField): ?>
                                    <tr>
                                        <td class="field-name">
                                            <? if ($arUserField["MANDATORY"] == "Y"): ?>
                                                <span class="starrequired">*</span>
                                            <? endif; ?>
                                            <?= $arUserField["EDIT_FORM_LABEL"] ?>:
                                        </td>
                                        <td class="field-value">
                                            <? $APPLICATION->IncludeComponent(
                                                "bitrix:system.field.edit",
                                                $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                                                array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField), null, array("HIDE_ICONS" => "Y")); ?></td>
                                    </tr>
                                <? endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <? endif; ?>

                    <div class="description"><? echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"]; ?></div>

                    <div class="wrap-buttons-between">
                        <span class="false">.</span>
                        <button class="ibutton" type="submit" name="save"
                                value="<?= (($arResult["ID"] > 0) ? Loc::getMessage("MAIN_SAVE") : Loc::getMessage("MAIN_ADD")) ?>"><?= (($arResult["ID"] > 0) ? Loc::getMessage("MAIN_SAVE") : Loc::getMessage("MAIN_ADD")) ?></button>
                    </div>

                </form>

                <?
                if ($arResult["SOCSERV_ENABLED"] && FALSE) {
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
		
		<?
		// Если есть данные хоть в одном поле - показывать поля сразу
		$flagShowFields = false;
		if((isset($arResult['arUser']['WORK_COMPANY']) && $arResult['arUser']['WORK_COMPANY'] != '') || 
			(isset($arResult['arUser']['UF_INN']) && $arResult['arUser']['UF_INN'] != '') ||
			(isset($arResult['arUser']['UF_PSRNIE']) && $arResult['arUser']['UF_PSRNIE'] != '') ||
			(isset($arResult['arUser']['WORK_STREET']) && $arResult['arUser']['WORK_STREET'] != '') ||
			(isset($arResult['arUser']['PERSONAL_STREET']) && $arResult['arUser']['PERSONAL_STREET'] != '') ||
			(isset($arResult['arUser']['UF_OKVED']) && $arResult['arUser']['UF_OKVED'] != '') ||
			(isset($arResult['arUser']['UF_BANK']) && $arResult['arUser']['UF_BANK'] != '') ||
			(isset($arResult['arUser']['UF_SETTLEMENT_ACCOUNT']) && $arResult['arUser']['UF_SETTLEMENT_ACCOUNT'] != '') ||
			(isset($arResult['arUser']['UF_CORRESPONDENT_ACCOUNT']) && $arResult['arUser']['UF_CORRESPONDENT_ACCOUNT'] != '') ||
			(isset($arResult['arUser']['UF_IBIKNN']) && $arResult['arUser']['UF_IBIKNN'] != '') ||
			(isset($arResult['arUser']['UF_TAXATION']) && $arResult['arUser']['UF_TAXATION'] != ''))
			$flagShowFields = true;
		?>
		
        <div class="partner-wrapper <?=(($flagShowFields) ? 'accepted' : '')?>">
            <form class="form" method="post" name="became_partner">
                <div class="partner-accept">
                    <p>
                        <?=Loc::getMessage('PROFILE_NEW_PARTNER')?>
                    </p>
                    <div class="ibutton accept-btn"><?=Loc::getMessage('PROFILE_BECOME_PARTNER')?></div>
                </div>
                <div class="partner-form">
                    <h2>
                        <?=Loc::getMessage('PROFILE_ORGANIZATION')?>
                    </h2>
                    <?= in_array(17, CUser::GetUserGroup($USER->GetId())) ? '<div class="partner-status">Статус партнера: Активен</div>' : '';?>
                    <div class="field">
                        <input class="field-style" type="text" placeholder=" " name="WORK_COMPANY" id="WORK_COMPANY"
                               maxlength="50" value="<?= $arResult['arUser']['WORK_COMPANY'] ?>" required/>
                        <label class="placeholder" for="WORK_COMPANY"><?=Loc::getMessage('PROFILE_ORGANIZATION_NAME')?></label>
                    </div>
                    <div class="field">
                        <input class="field-style" type="text" placeholder=" " name="UF_INN" id="UF_INN" maxlength="50"
                               value="<?= $arResult['arUser']['UF_INN'] ?>" required/>
                        <label class="placeholder" for="UF_INN"><?=Loc::getMessage('PROFILE_INN')?></label>
                    </div>
                    <div class="field">
                        <input class="field-style" type="number" placeholder=" " name="UF_PSRNIE" id="UF_PSRNIE"
                               maxlength="50" value="<?= $arResult['arUser']['UF_PSRNIE'] ?>" required/>
                        <label class="placeholder" for="UF_PSRNIE"><?=Loc::getMessage('PROFILE_PSRNIE')?></label>
                    </div>
                    <div class="field">
                        <input class="field-style" type="text" placeholder=" " name="WORK_STREET" id="WORK_STREET"
                               maxlength="50" value="<?= $arResult['arUser']['WORK_STREET'] ?>" required/>
                        <label class="placeholder" for="WORK_STREET"><?=Loc::getMessage('PROFILE_WORK_STREET')?></label>
                    </div>
                    <div class="field">
                        <input class="field-style" type="text" placeholder=" " name="PERSONAL_STREET" id="PERSONAL_STREET"
                               maxlength="50" value="<?= $arResult['arUser']['PERSONAL_STREET'] ?>" required/>
                        <label class="placeholder" for="PERSONAL_STREET"><?=Loc::getMessage('PROFILE_STREET')?></label>
                    </div>
                    <div class="field">
                        <input class="field-style" type="text" placeholder=" " name="UF_OKVED" id="UF_OKVED" maxlength="50"
                               value="<?= $arResult['arUser']['UF_OKVED'] ?>" required/>
                        <label class="placeholder" for="UF_OKVED"><?=Loc::getMessage('PROFILE_OKVED')?></label>
                    </div>
                    <div class="field">
                        <input class="field-style" type="text" placeholder=" " name="UF_BANK" id="UF_BANK" maxlength="50"
                               value="<?= $arResult['arUser']['UF_BANK'] ?>" required/>
                        <label class="placeholder" for="UF_BANK"><?=Loc::getMessage('PROFILE_BANK')?></label>
                    </div>
                    <div class="field">
                        <input class="field-style" type="number" placeholder=" " name="UF_SETTLEMENT_ACCOUNT"
                               id="UF_SETTLEMENT_ACCOUNT" maxlength="50" value="<?= $arResult['arUser']['UF_SETTLEMENT_ACCOUNT'] ?>" required/>
                        <label class="placeholder" for="UF_SETTLEMENT_ACCOUNT"><?=Loc::getMessage('PROFILE_SETTLEMENT_ACCOUNT')?></label>
                    </div>
                    <div class="field">
                        <input class="field-style" type="text" placeholder=" " name="UF_CORRESPONDENT_ACCOUNT"
                               id="UF_CORRESPONDENT_ACCOUNT" maxlength="50" value="<?= $arResult['arUser']['UF_CORRESPONDENT_ACCOUNT'] ?>" required/>
                        <label class="placeholder" for="UF_CORRESPONDENT_ACCOUNT"><?=Loc::getMessage('PROFILE_CORRESPONDENT_ACCOUNT')?></label>
                    </div>
                    <div class="field">
                        <input class="field-style" type="number" placeholder=" " name="UF_IBIKNN" id="UF_IBIKNN" maxlength="50"
                               value="<?= $arResult['arUser']['UF_IBIKNN'] ?>" required/>
                        <label class="placeholder" for="UF_IBIKNN"><?=Loc::getMessage('PROFILE_IBIKNN')?></label>
                    </div>
                    <div class="field">
                        <input class="field-style" type="text" placeholder=" " name="UF_TAXATION" id="UF_TAXATION"
                               maxlength="50" value="<?= $arResult['arUser']['UF_TAXATION'] ?>" required/>
                        <label class="placeholder" for="UF_TAXATION"><?=Loc::getMessage('PROFILE_TAXATION')?></label>
                    </div>
					<input hidden name="mode" value="new-partner">
                    <button class="ibutton partner-save" type="submit" name="partner_save" value=""><?=Loc::getMessage('PROFILE_SAVE')?></button>
                </div>
            </form>
        </div>
        <? //endif?>

    </div>


<?
//d($arResult);
?>