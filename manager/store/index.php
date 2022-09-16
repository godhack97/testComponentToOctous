<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Остатки в магазинах");
?>
<?
if(!CSite::InGroup(array(1, 8, 9, 15, 16)))
  LocalRedirect('/personal/');
?>

<?$APPLICATION->IncludeComponent("bitrix:menu", "subsections", Array(
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
);?>

<?if($APPLICATION->GetCurDir() == '/manager/store/'):?>
  <section class="search-scu">
    <?$APPLICATION->IncludeComponent("arlix:store.search", "", [], false);?>
  </section>
<?endif?>

<br>
<? $APPLICATION->IncludeComponent("arlix:store.page", "", [], false); ?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>