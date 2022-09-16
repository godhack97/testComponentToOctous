<?php
  require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
  $APPLICATION->SetTitle("Товары");

  global $USER;
  
  use Bitrix\Main\Config\Option;
  
  if($USER->IsAuthorized() && CSite::InGroup(explode(',', Option::get('sodamoda.core', 'group_manage_lists'))))
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
    ), false);
    ?>
    <h1>Товары</h1>
    <br>
    <?php
      //$GLOBALS['product_owner'] = ['PROPERTY_PRODUCT_OWNER' => $USER->GetId()];
  
    $APPLICATION->IncludeComponent('arlix:manage.product_list', '', [
      "IS_OWNER" => 'N',
    ], false);
  }
  else {
    $APPLICATION->AuthForm(GetMessage('FRLM_NEED_AUTH'));
    //LocalRedirect('/personal');
  }

  require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>