<?php

use Bitrix\Main\Localization\Loc;
use App\Helpers\Language;

global $APPLICATION, $USER;

$isAddressRequired = false;
$isAddressReadonly = false;
$isAddressDisplayNone = false;

$lang = Language::getLanguageBySiteId();

foreach ($arResult["JS_DATA"]['DELIVERY'] as $delivery) {
    if ($delivery['ID'] == 47 && $delivery['CHECKED'] == 'Y') { // PickPoint
        $isAddressDisplayNone = true;
    }
}

// Функция сортировки
function sortArrayInSort_2($x, $y)
{
    return ($x['SORT'] > $y['SORT']);
}
uasort($arResult["JS_DATA"]["ORDER_PROP"]["properties"], 'sortArrayInSort_2');

// dd($arResult["JS_DATA"]["ORDER_PROP"]["properties"]);
foreach ($arResult["JS_DATA"]["ORDER_PROP"]["properties"] as $properties) {
    $isDisplayNone = false;
    if ($arResult["JS_DATA"]['DELIVERY'][47]['CHECKED'] == 'Y' && $properties["CODE"] == 'ADDRESS') {
        $isDisplayNone = true;
    }

    if (in_array($properties["CODE"], ['CITY', 'PVZ'])) {
        ?>
        <input type="hidden" name="ORDER_PROP_<?= $properties["ID"] ?>" value="<?= $properties["VALUE"][0] ?>" id="<?= $properties["CODE"] ?>">
    <?php
        continue;
    } ?>

    <div class="field field-code-<?= strtolower($properties['CODE']) ?>" data-type="<?= $properties["TYPE"] ?>" <?php if (strtolower($properties['CODE']) == "email" && trim($_REQUEST["set-email"]) == "Y") { ?>style="display: block;" <?php } ?>>
        <?php
        switch ($properties["TYPE"]) {
            case "LOCATION":
                if (empty($properties["VALUE"][0])) { // Принудительно ставим город Москва по-умолчанию
                    $properties["VALUE"][0] = '0000073738';
                }
        ?>
                <?php $APPLICATION->IncludeComponent("bitrix:sale.location.selector.search", "", [
                    "COMPONENT_TEMPLATE" => ".default",
                    "CODE" => $properties["VALUE"][0],
                    "INPUT_NAME" => "ORDER_PROP_" . $properties["ID"],
                    "PROVIDE_LINK_BY" => "code",
                    "JSCONTROL_GLOBAL_ID" => "",
                    "JS_CALLBACK" => "CartCallback",
                    "FILTER_BY_SITE" => "Y",
                    "SHOW_DEFAULT_LOCATIONS" => "Y",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "36000000",
                    "FILTER_SITE_ID" => SITE_ID,
                    "INITIALIZE_BY_GLOBAL_EVENT" => "",
                    "SUPPRESS_ERRORS" => "N",
                    "CLASS_ID" => "" . $properties["CODE"],
                ]);
                ?>
            <?php
                break;

            case "DATE":
            ?>
                <!--<input type="text"
                       size="<?= $properties["SIZE"] ?>"
                       name="ORDER_PROP_<?= $properties["ID"] ?>"
                       value="<?= $properties["VALUE"][0] ?>"
                       id="<?= $properties["CODE"] ?>"
                       class="field-style">
                <div class="calendar-picker"><img src="<?= $templateFolder ?>/img/calendar.svg" alt="<?= $properties["NAME"] ?>"></div>
        -->
                <?php //echo CalendarDate("ORDER_PROP_".$properties["ID"], $properties["VALUE"][0], "ORDER_FORM", "15", "class=\"field-style\"")
                ?>
                <?php //=Calendar("ORDER_PROP_".$properties["ID"], "ORDER_FORM")
                ?>
                <?php
                if ($arParams['USER_SELECTION'] == 'Y') {
                    $APPLICATION->IncludeComponent('bitrix:main.calendar', 'cart.create', [
                        'SHOW_INPUT' => 'Y',
                        'FORM_NAME' => 'ORDER_FORM',
                        'INPUT_NAME' => "ORDER_PROP_" . $properties["ID"],
                        'INPUT_VALUE' => '', //$properties["VALUE"][0],
                        'SHOW_TIME' => 'Y',
                        'HIDE_TIMEBAR' => 'N',
                        'INPUT_ADDITIONAL_ATTR' => 'class="field-style js-calendar-field" placeholder="' . Loc::getMessage("SALE_ORDER_PROPERTY_DELIVERY_DAT") . '"'
                    ]);
                }

                break;

            case "ENUM":
                //if($USER->IsAdmin() && in_array($properties["CODE"], ['BIRTHDAY_MONTH']))
                if (in_array($properties["CODE"], ['BIRTHDAY_MONTH'])) {
                    ?>
                    <div class="birthday-indent">
                        <input type="text" data-dbg="birthday enum" placeholder=" " size="<?= $properties["SIZE"] ?>" name="ORDER_PROP_<?= $properties["ID"] ?>" value="<?= $properties["VALUE"][0] ?>" id="<?= $properties["CODE"] ?>" <?php if ($properties["REQUIRED"] == "Y") : ?>aria-required="true" required="" <?php endif ?> class="field-style">
                        <label class="placeholder" for="<?= $properties["CODE"] ?>"><?= Loc::getMessage("SALE_ORDER_" . $properties["CODE"] . "_TITLE") ?></label>
                    </div>
                <?php
                }
                break;

            case "STRING":
            default:

                if ($properties["MULTILINE"] == "Y") {
                    ?><textarea name="ORDER_PROP_<?= $properties["ID"] ?>" placeholder="<?= $properties["DESCRIPTION"] ? $properties["DESCRIPTION"] : $properties["NAME"] ?>" cols="<?= $properties["COLS"] ?>" rows="<?= $properties["ROWS"] ?>" id="<?= $properties["CODE"] ?>" <?php if ($properties["REQUIRED"] == "Y") { ?>aria-required="true" required="" <?php } ?> class="field-style"><?= $properties["VALUE"][0] ?></textarea><?php
                } else {
                    if ($isAddressReadonly && $properties["CODE"] == 'ADDRESS') {
                        $rsUser = CUser::GetByID($USER->GetID());
                        $arUser = $rsUser->Fetch(); ?><input type="text" data-dbg="1" placeholder=" " size="<?= $properties["SIZE"] ?>" name="ORDER_PROP_<?= $properties["ID"] ?>" value="<?= $arUser['PERSONAL_STREET']; ?>" readonly="readonly" id="<?= $properties["CODE"] ?>" <?php if ($properties["REQUIRED"] == "Y") : ?>aria-required="true" required="" <?php endif ?> class="field-style"><?php
                    } elseif (in_array($properties["CODE"], ['BIRTHDAY_DAY', 'BIRTHDAY_YEAR'])) {
                        /* if($USER->IsAdmin() && in_array($properties["CODE"], ['BIRTHDAY_DAY']))
            {
            ?>
              <select id="selectProduct" name="ORDER_PROP_<?= $properties["ID"] ?>" required="" aria-required="true">
                <option value="" selected="" disabled="">День рождения</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
              </select>
<!--              <label class="placeholder" for="<?= $properties["CODE"] ?>"><?=Loc::getMessage("SALE_ORDER_".$properties["CODE"]."_TITLE")?></label>-->
            <?
            }
            else
            { */
?>
                        <input type="text" data-dbg="birthday" placeholder=" " size="<?= $properties["SIZE"] ?>" name="ORDER_PROP_<?= $properties["ID"] ?>" value="<?= $properties["VALUE"][0] ?>" id="<?= $properties["CODE"] ?>" <?php if ($properties["REQUIRED"] == "Y") : ?>aria-required="true" required="" <?php endif ?> class="field-style">
                        <label class="placeholder" for="<?= $properties["CODE"] ?>"><?= Loc::getMessage("SALE_ORDER_" . $properties["CODE"] . "_TITLE") ?></label>
                    <?php
            // }
                    } else {
                        ?>
                        <input type="text" data-dbg="2" placeholder=" " size="<?= $properties["SIZE"] ?>" name="ORDER_PROP_<?= $properties["ID"] ?>" value="<?= $properties["VALUE"][0] ?>" id="<?= $properties["CODE"] ?>" <?php if ($properties["REQUIRED"] == "Y") : ?>aria-required="true" required="" <?php endif ?> class="field-style" data-auth="<?= $USER->IsAuthorized(); ?>">
                        <label class="placeholder" for="<?= $properties["CODE"] ?>"><?= Loc::getMessage("SALE_ORDER_" . $properties["CODE"] . "_TITLE") ?></label>
                        <?php
                          if ($properties["CODE"] == 'PHONE') {
                          ?>
                            <a href="javascript:void(0)" class="black button-confirm-phone" id="js-button-confirm-phone" style="<?= ($USER->IsAuthorized() && $arResult['PHONE_CONFIRMED']) ? 'display: none;' : 'display: block;'; ?>"><?= $lang["CONFIRM"] ?></a>
        <?php
                }
                    }
                }
} ?>
    </div>
    <?php
    if ($properties["CODE"] == 'BIRTHDAY_YEAR') {
        ?><div style="clear:both"></div><?php
    }
}
                                    ?>

<div class="field">
    <input type="text" name="ORDER_DESCRIPTION" id="ORDER_DESCRIPTION" value="<?= $arResult["JS_DATA"]["ORDER_DESCRIPTION"] ?>" placeholder=" ">
    <label class="placeholder" for="ORDER_DESCRIPTION"><?= Loc::getMessage("SALE_ORDER_PROPERTY_ORDER_COMMENT") ?></label>
</div>