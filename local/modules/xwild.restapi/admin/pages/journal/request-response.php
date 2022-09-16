<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

    use Xwild\Restapi\Foundation\Page;
    use Xwild\Restapi\Foundation\Journal;
    use Xwild\Restapi\Foundation\Settings;
    use Bitrix\Main\Localization\Loc;

    Bitrix\Main\Loader::includeModule('xwild.restapi');

    Page::getInstance()->checkAccess('accessJournal');
    Page::getInstance()->setTitle(getMessageModule('XwildRestMenuItemJournalRequestResponse'));

    $sTableID = 'tbl_'.Settings::getInstance()->getTable()['request-response'];
    $oSort = new CAdminSorting($sTableID, $by, $order);
    $lAdmin = new CAdminList($sTableID, $oSort);

    if ($ids = $lAdmin->GroupAction()) {
        if ($_REQUEST['action_target'] === 'selected') {
            $ids = '*';
        }
        if ($ids) {
            switch ($_REQUEST['action']) {
                case 'delete':
                    Journal::getInstance()->deleteData('request-response', $ids);
                    break;
            }
        }
    }
    function CheckFilter() {
        global $arFilterFields, $lAdmin;
        foreach ($arFilterFields as $f) global $$f;
        return count($lAdmin->arFilterErrors) == 0;
    }

    $arFilterFields = [
        'find_id', 'find_date_from', 'find_date_to', 'find_client_id', 'find_ip', 'find_method',
    ];
    $arFilter = [];
    $lAdmin->InitFilter($arFilterFields);
    InitSorting();
    if (CheckFilter()) {
        $arFilter = [
            'ID'        => $find_id, 'DATETIME_FROM' => $find_date_from, 'DATETIME_TO' => $find_date_to,
            'CLIENT_ID' => $find_client_id, 'IP' => $find_ip, 'METHOD' => $find_method,
        ];
    }
    $arSort = [
        'field' => $by ? $by : 'ID', 'order' => $order ? $order : 'DESC'
    ];
    $arNavParams = (isset($_REQUEST['mode']) && $_REQUEST['mode'] === 'excel') ? false : ['nPageSize' => CAdminResult::GetNavSize($sTableID)];
    $rsData = Journal::getInstance()->getData('request-response', $arFilter, $arSort);
    $rsData = new CAdminResult($rsData, $sTableID);
    $rsData->NavStart();
    $lAdmin->NavText($rsData->GetNavPrint(getMessageModule('XwildRestRequests')));
    $arHeaders = [
        [
            'id' => 'ID', 'content' => getMessageModule('XwildRestId'), 'sort' => 'ID', 'default' => true, 'align' => 'right',
        ],
        [
            'id' => 'DATETIME', 'content' => getMessageModule('XwildRestDateTime'), 'sort' => 'DATETIME', 'default' => true
        ],
        [
            'id' => 'CLIENT_ID', 'content' => getMessageModule('XwildRestClientId'), 'sort' => 'CLIENT_ID', 'default' => true
        ],
        [
            'id' => 'IP', 'content' => getMessageModule('XwildRestIp'), 'sort' => 'IP', 'default' => true
        ],
        [
            'id' => 'METHOD', 'content' => getMessageModule('XwildRestMethod'), 'sort' => 'METHOD', 'default' => true
        ],
    ];
    $lAdmin->AddHeaders($arHeaders);
    while ($ar = $rsData->fetch()) {
        $row =& $lAdmin->AddRow($ar['ID'], $ar);
        if ($ar['ID']) {
            $row->AddViewField('ID', '<a href="/bitrix/admin/xwild-restapi-journal-request-response-record.php?lang='.LANGUAGE_ID.'&id='.$ar['ID'].'">'.$ar['ID'].'</a>');
        }
        $arActions = [];
        $arActions[] = [
            'ICON' => 'view', 'DEFAULT' => 'Y', 'TEXT' => getMessageModule('XwildRestButtonView'),
            'LINK' => 'xwild-restapi-journal-request-response-record.php?lang='.LANGUAGE_ID.'&id='.$ar['ID'],
        ];
        $arActions[] = [
            'ICON'   => 'delete', 'DEFAULT' => 'N', 'TEXT' => getMessageModule('XwildRestButtonDelete'),
            'ACTION' => "if(confirm('".GetMessageJS('XwildRestConfirmDelete')."')) ".$lAdmin->ActionDoGroup($ar['ID'], 'delete'),
        ];
        $row->AddActions($arActions);
    }
    $lAdmin->AddGroupActionTable(['delete' => true]);
    $lAdmin->CheckListMode();
    $arFilterNames = [
        'find_id'        => getMessageModule('XwildRestId'), 'find_date_from' => getMessageModule('XwildRestDateTime'),
        'find_client_id' => getMessageModule('XwildRestClientId'), 'find_ip' => getMessageModule('XwildRestIp'),
        'find_method'    => getMessageModule('XwildRestMethod'),
    ];
    $oFilter = new CAdminFilter($sTableID.'_filter', $arFilterNames);
    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';
?>
    <form name="filter" method="GET" action="<?= $APPLICATION->GetCurPage() ?>?">
        <input type="hidden" name="lang" value="<?= LANG ?>">
        <? $oFilter->Begin() ?>
        <tr>
            <td><?= getMessageModule('XwildRestId') ?>:</td>
            <td><?= InputType('text', 'find_id', htmlspecialcharsbx($find_id), false) ?></td>
        </tr>
        <tr>
            <td><?= getMessageModule('XwildRestDateTime') ?>:</td>
            <td><?= CalendarPeriod('find_date_from', $find_date_from, 'find_date_to', $find_date_to, 'filter', 'Y') ?></td>
        </tr>
        <tr>
            <td><?= getMessageModule('XwildRestClientId') ?>:</td>
            <td><?= InputType('text', 'find_client_id', htmlspecialcharsbx($find_client_id), false) ?></td>
        </tr>
        <tr>
            <td><?= getMessageModule('XwildRestIp') ?>:</td>
            <td><?= InputType('text', 'find_ip', htmlspecialcharsbx($find_ip), false) ?></td>
        </tr>
        <tr>
            <td><?= getMessageModule('XwildRestMethod') ?>:</td>
            <td><?= InputType('text', 'find_method', htmlspecialcharsbx($find_method), false) ?></td>
        </tr>
        <?
            $oFilter->Buttons(['table_id' => $sTableID, 'url' => $APPLICATION->GetCurPage(), 'form' => 'filter']);
            $oFilter->End();
        ?>
    </form>
<?php
    $lAdmin->DisplayList();
    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
