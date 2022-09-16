<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

    use Xwild\Restapi\Foundation\Page;
    use Xwild\Restapi\Foundation\Journal;
    use Bitrix\Main\Localization\Loc;

    Bitrix\Main\Loader::includeModule('xwild.restapi');

    Page::getInstance()->checkAccess('accessJournal');
    Page::getInstance()->setTitle(getMessageModule('XwildRestRequest').' №'.(int)$_GET['id']);
    $tabControl = new CAdminTabControl('tabControl', [
        [
            'DIV'   => 'tab-1', 'TAB' => getMessageModule('XwildRestTabMainTitle'),
            'TITLE' => getMessageModule('XwildRestTabMainDescription')
        ], [
            'DIV'   => 'tab-2', 'TAB' => getMessageModule('XwildRestTabRequestTitle'),
            'TITLE' => getMessageModule('XwildRestTabRequestDescription')
        ], [
            'DIV'   => 'tab-3', 'TAB' => getMessageModule('XwildRestTabResponseTitle'),
            'TITLE' => getMessageModule('XwildRestTabResponseDescription')
        ]
    ]);
    $arResult = Journal::getInstance()->getData('request-response', ['ID' => (int)$_GET['id']])->fetchRaw();
    $arResult['REQUEST'] = json_decode($arResult['REQUEST'], true);
    $arResult['RESPONSE'] = json_decode($arResult['RESPONSE'], true);
    $context = new CAdminContextMenu([
        [
            'TEXT' => getMessageModule('XwildRestButtonBackText'), 'TITLE' => getMessageModule('XwildRestButtonBackTitle'),
            'LINK' => 'xwild-restapi-journal-request-response.php?lang='.LANGUAGE_ID, 'ICON' => 'btn_list'
        ]
    ]);
    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';
    $context->Show();
    $tabControl->Begin();
?>
<? $tabControl->BeginNextTab() ?>
    <tr>
        <td width="45%" valign="middle"><?= getMessageModule('XwildRestId') ?>
        <td>
        <td width="55%" valign="middle"><?= $arResult['ID'] ?>
        <td>
    </tr>
    <tr>
        <td width="45%" valign="middle"><?= getMessageModule('XwildRestMethod') ?>
        <td>
        <td width="55%" valign="middle"><?= $arResult['METHOD'] ?>
        <td>
    </tr>
    <tr>
        <td width="45%" valign="middle"><?= getMessageModule('XwildRestDateTime') ?>
        <td>
        <td width="55%" valign="middle"><?= $arResult['DATETIME'] ?>
        <td>
    </tr>
    <tr>
        <td width="45%" valign="middle"><?= Loc::getMessage('XwildRestIp') ?>
        <td>
        <td width="55%" valign="middle"><?= $arResult['IP'] ?>
        <td>
    </tr>
<? if ($arResult['CLIENT_ID']): ?>
    <tr>
        <td width="45%" valign="middle"><?= Loc::getMessage('XwildRestClientId') ?>
        <td>
        <td width="55%" valign="middle"><?= $arResult['CLIENT_ID'] ?>
        <td>
    </tr>
<? endif ?>
<? if ($arResult['REQUEST']): ?>
    <? $tabControl->BeginNextTab() ?>
    <tr>
        <td colspan="4">
            <? \Xwild\Restapi\Foundation\Helper::getInstance()->_print($arResult['REQUEST']) ?>
        </td>
    </tr>
<? endif ?>
<? if ($arResult['RESPONSE']): ?>
    <? $tabControl->BeginNextTab() ?>
    <tr>
        <td colspan="4"><? \Xwild\Restapi\Foundation\Helper::getInstance()->_print($arResult['RESPONSE']) ?></td>
    </tr>
<? endif ?>
<?php
    $tabControl->End();
    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
