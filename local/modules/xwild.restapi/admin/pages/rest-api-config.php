<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

    use Bitrix\Main\Loader;
    use Bitrix\Main\Localization\Loc;
    use Xwild\Restapi\Foundation\Page;
    use Xwild\Restapi\Foundation\Helper;
    use Xwild\Restapi\Foundation\Config;
    use Xwild\Restapi\Foundation\Settings;

    Loader::includeModule('xwild.restapi');

    Page::getInstance()->checkAccess('accessConfig');
    Page::getInstance()->setTitle(getMessageModule('XwildRestConfigPageTitle'));

    $tabs = [
        ['DIV' => 'tab-1', 'TAB' => getMessageModule('XwildRestTabMainTitle')],
        ['DIV' => 'tab-2', 'TAB' => getMessageModule('XwildRestTabRoutesTitle')],
        ['DIV' => 'tab-3', 'TAB' => getMessageModule('XwildRestTabAccessTitle')]
    ];
    $tabControl = new CAdminTabControl('tabControl', $tabs);
    $groups = [];
    $result = CGroup::GetList($by = 'NAME', $order = 'ASC', ['ACTIVE' => 'Y', 'ANONYMOUS' => 'N']);
    while ($group = $result->fetch()) {
        if ($group['ID'] == Helper::getInstance()->adminGroupId() || $group['ID'] == Helper::getInstance()->ratingVoteGroupId() || $group['ID'] == Helper::getInstance()->ratingVoteAuthorityGroupId())
            continue;
        $groups['REFERENCE_ID'][] = $group['ID'];
        $groups['REFERENCE'][] = $group['NAME'];
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
    $tabControl->Begin()
?>
    <form method="POST" action="<?= $APPLICATION->GetCurUri() ?>">
        <?= bitrix_sessid_post() ?>
        <? $tabControl->BeginNextTab() ?>
        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestUseRestApi') ?>
            <td>
            <td width="55%" valign="middle">
                <?= InputType('checkbox', 'parameter:useRestApi', true, Config::getInstance()->getOption('useRestApi')) ?>
                <? ShowJSHint(getMessageModule('XwildRestUseRestApiHint')) ?>
            <td>
        </tr>
        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestPathRestApi') ?>
            <td>
            <td width="55%" valign="middle">
                <?= InputType('text', 'parameter:pathRestApi', Config::getInstance()->getOption('pathRestApi'), false, false, false, 'size="10"') ?>
                <? ShowJSHint(getMessageModule('XwildRestPathRestApiHint')) ?>
            <td>
        </tr>
        <tr>
            <td colspan="4" align="center">&nbsp;</td>
        </tr>

        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestUseLateStart') ?>
            <td>
            <td width="55%" valign="middle">
                <?= InputType('checkbox', 'parameter:useLateStart', true, Config::getInstance()->getOption('useLateStart')) ?>
                <? ShowJSHint(getMessageModule('XwildRestUseLateStartHint')) ?>
            <td>
        </tr>

        <tr>
            <td colspan="4" align="center">&nbsp;</td>
        </tr>
        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestUseJournal') ?>
            <td>
            <td width="55%" valign="middle">
                <?= InputType('checkbox', 'parameter:useJournal', true, Config::getInstance()->getOption('useJournal')) ?>
                <? ShowJSHint(getMessageModule('XwildRestUseJournalHint')) ?>
            <td>
        </tr>
        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestShowExamples') ?>
            <td>
            <td width="55%" valign="middle">
                <?= InputType('checkbox', 'parameter:showExamples', true, Config::getInstance()->getOption('showExamples')) ?>
                <? ShowJSHint(getMessageModule('XwildRestShowExamplesHint')) ?>
            <td>
        </tr>
        <? $tabControl->BeginNextTab() ?>
        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestUseNativeRoute') ?>
            <td>
            <td width="55%" valign="middle">
                <?= InputType('checkbox', 'parameter:useNativeRoute', true, Config::getInstance()->getOption('useNativeRoute')) ?>
                <? ShowJSHint(getMessageModule('XwildRestUseNativeRouteHint')) ?>
            <td>
        </tr>

        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestUseExampleRoute') ?>
            <td>
            <td width="55%" valign="middle">
                <?= InputType('checkbox', 'parameter:useExampleRoute', true, Config::getInstance()->getOption('useExampleRoute')) ?>
                <? ShowJSHint(getMessageModule('XwildRestUseExampleRouteHint')) ?>
            <td>
        </tr>

        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestLocalRouteMap') ?>
            <td>
            <td width="55%" valign="middle">
                <?= InputType('text', 'parameter:localRouteMap', Config::getInstance()->getOption('localRouteMap'), false, false, false, 'size="30"') ?>
                <? ShowJSHint(getMessageModule('XwildRestLocalRouteMapHint')) ?>
            <td>
        </tr>
        <? $tabControl->BeginNextTab() ?>
        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestAccessDocumentation') ?>
            <td>
            <td width="55%" valign="middle">
                <?= SelectBoxMFromArray('parameter:accessDocumentation[]', $groups, explode('|', Config::getInstance()->getOption('accessDocumentation')), '', false, 5, 'class ="inputselect"') ?>
                <? ShowJSHint(getMessageModule('XwildRestAccessDocumentationHint')) ?>
            <td>
        </tr>
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestAccessSecurity') ?>
            <td>
            <td width="55%" valign="middle">
                <?= SelectBoxMFromArray('parameter:accessSecurity[]', $groups, explode('|', Config::getInstance()->getOption('accessSecurity')), '', false, 5, 'class ="inputselect"') ?>
                <? ShowJSHint(getMessageModule('XwildRestAccessSecurityHint')) ?>
            <td>
        </tr>
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestAccessJournal') ?>
            <td>
            <td width="55%" valign="middle">
                <?= SelectBoxMFromArray('parameter:accessJournal[]', $groups, explode('|', Config::getInstance()->getOption('accessJournal')), '', false, 5, 'class ="inputselect"') ?>
                <? ShowJSHint(getMessageModule('XwildRestAccessJournalHint')) ?>
            <td>
        </tr>
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestAccessSupport') ?>
            <td>
            <td width="55%" valign="middle">
                <?= SelectBoxMFromArray('parameter:accessSupport[]', $groups, explode('|', Config::getInstance()->getOption('accessSupport')), '', false, 5, 'class ="inputselect"') ?>
                <? ShowJSHint(getMessageModule('XwildRestAccessSupportHint')) ?>
            <td>
        </tr>

        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td width="45%" valign="middle"><?= getMessageModule('XwildRestaccessMenuItems') ?>
            <td>
            <td width="55%" valign="middle">
                <?= SelectBoxMFromArray('parameter:accessMenuItems[]', $groups, explode('|', Config::getInstance()->getOption('accessMenuItems')), '', false, 5, 'class ="inputselect"') ?>
                <? ShowJSHint(getMessageModule('XwildRestaccessMenuItemsHint', ['#MODULE_NAME#' => Settings::getInstance()->getModule()['name']])) ?>
            <td>
        </tr>
        <?=InputType('hidden', 'form', basename(__FILE__, '.php'), false); ?>
    </form>

<?php
    $tabControl->Buttons();
    echo InputType('submit', 'save', Loc::getMessage('XwildRestButtonSave'), false, false, false, 'class="adm-btn-save"');
    echo InputType('submit', 'restore', Loc::getMessage('XwildRestButtonRestore'), false);
    $tabControl->End();
    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
