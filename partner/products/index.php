<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Товары");

use Bitrix\Main\Config\Option;

$groups = strpos(',', Option::get('sodamoda.core', 'group_partner_lists')) !== -1 ? explode(',', Option::get('sodamoda.core', 'group_partner_lists')) : [Option::get('sodamoda.core', 'group_partner_lists')];
if($USER->IsAuthorized() && CSite::InGroup($groups))
{
	$APPLICATION->IncludeComponent("bitrix:menu", "subsections", Array(
        "ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
        "CHILD_MENU_TYPE" => "subsections",	// Тип меню для остальных уровней
        "DELAY" => "N",	// Откладывать выполнение шаблона меню
        "MAX_LEVEL" => "1",	// Уровень вложенности меню
        "MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
        "MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
        "MENU_CACHE_TYPE" => "N",	// Тип кеширования
        "MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
        "ROOT_MENU_TYPE" => "subsections",	// Тип меню для первого уровня
        "USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
        "COMPONENT_TEMPLATE" => "subsections"
		),
        false
    );
	?>

    <h1>
        Мои товары
    </h1>
    <br>

	<?
	//var_dump($USER->GetId());
	$GLOBALS['product_owner'] = ['PROPERTY_PRODUCT_OWNER' => $USER->GetId()];
  
  $APPLICATION->IncludeComponent('arlix:manage.product_list', '', [
    "IS_OWNER" => 'Y',
  ], false);


}
else{
  $APPLICATION->AuthForm(GetMessage('FRLM_NEED_AUTH'));
  //LocalRedirect('/personal');
}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>