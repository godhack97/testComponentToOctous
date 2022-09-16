<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale;

/**
 * @var array $arParams
 * @var array $arResult
 * @var $APPLICATION CMain
 */

if ($arParams['SET_TITLE'] == 'Y') {
    $APPLICATION->SetTitle(Loc::getMessage('SOA_ORDER_COMPLETE'));
}
?>

<?php if (!empty($arResult['ORDER'])) : ?>
    <table class="sale_order_full_table" data-table="1">
        <tr>
            <td>
                <?= Loc::getMessage('SOA_ORDER_SUC', [
                    '#ORDER_DATE#' => $arResult['ORDER']['DATE_INSERT'],
                    '#ORDER_ID#' => $arResult['ORDER']['ACCOUNT_NUMBER']
                ]) ?>

                <?php
                $order = Sale\Order::load($arResult['ORDER_ID']);
                if (\Bitrix\Sale\Helpers\Order::isAllowGuestView($order)) {
                    $publicLink = \Bitrix\Sale\Helpers\Order::getPublicLink($order);
                    if (!empty($publicLink)) {
                        ?>
                        <br>
                        <a class="black hover" target="_blank" href="<?= $publicLink ?>"><?= Loc::getMessage('SALE_ORDER_CONFIRM_YOUR_ORDER') ?></a>
                        <br>
                <?php
                    }
                }
                ?>
            </td>
        </tr>
    </table>

    <?php
    if ($arResult['ORDER']['IS_ALLOW_PAY'] === 'Y') {
        if (!empty($arResult['PAYMENT'])) {
            foreach ($arResult['PAYMENT'] as $payment) {
                if ($payment['PAID'] != 'Y') {
                    if (
                        !empty($arResult['PAY_SYSTEM_LIST'])
                        && array_key_exists($payment['PAY_SYSTEM_ID'], $arResult['PAY_SYSTEM_LIST'])
                    ) {
                        $arPaySystem = $arResult['PAY_SYSTEM_LIST'][$payment['PAY_SYSTEM_ID']];

                        if (empty($arPaySystem['ERROR'])) {
                            $arOrder = CSaleOrder::GetByID($arResult['ORDER_ID']); ?>
                            <br />
                            <table class="sale_order_full_table" data-table="2">
                                <tr<?php if ($arPaySystem['ID'] == PAY_SBER_ID) : ?> style="display:none" <?php endif ?>>
                                    <td class="ps_logo">
                                        <?= CFile::ShowImage($arPaySystem['LOGOTIP'], 100, 100, 'border=0" style=""', '', false) ?>
                                        <div class="pay_name"><?= Loc::getMessage('SOA_PAY') ?>:</div>
                                        <?php $paymentName = Loc::getMessage('PAYMENT_NAME_' . $arPaySystem['ID']); ?>
                                        <div class="paysystem_name"><?= (empty($paymentName)) ? $arPaySystem['NAME'] : $paymentName ?></div>
                                        <br />
                                    </td>
                                    </tr>
                                    <tr>
                                        <td>

                                            <?php if (in_array($arPaySystem['ID'], [9, PAY_SBER_ID])) : ?>
                                                <br>
                                                <h2><?= Loc::getMessage('SALE_ORDER_CONFIRM_EXPECT_PAYMENT') ?></h2>
                                            <?php endif ?>

                                            <?php if ($arPaySystem['ID'] == 10 && in_array($arOrder['DELIVERY_ID'], [4, 6, 5])) : ?>
                                                <br>
                                                <h2><?= Loc::getMessage('SALE_ORDER_CONFIRM_PPROMPPT_FITTING') ?></h2>
                                            <?php endif ?>

                                            <?php if (strlen($arPaySystem['ACTION_FILE']) > 0 && $arPaySystem['NEW_WINDOW'] == 'Y' && $arPaySystem['IS_CASH'] != 'Y') : ?>
                                                <?php
                                                $orderAccountNumber = urlencode(urlencode($arResult['ORDER']['ACCOUNT_NUMBER']));
                                                $paymentAccountNumber = $payment['ACCOUNT_NUMBER']; ?>
                                                <script>
                                                    window.open('<?= $arParams['PATH_TO_PAYMENT'] ?>?ORDER_ID=<?= $orderAccountNumber ?>&PAYMENT_ID=<?= $paymentAccountNumber ?>');
                                                </script>
                                                <?= Loc::getMessage('SOA_PAY_LINK', ['#LINK#' => $arParams['PATH_TO_PAYMENT'] . '?ORDER_ID=' . $orderAccountNumber . '&PAYMENT_ID=' . $paymentAccountNumber]) ?>
                                                <?php if (CSalePdf::isPdfAvailable() && $arPaySystem['IS_AFFORD_PDF']) : ?>
                                                    <br />
                                                    <?= Loc::getMessage('SOA_PAY_PDF', ['#LINK#' => $arParams['PATH_TO_PAYMENT'] . '?ORDER_ID=' . $orderAccountNumber . '&pdf=1&DOWNLOAD=Y']) ?>
                                                <?php endif ?>
                                            <?php else : ?>
                                                <?php
                                                $arPaySystem['BUFFERED_OUTPUT'] = str_replace('btn btn-default btn-buy btn-md', 'ibutton', $arPaySystem['BUFFERED_OUTPUT']);
                                                $arPaySystem['BUFFERED_OUTPUT'] = str_replace('sberbank__payment-link', 'sberbank__payment-link ibutton', $arPaySystem['BUFFERED_OUTPUT']);
                                                $arPaySystem['BUFFERED_OUTPUT'] = str_replace(Loc::getMessage('SALE_ORDER_CONFIRM_PEY_REDIRECT'), Loc::getMessage('SALE_ORDER_CONFIRM_PAY'), $arPaySystem['BUFFERED_OUTPUT']); ?>
                                                <?= $arPaySystem['BUFFERED_OUTPUT'] ?>
                                            <?php endif ?>
                                        </td>
                                    </tr>
                            </table>

                        <?php
                        } else {
                            ?>
                            <span style="color:red;"><?= Loc::getMessage('SOA_ORDER_PS_ERROR') ?></span>
                        <?php
                        }
                    } else {
                        ?>
                        <span style="color:red;"><?= Loc::getMessage('SOA_ORDER_PS_ERROR') ?></span>
        <?php
                    }
                }
            }
        }
    } else {
        ?>
        <br /><strong><?= $arParams['MESS_PAY_SYSTEM_PAYABLE_ERROR'] ?></strong>
    <?php
    }
    ?>


    <?php if (Loc::getCurrentLang() == 'ru' && $USER->IsAdmin()) : ?>

        <a class="ibutton js-open-userinfo" data-user="<?= $arResult['ORDER']['USER_ID'] ?>" data-order="<?= $arResult['ORDER_ID'] ?>">Получить кэшбэк 5%</a>
    <?php endif ?>

<?php else : ?>
    <?php
    if ($arResult['ORDER_ID'] > 0) {
        global $USER;
        $order = Sale\Order::load($arResult['ORDER_ID']);
        if ($order->getField('CREATED_BY') != $order->getUserId() && $order->getField('CREATED_BY') == $USER->getID()) {
            if (\Bitrix\Sale\Helpers\Order::isAllowGuestView($order)) {
                $publicLink = \Bitrix\Sale\Helpers\Order::getPublicLink($order);
                if (!empty($publicLink)) {
                    LocalRedirect($publicLink);
                }
            }
        }
    }
    ?>
    <b><?= Loc::getMessage('SOA_ERROR_ORDER') ?></b>
    <br />
    <br />
    <table class="sale_order_full_table" data-table="3">
        <tr>
            <td>
                <?= Loc::getMessage('SOA_ERROR_ORDER_LOST', ['#ORDER_ID#' => $arResult['ACCOUNT_NUMBER']]) ?>
                <?= Loc::getMessage('SOA_ERROR_ORDER_LOST1') ?>
            </td>
        </tr>
    </table>
<?php endif ?>

<?php if ($_GET['admin'] == 'y') : ?>
    <a class="ibutton js-open-userinfo">Получить кэшбек 5% с заказа</a>
<?php endif ?>

<?php if (SITE_ID !== 's1') : ?>
    <script>
        let stringPrice = $(".sberbank__price-string").text();
        let newString = stringPrice.replace(/\(.*\)/, "");
        $(".sberbank__price-string").html(newString);
    </script>
<?php endif ?>