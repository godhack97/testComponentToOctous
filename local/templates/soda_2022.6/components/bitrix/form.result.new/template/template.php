<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<div class="form-center">

  <?if($arResult["isFormErrors"] == "Y"):?>
    <?=$arResult["FORM_ERRORS_TEXT"]?>
  <?endif?>

  <?if(htmlspecialcharsbx($_GET['formresult']) == 'addok'):?>
    <? // $arResult["FORM_NOTE"]?>
    <script>
      $(function(){
        popupOpenMessage('<?=GetMessage("FORM_SUCCESS")?>');
      });
    </script>
  <?endif?>

  <? $arResult["FORM_HEADER"] = str_replace('<form', '<form class="form"', $arResult["FORM_HEADER"]); ?>
  <?=$arResult["FORM_HEADER"]?>

  <?
  if ($arResult["isFormDescription"] == "Y" || $arResult["isFormTitle"] == "Y" || $arResult["isFormImage"] == "Y")
  {
    if ($arResult["isFormTitle"])
    {
      ?><h1><?=GetMessage("FORM_NAME")?></h1><?
    }

    if ($arResult["isFormImage"] == "Y")
    {
      ?>
      <a href="<?=$arResult["FORM_IMAGE"]["URL"]?>" target="_blank" alt="<?=GetMessage("FORM_ENLARGE")?>"><img src="<?=$arResult["FORM_IMAGE"]["URL"]?>" <?if($arResult["FORM_IMAGE"]["WIDTH"] > 300):?>width="300"<?elseif($arResult["FORM_IMAGE"]["HEIGHT"] > 200):?>height="200"<?else:?><?=$arResult["FORM_IMAGE"]["ATTR"]?><?endif;?> hspace="3" vscape="3" border="0" /></a>
      <?//=$arResult["FORM_IMAGE"]["HTML_CODE"]?>
      <?
    }
    ?>

    <?if($arResult["FORM_DESCRIPTION"]):?>
      <p><?=$arResult["FORM_DESCRIPTION"]?></p>
    <?endif?>

    <?
  }
  ?>

  <br /><br />

  <?
  foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion)
  {
    if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden')
    {
      echo $arQuestion["HTML_CODE"];
    }
    else
    {
      if($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'dropdown')
      {
        $arQuestion["HTML_CODE"] = str_replace('value="32"', 'value=""', $arQuestion["HTML_CODE"]);
        $arQuestion["HTML_CODE"] = str_replace('value="33"', 'value=""', $arQuestion["HTML_CODE"]);
        
        foreach($arQuestion['STRUCTURE'] as $option)
        {
          if(SITE_ID == 's2')
          {
            $message = current(explode('#', $option['MESSAGE']));
            $value = end(explode('#', $option['MESSAGE']));
            $arQuestion["HTML_CODE"] = str_replace('>'. $message .'#', '>', $arQuestion["HTML_CODE"]);
            $arQuestion["HTML_CODE"] = str_replace('value="'. $option['ID'] .'"', 'value="'. $value .'"', $arQuestion["HTML_CODE"]);
          }
          else
          {
            $message = end(explode('#', $option['MESSAGE']));
            $value = current(explode('#', $option['MESSAGE']));
            $arQuestion["HTML_CODE"] = str_replace('#'. $message .'<', '<', $arQuestion["HTML_CODE"]);
            $arQuestion["HTML_CODE"] = str_replace('value="'. $option['ID'] .'"', 'value="'. $value .'"', $arQuestion["HTML_CODE"]);
          }
        }
        ?>
          <div class="field">
            <? $arQuestion["HTML_CODE"] = str_replace('<select class="inputselect"', '<select', $arQuestion["HTML_CODE"]); ?>
            <?=$arQuestion["HTML_CODE"]?>
            <!-- label class="placeholder" for="<?=$fieldName?>"><?=$arQuestion["CAPTION"]?></label -->
          </div>
        <? 
      }
      else
      {
        $fieldName = 'form_'. $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] .'_'. $arQuestion['STRUCTURE'][0]['ID'];
        ?>
          <div class="field">
            <? 
              $arQuestion["HTML_CODE"] = str_replace('<input', '<input placeholder=" " id="'. $fieldName .'"'. (($arQuestion["REQUIRED"] == "Y") ? ' aria-required="true" required=""' : ''), $arQuestion["HTML_CODE"]);
              if($arQuestion['STRUCTURE'][0]['ID'] == 29) 
                $arQuestion["HTML_CODE"] = str_replace('class="inputtext"', 'class="inputtext js-mask-phone"', $arQuestion["HTML_CODE"]);
            ?>
            <?=$arQuestion["HTML_CODE"]?>
            <label class="placeholder" for="<?=$fieldName?>"><?=GetMessage('FORM_FIELD_'. $arQuestion['STRUCTURE'][0]['ID'])?></label>
          </div>
        <? 
      }
    }
  }
  ?>

  <?
  if($arResult["isUseCaptcha"] == "Y")
  {
    ?>
    <?=GetMessage("FORM_CAPTCHA_TABLE_TITLE")?>
    <input type="hidden" name="captcha_sid" value="<?=htmlspecialcharsbx($arResult["CAPTCHACode"]);?>" />
    <img src="/bitrix/tools/captcha.php?captcha_sid=<?=htmlspecialcharsbx($arResult["CAPTCHACode"]);?>" width="180" height="40" />
    <?=GetMessage("FORM_CAPTCHA_FIELD_TITLE")?>
    <?=$arResult["REQUIRED_SIGN"];?>
    <input type="text" name="captcha_word" size="30" maxlength="50" value="" class="inputtext" />
    <?
  } // isUseCaptcha
  ?>

  <div class="field">
    <input class="ibutton"<?=(intval($arResult["F_RIGHT"]) < 10 ? " disabled=\"disabled\"" : "");?> type="submit" name="web_form_submit" value="<?=GetMessage("FORM_SEND")?>" />
  </div>

  <?=$arResult["FORM_FOOTER"]?>


</div>