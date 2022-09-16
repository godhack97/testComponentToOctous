<?php
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

	use \Bitrix\Main\UserTable;
	use \Bitrix\Main\UserPhoneAuthTable;
	
	global $USER;

	if(!function_exists('p')){
        function p($v){
            global $USER;
            if($USER->IsAdmin()){
                ?>
                <pre><?
                print_r($v); ?></pre><?
            }

            return true;
        }
    }
	
	if($USER->IsAdmin()){
		
		$userPhone = UserPhoneAuthTable::getList(["filter" =>	["=USER_ID" => 6980,]])->fetch();
			p($userPhone);
		$dbUser = UserTable::getList([
		  'select' => [
			'ID',
			'LOGIN',
			'EMAIL',
			'PERSONAL_PHONE',
		  ],
		  'filter' => [
			"LOGIC" => "OR",
			'!=PERSONAL_PHONE' => false,
		  ],
		]); 
		
		$not_personal_phone = [];
		$changedPhone = 0;
		while ($arRes = $dbUser->fetch()) 
		{
			$userPhone = UserPhoneAuthTable::getList([
				"filter" =>	[
					"=USER_ID" => $arRes['ID'],
				]
			])->fetch();
			if(!isset($userPhone['PHONE_NUMBER'])){
				$rs = UserPhoneAuthTable::add([
					"USER_ID" => $arRes['ID'],
					"PHONE_NUMBER" => $arRes["PERSONAL_PHONE"],
				]);
				
				if('89117675392' == $arRes["PERSONAL_PHONE"]){
					
					
				}
				$changedPhone++;
					//p($userPhoneCheck->getMessage());
				$arRes['ERRORS'] = $rs->getErrorMessages();	
				if(isset($arRes['ERRORS'][0])){
					if(preg_match('/^Пользователь с номером телефона/',$arRes['ERRORS'][0])){
						$userPhoneCheck = UserPhoneAuthTable::getList([
							"filter" =>	[
								"=PHONE_NUMBER" => str_replace('89', '+79', $arRes["PERSONAL_PHONE"]),
							]
						])->fetch();
						$arRes['ERRORS_DOUBLE_USER_ID'] = $userPhoneCheck['USER_ID'];
						//p($userPhoneCheck);
					}
				}
			}
			
			$arRes['PHONE_NUMBER'] = $userPhone['PHONE_NUMBER'];
			//$arRes['PHONE_NUMBER'] = isset($userPhone['PHONE_NUMBER']);
			$not_personal_phone[] = $arRes;
		}
		unset($dbUser);
		unset($arRes);
		
		p($changedPhone);
		foreach($not_personal_phone as $key => $val){
			if(!isset($val['ERRORS'])){
				unset($not_personal_phone[$key]);
			}
		}
		p($not_personal_phone);
	}

    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>