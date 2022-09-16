<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	use \Bitrix\Main\UserPhoneAuthTable;
	
	if($USER->IsAuthorized())
	{
		$arResult['DOMESTIC_ACCOUNT'] = CSaleUserAccount::GetByUserID($USER->GetId(), 'RUB');
		
		$userPhoneCheck = UserPhoneAuthTable::getList(["filter" =>	["=USER_ID" => $USER->GetID()]])->fetch();
		$arResult['PHONE_CONFIRMED'] = $userPhoneCheck['CONFIRMED'] === 'Y'; 
	}