<?
  require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

  $APPLICATION->SetTitle("Создание");

  global $USER;
  
  use Bitrix\Main\Config\Option;
  
  $groups = strpos(',', Option::get('sodamoda.core', 'group_partner_lists')) !== -1 ? explode(',', Option::get('sodamoda.core', 'group_partner_lists')) : [Option::get('sodamoda.core', 'group_partner_lists')];
  if($USER->IsAuthorized() && CSite::InGroup($groups)) {
    $APPLICATION->IncludeComponent("bitrix:menu", "subsections", array(
      "ALLOW_MULTI_SELECT" => "N",  // Разрешить несколько активных пунктов одновременно
      "CHILD_MENU_TYPE" => "subsections",  // Тип меню для остальных уровней
      "DELAY" => "N",  // Откладывать выполнение шаблона меню
      "MAX_LEVEL" => "1",  // Уровень вложенности меню
      "MENU_CACHE_GET_VARS" => "",  // Значимые переменные запроса
      "MENU_CACHE_TIME" => "3600",  // Время кеширования (сек.)
      "MENU_CACHE_TYPE" => "N",  // Тип кеширования
      "MENU_CACHE_USE_GROUPS" => "Y",  // Учитывать права доступа
      "ROOT_MENU_TYPE" => "subsections",  // Тип меню для первого уровня
      "USE_EXT" => "Y",  // Подключать файлы с именами вида .тип_меню.menu_ext.php
      "COMPONENT_TEMPLATE" => "subsections"
    ),
      false
    );
    ?>
    <?php
    $APPLICATION->IncludeComponent('arlix:manage.product', '', [
      'ACTION' => 'add',  // Разрешить несколько активных пунктов одновременно
      'PATH_ADD' => '/partner/products/add/',  // Разрешить несколько активных пунктов одновременно
      'PATH_RETURN' => '/partner/products/',  // Разрешить несколько активных пунктов одновременно
      'IS_ADMIN' => CSite::InGroup([1])
    ], false);
    ?>
    <?php
  }
  else {
    $APPLICATION->AuthForm(GetMessage('FRLM_NEED_AUTH'));
    // LocalRedirect('/personal');
  }

  require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>