<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => Loc::getMessage('ARLIX_ELEMENT_ADMIN_STORE_NAME'),
	"DESCRIPTION" => Loc::getMessage('ARLIX_ELEMENT_ADMIN_STORE_DESCRIPTION'),
	"SORT" => 20,
	"PATH" => array(
		"ID" => 'arlix',
		"NAME" => Loc::getMessage('ARLIX_ELEMENT_ADMIN_STORE_SEARCH_GROUP'),
		"SORT" => 10,
		"CHILD" => array(
			"ID" => 'order',
			"NAME" => Loc::getMessage('ARLIX_ELEMENT_ADMIN_STORE_SEARCH_DIR'),
			"SORT" => 10
		)
	),
);

?>