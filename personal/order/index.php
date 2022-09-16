<?
define('NEED_AUTH', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заказы");
?>

<?
global $USER;
$arOrders = Array();



CModule::IncludeModule('sale');

// Получаем список заказов текущего пользователя
$rsOrders = CSaleOrder::GetList(Array("DATE_INSERT" => "DESC"), Array('USER_ID' => $USER->GetId()));

while ($arOrder = $rsOrders->Fetch())
  $arOrders[] = $arOrder;

foreach ($arOrders as $key => $item)
{
  // Считаем количство товаров
  $arOrders[$key]['GOODS'] = 0;
  $orderItems = CSaleBasket::GetList(array("ID" => "ASC"), array("ORDER_ID" => $item['ID']));

  while ($orderItem = $orderItems->Fetch()) {
    $arOrders[$key]['QUANTITY'] += 1;
  }

  if ($item['CANCELED'] == 'Y')
    $arOrders[$key]['STATUS'] = 'Отменен';
  else
  {
    $arStatus = CSaleStatus::GetByID($item['STATUS_ID']);
    $arOrders[$key]['STATUS'] = $arStatus['NAME'];
  }
}

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

<h1>Заказы</h1>
<br>

<?if (empty($arOrders)):?>

  <div class="no-result gray">Заказов пока нет</div>
  
<?else:?>
  
  <div class="orders-list">
    <?foreach ($arOrders as $arOrder):?>
      <div class="item">
        <div class="title">
          <div class="number"><a class="black" href="<?=$arOrder['ID']?>/">№ <?=$arOrder['ID']?></a></div>
          <div class="subtext"><?=$arOrder['STATUS']?></div>
        </div>
        <div class="summ">
          <div class="subtext">Сумма</div>
          <div class="text"><?=number_format($arOrder['PRICE'],0,'',' ')?>&nbsp;&#8381;</div>
        </div>
        <div class="quantity">
          <div class="subtext">Количество</div>
          <div class="text"><?=$arOrder['QUANTITY']?> шт.</div>
        </div>
        <div class="link">
          <a href="<?=$arOrder['ID']?>/" class="black">Подробнее</a>
        </div>
      </div>
    <?endforeach?>
  </div>
  
<?endif?>
  
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>