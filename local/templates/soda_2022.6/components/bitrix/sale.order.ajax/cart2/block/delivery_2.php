<?global $USER;
$arDeliveries = $arResult["JS_DATA"]["DELIVERY"];
//dbgz($arDeliveries);

$arFreeID = [66, 47, 60, 72, 3, 67];
$arDeliveriesLeft = [];
$arDeliveriesRight = []; 


if(count($arDeliveries) > 1)
{
  foreach ($arDeliveries as $arDelivery)
  {
    if($arDelivery['ID'] == 47)
    {
      $arDelivery['DESCRIPTION'] = str_replace('<a class="btn btn-default"', '<a class="black js-pp-pvz"', $arDelivery['DESCRIPTION']);
      $arDelivery['DESCRIPTION'] = str_replace('Выбрать пункт доставки', 'Выбрать пункт выдачи', $arDelivery['DESCRIPTION']);
    }
    
    if($arDelivery['ID'] == 60)
    {
      $arDelivery['PERIOD_TEXT'] = substr($arDelivery['PERIOD_TEXT'], strpos($arDelivery['PERIOD_TEXT'], '<input'));
      $arDelivery['DESCRIPTION'] .= $arDelivery['PERIOD_TEXT'];
    }
    
    if(in_array($arDelivery['ID'], $arFreeID))
      $arDeliveriesRight[ $arDelivery['ID'] ] = $arDelivery;
    else
      $arDeliveriesLeft[ $arDelivery['ID'] ] = $arDelivery;
  }
}

$isTwoCols = false;
if(!empty($arDeliveriesRight) && !empty($arDeliveriesLeft))
{
  $isTwoCols = true;
  $arDeliveries = $arDeliveriesLeft;
}
?>

<h1>Способы доставки</h1>
<br/>

<div class="text">
  Сроки доставки указаны в рабочих днях
</div>

<div class="items">
  <div class="payment">
    <div class="field-radio">
      <label> 
        <input id="ID_DELIVERY_ID_4" value="4" type="radio" name="DELIVERY_ID" aria-required="true" required="">
        <div class="label">EMS (770 руб.)</div>
      </label>
    </div>
  </div>
  <?foreach ($arDeliveries as $delivery):?>
    <div class="payment<?if($delivery['CHECKED'] == 'Y'):?> active<?endif?>">
      <div class="field-radio">
        <label> 
          <input
            id="ID_DELIVERY_ID_<?=$delivery["ID"]?>"
            value="<?=$delivery["ID"]?>"
            type="radio"
            name="DELIVERY_ID"
            <?if($delivery["CHECKED"]=="Y"):?>checked="checked"<?endif?>
            aria-required="true" required="">
          <div class="label"><?=$delivery["OWN_NAME"]?> (<?=$arResult['DELIVERY_PRICE_CUSTOM'][$delivery["ID"]]?>)</div>
          <?if($delivery["ID"] == 72):?>
            <div class="description" id="pvz-insert"></div>
          <?endif?>
        </label>
      </div>
    </div>
  <?endforeach?>
</div>