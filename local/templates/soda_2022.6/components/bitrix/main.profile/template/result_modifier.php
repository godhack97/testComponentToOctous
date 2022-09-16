<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\UserPhoneAuthTable;

if($USER->IsAuthorized())
{
  $arResult['DOMESTIC_ACCOUNT'] = CSaleUserAccount::GetByUserID($USER->GetId(), 'RUB');

  $userPhoneCheck = UserPhoneAuthTable::getList(["filter" =>	["=USER_ID" => $USER->GetID()]])->fetch();
  $arResult['PHONE_CONFIRMED'] = $userPhoneCheck['CONFIRMED'] === 'Y'; 
}

// Если есть данные хоть в одном поле - показывать форму партнёра
$arResult['FLAG_SHOW_PARTNER'] = false;
if((isset($arResult['arUser']['WORK_COMPANY']) && !empty($arResult['arUser']['WORK_COMPANY'])) || 
  (isset($arResult['arUser']['UF_INN']) && !empty($arResult['arUser']['UF_INN'])) ||
  (isset($arResult['arUser']['UF_PSRNIE']) && !empty($arResult['arUser']['UF_PSRNIE'])) ||
  (isset($arResult['arUser']['WORK_STREET']) && !empty($arResult['arUser']['WORK_STREET'])) ||
  (isset($arResult['arUser']['PERSONAL_STREET']) && !empty($arResult['arUser']['PERSONAL_STREET'])) ||
  (isset($arResult['arUser']['UF_OKVED']) && !empty($arResult['arUser']['UF_OKVED'])) ||
  (isset($arResult['arUser']['UF_BANK']) && !empty($arResult['arUser']['UF_BANK'])) ||
  (isset($arResult['arUser']['UF_SETTLEMENT_ACCOUNT']) && !empty($arResult['arUser']['UF_SETTLEMENT_ACCOUNT'])) ||
  (isset($arResult['arUser']['UF_CORRESPONDENT_ACCOUNT']) && !empty($arResult['arUser']['UF_CORRESPONDENT_ACCOUNT'])) ||
  (isset($arResult['arUser']['UF_IBIKNN']) && !empty($arResult['arUser']['UF_IBIKNN'])) ||
  (isset($arResult['arUser']['UF_TAXATION']) && !empty($arResult['arUser']['UF_TAXATION'])))
  $arResult['FLAG_SHOW_PARTNER'] = true;