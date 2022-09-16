<?php
  require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
  $APPLICATION->SetTitle("Настройки пользователя");

  global $USER;
  
  use Bitrix\Main\Config\Option;

  if($USER->IsAuthorized() && CSite::InGroup(explode(',', Option::get('sodamoda.core', 'group_manage_lists')))):

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
    );?>

  <?php
  endif;

  $APPLICATION->IncludeComponent("bitrix:main.profile", "template", Array(
    "SET_TITLE" => "N",	// Устанавливать заголовок страницы
    ),
    false
  );

  require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>