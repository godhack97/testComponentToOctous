<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if($arParams['CURRENT_USER'] != $arResult['USER_ID'])
{
  ?><p><?=Loc::getMessage('SALE_PERSONAL_ORDER_NUM')?> <?=$arParams['ID']?> <?=Loc::getMessage('SALE_PERSONAL_BELONG_ANOTHER')?></p><?
  return;
}

if(empty($arParams['RETURN_ITEMS']))
{
  ?><p><?=Loc::getMessage('SALE_PERSONAL_NOT_FOUND')?></p><?
  return;
}


$arReturnItems = explode('-', $arParams['RETURN_ITEMS']);

foreach($arResult['SHIPMENT'] as $arItem)
  $arShipment = $arItem;

foreach($arResult['PAYMENT'] as $arItem)
  $arPayment = $arItem;

$isPaySite = false;
//if(in_array($arPayment['PAY_SYSTEM_ID'], Array(9, 14))) // Оплата на сайте (не курьеру)
//  $isPaySite = true;


$arUser = [];
foreach($arResult['ORDER_PROPS'] as $arProp)
{
  if($arProp['CODE'] == 'CONTACT_PERSON')
    $arUser['NAME'] = $arProp['VALUE'];

  if($arProp['CODE'] == 'PHONE')
    $arUser['PHONE'] = $arProp['VALUE'];

  if($arProp['CODE'] == 'LOCATION')
    $arUser['CITY'] = $arProp['VALUE'];
}

$isBigTown = false;
if(stristr($arUser['CITY'], 'Москва') || stristr($arUser['CITY'], 'Санкт-Петербург'))
  $isBigTown = true;

$dateNow = ConvertTimeStamp(time(), "SHORT");

?>
<div class="section-auth mode-single">
  <form name="return">
    <input type="hidden" name="mode" value="return">
    <input type="hidden" name="ORDER_ID" value="<?=$arParams['ID']?>">
    <input type="hidden" name="price" value="<?=$arResult['PRICE_FORMATED']?>">
    <input type="hidden" name="date" value="<?=strtolower(FormatDate('«j» F Y г.', strtotime($arResult['DATE_INSERT_FORMATED'])))?>">
    <input type="hidden" name="date_now" value="<?=strtolower(FormatDate('«j» F Y г.', strtotime($dateNow)))?>">
    <div class="form">

      <div class="right-intro">
        <div class="text">
          <?=Loc::getMessage('SALE_PERSONAL_IP')?>
        </div>
        <div class="field">
          <input type="text" placeholder=" " size="40" name="name" value="<?=$arUser['NAME']?>" aria-required="true" required="" class="field-style" id="field_name">
          <label class="placeholder" for="field_name"><?=Loc::getMessage('SALE_PERSONAL_FULL_NAME')?></label>
        </div>
        <div class="field">
          <input type="text" placeholder=" " size="40" name="USER_PHONE" value="<?=$arUser['PHONE']?>" aria-required="true" required="" class="field-style" id="field_phone">
          <label class="placeholder" for="field_phone"><?=Loc::getMessage('SALE_PERSONAL_PHONE')?></label>
        </div>
        
        <div class="text text-caption"><?=Loc::getMessage('PASSPORT_DATA')?></div>
        <div class="field-cols field-cols-2">
          <div class="field">
            <input type="text" placeholder=" " size="40" name="PASSPORT_SERIES" value="" aria-required="true" required="" class="field-style" id="field_passport_series">
            <label class="placeholder" for="field_passport_series"><?=Loc::getMessage('PASSPORT_SERIES')?></label>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="PASSPORT_NUMBER" value="" aria-required="true" required="" class="field-style" id="field_passport_number">
            <label class="placeholder" for="field_passport_number"><?=Loc::getMessage('PASSPORT_NUMBER')?></label>
          </div>
        </div>
        <div class="field">
          <input type="text" placeholder=" " size="40" name="PASSPORT_ISSUED" value="" aria-required="true" required="" class="field-style" id="field_passport_issued">
          <label class="placeholder" for="field_passport_issued"><?=Loc::getMessage('PASSPORT_ISSUED')?></label>
        </div>
        <div class="field">
          <input type="text" placeholder=" " size="40" name="PASSPORT_ADDRESS" value="" aria-required="true" required="" class="field-style" id="field_passport_address">
          <label class="placeholder" for="field_passport_address"><?=Loc::getMessage('PASSPORT_ADDRESS')?></label>
        </div>
      </div>
      <div class="clear"></div>

      <h3><?=Loc::getMessage('SALE_PERSONAL_APPLICATION_RETURN')?></h3>

      <p>
        <?=strtolower(FormatDate('«j» F Y г.', strtotime($arResult['DATE_INSERT_FORMATED'])))?>&nbsp;<?=Loc::getMessage('SALE_PERSONAL_APPLICATION_ONE')?><?=$arResult['ID']?>&nbsp;<?=Loc::getMessage('SALE_PERSONAL_APPLICATION_TWO')?> <?=$arResult['PRICE_FORMATED']?> <?=Loc::getMessage('SALE_PERSONAL_APPLICATION_THREE')?>
      </p>

      <table class="basket-table">
        <tbody>
          <tr class="head">
            <th class="image"><?=Loc::getMessage('SALE_PERSONAL_ARTICUL')?></th>
            <th class="desc"><?=Loc::getMessage('SALE_PERSONAL_NAME')?></th>
            <th class="size"><?=Loc::getMessage('SALE_PERSONAL_SIZE')?></th>
            <th class="count"><?=Loc::getMessage('SALE_PERSONAL_QUANTITY')?></th>
            <th class="summa"><?=Loc::getMessage('SALE_PERSONAL_PRICE')?></th>
          </tr>
        </tbody>
        <tbody id="basket-item-table">
          <?

          $aItems = Array();

          foreach ($arResult['BASKET'] as $arItem):?>
            <?if(in_array($arItem['ID'], $arReturnItems)):?>
              <?
                $size = '';
                foreach($arItem['PROPS'] as $item)
                {
                  if($item['CODE'] == 'SIZES')
                    $size = $item['VALUE'];
                }
              ?>
              <input type="hidden" name="product[]" value="<?=$arItem['PRODUCT_ID'] .'##'. $arItem['PROPERTY_CML2_ARTICLE_VALUE'] .'##'. $arItem['NAME'] .'##'. $arItem['QUANTITY'] .'##'. $arItem['PRICE']?>">
              <tr class="basket-items-list-item-container" data-id="<?=$arItem['ID']?>">
                <td class="image">
                  <a class="hover" target="_blank" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['PROPERTY_CML2_ARTICLE_VALUE']?></a>
                </td>
                <td class="desc">
                  <div class="name"><a class="hover" target="_blank" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a></div>
                  <div class="mobile-size"><?=$size?></div>
                </td>
                <td class="size"><?=$size?></td>
                <td class="count"><?=$arItem['QUANTITY']?></td>
                <td class="summa"><?=$arItem['PRICE_FORMATED']?></td>
              </tr>
              <?
              $aTmp = Array(
                $arItem['PROPERTY_CML2_ARTICLE_VALUE'],
                $arItem['NAME'],
                $size,
                $arItem['QUANTITY'],
                $arItem['PRICE_FORMATED']
              );
              $aItems[] = implode(";", $aTmp);
              ?>
            <?endif?>
          <?endforeach?>
        </tbody>
      </table>

      <input type="hidden" name="items" value="<?=implode("+", $aItems)?>">

      <?=Loc::getMessage('SALE_PERSONAL_SPECIFY_RETURNED')?>
      <div class="reasons">
        <div class="field-radio">
          <label> 
            <input value="<?=Loc::getMessage('SALE_PERSONAL_NOT_SIZE')?>" type="radio" name="REASONS" aria-required="true" required="">
            <div class="label"><?=Loc::getMessage('SALE_PERSONAL_NOT_SIZE')?></div>
          </label>
        </div>
        <div class="field-radio">
          <label>
            <input value="<?=Loc::getMessage('SALE_PERSONAL_NOT_PRODUCT')?>" type="radio" name="REASONS" aria-required="true" required="">
            <div class="label"><?=Loc::getMessage('SALE_PERSONAL_NOT_PRODUCT')?></div>
          </label>
        </div>
        <div class="field-radio">
          <label> 
            <input value="<?=Loc::getMessage('SALE_PERSONAL_NOT_COME_DATE')?>" type="radio" name="REASONS" aria-required="true" required="">
            <div class="label"><?=Loc::getMessage('SALE_PERSONAL_NOT_COME_DATE')?></div>
          </label>
        </div>
        <div class="field-radio">
          <label> 
            <input value="<?=Loc::getMessage('SALE_PERSONAL_NOT_QUALITY')?>" type="radio" name="REASONS" aria-required="true" required="">
            <div class="label"><?=Loc::getMessage('SALE_PERSONAL_NOT_QUALITY')?></div>
          </label>
        </div>
        <div class="field-radio">
          <label> 
            <input value="<?=Loc::getMessage('SALE_PERSONAL_REFUSE')?>" type="radio" name="REASONS" aria-required="true" required="">
            <div class="label"><?=Loc::getMessage('SALE_PERSONAL_REFUSE')?></div>
          </label>
        </div>
        <div class="field-radio">
          <label>
            <input value="<?=Loc::getMessage('SALE_PERSONAL_OTHER')?>" type="radio" name="REASONS" aria-required="true" required="">
            <div class="label"><?=Loc::getMessage('SALE_PERSONAL_OTHER')?></div>
          </label>
        </div>
      </div>

      <?if($isPaySite):?>
      
        <h3><?=Loc::getMessage('SALE_PERSONAL_INFO_DEPARTURE')?></h3>

        <div class="requisites">
          <div class="field">
            <input type="text" placeholder=" " size="40" name="TC_NAME" value="" class="field-style" aria-required="true" required="" id="req_TC_NAME">
            <label class="placeholder" for="req_TC_NAME"><?=Loc::getMessage('SALE_PERSONAL_NAME_TRANSPORT_COMPANY')?></label>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="TC_TRACK" value="" class="field-style" aria-required="true" required="" id="req_TC_TRACK">
            <label class="placeholder" for="req_TC_TRACK"><?=Loc::getMessage('SALE_PERSONAL_TRACK_NUM')?></label>
          </div>
          <div class="field">
            <input type="text" placeholder="<?=Loc::getMessage('SALE_PERSONAL_DEPARTURE_DATE')?>" size="40" name="TC_DATE" value="" class="field-style" aria-required="true" required="" id="req_TC_DATE">
            <label class="placeholder" for="req_TC_DATE"><?=Loc::getMessage('SALE_PERSONAL_DEPARTURE_DATE')?></label>
          </div>
        </div>

      <?else:?>
        
        <h3><?=Loc::getMessage('SALE_PERSONAL_DETAILS_REFUND')?></h3>

        <div class="requisites">
          <div class="field">
            <input type="text" placeholder=" " size="40" name="ACCOUNT" value="" aria-required="true" required="" class="field-style" id="field_ACCOUNT">
            <label class="placeholder" for="field_ACCOUNT"><?=Loc::getMessage('SALE_PERSONAL_PERSONAL_ACCOUNT_NUM')?></label>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="BIC" value="" aria-required="true" required="" class="field-style" id="field_BIC">
            <label class="placeholder" for="field_BIC"><?=Loc::getMessage('SALE_PERSONAL_BIK')?></label>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="BANK" value="" aria-required="true" required="" class="field-style" id="field_BANK">
            <label class="placeholder" for="field_BANK"><?=Loc::getMessage('SALE_PERSONAL_BANK_NAME')?></label>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="CARD" value="" aria-required="true" required="" class="field-style" id="field_CARD">
            <label class="placeholder" for="field_CARD"><?=Loc::getMessage('SALE_PERSONAL_BANK_CARD')?></label>
          </div>
        </div>

        <?=Loc::getMessage('SALE_PERSONAL_RETURN_METHOD')?>
        <div class="reasons mode-width-full">
          <div class="field-radio">
            <label>
              <input value="<?=Loc::getMessage('SALE_PERSONAL_COURIER')?>" type="radio" data-type="curier" name="RETURN_TYPE" aria-required="true" required="">
              <div class="label"><?=Loc::getMessage('SALE_PERSONAL_COURIER')?></div>
            </label>
          </div>
          <?if($isBigTown):?>
            <div class="field-radio">
              <label>
                <input value="<?=Loc::getMessage('SALE_PERSONAL_STORE')?>" type="radio" data-type="shop" name="RETURN_TYPE" aria-required="true" required="">
                <div class="label"><?=Loc::getMessage('SALE_PERSONAL_STORE')?></div>
              </label>
            </div>
          <?endif?>
          <div class="field-radio">
            <label>
              <input value="<?=Loc::getMessage('SALE_PERSONAL_TRANSPORT_COMPANY')?>" type="radio" data-type="tc" name="RETURN_TYPE" aria-required="true" required="">
              <div class="label"><?=Loc::getMessage('SALE_PERSONAL_TRANSPORT_COMPANY')?></div>
            </label>
          </div>
        </div>

        <div class="requisites display-none fields-return-tc">
          <div class="field">
            <input type="text" placeholder=" " size="40" name="TC_NAME" value="" class="field-style" id="TC_NAME">
            <label class="placeholder" for="TC_NAME"><?=Loc::getMessage('SALE_PERSONAL_NAME_TRANSPORT_COMPANY')?></label>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="TC_TRACK" value="" class="field-style" id="TC_TRACK">
            <label class="placeholder" for="TC_TRACK"><?=Loc::getMessage('SALE_PERSONAL_TRACK_NUM')?></label>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="TC_DATE" value="" class="field-style" id="TC_DATE">
            <label class="placeholder" for="TC_DATE"><?=Loc::getMessage('SALE_PERSONAL_DEPARTURE_DATE')?></label>
          </div>
        </div>
        
      <?endif?>
      <?=Loc::getMessage('SALE_PERSONAL_INFO')?>
      <? //dbgz($arResult); ?>

    </div>
    <br>
    <input class="ibutton" type="submit" value="<?=Loc::getMessage('SALE_PERSONAL_SEND')?>" name="RETURN_SEND">
  </form>
</div>