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
			"TAB" 	  => Loc::getMessage("NETWORK_PAYMENT_TAB_GENERAL_NAME"),
			"TITLE"   => Loc::getMessage("NETWORK_PAYMENT_TAB_GENERAL_TITLE"),
			"OPTIONS" => [
				Loc::getMessage("NETWORK_FIELD_API_KEY"),
				[
					"api_key",
					Loc::getMessage("NETWORK_FIELD_API_KEY"),
					"",
					['text', 40]
				],
				Loc::getMessage("NETWORK_FIELD_UAT_IDENTITY_URL"),
				[
					"uat_identity_url",
					Loc::getMessage("NETWORK_FIELD_UAT_IDENTITY_URL"),
					"https://identity-uat.ngenius-payments.com",
					['text', 40]
				],
				Loc::getMessage("NETWORK_FIELD_LIVE_IDENTITY_URL"),
				[
					"live_identity_url",
					Loc::getMessage("NETWORK_FIELD_LIVE_IDENTITY_URL"),
					"https://identity.ngenius-payments.com",
					['text', 40]
				],
				Loc::getMessage("NETWORK_FIELD_UAT_API_URL"),
				[
					"uat_api_url",
					Loc::getMessage("NETWORK_FIELD_UAT_API_URL"),
					"https://api-gateway-uat.ngenius-payments.com",
					['text', 40]
				],
				Loc::getMessage("NETWORK_FIELD_LIVE_API_URL"),
				[
					"live_api_url",
					Loc::getMessage("NETWORK_FIELD_LIVE_API_URL"),
					"https://api-gateway.ngenius-payments.com",
					['text', 40]
				],
				Loc::getMessage("NETWORK_FIELD_TOKEN_ENDPOINT"),
				[
					"token_endpoint",
					Loc::getMessage("NETWORK_FIELD_TOKEN_ENDPOINT"),
					"/identity/auth/access-token",
					['text', 40]
				],
				Loc::getMessage("NETWORK_FIELD_ORDER_ENDPOINT"),
				[
					"order_endpoint",
					Loc::getMessage("NETWORK_FIELD_ORDER_ENDPOINT"),
					"/transactions/outlets/%s/orders",
					['text', 40]
				],
				Loc::getMessage("NETWORK_FIELD_FETCH_ENDPOINT"),
				[
					"fetch_endpoint",
					Loc::getMessage("NETWORK_FIELD_FETCH_ENDPOINT"),
					"/transactions/outlets/%s/orders/%s",
					['text', 40]
				],
				Loc::getMessage("NETWORK_FIELD_CAPTURE_ENDPOINT"),
				[
					"capture_endpoint",
					Loc::getMessage("NETWORK_FIELD_CAPTURE_ENDPOINT"),
					"/transactions/outlets/%s/orders/%s/payments/%s/captures",
					['text', 40]
				],
				Loc::getMessage("NETWORK_FIELD_REFUND_ENDPOINT"),
				[
					"refund_endpoint",
					Loc::getMessage("NETWORK_FIELD_REFUND_ENDPOINT"),
					"/transactions/outlets/%s/orders/%s/payments/%s/captures/%s/refund",
					['text', 40]
				],
				Loc::getMessage("NETWORK_FIELD_VOID_ENDPOINT"),
				[
					"void_endpoint",
					Loc::getMessage("NETWORK_FIELD_VOID_ENDPOINT"),
					"/transactions/outlets/%s/orders/%s/payments/%s/cancel",
					['text', 40]
				],
				Loc::getMessage("NETWORK_FIELD_ENVIRONMENT"),
				[
					"environment",
					Loc::getMessage("NETWORK_FIELD_ENVIRONMENT"),
					"uat",
					['selectbox', [
						"uat"	=>	"UAT",
						"live"	=>	"Live",
					]]
				],
			]
		],
		[
			"DIV" 	  => "payment",
			"TAB" 	  => Loc::getMessage("NETWORK_PAYMENT_TAB_PAYMENT_NAME"),
			"TITLE"   => Loc::getMessage("NETWORK_PAYMENT_TAB_PAYMENT_TITLE"),
			"OPTIONS" => [
				Loc::getMessage("NETWORK_FIELD_ENABLED"),
				[
					"payment_enabled",
					Loc::getMessage("NETWORK_FIELD_ENABLED"),
					"",
					['checkbox'],
					'Y'
				],
				Loc::getMessage("NETWORK_FIELD_OUTLET_REF"),
				[
					"outlet_ref",
					Loc::getMessage("NETWORK_FIELD_OUTLET_REF"),
					"",
					['text', 40]
				],
				Loc::getMessage("NETWORK_FIELD_TENANT"),
				[
					"tenant",
					Loc::getMessage("NETWORK_FIELD_TENANT"),
					"networkinternational",
					['selectbox', [
						"networkinternational"	=>	"Network International",
					]]
				],
				Loc::getMessage("NETWORK_FIELD_PAYMENT_ACTION"),
				[
					"payment_action",
					Loc::getMessage("NETWORK_FIELD_PAYMENT_ACTION"),
					"sale",
					['selectbox', [
						"sale"	=>	"Sale",
						"auth"	=>	"Authorize",
					]]
				],
				Loc::getMessage("NETWORK_FIELD_ORDER_STATUS"),
				[
					"order_status",
					Loc::getMessage("NETWORK_FIELD_ORDER_STATUS"),
					"ngenius_pending",
					['selectbox', [
						"ngenius_pending"	=>	"n-genius Pending",
					]]
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
		<input type="submit" name="apply" value="<?=(Loc::GetMessage("NETWORK_OPTIONS_INPUT_APPLY")); ?>" class="adm-btn-save" />
		<input type="submit" name="default" value="<?=(Loc::GetMessage("NETWORK_OPTIONS_INPUT_DEFAULT")); ?>" />
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