<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;
use Sodamoda\Core\Partners\Personal\Product;

global $USER;

Loader::includeModule('highloadblock');
Loader::includeModule('iblock');
Loader::includeModule('sodamoda.core');

$sodamodaCore = Product::getInstance();
$rsAction = $sodamodaCore->productAction($_POST, $_FILES);
echo $rsAction;
die();