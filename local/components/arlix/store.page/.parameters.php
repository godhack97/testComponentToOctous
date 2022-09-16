<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__); 

try
{

	$arComponentParameters = array(

	);
}
catch (Main\LoaderException $e)
{
	ShowError($e->getMessage());
}
?>