<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

    use Bitrix\Main\Loader;
    use Bitrix\Main\Localization\Loc;
    use Xwild\Restapi\Foundation\Page;
    use Xwild\Restapi\Foundation\Helper;
    use Xwild\Restapi\Foundation\Config;
    use Xwild\Restapi\Foundation\Settings;
    use Xwild\Restapi\Foundation\Core;

    Loader::includeModule('xwild.restapi');
    Page::getInstance()->checkAccess('accessSecurity');
    Page::getInstance()->setTitle(getMessageModule('XwildRestSecurityPageTitle'));
    $tabs = [
        ['DIV' => 'tab-1', 'TAB' => getMessageModule('XwildRestTabAuthorizationTitle')],
        ['DIV' => 'tab-2', 'TAB' => getMessageModule('XwildRestTabRequestLimitTitle')],
        ['DIV' => 'tab-3', 'TAB' => getMessageModule('XwildRestTabFiltersTitle')],
    ];
    $tabControl = new CAdminTabControl('tabControl', $tabs);
    $groups = [];
    $result = CGroup::GetList($by = 'NAME', $order = 'ASC', ['ACTIVE' => 'Y', 'ANONYMOUS' => 'N']);
    while ($group = $result->fetch()) {
        if ($group['ID'] == Helper::getInstance()->adminGroupId() || $group['ID'] == Helper::getInstance()->ratingVoteGroupId() || $group['ID'] == Helper::getInstance()->ratingVoteAuthorityGroupId())
            continue;
        $groups[$group['ID']] = $group['NAME'];
    }
    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';
    if ($_POST) {
        $_POST['form'] = basename(__FILE__, '.php');
        if (isset($_POST['save'])) {
            Config::getInstance()->setOption();
            echo CAdminMessage::ShowNote(getMessageModule('XwildRestSaved'));
        }
        elseif (isset($_POST['restore'])) {
            Config::getInstance()->restoreOption();
            echo CAdminMessage::ShowNote(getMessageModule('XwildRestRestored'));
        }
    }
    if ($_GET['generateToken'] == 'Y') {

        global $USER;


        $user = new CUser();
        $counter = 0;
        if ($users = CUser::GetList(
                $by = 'ID',
                $order = 'DESC',
                [
                        Settings::getInstance()->getTokenField()['code'] => false,
                        'ACTIVE' => 'Y',
                        'ID' => $USER->GetID()
                ],
                ['FIELDS' => ['ID', 'LOGIN'], 'SELECT' => [Settings::getInstance()->getTokenField()['code']]])) {
            while ($ar = $users->fetch()) {
                if (!array_key_exists(Settings::getInstance()->getTokenField()['code'], $ar))
                    continue;
                $token = Helper::getInstance()->generateToken($ar['ID'], $ar['LOGIN']);
                if ($user->update($ar['ID'], [
                    Settings::getInstance()->getTokenField()['code'] => $token,
                    Settings::getInstance()->getTokenExpireField()['code'] => Settings::getInstance()->getTokenExpire()
                ])) {
                    $counter++;
                }
            }
        }
        if ($counter > 0) {
            echo CAdminMessage::ShowNote(getMessageModule('XwildRestTokenGenerated', ['#COUNT#' => $counter]));
        }
        else {
            echo CAdminMessage::ShowNote(getMessageModule('XwildRestTokenNotGenerated'));
        }
    }
    if (Config::getInstance()->getOption('tokenFieldCode')) {
        if (!Core::getInstance()->getDB()->query('SELECT FIELD_NAME FROM b_user_field WHERE FIELD_NAME="'.Config::getInstance()->getOption('tokenFieldCode').'" LIMIT 1')->fetchRaw()) {
            CAdminMessage::ShowMessage(getMessageModule('XwildRestTokenFieldCodeNotFound', ['#REST_API_TOKEN_FIELD_CODE#' => Config::getInstance()->getOption('tokenFieldCode')]));
        };
    }
    $tabControl->Begin();
?>
    <form method="POST" action="<?= $APPLICATION->GetCurUri() ?>">
        <?= bitrix_sessid_post() ?>
        <? $tabControl->BeginNextTab() ?>
        <tr>
            <td width='45%' valign='top'><?= getMessageModule('XwildRestUseLoginPassword') ?>
            <td>
            <td width='55%' valign='middle'>
                <?= InputType('checkbox', 'parameter:useLoginPassword', true, Config::getInstance()->getOption('useLoginPassword')) ?>
                <? ShowJSHint(getMessageModule('XwildRestUseLoginPasswordHint')) ?>
            <td>
        </tr>
        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestUseToken') ?>
            <td>
            <td width="55%" valign="middle">
                <?= InputType('checkbox', 'parameter:useToken', true, Config::getInstance()->getOption('useToken')) ?>
                <? ShowJSHint(getMessageModule('XwildRestUseTokenHint', [
                    '#FIELD_NAME_REST_API_TOKEN#'        => getMessageModule('XwildRestTokenField'),
                    '#FIELD_NAME_REST_API_TOKEN_EXPIRE#' => getMessageModule('XwildRestTokenFieldExpire')
                ])) ?>
            <td>
        </tr>
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestTokenKey') ?>
            <td>
            <td width="55%" valign="middle">
                <?= InputType('text', 'parameter:tokenKey', Config::getInstance()->getOption('tokenKey'), false, false, false, Config::getInstance()->getOption('useToken') ? '' : 'disabled') ?>
                <? ShowJSHint(getMessageModule('XwildRestTokenKeyHint')) ?>
            <td>
        </tr>
        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestTokenLifetime') ?>
            <td>
            <td width="55%" valign="middle">
                <?= InputType('text', 'parameter:tokenLifetime', Config::getInstance()->getOption('tokenLifetime'), false, false, false, Config::getInstance()->getOption('useToken') ? '' : 'disabled') ?>
                <? ShowJSHint(getMessageModule('XwildRestTokenLifetimeHint')) ?>
            <td>
        </tr>
        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestTokenFieldCode') ?>
            <td>
            <td width="55%" valign="middle">
                <?= InputType('text', 'parameter:tokenFieldCode', Config::getInstance()->getOption('tokenFieldCode'), false, false, false, Config::getInstance()->getOption('useToken') ? '' : 'disabled') ?>
                <? ShowJSHint(getMessageModule('XwildRestTokenFieldCodeHint', ['#REST_API_TOKEN_FIELD_CODE#' => Settings::getInstance()->getTokenField()['code']])) ?>
            <td>
        </tr>
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td width='45%' valign='middle'><?= getMessageModule('XwildRestGenerateToken') ?>
            <td>
            <td width='55%' valign='middle'>
                <?= print_url(str_replace('&generateToken=Y', '', $APPLICATION->GetCurUri()).'&generateToken=Y', getMessageModule('XwildRestGenerateTokenLinkText')) ?>
                <? ShowJSHint(getMessageModule('XwildRestGenerateTokenHint')) ?>
            <td>
        </tr>
        <? if (Config::getInstance()->getOption('showExamples')): ?>
            <tr>
                <td width="45%" valign="middle"><?= getMessageModule('XwildRestExample') ?>
                <td>
                <td width="55%" valign="middle"
                    style="color: <?= Config::getInstance()->getOption('useToken') ? 'rgb(34, 162, 59)' : 'rgb(206, 0, 0)' ?>">
                    <?= getMessageModule('XwildRestExampleToken', [
                        '#KEYWORD#' => Config::getInstance()->getOption('tokenKey') ? Config::getInstance()->getOption('tokenKey').':' : '',
                        '#TOKEN#'   => '434337b6-f12691d2-47bf6fb9-c040ae6b'
                    ]) ?>
                    <? ShowJSHint(getMessageModule('XwildRestExampleHint')) ?>
                <td>
            </tr>
        <? endif ?>
        <? $tabControl->BeginNextTab() ?>
        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestUseRequestLimit') ?>
            <td>
            <td width="55%" valign="middle">
                <?= InputType('checkbox', 'parameter:useRequestLimit', true, Config::getInstance()->getOption('useRequestLimit'), false, false, Config::getInstance()->getOption('useToken') || Config::getInstance()->getOption('useLoginPassword') ? '' : 'disabled') ?>
                <? ShowJSHint(getMessageModule('XwildRestUseRequestLimitHint')) ?>
            <td>
        </tr>

        <? if (count($groups) > 0): ?>
            <? $current = json_decode(Config::getInstance()->getOption('requestLimit'), true) ?>
            <? foreach ($groups as $id => $name): ?>
                <tr>
                    <td colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td width="45%" valign="middle"><?= getMessageModule('XwildRestGroup') ?>
                    <td>
                    <td width="55%" valign="middle">
                        <?= $name ?>
                        <? ShowJSHint(getMessageModule('XwildRestGroupHint')) ?>
                    <td>
                </tr>

                <tr>
                    <td width="45%" valign="middle"><?= getMessageModule('XwildRestNumber') ?>
                    <td>
                    <td width="55%" valign="middle">
                        <?= InputType('text', 'data:requestLimitNumber-'.$id, $current[$id]['number'], false, false, false) ?>
                        <? ShowJSHint(getMessageModule('XwildRestNumberHint')) ?>
                    <td>
                </tr>

                <tr>
                    <td width="45%" valign="middle"><?= getMessageModule('XwildRestPeriod') ?>
                    <td>
                    <td width="55%" valign="middle">
                        <?= InputType('text', 'data:requestLimitPeriod-'.$id, $current[$id]['period'], false, false, false) ?>
                        <? ShowJSHint(getMessageModule('XwildRestPeriodHint')) ?>
                    <td>
                </tr>

            <? endforeach ?>
        <? else: ?>
            <tr>
                <td colspan="4" align="center">
                    <? Helper::getInstance()->note(getMessageModule('XwildRestNoteNotEnoughGroups', ['#LANG#' => LANG])) ?>
                </td>
            </tr>
        <? endif ?>

        <? $tabControl->BeginNextTab() ?>

        <tr>
            <td width='45%' valign='top'><?= getMessageModule('XwildRestUseCorsFilter') ?>
            <td>
            <td width='55%' valign='middle'>
                <?= InputType('checkbox', 'parameter:useCorsFilter', true, Config::getInstance()->getOption('useCorsFilter')) ?>
                <? ShowJSHint(getMessageModule('XwildRestUseCorsFilterHint')) ?>
            <td>
        </tr>
        <tr>
            <td width='45%'
                valign='top'><?= getMessageModule('XwildRestCorsListDomains') ?>
            <td>
            <td width='55%' valign='top'>
                <textarea name="parameter:corsListDomains" cols="50"
                          rows="5"><?= Config::getInstance()->getOption('corsListDomains') ?></textarea>
                <? ShowJSHint(getMessageModule('XwildRestCorsListDomainsHint')) ?>
            <td>
        </tr>
        <?=InputType('hidden', 'form', basename(__FILE__, '.php'), false); ?>
    </form>
<?php
    $tabControl->Buttons();
    echo InputType('submit', 'save', getMessageModule('XwildRestButtonSave'), false, false, false, Config::getInstance()->getOption('useRestApi') ? 'class="adm-btn-save"' : 'disabled');
    echo InputType('submit', 'restore', getMessageModule('XwildRestButtonRestore'), false, false, false, Config::getInstance()->getOption('useRestApi') ? '' : 'disabled');
    $tabControl->End();
    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
