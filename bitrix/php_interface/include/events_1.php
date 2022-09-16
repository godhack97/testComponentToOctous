<?

use Bitrix\Sale,
    \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Application,
    \Bitrix\Main\Loader,
    \Bitrix\Highloadblock\HighloadBlockTable,
    \Bitrix\Main\Web\Cookie;

AddEventHandler('main', 'OnAfterUserAdd', Array('OnAfterUserAddClass', 'OnAfterUserAddHandler'));
AddEventHandler('main', 'OnBeforeEventSend', Array("OnBeforeEventSendClass", "OnBeforeEventSendHandler"));
AddEventHandler('main', 'OnAdminTabControlBegin', 'RemoveYandexDirectTab');

AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array("OnBeforeIBlockElementUpdateClass", "OnBeforeIBlockElementUpdateHandler"));
AddEventHandler("iblock", "OnBeforeIBlockElementAdd", Array("OnBeforeIBlockElementAddClass", "OnBeforeIBlockElementAddHandler"));

AddEventHandler('sale', 'OnOrderNewSendEmail', 'customOnOrderSendEmail');
AddEventHandler('sale', 'OnOrderPaySendEmail', 'customOnOrderSendEmail');

AddEventHandler("catalog", "OnSuccessCatalogImport1C", "updateNormalWeightFrom1C");

\Bitrix\Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleOrderEntitySaved', 'OnOrderPropertyChange');
\Bitrix\Main\EventManager::getInstance()->addEventHandler('sale', 'onSaleAdminOrderInfoBlockShow', 'onSaleAdminOrderInfoBlockShow');
\Bitrix\Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleBeforeStatusOrderChange', 'customOnSaleBeforeStatusOrderChange');
\Bitrix\Main\EventManager::getInstance()->addEventHandler('sale', 'OnBeforeSalePaymentSetField', 'customOnBeforeSalePaymentSetField');
\Bitrix\Main\EventManager::getInstance()->addEventHandler('sale', 'onSaleDeliveryServiceCalculate', 'customDeliveryCalculate');

\Bitrix\Main\EventManager::getInstance()->addEventHandler('sale', 'OnSalePaymentEntitySaved', ['sendOrderInfo', 'sendInfoOrderByTypePayment']);
\Bitrix\Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleShipmentEntitySaved', ['sendOrderInfo', 'sendInfoOrderByTypeDelivery']);
\Bitrix\Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleShipmentItemEntitySaved', ['sendOrderInfo', 'sendInfoOrderByModifyItem']);

\Bitrix\Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleOrderSaved', 'OnSaleOrderSavedCustom');

\Bitrix\Main\EventManager::getInstance()->addEventHandler('main', 'OnAdminSaleOrderEdit', 'checkManagerResponsibility');
\Bitrix\Main\EventManager::getInstance()->addEventHandler('main', 'OnAdminSaleOrderView', 'checkManagerResponsibility');


function checkManagerResponsibility($params) 
{
  // Если в админке заказ смотрит/редактирует менеджер из группы 15, то он автоматически проставляется ответственным
  if ($params['ID'] > 0)
  {
    $order = Sale\Order::load($params['ID']);

    $responsibleId = $order->getField('RESPONSIBLE_ID');
    $currentUserId = $GLOBALS["USER"]->GetID();
    if (!$responsibleId && $currentUserId)
    {
      $result = \Bitrix\Main\UserGroupTable::getList(array(
        'filter' => array('USER_ID' => $currentUserId, 'GROUP.ACTIVE' => 'Y', 'GROUP_ID' => 15),
        'select' => array('GROUP_ID')
      ));

      if ($arGroup = $result->fetch()) 
      {
        $order->setField('RESPONSIBLE_ID', $currentUserId);
        $order->save();
        LocalRedirect($GLOBALS["APPLICATION"]->GetCurUri());
      }
    }
  }
}


function OnSaleOrderSavedCustom(Bitrix\Main\Event $event)
{
  
  $order = $event->getParameter("ENTITY");
  $isNew = $event->getParameter("IS_NEW");
  
  $ORDER_ID = $order->getId();
  
  logArlix('OnSaleOrderSavedCustom start $isNew = '. $isNew .' ORDER_ID = '. $ORDER_ID, 'log_create_order.txt', __FILE__ .' :'. __LINE__);
  
  if ($isNew)
  {
    $basket = $order->getBasket();
    foreach ($basket as $basketItem) 
    {
      $productId = $basketItem->getProductId();

      // Проверка поступления на склад
      CBitrixComponent::includeComponentClass("arlix:product.subscribe");
      $isAvailable = LikesComponent::isAvailable($productId);
      
      if(!$isAvailable) 
      {
//        $propertyCollection = $order->getPropertyCollection();
//        $locPropValue = $propertyCollection->getDeliveryLocation();
//        $locationId = $locPropValue->getValue();
//        
//        if($locationId == '0000073738' || $locationId == '0000103664' || $locationId == '0000445112') 
//        {
//          foreach ($propertyCollection as $propertyItem) {
//            switch ($propertyItem->getField("CODE")) {
//              case 'ORDER_OUT_OF_STOCK':
//                $propertyItem->setField("VALUE", 'Y');
//                $order->save();
//                break;
//            }
//          }
//        }
      }
    }

    
    // Если заказ оформляет менеджер из группы 15, то он автоматически проставляется ответственным
//    $responsibleId = $order->getField('RESPONSIBLE_ID');
//    $currentUserId = $GLOBALS["USER"]->GetID();
//    
//    if (!$responsibleId && $currentUserId || true)
//    {
//      $result = \Bitrix\Main\UserGroupTable::getList(array(
//          'filter' => array('USER_ID' => $currentUserId, 'GROUP.ACTIVE' => 'Y', 'GROUP_ID' => 15),
//          'select' => array('GROUP_ID')
//      ));
//
//      if ($arGroup = $result->fetch()) {
//        $order->setField('RESPONSIBLE_ID', $currentUserId);
//        $order->save();
//      }
//    }
    
    //logArlix('OnSaleOrderSavedCustom finish $currentUserId = '. $currentUserId, 'log_create_order.txt', __FILE__ .' :'. __LINE__);
  }
}



function customDeliveryCalculate(\Bitrix\Main\Event $event)
{
  /** @var Delivery\CalculationResult $baseResult */
  $baseResult = $event->getParameter('RESULT');
  $shipment = $event->getParameter('SHIPMENT');

  //logArlix('customDeliveryCalculate start', 'log_shipment.txt', __FILE__ .' :'. __LINE__);
  
  if ($shipment instanceof \Bitrix\Sale\Shipment)
    $shipmentID = $shipment->getDeliveryId();
  else
    $shipmentID = 0;


  $price = $baseResult->getDeliveryPrice();

  if(SITE_ID == 's1') $priceFinish = ceil($price / 10) * 10;
  else $priceFinish = $price; //ceil($price / 10) * 10;

  // Для отображения скидки на доставку, используется в шаблоне заказа
  $_SESSION['PRICE_OLD_DELIVERY'] = 0;

  if (in_array($shipmentID, Array(47))) // ($price <= 399 && in_array($shipmentID, Array(47)))   // Array(66, 47, 60, 72, 3, 67)
  {
    $price = 0;
    $_SESSION['PRICE_OLD_DELIVERY'] = $priceFinish;
  }
  else
    $price = $priceFinish;

  //  if($shipmentID == 47 && $price <= 399) // PickPoint
  //    $priceFinish = 0;

  if(in_array($shipmentID, [80, 1]) && SITE_ID != 's1') // EMS Russian Post
    $price = 30;
  
  $baseResult->setDeliveryPrice($price);

  //logArlix('customDeliveryCalculate $shipmentID='. $shipmentID .' $price='. $price, 'log_shipment.txt', __FILE__ .' :'. __LINE__);

  //AddMessage2Log('myCalc '. print_r($baseResult, 1));

  //    $event->addResult(
  //        new EventResult(
  //            EventResult::SUCCESS, array('RESULT' => $baseResult)
  //        )
  //    );
}



function customOnBeforeSalePaymentSetField(\Bitrix\Main\Event $event)
{
  $name = $event->getParameter('NAME');
  $value = $event->getParameter('VALUE');

  //AddMessage2Log('OnBeforeSalePaymentSetField ' . $name . ' _ ' . print_r($value, 1));

  global $USER;
  if ($USER->GetID() > 0) {
    if ($name == 'PAID' && $value == 'Y') {

      //AddMessage2Log('OnBeforeSalePaymentSetField REQUEST ' . print_r($_REQUEST, 1));
      //OnBeforeSalePaymentSetField REQUEST Array
      //(
      //    [PAYMENT] => SBERBANK
      //    [ORDER_ID] => 5362
      //    [PAYMENT_ID] => 5364
      //    [orderId] => b33444fe-60b9-7b77-9ecb-892401c152ee
      //    [lang] => ru
      //    [_ym_uid] => 1586210084504349043
      //    [BX_USER_ID] => 7730708a5979178c74802e4c22942a3b
      //    [c2d_widget_id] => {"15aad781394791d77a896302f721c2c9":"[chat] ug60dhx87y"}
      //    [sodamoda_ru_UTM] =>
      //    [BITRIX_SM_RULES_COOKIE] => Y
      //    [BITRIX_SM_SOUND_LOGIN_PLAYED] => Y
      //    [BITRIX_SM_LOGIN] => zenkot
      //    [_ym_d] => 1601986799
      //    [BITRIX_SM_UIDL] => zenkot
      //    [BITRIX_SM_SALE_UID] => 93842
      //    [BITRIX_SM_GUEST_ID] => 61
      //    [_ym_isad] => 2
      //    [BITRIX_CONVERSION_CONTEXT_s1] => {"ID":2,"EXPIRE":1602968340,"UNIQUE":["conversion_visit_day","sale_cart_add_day","sale_order_add_day","sale_payment_add_day"]}
      //    [BITRIX_SM_NCC] => Y
      //    [PHPSESSID] => 67457ff0c85f8f0f0aedd93ebfd1a19c
      //    [BITRIX_SM_UIDH] => cc360aa509b420220ab5341aa1876d12
      //    [_ym_visorc_54055981] => w
      //    [_ym_visorc_26812653] => b
      //    [BITRIX_SM_LAST_VISIT] => 17.10.2020 14:00:21
      //    [IPOLH_DPD_ORDER] =>
      //    [IPOLH_DPD_TARIFF] =>
      //)


      $result = \Bitrix\Main\UserGroupTable::getList([
        'filter' => ['USER_ID' => $USER->GetID(), 'GROUP.ACTIVE' => 'Y'],
        'select' => ['GROUP_ID', 'GROUP_CODE' => 'GROUP.STRING_ID'],
        'order' => ['GROUP.C_SORT' => 'ASC'],
      ]);

      $isAdmin = false;
      while ($arGroup = $result->fetch()) {
        if (in_array($arGroup['GROUP_ID'], [1, 14]))
          $isAdmin = true;
      }

      //      if (!$isAdmin) {
      //        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::ERROR, new \Bitrix\Sale\ResultError('У вас нет доступа к проведению оплаты заказа.', 'code'), 'sale');
      //      }

      //AddMessage2Log('OnBeforeSalePaymentSetField FINISH');
    }
  }
}



function customOnSaleBeforeStatusOrderChange(\Bitrix\Main\Event $event)
{
  $value = $event->getParameter('VALUE');
  if($value !== 'N' && $value !== 'W') {
    global $USER;
    if ($USER->GetID() > 0) {
      $result = \Bitrix\Main\UserGroupTable::getList([
        'filter' => ['USER_ID' => $USER->GetID(), 'GROUP.ACTIVE' => 'Y'],
        'select' => ['GROUP_ID', 'GROUP_CODE' => 'GROUP.STRING_ID'],
        'order' => ['GROUP.C_SORT' => 'ASC'],
      ]);

      $isAdmin = false;
      while ($arGroup = $result->fetch()) {
        if (in_array($arGroup['GROUP_ID'], [1, 14]))
          $isAdmin = true;
      }

      if (!$isAdmin) {
        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::ERROR, new \Bitrix\Sale\ResultError('На выбранный статус заказа изменение запрещено.', 'code'), 'sale');
      }
    }
  }
}



function onSaleAdminOrderInfoBlockShow(\Bitrix\Main\Event $event)
{
  $order = $event->getParameter("ORDER");
  $propertyCollection = $order->getPropertyCollection();
  $ar = $propertyCollection->getArray();

  $arProps = [];
  foreach ($ar['properties'] as $v)
    $arProps[$v['CODE']] = $v['VALUE'][0];

  $shipmentCollection = $order->getShipmentCollection();
  $shipment = $shipmentCollection[0];

  $locPropValue = $propertyCollection->getDeliveryLocation();
  $res = \Bitrix\Sale\Location\LocationTable::getList(array(
    'filter' => array(
      '=CODE' => $locPropValue->getValue(),
      '=PARENTS.NAME.LANGUAGE_ID' => LANGUAGE_ID,
      '=PARENTS.TYPE.NAME.LANGUAGE_ID' => LANGUAGE_ID,
    ),
    'select' => array(
      'I_ID' => 'PARENTS.ID',
      'I_NAME_RU' => 'PARENTS.NAME.NAME',
      'I_TYPE_CODE' => 'PARENTS.TYPE.CODE',
      'I_TYPE_NAME_RU' => 'PARENTS.TYPE.NAME.NAME'
    ),
    'order' => array(
      'PARENTS.DEPTH_LEVEL' => 'asc'
    )
  ));

  while ($item = $res->fetch()) {
    if (!empty($item)) {
      if ($item['I_TYPE_CODE'] == 'COUNTRY')
        $countryName = $item['I_NAME_RU'];

      if ($item['I_TYPE_CODE'] == 'REGION')
        $regionName = $item['I_NAME_RU'];

      if ($item ['I_TYPE_CODE'] == 'CITY')
        $cityName = $item['I_NAME_RU'];

      if ($item['I_TYPE_CODE'] == 'SUBREGION')
        $subregionName = $item['I_NAME_RU'];
    }
  }

  $arFullCity = [$countryName, $regionName, $subregionName, $cityName];
  TrimArr($arFullCity);

  // print_r($locPropValue);

  // $arStatus = CSaleStatus::GetByID($shipment->getField('STATUS_ID'));


  // print_r($shipment);

  $arRes = [];

  if ($arProps['LOCATION'] && !empty(implode(', ', $arFullCity)))
    $arRes[] = [
      'TITLE' => 'Город:',
      'VALUE' => implode(', ', $arFullCity),
      'ID' => 'LOCATION'
    ];

  if ($arProps['ADDRESS'] && !empty($arProps['ADDRESS']))
    $arRes[] = [
      'TITLE' => 'Адрес доставки:',
      'VALUE' => $arProps['ADDRESS'],
      'ID' => 'ADDRESS'
    ];

  if ($order->getField('USER_DESCRIPTION') && !empty($order->getField('USER_DESCRIPTION')))
    $arRes[] = [
      'TITLE' => 'Комментарий:',
      'VALUE' => $order->getField('USER_DESCRIPTION'),
      'ID' => 'USER_DESCRIPTION'
    ];
  
  $basket = $order->getBasket();
  $preorder = false;
  foreach ($basket as $basketItem){
    $basketPropertyCollection = $basketItem->getPropertyCollection();
    $allPropProduct = $basketPropertyCollection->getPropertyValues();
    foreach ($allPropProduct as $prop){
      switch ($prop['CODE']){
        case 'PREORDER_PRODUCT':
          $preorder = $prop['VALUE'] === 'Y';
          break;
      }
    }
  }
  if($preorder){
    $arRes[] = [
      'TITLE' => 'В заказе присутствует товар с предзаказом:',
      'VALUE' => 'Да',
      'ID' => 'ORDER_PREORDER_PRODUCT'
    ];
    //$order->setField('ORDER_PREORDER_PRODUCT', 'Y');
    //$order->save();
    //$propertyCollection = $order->getPropertyCollection();
    
    //print_r('<pre>');
    //print_r($arItem);
    //print_r('</pre>');
    foreach ($propertyCollection as $propertyItem) {
      switch ($propertyItem->getField('CODE')) {
        case 'ORDER_PREORDER_PRODUCT':
          $propertyItem->setField('VALUE', 'Y');
          $order->save();
          break;
      }
    }
  }
  
    $paymentCollection = $order->getPaymentCollection();
    if($paymentCollection->isExistsInnerPayment()){
        $payment = $paymentCollection[0];
        $arRes[] = [
            'TITLE' => 'Оплачено бонусами:',
            'VALUE' => $payment->getSum(),
            'ID' => 'ORDER_BONUS_PAY'
        ];
    }

  //      if ($shipment->getField('TRACKING_STATUS'))
  //        $arRes[] = [
  //          'TITLE' => 'Статус доставки:',
  //          'VALUE' => $shipment->getField('TRACKING_STATUS'),
  //          'ID' => 'TRACKING_STATUS'
  //        ];
  //
  //        if ($shipment->getField('TRACKING_NUMBER'))
  //          $arRes[] = [
  //            'TITLE' => 'Трек-номер:',
  //            'VALUE' => $shipment->getField('TRACKING_NUMBER'),
  //            'ID' => 'TRACKING_NUMBER'
  //          ];
  //
  //        if ($shipment->getField('TRACKING_DESCRIPTION'))
  //          $arRes[] = [
  //            'TITLE' => 'Сообщение от службы доставки:',
  //            'VALUE' => $shipment->getField('TRACKING_DESCRIPTION'),
  //            'ID' => 'TRACKING_DESCRIPTION'
  //          ];
  //
  //        $arRes[] = [
  //          'TITLE' => 'Статус оплаты:',
  //          'VALUE' => ($order->isPaid()) ? 'Оплачен' : 'Не оплачен',
  //          'ID' => 'isPaid'
  //        ];

  return new \Bitrix\Main\EventResult(
    \Bitrix\Main\EventResult::SUCCESS,
    $arRes,
    'sale'
  );
}



class OnBeforeIBlockElementUpdateClass
{
  function OnBeforeIBlockElementUpdateHandler(&$arFields)
  {
    if ($arFields['TMP_ID'] && in_array($arFields['IBLOCK_ID'], Array(CATALOG_ID, CATALOG_OFFERS_ID))) {
      unset($arFields['ACTIVE']);
    }
    
    if (in_array($arFields["IBLOCK_ID"], [CATALOG_ID, COLORS_ID])) {
      imagesResizeOriginal($arFields);
    }
  }
}


class OnBeforeIBlockElementAddClass
{
  function OnBeforeIBlockElementAddHandler(&$arFields)
  {
    if ($arFields['TMP_ID'] && $arFields['IBLOCK_ID'] == CATALOG_ID) {
      $arFields['ACTIVE'] = 'N';
    }
    
    if (in_array($arFields["IBLOCK_ID"], [CATALOG_ID, COLORS_ID])) {
      imagesResizeOriginal($arFields);
    }
  }
}


// Отключаем вкладку "Реклама" при редактировании элемента
function RemoveYandexDirectTab(&$TabControl)
{
  if ($GLOBALS['APPLICATION']->GetCurPage() == '/bitrix/admin/iblock_element_edit.php') {
    foreach ($TabControl->tabs as $Key => $arTab) {
      if ($arTab['DIV'] == 'seo_adv_seo_adv') {
        //unset($TabControl->tabs[$Key]);
        $TabControl->tabs[$Key] = '';
      }
    }
  }
}


class OnAfterUserAddClass
{
  function OnAfterUserAddHandler(&$arFields)
  {
    if ($arFields['EXTERNAL_AUTH_ID'] == 'socservices' && $arFields['ID']) {
      if (CModule::IncludeModule('sale')) {
        CSaleUserAccount::UpdateAccount($arFields['ID'], 500, 'RUB', 'Бонус за регистрацию через соц. сети');
      }
    }
  }
}


class OnBeforeEventSendClass
{
  function OnBeforeEventSendHandler(&$arFields, &$arTemplate)
  {
    //logArlix('OnBeforeEventSendHandler start', 'log_create_order.txt', __FILE__ .' :'. __LINE__);
    
    // Форма возврата, редактируем для письма список товаров
    if ($arFields['RS_FORM_SID'] == 'SIMPLE_FORM_1') 
    {
      $arProducts = explode('<br>', $arFields['SIMPLE_QUESTION_954']);
      $arFields['SIMPLE_QUESTION_954'] = '';

      foreach ($arProducts as $product) {
        $arProduct = explode('##', $product);
        $arFields['SIMPLE_QUESTION_954'] .= $arProduct[2] . ' — ' . $arProduct[3] . 'x' . number_format($arProduct[4], 2, '.', ' ') . ' руб.<br>';
      }

      $arFields['SIMPLE_QUESTION_954'] = trim($arFields['SIMPLE_QUESTION_954'], '<br>');
      $arFields['SIMPLE_QUESTION_954_RAW'] = $arFields['SIMPLE_QUESTION_954'];
    }

    
    if ($arTemplate['EVENT_NAME'] == 'USER_INFO' && $arFields['USER_ID']) 
    {
      logArlix('OnBeforeEventSendHandler start USER_INFO', 'log_create_order.txt', __FILE__ .' :'. __LINE__);
      
      $password = randString(4, array("123456789")); // uniqid();
      $user = new CUser;
      $fields = Array(
        "PASSWORD" => $password,
        "CONFIRM_PASSWORD" => $password,
      );
      $user->Update($arFields['USER_ID'], $fields);

      $arFields['USER_PASSWORD'] = $_SESSION['USER_PASS_FOR_ORDER_INFO'] = $password;

        if (preg_match("#^[0-9]+$#", $arFields['LOGIN'])) {
            $arEventField = [
                "LOGIN" => $arFields["LOGIN"],
                "EMAIL" => $arFields["EMAIL"],
                "USER_PHONE" => $arFields["LOGIN"],
                "PASSWORD" => $password,
            ];
            $sms = new \Bitrix\Main\Sms\Event("SMS_USER_REGISTER", $arEventField);
            $sms->setSite('s1');
            $sms->setLanguage('ru');
            $result = $sms->send();
    }
    }

    
    if ($arTemplate['EVENT_NAME'] == 'SALE_NEW_ORDER' && !empty($arFields['USER_LOGIN'])) 
    {
      logArlix('OnBeforeEventSendHandler start SALE_NEW_ORDER '. $_SESSION['USER_PASS_FOR_ORDER_INFO'], 'log_create_order.txt', __FILE__ .' :'. __LINE__);
      
      if (!empty($_SESSION['USER_PASS_FOR_ORDER_INFO'])) {
        $arFields['NEW_USER_INFO'] = "<b>Данные для входа в личный кабинет:</b><br>
                                        Логин: " . $arFields['USER_LOGIN'] . "<br>
                                        Пароль: " . $_SESSION['USER_PASS_FOR_ORDER_INFO'] . "<br><br>";
        $_SESSION['USER_PASS_FOR_ORDER_INFO'] = '';
      }

      $userID = \Bitrix\Main\UserTable::getList(Array(
        "select" => ["ID"],
        "filter" => ['LOGIN' => $arFields['USER_LOGIN']],
      ))->fetch()['ID'];

      logArlix('OnBeforeEventSendHandler start SALE_NEW_ORDER $userID='. $userID, 'log_create_order.txt', __FILE__ .' :'. __LINE__);
      
      if ($userID > 0) {
        $res = \Bitrix\Main\UserGroupTable::getList(Array(
          "select" => ["*"],
          "filter" => ['USER_ID' => $userID, 'GROUP_ID' => 15],
        ));
        if ($res->getSelectedRowsCount() > 0) {
          $arFields['ADDITIONAL_INFO'] = '<br><b>Заказ был создан менеджером</b>';
          logArlix('OnBeforeEventSendHandler start SALE_NEW_ORDER $userID='. $userID .' '. $arFields['ADDITIONAL_INFO'], 'log_create_order.txt', __FILE__ .' :'. __LINE__);
        }
      }
    }

    
    // Номер отправления вашего заказа
    if ($arTemplate['EVENT_NAME'] == 'SALE_ORDER_TRACKING_NUMBER' && $arFields['FLAG_CRON_PICKPOINT'] != 'Y') 
    {
      CModule::IncludeModule("sale");

      //AddMessage2Log('OnBeforeEventSendHandler START $arFields '. print_r($arFields, 1) .' $arTemplate '. print_r($arTemplate, 1));

      $arDate = explode(' ', $arFields['ORDER_DATE']);
      $arFields['ORDER_DATE_DAY'] = $arDate[0];
      $arFields['ORDER_DATE_TIME'] = $arDate[1];
      $arFields['LINK_TRACKING'] = '';
      $arOrder = CSaleOrder::GetByID($arFields['ORDER_ID']);

      //AddMessage2Log('OnBeforeEventSendHandler $arOrder '. print_r($arOrder, 1));

      if (stristr($arOrder['DELIVERY_ID'], 'sdek')) {
        $arFields['LINK_TRACKING'] = 'Отследить заказ:<br>'
          . '<a href="https://cdek.ru/tracking?order_id=' . $arFields['ORDER_TRACKING_NUMBER'] . '">https://cdek.ru/tracking?order_id=' . $arFields['ORDER_TRACKING_NUMBER'] . '</a><br>'
          . '<br>';
      }

      if (stristr($arOrder['DELIVERY_ID'], 'pickpoint')) {
        $arFields['LINK_TRACKING'] = 'Отследить заказ:<br>'
          . '<a href="https://pickpoint.ru/monitoring/">https://pickpoint.ru/monitoring/</a><br>'
          . '<br>';
      }

      if (stristr($arOrder['DELIVERY_ID'], 'dpd')) {
        $arFields['LINK_TRACKING'] = 'Отследить заказ:<br>'
          . '<a href="https://www.dpd.ru/ols/trace2/standard.do2">https://www.dpd.ru/ols/trace2/standard.do2</a><br>'
          . '<br>';
      }

      if (stristr($arOrder['DELIVERY_ID'], 'new40') || stristr($arOrder['DELIVERY_ID'], 'new65') || stristr($arOrder['DELIVERY_ID'], 'new66')) // EMS и Нестандартная посылка
      {
        $arFields['LINK_TRACKING'] = 'Отследить заказ:<br>'
          . '<a href="https://www.pochta.ru/tracking#' . $arFields['ORDER_TRACKING_NUMBER'] . '">https://www.pochta.ru/tracking#' . $arFields['ORDER_TRACKING_NUMBER'] . '</a><br>'
          . '<br>';
      }

      $arDelivery = CSaleDelivery::GetByID($arOrder['DELIVERY_ID']);
      $arFields['DELIVERY_NAME'] = getDeliveryName($arDelivery, $arOrder['DELIVERY_ID']);
    }
    
    logArlix('OnBeforeEventSendHandler finish', 'log_create_order.txt', __FILE__ .' :'. __LINE__);

  }
}


function setOrderOutOfStock($orderId)
{
  $order = Sale\Order::load($orderId);
  $basket = $order->getBasket();

  $arItemsForMail = [];

  foreach ($basket as $basketItem) 
  { 
    $productId = $basketItem->getProductId();

    // артикул размер

    CBitrixComponent::includeComponentClass('arlix:product.subscribe');
    $isAvailable = LikesComponent::isAvailable($productId);

    if (!$isAvailable) 
    {
      $basketPropertyCollection = $basketItem->getPropertyCollection();
      $basketPropertyValues = $basketPropertyCollection->getPropertyValues();

      $arItemsForMail[$productId] = [
          'NAME' => $basketItem->getField('NAME'),
          'DETAIL_PAGE_URL' => $basketItem->getField('DETAIL_PAGE_URL'),
          'CML2_ARTICLE' => $basketPropertyValues['CML2_ARTICLE']['VALUE'],
          'SIZES' => $basketPropertyValues['SIZES']['VALUE'],
          'COLOR' => $basketPropertyValues['COLOR']['VALUE'],
      ];

      $propertyCollection = $order->getPropertyCollection();
      $locPropValue = $propertyCollection->getDeliveryLocation();
      $locationId = $locPropValue->getValue();

      if ($locationId == '0000073738' || $locationId == '0000103664' || $locationId == '0000445112') 
      {
        foreach ($propertyCollection as $propertyItem) {
          switch ($propertyItem->getField('CODE')) {
            case 'ORDER_OUT_OF_STOCK':
              $propertyItem->setField('VALUE', 'Y');
              $order->save();
              break;
          }
        }
      }
    }
  }


  // Если товара нет в наличии, уведомляем менеджера, что был оформлен заказ с отсутствующими товарами
  if (count($arItemsForMail)) 
  {
    $siteUrl = SITE_SERVER_PROTOCOL . SITE_SERVER_NAME;

    foreach ($arItemsForMail as $arItem) {
      $strItems .= '<a href="'. $siteUrl . $arItem['DETAIL_PAGE_URL'] .'">'. $arItem['NAME'] .' арт. '. $arItem['CML2_ARTICLE'] .'</a><br>';
      $strItemsTgr .= "<a href=\"". $siteUrl . $arItem['DETAIL_PAGE_URL'] ."\">". $arItem['NAME'] .' арт. '. $arItem['CML2_ARTICLE'] ."</a>\n";
    }

    $arEventFields = array(
      'ORDER_ID' => $orderId,
      'ITEMS' => $strItems,
    );

    CEvent::Send('SALE_NEW_ORDER_AVAILABLE_CHECK', SITE_ID, $arEventFields);
    
    telegramToChannel("Оформлен заказ <a href=\"". $siteUrl ."/bitrix/admin/sale_order_view.php?ID=". $arEventFields['ORDER_ID'] ."&lang=ru\">". $arEventFields['ORDER_ID'] ."</a> с отсутствующими остатками:\n". $strItemsTgr);
  }
}


function customOnOrderSendEmail($orderId, &$eventName, &$arFields)
{
  //CModule::IncludeModule("sale");

  //logArlix(print_r($arFields, 1), 'myfile.txt', __FILE__ .' :'. __LINE__);
  logArlix('customOnOrderSendEmail start ORDER_ID = '. $orderId, 'log_create_order.txt', __FILE__ .' :'. __LINE__);
  
  if($eventName == 'SALE_NEW_ORDER') {
    // Стандартный заголовок письма
    $arFields['SUBJECT'] = Loc::getMessage('SALE_NEW_ORDER_ORDER_NUM').$orderId;
  }

  $dbProps = CSaleOrderPropsValue::GetList(Array(), Array("ORDER_ID" => $orderId));
  while ($row = $dbProps->Fetch())
    $arFields['USER_' . $row['CODE']] = $row['VALUE'];

  $arOrder = CSaleOrder::GetByID($orderId);

  setOrderOutOfStock($orderId);
  
  // Если заказ оформляет менеджер из группы 15, то он автоматически проставляется ответственным
  $order = Sale\Order::load($orderId);
  $responsibleId = $order->getField('RESPONSIBLE_ID');
  $currentUserId = $GLOBALS["USER"]->GetID();
  if (!$responsibleId && $currentUserId) 
  {
    $result = \Bitrix\Main\UserGroupTable::getList(array(
        'filter' => array('USER_ID' => $currentUserId, 'GROUP.ACTIVE' => 'Y', 'GROUP_ID' => 15),
        'select' => array('GROUP_ID')
    ));

    if ($arGroup = $result->fetch()) {
      $order->setField('RESPONSIBLE_ID', $currentUserId);
      $order->save();
    }
  }
  

  $arFields['USER_DESCRIPTION'] = ($arOrder['USER_DESCRIPTION'] ? $arOrder['USER_DESCRIPTION'] : '-');
  $arFields['USER_LOGIN'] = $arOrder['USER_LOGIN'];

  $arPaySys = CSalePaySystem::GetByID($arOrder['PAY_SYSTEM_ID'], $arOrder['PERSON_TYPE_ID']);
  $arFields['USER_PAY_SYSTEM'] = $arPaySys['NAME'];

  $arDelivery = CSaleDelivery::GetByID($arOrder['DELIVERY_ID']);

  $arDelivery['NAME'] = getDeliveryName($arDelivery, $arOrder['DELIVERY_ID']);

  if ($arFields['USER_LOCATION']) {
    $arLocs = CSaleLocation::GetByID($arFields['USER_LOCATION'], LANGUAGE_ID);
    $city = $arLocs['CITY_NAME'];
  }

  if($eventName == 'SALE_ORDER_PAID') {
    $basket = $order->getBasket();
    $preorder = false;
    foreach ($basket as $basketItem){
      $basketPropertyCollection = $basketItem->getPropertyCollection();
      $allPropProduct = $basketPropertyCollection->getPropertyValues();
      foreach ($allPropProduct as $prop){
        switch ($prop['CODE']){
          case 'PREORDER_PRODUCT':
            $preorder[$basketItem->getProductId()] = $prop['VALUE'] === 'Y';
            break;
        }
      }
    }
    $arFields['DELIVERY_DATA'] = $arDelivery;
    $arFields['SUBJECT_INFO'] = (!empty($city) ? $city . ', ' : '') . $arDelivery['NAME'];

    $arProducts = [];
    $dbBasket = CSaleBasket::GetList(Array("ID" => "ASC"), Array("ORDER_ID" => $orderId));
    while ($arItem = $dbBasket->Fetch()) {
      $arProducts[] = ($preorder[$arItem['PRODUCT_ID']] ? '<b>[ПРЕДЗАКАЗ]</b>&nbsp;' : '').$arItem['NAME'] . ' &mdash; ' . $arItem['QUANTITY'] . ' ' . $arItem['MEASURE_NAME'] . '. &times; ' . number_format($arItem['PRICE'], 2, '.', ' ') .' '. Loc::getMessage('SALE_NEW_ORDER_CURRENCY').'<br>';
    }
  }

  if($eventName == 'SALE_NEW_ORDER') {
    $arStatus = CSaleStatus::GetByID($arOrder['STATUS_ID'], LANGUAGE_ID);
  }
  $arDate = explode(' ', $arFields['ORDER_DATE']);

  if($eventName == 'SALE_NEW_ORDER') 
  {
    if (in_array($arPaySys['ID'], Array(9, 14)) && $arOrder['PAYED'] != 'Y') // sberbank
    {
      $order = Sale\Order::load($orderId);
      $publicLink = Sale\Helpers\Order::getPublicLink($order);

      //$PAYMENT_LINK = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/personal/order/' . $orderId . '/?by=' . $arOrder['USER_ID'] . '">Оплатить</a>';
      $PAYMENT_LINK = '<a href="' . $publicLink . '">'.Loc::getMessage('SALE_NEW_ORDER_PAY').'</a>';
      $PAYMENT_DESC = '<b>'.Loc::getMessage('SALE_NEW_ORDER_EXPECT_PAYMENT').'</b><br><br>';
    }

    if ($arPaySys['ID'] == 10) // courier Курьеру при получении
    {
      //logArlix('customOnOrderSendEmail $arPaySys[ID]='. $arPaySys['ID'], 'log_create_order.txt', __FILE__ .' :'. __LINE__);
      
      $arFields['EMAIL_PAID'] = 'paid@sodamoda.ru';
      $arFields['SUBJECT'] = $orderId .' '. Loc::getMessage('SALE_NEW_ORDER_NOT_PAID') .' '. ($city ? $city . ', ' : '') . $arDelivery['NAME'];

      if (in_array($arOrder['DELIVERY_ID'], Array(4, 6, 5)))
        $MESSAGE_DELIVERY = Loc::getMessage('SALE_NEW_ORDER_DESCRIPTION').'<br><br>';
      
      //logArlix('customOnOrderSendEmail $arPaySys[ID]='. $arPaySys['ID'], 'log_create_order.txt', __FILE__ .' :'. __LINE__);
    }

    $STATUS_INFO = Loc::getMessage('SALE_NEW_ORDER_ORDER_NUMBER').': <b>' . $orderId . '</b><br>'
      . Loc::getMessage('SALE_NEW_ORDER_DATE').': ' . $arDate[0] . '<br>'
      . Loc::getMessage('SALE_NEW_ORDER_TIME').': ' . $arDate[1] . '<br><br>'
      . Loc::getMessage('SALE_NEW_ORDER_STATUS_ORDER').': <b>' . $arStatus['NAME'] . ($PAYMENT_LINK ? '. ' . $PAYMENT_LINK : '') . '</b><br>'
      . ($PAYMENT_DESC ? $PAYMENT_DESC : '');
  }

  if($eventName == 'SALE_ORDER_PAID') {
    $DETAIL_INFO = Loc::getMessage('SALE_NEW_ORDER_ORDER_NUMBER').': <b>' . $orderId . '</b><br>'
      . Loc::getMessage('SALE_NEW_ORDER_DATE').': ' . $arDate[0] . '<br>'
      . Loc::getMessage('SALE_NEW_ORDER_TIME').': ' . $arDate[1] . '<br><br>'
      . Loc::getMessage('SALE_NEW_ORDER_STATUS_ORDER').': <b>'.Loc::getMessage('SALE_NEW_ORDER_PAID').'</b><br><br>';

    $DETAIL_INFO .= Loc::getMessage('SALE_NEW_ORDER_ORDER_LIST').':<br>';
    foreach ($arProducts as $item)
      $DETAIL_INFO .= $item;
  }
  
  
  if(empty($order))
    $order = Sale\Order::load($orderId);

  $DETAIL_INFO .= '<br>'
    . Loc::getMessage('SALE_NEW_ORDER_GOODS_COST') .': ' . CurrencyFormat($arOrder['PRICE'] - $arOrder['PRICE_DELIVERY'], $order->getCurrency()).' <br>'
    . Loc::getMessage('SALE_NEW_ORDER_DELIVERY_COST').': ' . CurrencyFormat($arOrder['PRICE_DELIVERY'], $order->getCurrency()).' <br>'
    . Loc::getMessage('SALE_NEW_ORDER_TOTAL').'<b>: ' . CurrencyFormat($arOrder['PRICE'], $order->getCurrency()).' </b><br>'          
    . '<br>'
    . Loc::getMessage('SALE_NEW_ORDER_PAYMENT_METHOD').': <b>' . Loc::getMessage('SALE_NEW_ORDER_PAYSYSTEM_'.$arPaySys['ID']). '</b><br>'
    . Loc::getMessage('SALE_NEW_ORDER_DELIVERY_METHOD').': <b>' . $arDelivery['NAME'] . '</b><br>'
    . '<br>' . (($MESSAGE_DELIVERY && $eventName == 'SALE_NEW_ORDER') ? $MESSAGE_DELIVERY : '')
    . Loc::getMessage('SALE_NEW_ORDER_DETAILED').':<br>'
    . Loc::getMessage('SALE_NEW_ORDER_NAME').': <b>' . $arFields['USER_CONTACT_PERSON'] . '</b><br>'
    . Loc::getMessage('SALE_NEW_ORDER_PHONE').': <b>' . phoneFormat($arFields['USER_PHONE']) . '</b><br>'
    . Loc::getMessage('SALE_NEW_ORDER_EMAIL').': <b>' . $arFields['EMAIL'] . '</b><br>'
    . '<br>'
    . Loc::getMessage('SALE_NEW_ORDER_DELIVERY_CITY').': <b>' . $city . '</b><br>'
    . Loc::getMessage('SALE_NEW_ORDER_DELIVERY_ADDRESS').': <b>' . $arFields['USER_ADDRESS'] . '</b><br>'
    . Loc::getMessage('SALE_NEW_ORDER_POSTCODE').': <b>' . $arFields['USER_INDEX'] . '</b><br>'
    . '<br>';

  
  if (in_array($arDelivery['ID'], Array(3, 67))) // Самовывоз
    $arFields['USER_DATE'] = '';

  
  $DETAIL_INFO .= Loc::getMessage('SALE_NEW_ORDER_COMMENT').': <b>' . (empty($arFields['USER_DESCRIPTION']) ? '-' : $arFields['USER_DESCRIPTION']) . '</b><br>';

  
  if($eventName == 'SALE_NEW_ORDER' && LANGUAGE_ID == 'en') 
  {
    $ORDER_LIST = '';
    Loader::includeModule("sale");
    Loader::includeModule("highloadblock");
    $hlBlockId = HL_COLOR_ID;
    $hlblock = HighloadBlockTable::getById($hlBlockId)->fetch();
    $entity = HighloadBlockTable::compileEntity($hlblock);
    $PsuTaskControl = $entity->getDataClass();

    $order = Bitrix\Sale\Order::load($orderId);
    $basket = $order->getBasket();
    foreach ($basket as $basketItem) {
      $mxResult = CCatalogSku::GetProductInfo($basketItem->getProductId());
      if (is_array($mxResult)) {
        $arProduct = CCatalogProduct::GetByIDEx($mxResult['ID']);
        $arProductOffer = CCatalogProduct::GetByIDEx($basketItem->getProductId());

        $color = '';
        $rsList = $PsuTaskControl::getList(['filter' => ["UF_XML_ID" => $arProductOffer["PROPERTIES"]["COLOR"]["VALUE"]] ]);
        if ($el = $rsList->fetch()) {
          $color = $el["UF_NAME_EN"];
        }

        $ORDER_LIST .= $arProduct['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE'].' ('.$arProductOffer['PROPERTIES']['SIZES']['VALUE_ENUM'].' ('.$color.')) [Size: '.$arProductOffer['PROPERTIES']['SIZES']['VALUE_ENUM'].'; Color: '.$color.'; Article: '.$arProduct['PROPERTIES']['CML2_ARTICLE']['VALUE'].'] - '. $basketItem->getQuantity() . 'pcs. x '.CurrencyFormat($basketItem->getPrice(), $order->getCurrency()).'<br />';
      }
    }

    $arFields['ORDER_LIST'] = $ORDER_LIST;
  }
  

  $arFields['DETAIL_INFO'] = $DETAIL_INFO;
  if($eventName == 'SALE_NEW_ORDER') {
    $arFields['STATUS_INFO'] = $STATUS_INFO;
  }
  
  
  logArlix('customOnOrderSendEmail finish ORDER_ID = '. $orderId, 'log_create_order.txt', __FILE__ .' :'. __LINE__);  
}


function updateNormalWeightFrom1C()
{
  if (CModule::IncludeModule("catalog")) {
    $arFilter = Array("IBLOCK_ID" => CATALOG_ID, "ACTIVE" => "Y", "INCLUDE_SUBSECTIONS" => "Y");
    $rs = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 100000), Array("ID", "PROPERTY_ATT_WEIGHT"));
    while ($ob = $rs->GetNextElement()) {
      $arFields = $ob->GetFields();
      $offers = CCatalogSKU::getOffersList($arFields['ID']);

      if (!empty($offers[$arFields['ID']])) {
        foreach ($offers[$arFields['ID']] as $offer) {
          $arOfferFields = Array('WEIGHT' => $arFields['PROPERTY_ATT_WEIGHT_VALUE']);
          CCatalogProduct::Update($offer['ID'], $arOfferFields);
        }
      }

    }
  }
}

class sendOrderInfo
{
  //при смене способа оплаты
  function sendInfoOrderByTypePayment(Bitrix\Main\Event $event)
  {
    $arValues = $event->getParameter('VALUES');
    if ($arValues['PAY_SYSTEM_ID'] > 0) {
      $parameters = $event->getParameters();
      $order = $parameters['ENTITY']->getOrder();

      if(!$order->getDiscount()->isOrderNew()){
        self::sendModifyOrderMessage($order);
      }

    }
  }

  function sendInfoOrderByTypeDelivery(Bitrix\Main\Event $event)
  {
    $arValues = $event->getParameter('VALUES');
    if ($arValues['DELIVERY_ID'] > 0) {
      $parameters = $event->getParameters();
      $order = $parameters['ENTITY']->getOrder();

      if(!$order->getDiscount()->isOrderNew()) {
        self::sendModifyOrderMessage($order);
      }

    }
  }

  function sendInfoOrderByModifyItem(Bitrix\Main\Event $event)
  {
    
    $parameters = $event->getParameters();
    $orderId = $parameters['ENTITY']->getBasketItem()->getField('ORDER_ID');
    
    logArlix('sendInfoOrderByModifyItem start ORDER_ID = '. $orderId, 'log_create_order.txt', __FILE__ .' :'. __LINE__);
    
    $order = Sale\Order::load($orderId);

    $arValues = $event->getParameter('VALUES');
    if(!self::checkSendedMessage($orderId) && !isset($arValues['RESERVED_QUANTITY']) && !$parameters['ENTITY']->getCollection()->getShipment()->getCollection()->getOrder()->getDiscount()->isOrderNew()) {
      self::sendModifyOrderMessage($order);
    }

  }

  //проверить,и была ли отправка сообщения
  static function checkSendedMessage($orderId)
  {
    logArlix('checkSendedMessage start ORDER_ID = '. $orderId, 'log_create_order.txt', __FILE__ .' :'. __LINE__);
    $currentDateTime = new \DateTime();
    $dateTime = new \DateTime('-30 seconds');
    $filter = [
      'EVENT_NAME' => 'ORDER_CHANGE',
      '>=DATE_INSERT' => $dateTime->format('d.m.Y H:i:s'),
      '<=DATE_INSERT' => $currentDateTime->format('d.m.Y H:i:s')
    ];

    $dbResEvent = Bitrix\Main\Mail\Internal\EventTable::getlist([
      'order' => ['ID' => 'DESC'],
      'filter' => $filter
    ]);
    $isSend = false;
    while($arRes = $dbResEvent->fetch()) {
      if($arRes['C_FIELDS']['ORDER_ID'] == $orderId) {
        $isSend = true;
        break;
      }
    }

    return $isSend;
  }

  static function sendModifyOrderMessage(Bitrix\Sale\Order $order)
  {
    $arOrderVals = $order->getFields()->getValues();

    $propertyCollection = $order->getPropertyCollection();
    $arPropCollection = $propertyCollection->getArray();

    $userFio = $propertyCollection->getPayerName()->getValue();

    foreach ($arPropCollection['properties'] as $propCollection) {
      $arOrderProps[$propCollection['CODE']] = $propCollection['VALUE'][0];
    }

    $orderId = $order->getId();
    $orderDate = $order->getField("DATE_INSERT")->format("Y-m-d");
    $orderDate2 = $order->getField("DATE_INSERT")->format("H:i:s");

    $arStatus = CSaleStatus::GetByID($arOrderVals['STATUS_ID']);
    $nameStatus = $arStatus['NAME'];

    $city = '';
    if ($arOrderProps['LOCATION']) {
      $arLocs = CSaleLocation::GetByID($arOrderProps['LOCATION'], LANGUAGE_ID);
      $city = $arLocs['CITY_NAME'];
    }

    $arPaySys = CSalePaySystem::GetByID($arOrderVals['PAY_SYSTEM_ID'], $arOrderVals['PERSON_TYPE_ID']);

    $resultDelivery = \Bitrix\Sale\Delivery\Services\Table::getList(array(
      'filter' => array('ID' => $arOrderVals['DELIVERY_ID']),

    ));
    if ($delivery = $resultDelivery->fetch()) {
      $deliveryName = $delivery['NAME'];
    }

    //Корзина
    $basket = $order->getBasket();

    $basketItemsStr = 'Состав заказа<br />' . PHP_EOL;

    foreach ($basket as $basketItem) {
      $basketItemsStr .= $basketItem->getField('NAME') . ' &mdash; ' . $basketItem->getField('QUANTITY') . ' ' . $basketItem->getField('MEASURE_NAME') . '. &times; ' . number_format($basketItem->getField('PRICE'), 2, '.', ' ') . ' руб.<br>' . PHP_EOL;
    }

    if ($arOrderVals['PAY_SYSTEM_ID'] == 9 && $arOrderVals['PAYED'] != 'Y') {
      $PAYMENT_LINK = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/personal/order/' . $orderId . '/?by=' . $arOrderVals['USER_ID'] . '">Оплатить</a>';
    }

    $orderStatus = "
        Заказ номер: $orderId<br />
        Дата: $orderDate<br />
        Время: $orderDate2<br />
        <br />

        Статус заказа: $nameStatus. $PAYMENT_LINK<br />
        <br /> 
    ";

    $orderInfo = '<br>'
      . 'Стоимость товара: ' . number_format($arOrderVals['PRICE'] - $arOrderVals['PRICE_DELIVERY'], 2, '.', ' ') . ' руб.<br>' . PHP_EOL
      . 'Стоимость доставки: ' . number_format($arOrderVals['PRICE_DELIVERY'], 2, '.', ' ') . ' руб.<br>' . PHP_EOL
      . '<b>Итого: ' . number_format($arOrderVals['PRICE'], 2, '.', ' ') . ' руб.</b><br>' . PHP_EOL
      . '<br>' . PHP_EOL
      . 'Способ оплаты: <b>' . $arPaySys['NAME'] . '</b><br>' . PHP_EOL
      . 'Способ доставки: <b>' . $deliveryName . '</b><br>' . PHP_EOL
      . '<br>' . PHP_EOL
      . 'Детальная информация:<br>' . PHP_EOL
      . 'Имя: <b>' . $userFio . '</b><br>' . PHP_EOL
      . 'Телефон: <b>' . phoneFormat($arOrderProps['PHONE']) . '</b><br>' . PHP_EOL
      . 'E-mail: <b>' . $arOrderProps['EMAIL'] . '</b><br>' . PHP_EOL
      . '<br>' . PHP_EOL
      . 'Город доставки: <b>' . $city . '</b><br>' . PHP_EOL
      . 'Адрес доставки: <b>' . $arOrderProps['ADDRESS'] . '</b><br>' . PHP_EOL
      . '<br>' . PHP_EOL;

    if ($arOrderVals['USER_DESCRIPTION']) {
      $orderInfo .= 'Комментарий к заказу: <b>' . $arOrderVals['USER_DESCRIPTION'] . '</b><br>' . PHP_EOL;
    }

    $arSend = array(
      'ORDER_ID' => $orderId,
      'EMAIL' => $arOrderProps['EMAIL'],
      'ORDER_STATUS' => $orderStatus,
      'ORDER_INFO' => $orderInfo,
      'BASKET_ITEMS' => $basketItemsStr,
    );

    //AddMessage2Log('$arFields = '.print_r($arSend, true),'');

    CEvent::Send('ORDER_CHANGE', 's1', $arSend);
  }
}