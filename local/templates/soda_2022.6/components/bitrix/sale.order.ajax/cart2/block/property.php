<? 
global $APPLICATION;

$isAddressRequired    = false;
$isAddressReadonly    = false;
$isAddressDisplayNone = false;

foreach($arResult["JS_DATA"]['DELIVERY'] as $delivery)
{
  //  if($delivery['ID'] == 2 && $delivery['CHECKED'] == 'Y') // ТК СДЭК
  //    $isAddressRequired = true;
  
  //  if($delivery['ID'] == 72 && $delivery['CHECKED'] == 'Y') // ТК СДЭК Pickup
  //    $isAddressReadonly = true;
  
  if($delivery['ID'] == 47 && $delivery['CHECKED'] == 'Y') // PickPoint
    $isAddressDisplayNone = true;
}

//dbgz($arResult["JS_DATA"]['DELIVERY']);


foreach($arResult["JS_DATA"]["ORDER_PROP"]["properties"] as $properties)
{
  $isDisplayNone = false;
  if($arResult["JS_DATA"]['DELIVERY'][47]['CHECKED'] == 'Y' && $properties["CODE"] == 'ADDRESS') 
    $isDisplayNone = true;
  //  if($properties['CODE'] == 'ADDRESS' && $isAddressRequired)
  //    $properties['REQUIRED'] = 'Y';
  
  
  if($properties["CODE"] == 'CITY')
  {
    ?><input type="hidden" name="ORDER_PROP_<?= $properties["ID"] ?>" value="<?= $properties["VALUE"][0] ?>" id="<?= $properties["CODE"] ?>"><?
    continue;
  }
  ?>
    
  <div class="field field-code-<?= strtolower($properties['CODE']) ?>" <?if(strtolower($properties['CODE'])=="email" && trim($_REQUEST["set-email"])=="Y"){?>style="display: block;"<?}?>>
    <?
    switch($properties["TYPE"])
    {
      case "LOCATION":
                
        if(empty($properties["VALUE"][0])) // Принудительно ставим город Москва по-умолчанию
          $properties["VALUE"][0] = '0000073738';
        
        ?>
        <?$APPLICATION->IncludeComponent("bitrix:sale.location.selector.search", "", Array(
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
          "FILTER_SITE_ID" => "s1",
          "INITIALIZE_BY_GLOBAL_EVENT" => "",
          "SUPPRESS_ERRORS" => "N",
          "CLASS_ID" => "" . $properties["CODE"],
        ));
        ?>     
        <?
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
        <? //echo CalendarDate("ORDER_PROP_".$properties["ID"], $properties["VALUE"][0], "ORDER_FORM", "15", "class=\"field-style\"") ?>
        <? //=Calendar("ORDER_PROP_".$properties["ID"], "ORDER_FORM") ?>
        <?
        $APPLICATION->IncludeComponent('bitrix:main.calendar', '', Array(
            'SHOW_INPUT' => 'Y',
            'FORM_NAME' => 'ORDER_FORM',
            'INPUT_NAME' => "ORDER_PROP_" . $properties["ID"],
            'INPUT_VALUE' => '', //$properties["VALUE"][0],
            'SHOW_TIME' => 'N',
            'HIDE_TIMEBAR' => 'N',
            'INPUT_ADDITIONAL_ATTR' => 'class="field-style js-calendar-field" placeholder=" "'
        ));
        
        break;
      
      case "STRING":
      default:
        
        if($properties["MULTILINE"] == "Y")
        {
          ?><textarea name="ORDER_PROP_<?= $properties["ID"] ?>"
                      placeholder="<?= $properties["DESCRIPTION"] ? $properties["DESCRIPTION"] : $properties["NAME"] ?>"
                      cols="<?= $properties["COLS"] ?>"
                      rows="<?= $properties["ROWS"] ?>"
                      id="<?= $properties["CODE"] ?>"
                      <? if($properties["REQUIRED"] == "Y")
                      { ?>aria-required="true" required=""<? } ?>
                      class="field-style"><?= $properties["VALUE"][0] ?></textarea><?
        }
        else
        {
          if($isAddressReadonly && $properties["CODE"] == 'ADDRESS')
          {
            ?><input  type="text" data-dbg="1"
                      placeholder=" "
                      size="<?= $properties["SIZE"] ?>"
                      name="ORDER_PROP_<?= $properties["ID"] ?>"
                      value=""
                      readonly="readonly"
                      id="<?= $properties["CODE"] ?>"
                      <?if($properties["REQUIRED"] == "Y"):?>aria-required="true" required=""<?endif?>
                      class="field-style"><?
          }
          else
          {
            ?>
            <input  type="text" data-dbg="2"
                    placeholder=" "
                    size="<?= $properties["SIZE"] ?>"
                    name="ORDER_PROP_<?= $properties["ID"] ?>"
                    value="<?= $properties["VALUE"][0] ?>"
                    id="<?= $properties["CODE"] ?>"
                    <?if($properties["REQUIRED"] == "Y"):?>aria-required="true" required=""<?endif?>
                    class="field-style">
            <label class="placeholder" for="<?= $properties["CODE"] ?>"><?= $properties["DESCRIPTION"] ? $properties["DESCRIPTION"] : $properties["NAME"] ?></label>
            <?            
          }
        }
    }
    ?>
  </div>
  <?
}
?>

<div class="field">
  <input type="text" name="ORDER_DESCRIPTION" id="ORDER_DESCRIPTION" value="<?= $arResult["JS_DATA"]["ORDER_DESCRIPTION"] ?>" placeholder=" ">
  <label class="placeholder" for="ORDER_DESCRIPTION">Комментарий к заказу</label>
</div>