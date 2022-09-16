<?php
  use Bitrix\Main\Localization\Loc;
  use Bitrix\Main\HttpApplication;
  use Bitrix\Main\Loader;
  use Bitrix\Main\Config\Option;
  use Bitrix\Main\Data\Cache;
  
  //\CJSCore::RegisterExt('jquery_core', array(
  //	'js' => '//code.jquery.com/jquery-1.12.4.min.js'
  //));
  //\CJSCore::Init(array('jquery_core'));
  
  Loc::loadMessages(__FILE__);
  
  $request = HttpApplication::getInstance()->getContext()->getRequest();
  
  $module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);
  
  Loader::includeModule('sale');
  Loader::includeModule('iblock');
  Loader::includeModule($module_id);
  
  $aTabs = [
    [
      "DIV" 	  => "general",
      "TAB" 	  => Loc::getMessage("SODAMODA_GENERAL_TAB_NAME"),
      "TITLE"   => Loc::getMessage("SODAMODA_GENERAL_TAB_NAME"),
      "OPTIONS" => [
        Loc::getMessage("SODAMODA_GENERAL_TAB_TITLE"),
        [
          "bonus_discount_percent",
          Loc::getMessage("SODAMODA_BONUS_DISCOUNT_PERCENT"),
          "70",
          ['text', 10]
        ],
        [
          "store_lists",
          Loc::getMessage("SODAMODA_STORE_LIST"),
          "7, 8, 11, 12",
          ['text', 10]
        ],
        [
          "group_manage_lists",
          Loc::getMessage("SODAMODA_GROUP_MANAGE_LIST"),
          "1,8,9,15,16",
          ['text', 10]
        ],
        [
          "group_partner_lists",
          Loc::getMessage("SODAMODA_GROUP_PARTNER_LIST"),
          "17",
          ['text', 10]
        ],
        [
          "product_is_new_days",
          Loc::getMessage("SODAMODA_PRODUCT_IS_NEW_DAYS"),
          "90",
          ['text', 10]
        ],
        [
          //"tech_message",
          //Loc::getMessage("SODAMODA_GENERAL_TAB_TECH_MESSAGE"),
          //"Test message",
          //['textarea', 10, 50]
        ],
        [
          //"tech_show",
          //Loc::getMessage("SPCORE_GENERAL_TAB_TECH_SHOW"),
          //"",
          //["checkbox"]
        ],
      ]
    ],
  ];
  
  if($request->isPost() && check_bitrix_sessid()){
    foreach($aTabs as $aTab){
      foreach($aTab["OPTIONS"] as $arOption){
        if(!is_array($arOption)){
          continue;
        }
        
        if($arOption["note"]){
          continue;
        }
        
        if($request["apply"]){
          $optionValue = $request->getPost($arOption[0]);
          if($arOption[0] == "switch_on"){
            if($optionValue == ""){
              $optionValue = "N";
            }
          }
          
          if(isset($arOption[0])){
            Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(',', $optionValue) : $optionValue);
          }
        }
        elseif($request["default"]){
          Option::set($module_id, $arOption[0], $arOption[2]);
        }
      }
    }
    LocalRedirect($APPLICATION->GetCurPage()."?mid=".$module_id."&lang=".LANG);
  }
  
  $tabControl = new CAdminTabControl("tabControl", $aTabs);
  $tabControl->Begin();
?>
  <form id="option_form" action="<?=($APPLICATION->GetCurPage()); ?>?mid=<?=($module_id); ?>&lang=<?=(LANG); ?>" method="post">
    <?php
      foreach($aTabs as $aTab){
        if($aTab["OPTIONS"]){
          $tabControl->BeginNextTab();
          __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
        }
      }
      $tabControl->Buttons();
    ?>
    <input type="submit" name="apply" value="<?=(Loc::GetMessage("SODAMODA_OPTIONS_INPUT_APPLY")); ?>" class="adm-btn-save" />
    <input type="submit" name="default" value="<?=(Loc::GetMessage("SODAMODA_OPTIONS_INPUT_DEFAULT")); ?>" />
    <?=(bitrix_sessid_post()); ?>
    <script>
      jQuery(document).ready(function () {
        jQuery('select').on('change', function(){
          jQuery('#option_form').find('.adm-btn-save').trigger('click');
        });
      });
    </script>
  </form>
<?php
  $tabControl->End();