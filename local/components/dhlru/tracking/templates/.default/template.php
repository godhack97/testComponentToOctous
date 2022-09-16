<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>

<div class="tracking-container">
    
    <div class="tracking-form">
        <form action="<?=POST_FORM_ACTION_URI?>" method="GET">
            <?=bitrix_sessid_post()?>
            <input type="text" 
            name="BILL_NUMBER" 
            placeholder="<?=GetMessage('DHLRU_COMPONENT_VALUE')?>"
            value="<?=$arResult["BILL_NUMBER"]?>" 
            class="tracking-form__elem tracking-form__elem-inp">

            <input type="submit" 
                name="submit" 
                value="<?=GetMessage('DHLRU_COMPONENT_SEND_BTN')?>" 
                class="tracking-form__elem tracking-form__elem-btn">
        </form>
    </div>

    <?if(!empty($arResult['ERROR'])):?>
        <div class="tracking-error">
            <?=$arResult['ERROR']?>
        </div>
    <?endif?>

    <?if(!$arResult['RESPONCE'] && $arResult['BILL_NUMBER']):?>
        <div class="tracking-error">
            <?=GetMessage('DHLRU_COMPONENT_ERROR_RECIVE_DATA')?>
        </div>
    <?endif?>
    
    <?if(!empty($arResult['RESPONCE']) && $arResult['RESPONCE']['STATUS'] == 'ERROR'):?>
        <div class="tracking-error">
            <?=$arResult['RESPONCE']['ACTION_STATUS']?>
        </div>
    <?endif?>
    <?if (!empty($arResult['RESPONCE']) && $arResult['RESPONCE']['STATUS'] == 'OK') :?>
        <div class="tracking-items">
            <ul class="track-item">
            <?foreach($arResult['RESPONCE']['EVENTS'] as $arItem):?>
                <?php
                $style = ($arItem['SERVICE_EVENT']['EVENT_CODE'] == 'OK') ? 'track-item__event_success': '';
                ?>
                <li class="track-item__elem <?=$style?>">
                    <div class="track-item__date">
                        <span><?=$arItem['DATE']?></span> <?=$arItem['TIME']?>
                    </div>
                    <div class="track-item__event">
                        <?=$arItem['SERVICE_EVENT']['EVENT_CODE']?> <?//=$arItem['SERVICE_EVENT']['DESCRIPTION']?>
                        <?=$arItem['SERVICE_EVENT']['DESCRIPTION_EXT']['NAME']?>
                        <em><?=$arItem['SERVICEAREA']['SERVICE_AREA_CODE']?> <?=$arItem['SERVICEAREA']['DESCRIPTION']?></em>
                    </div>
                </li>
            <?endforeach?>
            </ul>
        </div>

        <div class="tracking-info">

            <div class="tracking-info__elem">
                <span><?=GetMessage('DHLRU_COMPONENT_SHIPPER_NAME')?>:</span> <?=$arResult['RESPONCE']['SHIPPER_NAME']?>
                <br>
                (<?=$arResult['RESPONCE']['SHIPPER']['COUNTRY_CODE']?>)
                <?=$arResult['RESPONCE']['SHIPPER']['CITY']?>, <?=$arResult['RESPONCE']['SHIPPER']['POSTAL_CODE']?>
            </div>
            <div class="tracking-info__elem">
                <span><?=GetMessage('DHLRU_COMPONENT_CONSIGNEE_NAME')?>:</span> <?=$arResult['RESPONCE']['CONSIGNEE_NAME']?>
                <br>
                (<?=$arResult['RESPONCE']['CONSIGNEE']['COUNTRY_CODE']?>)
                <?=$arResult['RESPONCE']['CONSIGNEE']['CITY']?>, <?=$arResult['RESPONCE']['CONSIGNEE']['POSTAL_CODE']?>
            </div>

            <div class="tracking-info__elem">
                <span><?=GetMessage('DHLRU_COMPONENT_SHIPMENT_DESC')?>:</span> <?=$arResult['RESPONCE']['SHIPMENT_DESC']?>
            </div>

            <div class="tracking-info__elem">
                <span><?=GetMessage('DHLRU_COMPONENT_PIECES')?>:</span> <?=$arResult['RESPONCE']['PIECES']?>
            </div>

            <div class="tracking-info__elem">
                <span><?=GetMessage('DHLRU_COMPONENT_WEIGHT')?>:</span> 
                <?=$arResult['RESPONCE']['WEIGHT']?> (<?=$arResult['RESPONCE']['WEIGHT_UNIT']?>)
            </div>                      
        </div>
    <?endif?>
</div>