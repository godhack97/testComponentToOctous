<?
global $USER;
$arGroups = $USER->GetUserGroupArray();
$isPartners = in_array(GROUP_USER_PARTNERS, $arGroups);
$arNewPay = [];
$isPaymentCash = 'N';


foreach($arResult["JS_DATA"]["ORDER_PROP"]["properties"] as $arProp)
{
  if($arProp['CODE'] == 'LOCATION')
  {
    $arLocs = CSaleLocation::GetByID($arProp['VALUE'][0], LANGUAGE_ID);
    if(in_array($arLocs['CITY_ID'], Array(134, 135, 177490)))
      $isPaymentCash = 'Y';
  }
}


foreach($arResult["JS_DATA"]["DELIVERY"] as $arDelivery)
{
  if($arDelivery['CHECKED'] == 'Y' && $arDelivery['ID'] == 47) // PickPoint
  {
    foreach ($arResult["JS_DATA"]["PAY_SYSTEM"] as $index => $arPayment)
    {
      if($arPayment['ID'] == 10) // Оплата курьеру при получении
      {
        unset($arResult["JS_DATA"]["PAY_SYSTEM"][ $index ]);
        break;
      }
    }
  }
}


foreach ($arResult["JS_DATA"]["PAY_SYSTEM"] as $val)
{
  if($isPaymentCash == 'Y' && $val['ID'] == 10) // Оплата курьеру при получении
  {
    $arNewPay[] = $val;
  }
  else if($val['ID'] != 10)
  {
    
    if($isPartners)
    {
      if($val["CODE"] == 'partners')
        $arNewPay[] = $val;
    }
    else
    {
      if($val["CODE"] != 'partners')
        $arNewPay[] = $val;
    }
    
  }
}


if(count($arNewPay) == 1 && empty($arNewPay[0]['CHECKED']))
  $arNewPay[0]['CHECKED'] = 'Y';


$arResult["JS_DATA"]["PAY_SYSTEM"] = $arNewPay;
?>

<h1>Способ оплаты</h1>
<br>
<div class="text intro-text-wrap"> 
  Выберите способ оплаты заказа
</div>
<div class="items<?if(count($arResult["JS_DATA"]["PAY_SYSTEM"]) <= 1):?> count-1<?endif?>">
  <?foreach ($arResult["JS_DATA"]["PAY_SYSTEM"] as $pay):?>
    <div class="payment payment-<?=$pay["PAY_SYSTEM_ID"]?>">
      <div class="field-radio">
        <label class="label-flx"> 
          <input id="pay-ID_PAY_SYSTEM_ID_<?=$pay["PAY_SYSTEM_ID"]?>" type="radio" name="PAY_SYSTEM_ID"
          <?if($pay["CHECKED"] == "Y"):?>checked="checked"<?endif?> value="<?=$pay["ID"]?>" aria-required="true" required="required">
          <div class="label"><?=$pay["PSA_NAME"]?></div>
          <? /* div class="description"><?=$pay["DESCRIPTION"]?></div */ ?>
        </label>
      </div>
    </div>
  <?endforeach?>
</div>