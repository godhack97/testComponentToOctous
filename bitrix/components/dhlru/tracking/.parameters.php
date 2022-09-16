<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use DhlRu\Delivery\Utils\COptionsDhlRU;

if (!Loader::includeModule('dhlru.delivery'))
	return;

$arAConfig = COptionsDhlRU::getOptionArray('PROPERTY_ACCOUNT');
$defaultAcc = COptionsDhlRU::getOptionDefault('PROPERTY_ACCOUNT')['ACCOUNT'];

$arrAccounts = array();
foreach($arAConfig['ACCOUNT'] as $account) {
	$arrAccounts[$account] = $account;
}

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		/*"ACCOUNT" => array(
			'TYPE'     => 'LIST',
			'NAME'     => GetMessage('DHLRU_COMPONENT_ACCOUNTS'),
			'VALUES'   => $arrAccounts,
			'DEFAULT'  => $defaultAcc,
			'PARENT'   => "BASE",
		),*/
	),
);
?>