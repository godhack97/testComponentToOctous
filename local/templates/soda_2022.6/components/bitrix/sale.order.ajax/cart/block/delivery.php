<?

use Bitrix\Main\Localization\Loc;

global $USER;

$isRostov = false;

$arDeliveries = $arResult["JS_DATA"]["DELIVERY"];

if (count($arDeliveries) > 1) {
    $arDeliveriesTmp = [];
    $arRostovDeliveries = []; //[74, 75];
    foreach ($arDeliveries as $index => $arDelivery) {
        if ($arDelivery["ID"] == 47) {
            $arDeliveriesTmp[] = $arDelivery;
            continue;
        }

        if ($isRostov) {
            if (in_array($arDelivery["ID"], $arRostovDeliveries))
                $arDeliveriesTmp[] = $arDelivery;
        } else {
            if (!in_array($arDelivery["ID"], $arRostovDeliveries))
                $arDeliveriesTmp[] = $arDelivery;
        }
    }

    $arDeliveries = $arDeliveriesTmp;

    foreach ($arDeliveries as $index => $arDelivery) {
        if ($arDelivery["ID"] == 47) {
            $arDelivery["DESCRIPTION"] = str_replace("<a class='btn btn-default'", "<a class='black js-pp-pvz'", $arDelivery["DESCRIPTION"]);
            $arDelivery["DESCRIPTION"] = str_replace("Выбрать пункт доставки", "Выбрать пункт выдачи", $arDelivery["DESCRIPTION"]);
        }

        if ($arDelivery["ID"] == 92) {
            $arDelivery["DESCRIPTION"] = str_replace("class='btn'", "class='btn-russian-post'", $arDelivery["DESCRIPTION"]);
            $arDelivery["DESCRIPTION"] = str_replace("Выбрать пункт выдачи на карте", "Выбрать пункт выдачи", $arDelivery["DESCRIPTION"]);
            $arDelivery["DESCRIPTION"] = preg_replace('/(<[^>]+) style=".*?"/i', "$1", $arDelivery["DESCRIPTION"]);
        }

        if ($arDelivery["ID"] == 60) {
            $arDelivery["PERIOD_TEXT"] = substr($arDelivery["PERIOD_TEXT"], strpos($arDelivery["PERIOD_TEXT"], "<input"));
            $arDelivery["DESCRIPTION"] .= $arDelivery["PERIOD_TEXT"];
        }

        $arDeliveries[$index] = $arDelivery;
    }
}
?>

<h1><?= Loc::getMessage("SALE_ORDER_DELIVERY_DELIVERY_METHOD") ?></h1>
<br />
<div class="text intro-text-wrap">
    <?= Loc::getMessage("SALE_ORDER_DELIVERY_DELIVERY_INDICATED_WORK_DAYS") ?>
</div>

<div class="items">
    <? foreach ($arDeliveries as $delivery) : ?>
        <?
        if ($delivery["ID"] == 47) {
            $cost = (int)str_replace(array(" ", "руб."), array(""), $arResult["DELIVERY_PRICE_CUSTOM"][$delivery["ID"]]["COST"]);
            // Если PickPoint дороже 400 руб., то отключаем его
            if ($cost > 400)
                continue;
        }
        ?>
        <div class="payment<? if ($delivery["CHECKED"] == "Y") : ?> active<? endif ?>">
            <div class="field-radio">
                <label class="label-flx">
                    <input id="ID_DELIVERY_ID_<?= $delivery["ID"] ?>" value="<?= $delivery["ID"] ?>" type="radio" name="DELIVERY_ID" <? if ($delivery["CHECKED"] == "Y") : ?>checked="checked" <? endif ?> aria-required="true" required="required">
                    <div class="label">
                        <?= $delivery["OWN_NAME"] ?>
                    </div>
                    <? if ($arResult["DELIVERY_PRICE_CUSTOM"][$delivery["ID"]]["PERIOD"]) : ?>
                        <div class="period">
                            <?= $arResult["DELIVERY_PRICE_CUSTOM"][$delivery["ID"]]["PERIOD"] ?>
                        </div>
                    <? endif ?>
                    <div class="cost">
                        <? if (!empty($arResult["DELIVERY_PRICE_CUSTOM"][$delivery["ID"]]["COST"])) : ?>
                            <? if ($arResult["USE_EXTEND_CURRENCY"]) : ?>
                                <?= $arResult["DELIVERY_PRICE_CUSTOM"][$delivery["ID"]]["COST_EXTEND"] ?>
                            <? else : ?>
                                <?= $arResult["DELIVERY_PRICE_CUSTOM"][$delivery["ID"]]["COST"] ?>
                            <? endif; ?>
                        <? endif ?>
                    </div>
                </label>

                <? if ($delivery["ID"] == 3) : ?>
                    <div class="description">ул. Малая Бронная, 19а</div>
                <? endif ?>

                <? if ($delivery["ID"] == 74) : ?>
                    <div class="description">ул. Большая Садовая, 182</div>
                <? endif ?>

                <? if ($delivery["ID"] == 67) : ?>
                    <div class="description">ул. Большая Конюшенная, д. 1</div>
                <? endif ?>

                <? if ($delivery["ID"] == 47 && $delivery["CHECKED"] == "Y") : ?>
                    <? $delivery["DESCRIPTION"] = str_replace(Loc::getMessage("SALE_ORDER_DELIVERY_POSTAMAT"), "", $delivery["DESCRIPTION"]); ?>
                    <div class="pvz-select"><?= $delivery["DESCRIPTION"] ?></div>
                <? endif ?>

                <? if ($delivery["ID"] == 92 && $delivery["CHECKED"] == "Y") : ?>
                    <? $delivery["DESCRIPTION"] = str_replace(Loc::getMessage("SALE_ORDER_DELIVERY_POSTAMAT"), "", $delivery["DESCRIPTION"]); ?>
                    <div class="pvz-select"><?= $delivery["DESCRIPTION"] ?></div>
                <? endif ?>

                <? if ($delivery["ID"] == 60 && $delivery["CHECKED"] == "Y") : ?>
                    <? $delivery["DESCRIPTION"] = str_replace(Loc::getMessage("SALE_ORDER_DELIVERY_POSTAMAT"), "", $delivery["DESCRIPTION"]); ?>
                    <div class="pvz-select"><?= $delivery["DESCRIPTION"] ?></div>
                <? endif ?>

                <? if ($delivery["ID"] == 72 && $delivery["CHECKED"] == "Y") : ?>
                    <div class="pvz-select" id="pvz-insert"></div>
                <? endif ?>
            </div>
        </div>
    <? endforeach ?>
</div>