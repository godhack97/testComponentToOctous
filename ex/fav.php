<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

//use Bitrix\Main;
//use Bitrix\Main\Application;
//use Bitrix\Main\Loader;
//use Bitrix\Main\Localization\Loc;
//use Bitrix\Main\Context;
//use Bitrix\Main\Web\Cookie;
//
//use tokit\Favorites\Tools;
//
//Loc::loadMessages(__FILE__);


//use Bitrix\Main\Loader;
//Loader::includeModule('tokit.favorites');
//CModule::IncludeModule("tokit.favorites");


$APPLICATION->SetTitle("EX");
?>

<?
//  global $USER;
//  
//  $arFields = array(
//    "ELEMENT_ID" => 7047
//  );
//  $arFields['USER_ID'] = $USER->GetID();
//    
//  $result = Tools::removeFromFav($arFields);
//
//  var_dump($result);
?>


 <?$APPLICATION->IncludeComponent(
	"tokit:basic.favorites.count",
	"template",
	Array(
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	)
);?>



  <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>