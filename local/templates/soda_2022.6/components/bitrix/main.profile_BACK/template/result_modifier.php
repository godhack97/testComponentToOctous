<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if($USER->IsAuthorized())
{
  $arResult['DOMESTIC_ACCOUNT'] = CSaleUserAccount::GetByUserID($USER->GetId(), 'RUB');
}