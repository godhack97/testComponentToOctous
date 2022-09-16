<?php
	namespace Sodamoda\Core\Events;
	
	use \Bitrix\Main\Loader;
  use \Bitrix\Sale\Order;
  use \Bitrix\Main\Localization\Loc;
  use \Bitrix\Highloadblock\HighloadBlockTable;
	use \Bitrix\Main\Mail\Event;
  
  use \Bitrix\Main\Diag\Debug;
  use Sodamoda\Core\Bx\Core;
  use Sodamoda\Core\Bx\Crm;
  
  class Sale{
    public function OnOrderNewSendEmail($orderId, &$eventName, &$arFields)
    {
      //CModule::IncludeModule("sale");
      
      //logArlix(print_r($arFields, 1), 'myfile.txt', __FILE__ .' :'. __LINE__);
      logArlix('customOnOrderSendEmail start ORDER_ID = ' . $orderId, 'log_create_order.txt', __FILE__ . ' :' . __LINE__);
      
      if ($eventName == 'SALE_NEW_ORDER') {
        // Стандартный заголовок письма
        $arFields['SUBJECT'] = Loc::getMessage('SALE_NEW_ORDER_ORDER_NUM') . $orderId;
      }
      
      $dbProps = \CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $orderId));
      while ($row = $dbProps->Fetch()) {
        $arFields['USER_' . $row['CODE']] = $row['VALUE'];
      }
      
      $arOrder = \CSaleOrder::GetByID($orderId);
      
      setOrderOutOfStock($orderId);
      
      // Если заказ оформляет менеджер из группы 15, то он автоматически проставляется ответственным
      $order = Order::load($orderId);
      $responsibleId = $order->getField('RESPONSIBLE_ID');
      $currentUserId = $GLOBALS['USER']->GetID();
      if (!$responsibleId && $currentUserId) {
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
      
      $arPaySys = \CSalePaySystem::GetByID($arOrder['PAY_SYSTEM_ID'], $arOrder['PERSON_TYPE_ID']);
      $arFields['USER_PAY_SYSTEM'] = $arPaySys['NAME'];
      
      $arDelivery = \CSaleDelivery::GetByID($arOrder['DELIVERY_ID']);
      
      $arDelivery['NAME'] = getDeliveryName($arDelivery, $arOrder['DELIVERY_ID']);
      
      if ($arFields['USER_LOCATION']) {
        $arLocs = \CSaleLocation::GetByID($arFields['USER_LOCATION'], LANGUAGE_ID);
        $city = $arLocs['CITY_NAME'];
      }
      if ($eventName == 'SALE_NEW_ORDER') {
        $basket = $order->getBasket();
        $preorder = false;
        
        foreach ($basket as $basketItem) {
          $basketPropertyCollection = $basketItem->getPropertyCollection();
          $allPropProduct = $basketPropertyCollection->getPropertyValues();
          foreach ($allPropProduct as $prop) {
            switch ($prop['CODE']) {
              case 'PREORDER_PRODUCT':
                $preorder[$basketItem->getProductId()] = $prop['VALUE'] === 'Y';
                break;
            }
          }
        }
        
        $arProducts = [];
        foreach ($basket as $basketItem) {
          $mxResult = \CCatalogSku::GetProductInfo($basketItem->getField('PRODUCT_ID'));
          $res = \CIBlockElement::GetByID($mxResult['ID']);
          if ($ar_res = $res->GetNext()) {
            $itemProduct = $ar_res;
          }
          $size = '';
          $color = '';
          $article = '';
          $basketPropertyCollection = $basketItem->getPropertyCollection();
          foreach ($basketPropertyCollection->getPropertyValues() as $prop) {
            switch ($prop['CODE']) {
              case 'SIZES':
                $size = $prop;
                break;
              case 'COLOR':
                $color = $prop;
                break;
              case 'CML2_ARTICLE':
                $article = $prop;
                break;
            }
          }
          
          $props = '';
          if(!empty($color) && is_array($color)){
            $props .= mb_strtoupper($color['VALUE']).', ';
          }
          if(!empty($size) && is_array($size)){
            $props .= trim($size['VALUE']).', ';
          }
          if(!empty($article) && is_array($article)){
            $props .= 'арт. '.$article['VALUE'];
          }
          
          $arProducts[] = ($preorder[$basketItem->getField('PRODUCT_ID')] ? '[<b>ПРЕДЗАКАЗ</b>]&nbsp;' : '') . mb_strtoupper(trim($itemProduct['NAME'])) . ', ' . $props . ' &mdash; ' . round($basketItem->getField('QUANTITY')) . ' ' . $basketItem->getField('MEASURE_NAME') . '. &times; ' . number_format($basketItem->getField('PRICE'), 2, '.', ' ') . ' руб.<br>' . PHP_EOL;
        }
      }
      if ($eventName == 'SALE_NEW_ORDER') {
        $arStatus = \CSaleStatus::GetByID($arOrder['STATUS_ID'], LANGUAGE_ID);
      }
      $arDate = explode(' ', $arFields['ORDER_DATE']);
      
      if ($eventName == 'SALE_NEW_ORDER') {
        if (in_array($arPaySys['ID'], array(9, 14)) && $arOrder['PAYED'] != 'Y') // sberbank
        {
          $order = Order::load($orderId);
          $publicLink = \Bitrix\Sale\Helpers\Order::getPublicLink($order);
          
          //$PAYMENT_LINK = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/personal/order/' . $orderId . '/?by=' . $arOrder['USER_ID'] . '">Оплатить</a>';
          $PAYMENT_LINK = '<a href="' . $publicLink . '">' . Loc::getMessage('SALE_NEW_ORDER_PAY') . '</a>';
          $PAYMENT_DESC = '<b>' . Loc::getMessage('SALE_NEW_ORDER_EXPECT_PAYMENT') . '</b><br><br>';
        }
        
        if ($arPaySys['ID'] == 10) // courier Курьеру при получении
        {
          //logArlix('customOnOrderSendEmail $arPaySys[ID]='. $arPaySys['ID'], 'log_create_order.txt', __FILE__ .' :'. __LINE__);
          
          $arFields['EMAIL_PAID'] = 'paid@sodamoda.ru';
          $arFields['SUBJECT'] = $orderId . ' ' . Loc::getMessage('SALE_NEW_ORDER_NOT_PAID') . ' ' . ($city ? $city . ', ' : '') . $arDelivery['NAME'];
          
          if (in_array($arOrder['DELIVERY_ID'], array(4, 6, 5)))
            $MESSAGE_DELIVERY = Loc::getMessage('SALE_NEW_ORDER_DESCRIPTION') . '<br><br>';
          
          //logArlix('customOnOrderSendEmail $arPaySys[ID]='. $arPaySys['ID'], 'log_create_order.txt', __FILE__ .' :'. __LINE__);
        }
        
        $STATUS_INFO = Loc::getMessage('SALE_NEW_ORDER_ORDER_NUMBER') . ': <b>' . $orderId . '</b><br>'
          . Loc::getMessage('SALE_NEW_ORDER_DATE') . ': ' . $arDate[0] . '<br>'
          . Loc::getMessage('SALE_NEW_ORDER_TIME') . ': ' . $arDate[1] . '<br><br>'
          . Loc::getMessage('SALE_NEW_ORDER_STATUS_ORDER') . ': <b>' . $arStatus['NAME'] . ($PAYMENT_LINK ? '. ' . $PAYMENT_LINK : '') . '</b><br>'
          . ($PAYMENT_DESC ? $PAYMENT_DESC : '');
      }
      
      if ($eventName == 'SALE_ORDER_PAID') {
        $basket = $order->getBasket();
        $preorder = false;
        foreach ($basket as $basketItem) {
          $basketPropertyCollection = $basketItem->getPropertyCollection();
          $allPropProduct = $basketPropertyCollection->getPropertyValues();
          foreach ($allPropProduct as $prop) {
            switch ($prop['CODE']) {
              case 'PREORDER_PRODUCT':
                $preorder[$basketItem->getProductId()] = $prop['VALUE'] === 'Y';
                break;
            }
          }
        }
        $arFields['DELIVERY_DATA'] = $arDelivery;
        $arFields['SUBJECT_INFO'] = (!empty($city) ? $city . ', ' : '') . $arDelivery['NAME'];
        
        $arProducts = [];
        foreach ($basket as $basketItem) {
          $mxResult = \CCatalogSku::GetProductInfo($basketItem->getField('PRODUCT_ID'));
          $res = \CIBlockElement::GetByID($mxResult['ID']);
          if ($ar_res = $res->GetNext()) {
            $itemProduct = $ar_res;
          }
          $size = '';
          $color = '';
          $article = '';
          
          $basketPropertyCollection = $basketItem->getPropertyCollection();
          foreach ($basketPropertyCollection->getPropertyValues() as $prop) {
            switch ($prop['CODE']) {
              case 'SIZES':
                $size = $prop;
                break;
              case 'COLOR':
                $color = $prop;
                break;
              case 'CML2_ARTICLE':
                $article = $prop;
                break;
            }
          }
          $props = '';
          if(!empty($color) && is_array($color)){
            $props .= mb_strtoupper($color['VALUE']).', ';
          }
          if(!empty($size) && is_array($size)){
            $props .= trim($size['VALUE']).', ';
          }
          if(!empty($article) && is_array($article)){
            $props .= 'арт. '.$article['VALUE'];
          }
          
          $arProducts[] = ($preorder[$basketItem->getField('PRODUCT_ID')] ? '[<b>ПРЕДЗАКАЗ</b>]&nbsp;' : '') . mb_strtoupper(trim($itemProduct['NAME'])) . ', ' . $props . ' &mdash; ' . round($basketItem->getField('QUANTITY')) . ' ' . $basketItem->getField('MEASURE_NAME') . '. &times; ' . number_format($basketItem->getField('PRICE'), 2, '.', ' ') . ' руб.<br>' . PHP_EOL;
        }
  
        $crmCore = Crm::getInstance();
        $title = 'Заказ с сайта '.$order->getId();
  
        $dealsByTitle = Core::dealList([
          'filter' => ['STAGE_ID' => 'NEW', 'TITLE' => $title],
          'select' => ['ID']
        ])['result'];
        if(count($dealsByTitle) > 0){
          $crmData = [
            'fields' => [
              ['code' => 'status', 'value' => 'UC_6AFUQ3'],
            ],
          ];
    
          $propertyCollection = $order->getPropertyCollection();
          $phonePropValue = $propertyCollection->getPhone()->getValue();
          $emailPropValue = $propertyCollection->getUserEmail()->getValue();
    
          if (!empty($phonePropValue)) {
            $crmData['phone'] = str_replace(['+', ' ', '.', '-', '(', ')'], '', $phonePropValue);
          }
          if (isset($emailPropValue) && $emailPropValue) {
            $crmData['email'] = $emailPropValue;
          }
    
          $fioValue = $propertyCollection->getItemByOrderPropertyId(7)->getValue();
          $crmData['name'] = $fioValue;
          $crmCore->updateDeal($dealsByTitle[0]['ID'], $crmData);
        }
      }
      if ($eventName == 'SALE_ORDER_PAID') {
        $DETAIL_INFO = Loc::getMessage('SALE_NEW_ORDER_ORDER_NUMBER') . ': <b>' . $orderId . '</b><br>'
          . Loc::getMessage('SALE_NEW_ORDER_DATE') . ': ' . $arDate[0] . '<br>'
          . Loc::getMessage('SALE_NEW_ORDER_TIME') . ': ' . $arDate[1] . '<br><br>'
          . Loc::getMessage('SALE_NEW_ORDER_STATUS_ORDER') . ': <b>' . Loc::getMessage('SALE_NEW_ORDER_PAID') . '</b><br><br>';
        
        $DETAIL_INFO .= Loc::getMessage('SALE_NEW_ORDER_ORDER_LIST') . ':<br>';
        foreach ($arProducts as $item)
          $DETAIL_INFO .= $item;
      }
      
      if ($eventName == 'SALE_NEW_ORDER') {
        $ORDER_LIST = '';
        
        foreach ($arProducts as $item)
          $ORDER_LIST .= $item;
        
        $arFields['ORDER_LIST'] = $ORDER_LIST;
      }
      
      
      if (empty($order))
        $order = Order::load($orderId);
      
      $DETAIL_INFO .= '<br>'
        . Loc::getMessage('SALE_NEW_ORDER_GOODS_COST') . ': ' . CurrencyFormat($arOrder['PRICE'] - $arOrder['PRICE_DELIVERY'], $order->getCurrency()) . ' <br>'
        . Loc::getMessage('SALE_NEW_ORDER_DELIVERY_COST') . ': ' . CurrencyFormat($arOrder['PRICE_DELIVERY'], $order->getCurrency()) . ' <br>'
        . Loc::getMessage('SALE_NEW_ORDER_TOTAL') . '<b>: ' . CurrencyFormat($arOrder['PRICE'], $order->getCurrency()) . ' </b><br>'
        . '<br>'
        . Loc::getMessage('SALE_NEW_ORDER_PAYMENT_METHOD') . ': <b>' . Loc::getMessage('SALE_NEW_ORDER_PAYSYSTEM_' . $arPaySys['ID']) . '</b><br>'
        . Loc::getMessage('SALE_NEW_ORDER_DELIVERY_METHOD') . ': <b>' . $arDelivery['NAME'] . '</b><br>'
        . '<br>' . (($MESSAGE_DELIVERY && $eventName == 'SALE_NEW_ORDER') ? $MESSAGE_DELIVERY : '')
        . Loc::getMessage('SALE_NEW_ORDER_DETAILED') . ':<br>'
        . Loc::getMessage('SALE_NEW_ORDER_NAME') . ': <b>' . $arFields['USER_CONTACT_PERSON'] . '</b><br>'
        . Loc::getMessage('SALE_NEW_ORDER_PHONE') . ': <b>' . phoneFormat($arFields['USER_PHONE']) . '</b><br>'
        . Loc::getMessage('SALE_NEW_ORDER_EMAIL') . ': <b>' . $arFields['EMAIL'] . '</b><br>'
        . '<br>'
        . Loc::getMessage('SALE_NEW_ORDER_DELIVERY_CITY') . ': <b>' . $city . '</b><br>'
        . Loc::getMessage('SALE_NEW_ORDER_DELIVERY_ADDRESS') . ': <b>' . $arFields['USER_ADDRESS'] . '</b><br>'
        . Loc::getMessage('SALE_NEW_ORDER_POSTCODE') . ': <b>' . $arFields['USER_INDEX'] . '</b><br>'
        . '<br>';
      
      
      if (in_array($arDelivery['ID'], array(3, 67))) // Самовывоз
        $arFields['USER_DATE'] = '';
      
      
      $DETAIL_INFO .= Loc::getMessage('SALE_NEW_ORDER_COMMENT') . ': <b>' . (empty($arFields['USER_DESCRIPTION']) ? '-' : $arFields['USER_DESCRIPTION']) . '</b><br>';
      
      
      if ($eventName == 'SALE_NEW_ORDER' && LANGUAGE_ID == 'en') {
        $ORDER_LIST = '';
        Loader::includeModule('sale');
        Loader::includeModule('highloadblock');
        $hlBlockId = HL_COLOR_ID;
        $hlblock = HighloadBlockTable::getById($hlBlockId)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlblock);
        $PsuTaskControl = $entity->getDataClass();
        
        $order = Order::load($orderId);
        $basket = $order->getBasket();
        foreach ($basket as $basketItem) {
          $mxResult = \CCatalogSku::GetProductInfo($basketItem->getProductId());
          if (is_array($mxResult)) {
            $arProduct = \CCatalogProduct::GetByIDEx($mxResult['ID']);
            $arProductOffer = \CCatalogProduct::GetByIDEx($basketItem->getProductId());
            
            $color = '';
            $rsList = $PsuTaskControl::getList(['filter' => ['UF_XML_ID' => $arProductOffer['PROPERTIES']['COLOR']['VALUE']]]);
            if ($el = $rsList->fetch()) {
              $color = $el['UF_NAME_EN'];
            }
            
            $ORDER_LIST .= $arProduct['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE'] . ' (' . $arProductOffer['PROPERTIES']['SIZES']['VALUE_ENUM'] . ' (' . $color . ')) [Size: ' . $arProductOffer['PROPERTIES']['SIZES']['VALUE_ENUM'] . '; Color: ' . $color . '; Article: ' . $arProduct['PROPERTIES']['CML2_ARTICLE']['VALUE'] . '] - ' . $basketItem->getQuantity() . 'pcs. x ' . CurrencyFormat($basketItem->getPrice(), $order->getCurrency()) . '<br />';
          }
        }
        
        $arFields['ORDER_LIST'] = $ORDER_LIST;
      }
      
      $arFields['DETAIL_INFO'] = $DETAIL_INFO;
      if ($eventName == 'SALE_NEW_ORDER') {
        $arFields['STATUS_INFO'] = $STATUS_INFO;
      }
      
      logArlix('customOnOrderSendEmail finish ORDER_ID = ' . $orderId, 'log_create_order.txt', __FILE__ . ' :' . __LINE__);
    }
    //public function OnSaleOrderEntitySaved(){}
    public function onSaleAdminOrderInfoBlockShow(\Bitrix\Main\Event $event)
    {
      $order = $event->getParameter('ORDER');
      $propertyCollection = $order->getPropertyCollection();
      $ar = $propertyCollection->getArray();
      
      $arProps = [];
      foreach ($ar['properties'] as $v) {
        $arProps[$v['CODE']] = $v['VALUE'][0];
      }
      
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
      foreach ($basket as $basketItem) {
        $basketPropertyCollection = $basketItem->getPropertyCollection();
        $allPropProduct = $basketPropertyCollection->getPropertyValues();
        foreach ($allPropProduct as $prop) {
          switch ($prop['CODE']) {
            case 'PREORDER_PRODUCT':
              $preorder = $prop['VALUE'] === 'Y';
              break;
          }
        }
      }
      if ($preorder) {
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
      if ($paymentCollection->isExistsInnerPayment()) {
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
    public function OnSaleBeforeStatusOrderChange(\Bitrix\Main\Event $event)
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
    public function OnBeforeSalePaymentSetField(\Bitrix\Main\Event $event)
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
            if (in_array($arGroup['GROUP_ID'], [1, 14])) {
              $isAdmin = true;
            }
          }
          
          //      if (!$isAdmin) {
          //        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::ERROR, new \Bitrix\Sale\ResultError('У вас нет доступа к проведению оплаты заказа.', 'code'), 'sale');
          //      }
          
          //AddMessage2Log('OnBeforeSalePaymentSetField FINISH');
        }
      }
    }
    
    function OnSaleOrderSaved(\Bitrix\Main\Event $event)
    {
      $order = $event->getParameter('ENTITY');
      $isNew = $event->getParameter('IS_NEW');
      
      $ORDER_ID = $order->getId();
      
      logArlix('OnSaleOrderSavedCustom start $isNew = ' . $isNew . ' ORDER_ID = ' . $ORDER_ID, 'log_create_order.txt', __FILE__ . ' :' . __LINE__);
      
      if ($isNew) {
        $basket = $order->getBasket();
        foreach ($basket as $basketItem) {
          $productId = $basketItem->getProductId();
          
          // Проверка поступления на склад
          \CBitrixComponent::includeComponentClass('arlix:product.subscribe');
          $isAvailable = \LikesComponent::isAvailable($productId);
          
          if (!$isAvailable) {
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
          
          //logArlix('OnSaleOrderSavedCustom finish $currentUserId = '. $currentUserId, 'log_create_order.txt', __FILE__ .' :'. __LINE__);
          
        }
        
        $crmCore = Crm::getInstance();
        $title = 'Заказ с сайта '.$order->getId();
        
        //на тесте офф
        $crmCore->createDeal($title, $order);
        
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
    function OnSaleOrderBeforeSaved(\Bitrix\Main\Event $event)
    {
      $order = $event->getParameter('ENTITY');
      $isNew = $event->getParameter('IS_NEW');
      
      $ORDER_ID = $order->getId();
      
      logArlix('OnSaleOrderSavedCustom start $isNew = ' . $isNew . ' ORDER_ID = ' . $ORDER_ID, 'log_create_order.txt', __FILE__ . ' :' . __LINE__);
      
      if ($isNew) {
        $basket = $order->getBasket();
        foreach ($basket as $basketItem) {
          $productId = $basketItem->getProductId();
          
          // Проверка поступления на склад
          \CBitrixComponent::includeComponentClass('arlix:product.subscribe');
          $isAvailable = \LikesComponent::isAvailable($productId);
          
          if (!$isAvailable) {
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
        
        $paymentCollection = $order->getPaymentCollection();
        if ($paymentCollection->isExistsInnerPayment()) {
          $payment = $paymentCollection[0];
          $propertyCollection = $order->getPropertyCollection();
          $orderPayBonus = $propertyCollection->getItemByOrderPropertyId(37);
          $orderPayBonus->setValue($payment->getSum());
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
    //при смене способа оплаты
    public function OnSalePaymentEntitySaved(\Bitrix\Main\Event $event)
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
    public function OnSaleShipmentEntitySaved(\Bitrix\Main\Event $event)
    {
      $arValues = $event->getParameter('VALUES');
      if ($arValues['DELIVERY_ID'] > 0) {
        $parameters = $event->getParameters();
        $order = $parameters['ENTITY']->getOrder();
        
        if(!$order->getDiscount()->isOrderNew()) {
          self::sendModifyOrderMessage($order);
          
          $crmCore = Crm::getInstance();
          $title = 'Заказ с сайта '.$order->getId();
  
          $dealsByTitle = Core::dealList([
            'filter' => ['TITLE' => $title],
            'select' => ['ID']
          ])['result'];
          if(count($dealsByTitle) > 0){
            $crmData = [
              'fields' => [
                ['code' => 'status', 'value' => '1'],
              ],
            ];
    
            $propertyCollection = $order->getPropertyCollection();
            $phonePropValue = $propertyCollection->getPhone()->getValue();
            $emailPropValue = $propertyCollection->getUserEmail()->getValue();
    
            if (!empty($phonePropValue)) {
              $crmData['phone'] = str_replace(['+', ' ', '.', '-', '(', ')'], '', $phonePropValue);
            }
            if (isset($emailPropValue) && $emailPropValue) {
              $crmData['email'] = $emailPropValue;
            }
    
            $fioValue = $propertyCollection->getItemByOrderPropertyId(7)->getValue();
            $crmData['name'] = $fioValue;
            $crmCore->updateDeal($dealsByTitle[0]['ID'], $crmData);
          }
        }
      }
    }
    public function sendInfoOrderByModifyItem(\Bitrix\Main\Event $event)
    {
      $parameters = $event->getParameters();
      $orderId = $parameters['ENTITY']->getBasketItem()->getField('ORDER_ID');
      
      logArlix('sendInfoOrderByModifyItem start ORDER_ID = '. $orderId, 'log_create_order.txt', __FILE__ .' :'. __LINE__);
      
      $order = Order::load($orderId);
      
      $arValues = $event->getParameter('VALUES');
      if(!self::checkSendedMessage($orderId) && !isset($arValues['RESERVED_QUANTITY']) && !$parameters['ENTITY']->getCollection()->getShipment()->getCollection()->getOrder()->getDiscount()->isOrderNew()) {
        self::sendModifyOrderMessage($order);
      }
    }
    
    public function OnSaleComponentOrderOneStepComplete($ID, $arOrder, $arParams)
    {
      logArlix('OnSaleComponentOrderOneStepComplete start $ORDER_ID = ' . $ID, 'log_create_order.txt', __FILE__ . ' :' . __LINE__);
      if ($ID) {
        $order = Order::load($ID);
        
        $request = \Bitrix\Main\Context::getCurrent()->getRequest();
        $USER_SELECTION_ID = (int)$request->getPost('user_selection_id');
        
        logArlix('OnSaleComponentOrderOneStepComplete $USER_SELECTION_ID = ' . $USER_SELECTION_ID, 'log_create_order.txt', __FILE__ . ' :' . __LINE__);
        
        if ($USER_SELECTION_ID > 0 && $USER_SELECTION_ID != (int)$order->getField('USER_ID')) {
          logArlix('OnSaleComponentOrderOneStepComplete $USER_SELECTION_ID = ' . $USER_SELECTION_ID, 'log_create_order.txt', __FILE__ . ' :' . __LINE__);
          $order->setFieldNoDemand(
            'USER_ID',
            $USER_SELECTION_ID
          );
          $order->save();
        }
      }
      
      logArlix('OnSaleComponentOrderOneStepComplete finish $ID=' . $ID, 'log_create_order.txt', __FILE__ . ' :' . __LINE__);
    }
    
    public function generateCoupon($ID, $val)
    {
      $sectionSertificateID = 121;
      if ($val === 'Y') {
        if (!Loader::includeModule('sale') || !Loader::includeModule('iblock')) return;
        
        $orderId = $ID;
        $order = Order::load($orderId);
        $basket = $order->getBasket();
        
        $arProductIDs = $arBasketItems = [];
        
        foreach ($basket as $basketItem) {
          $arProductIDs[] = $basketItem->getProductId();
          $arBasketItems[$basketItem->getProductId()] = [
            'PRICE' => $basketItem->getPrice(),
            'QUANTITY' => $basketItem->getQuantity()
          ];
        }
        
        $order = $basket->getOrder();
        $siteId = $order->getSiteId();
        $currency = $order->getCurrency();
        
        $rsElements = \Bitrix\Iblock\ElementTable::getList([
          'select' => ['ID'],
          'filter' => ['ID' => $arProductIDs, 'IBLOCK_SECTION_ID' => $sectionSertificateID]
        ]);
        foreach ($arProductIDs as $productID){
          $discountID = 0;
          switch ($productID){
            case 6696:
              $discountID = 36;
              break;
            case 6424:
              $discountID = 24;
              break;
          }
          if ($discountID > 0) {
            for ($i = 0; $i < $arBasketItems[$productID]['QUANTITY']; $i++) {
              $coupon = \Bitrix\Sale\Internals\DiscountCouponTable::generateCoupon(true);
              $activeFrom = new \Bitrix\Main\Type\DateTime();
              $activeTo = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime('+1 years'));
              if ($siteId == 's2') {
                $description = 'For order №' . $orderId;
              }
              else {
                $description = 'Для заказа №' . $orderId;
              }
              $addDb = \Bitrix\Sale\Internals\DiscountCouponTable::add([
                'DISCOUNT_ID' => $discountID,
                'COUPON' => $coupon,
                'TYPE' => \Bitrix\Sale\Internals\DiscountCouponTable::TYPE_ONE_ORDER,
                //'ACTIVE_FROM' => '',
                //'ACTIVE_TO' => '',
                'MAX_USE' => 1,
                'DESCRIPTION' => $description,
                'ACTIVE_FROM' => $activeFrom,
                'ACTIVE_TO' => $activeTo
              ]);
              
              if ($addDb->isSuccess()) {
                $propertyCollection = $order->getPropertyCollection();
                $email = $propertyCollection->getUserEmail()->getValue();
                
                $arEventFields = [
                  'EMAIL_ORDER' => $email,
                  'COUPON' => $coupon,
                  'COUPON_PRICE' => number_format($arBasketItems[$productID]['PRICE'], 0, '', ' '),
                  'IMAGE_URL' => $arBasketItems[$productID]['PRICE'],
                  'ACTIVE_TO' => FormatDate('d F Y года', $activeTo->getTimestamp(), time() + \CTimeZone::GetOffset())
                ];
                
                \CEvent::Send('GENERATE_COUPON', $siteId, $arEventFields, 'Y', '', [], ($siteId == 's2') ? 'en' : 'ru');
              }
            }
          }
        }
        /*

        while ($arElements = $rsElements->Fetch()) {
          $arActions['CLASS_ID'] = 'CondGroup';
          $arActions['DATA']['All'] = 'AND';
          $arActions['CLASS_ID'] = 'CondGroup';
          $arActions['CHILDREN'][0]['CLASS_ID'] = 'ActSaleBsktGrp';
          $arActions['CHILDREN'][0]['DATA']['Type'] = 'Discount';
          $arActions['CHILDREN'][0]['DATA']['Value'] = $arBasketItems[$arElements['ID']]['PRICE'];
          $arActions['CHILDREN'][0]['DATA']['Unit'] = 'CurAll';
          $arActions['CHILDREN'][0]['DATA']['All'] = 'OR';
          $arActions['CHILDREN'][0]['DATA']['True'] = 'True';

          $arConditions['CLASS_ID'] = 'CondGroup';
          $arConditions['DATA']['All'] = 'AND';
          $arConditions['DATA']['True'] = 'True';
          $arConditions['CHILDREN'] = '';

          $arFields = [
            'LID' => $siteId,
            'NAME' => 'Скидка по сертификату ' . $arBasketItems[$arElements['ID']]['PRICE'] . 'руб.',
            'PRIORITY' => 900,
            'CURRENCY' => $currency,
            'ACTIVE' => 'Y',
            'USER_GROUPS' => [2],
            'ACTIONS' => $arActions,
            'CONDITIONS' => $arConditions,
            'LAST_LEVEL_DISCOUNT' => 'Y'
          ];
          if ($siteId == 's2') {
            $arFields['NAME'] = 'Certificate discount ' . $arBasketItems[$arElements['ID']]['PRICE'] . ' $';
          }

          $rsSearchDiscount = \CSaleDiscount::GetList(
            ['SORT' => 'DESC'],
            [
              'LID' => 's1',
              'ACTIVE' => 'Y',
              'ACTIONS' => serialize($arActions),
            ],
            false,
            false,
            ['ID', 'ACTIONS']
          );
          if ($arSearchDiscount = $rsSearchDiscount->Fetch()){
            $discountID = $arSearchDiscount['ID'];
          }
          else{
            $discountID = \CSaleDiscount::Add($arFields);
          }
          //$discountID = \CSaleDiscount::Add($arFields);

        }
        */
      }
    }
    
    public function OnSaleComponentOrderProperties(&$arUserResult, \Bitrix\Main\HttpRequest $request, &$arParams, &$arResult)
    {
      global $USER, $APPLICATION;
      
      if ($USER->IsAuthorized()) {
        $userEmail = $request->get('ORDER_PROP_6');
        if (!empty($userEmail)) {
          $filter = [
            'EMAIL' => $userEmail,
          ];
          $rsUsers = \CUser::GetList(($by = 'personal_country'), ($order = 'desc'), $filter); // выбираем пользователей
          while ($arUser = $rsUsers->Fetch()) {
            $userObj = $arUser;
          };
          $userFN = $userObj['NAME']; //Имя
          $userLN = $userObj['LAST_NAME']; //Фамилия
          $userSN = $userObj['SECOND_NAME']; //Отчество
          
          if (empty($userLN)) {
            $fioField = $request->get('ORDER_PROP_7');
            if (!empty($fioField)) {
              $parseFIO = explode(' ', $fioField); //0 - Фамилия, 1 - Имя, 2 - Отчество
              
              $fields = [];
              if (empty($userLN)) {
                $fields['LAST_NAME'] = $parseFIO[0];
              }
              if (count($parseFIO) > 1 && empty($userFN)) {
                $fields['NAME'] = $parseFIO[1];
              }
              if (count($parseFIO) > 2 && empty($userSN)) {
                $fields['SECOND_NAME'] = $parseFIO[2];
              }
              if (count($fields) > 0) {
                $user = new \CUser;
                $user->Update($userObj['ID'], $fields);
              }
            }
          }
        }
        //if()
      }
      foreach ($arUserResult['ORDER_PROP'] as $kProp => $vProp) {
        switch ($kProp) {
          case 2:
            if ($request->get('ORDER_PROP_2') && !empty($request->get('ORDER_PROP_2'))) {
              $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_2');
	            $arResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_2');
            }
						else {
							if(!empty($APPLICATION->get_cookie('ORDER_PROP_' . $kProp))){
								$arUserResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
								$arResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
							}
              else {
                $arUserResult['ORDER_PROP'][$kProp] = '0000073738';
                $arResult['ORDER_PROP'][$kProp] = '0000073738';
              }
						}
            break;
          case 3:
            if ($request->get('ORDER_PROP_3') && !empty($request->get('ORDER_PROP_3'))) {
              $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_3');
	            $arResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_3');
            }
            else {
              if(!empty($APPLICATION->get_cookie('ORDER_PROP_' . $kProp))){
                $arUserResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
                $arResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
              }
            }
            break;
          case 4:
            if ($request->get('ORDER_PROP_4') && !empty($request->get('ORDER_PROP_4'))) {
              $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_4');
	            $arResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_4');
            }
            else {
              if(!empty($APPLICATION->get_cookie('ORDER_PROP_' . $kProp))){
                $arUserResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
                $arResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
              }
            }
            break;
          case 5:
            if ($request->get('ORDER_PROP_5') && !empty($request->get('ORDER_PROP_5'))) {
              $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_5');
	            $arResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_5');
            }
            else {
              if(!empty($APPLICATION->get_cookie('ORDER_PROP_' . $kProp))){
                $arUserResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
                $arResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
              }
            }
            break;
          case 6:
            if ($request->get('ORDER_PROP_6') && !empty($request->get('ORDER_PROP_6'))) {
              $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_6');
	            $arResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_6');
            }
            else {
              if(!empty($APPLICATION->get_cookie('ORDER_PROP_' . $kProp))){
                $arUserResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
                $arResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
              }
            }
            break;
          case 7:
            if ($request->get('ORDER_PROP_7') && !empty($request->get('ORDER_PROP_7'))) {
              $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_7');
	            $arResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_7');
            }
            else {
              if(!empty($APPLICATION->get_cookie('ORDER_PROP_' . $kProp))){
                $arUserResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
                $arResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
              }
            }
            break;
          case 14:
            if ($request->get('ORDER_PROP_14') && !empty($request->get('ORDER_PROP_14'))) {
              $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_14');
	            $arResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_14');
            }
            else {
              if(!empty($APPLICATION->get_cookie('ORDER_PROP_' . $kProp))){
                $arUserResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
                $arResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
              }
            }
            break;
          case 15:
            if ($request->get('ORDER_PROP_15') && !empty($request->get('ORDER_PROP_15'))) {
              $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_15');
	            $arResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_15');
            }
            else {
              if(!empty($APPLICATION->get_cookie('ORDER_PROP_' . $kProp))){
                $arUserResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
                $arResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
              }
            }
          case 17:
            if (preg_match('/^375/', $request->get('ORDER_PROP_17'))) {
              if ($request->get('ORDER_PROP_17') && !empty($request->get('ORDER_PROP_17'))) {
                $arUserResult['ORDER_PROP'][$kProp] = '+' . $request->get('ORDER_PROP_17');
	              $arResult['ORDER_PROP'][$kProp] = '+' . $request->get('ORDER_PROP_17');
              }
            }
            else {
              
              if ($request->get('ORDER_PROP_17') && !empty($request->get('ORDER_PROP_17'))) {
                $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_17');
	              $arResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_17');
              }
              else {
                if(!empty($APPLICATION->get_cookie('ORDER_PROP_' . $kProp))){
                  $arUserResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
                  $arResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
                }
              }
              
            }
            break;

//            if (preg_match('/^375/', $request->get('ORDER_PROP_17'))) {
//              if ($request->get('ORDER_PROP_17') && !empty($request->get('ORDER_PROP_17'))) {
//                $arUserResult['ORDER_PROP'][$kProp] = '+' . $request->get('ORDER_PROP_17');
//	              $arResult['ORDER_PROP'][$kProp] = '+' . $request->get('ORDER_PROP_17');
//              }
//            }
//            else {
//              if(!empty($APPLICATION->get_cookie('ORDER_PROP_' . $kProp))){
//                $arUserResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
//                $arResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
//              }
//            }
//            break;
          case 38:
            if ($request->get('ORDER_PROP_38') && !empty($request->get('ORDER_PROP_38'))) {
              $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_38');
	            $arResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_38');
            }
            else {
              if(!empty($APPLICATION->get_cookie('ORDER_PROP_' . $kProp))){
                $arUserResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
                $arResult['ORDER_PROP'][$kProp] = $APPLICATION->get_cookie('ORDER_PROP_' . $kProp);
              }
            }
            break;
        }
      }
	
    }
    public function OnSaleComponentOrderOneStepPersonType(&$arResult, &$arUserResult)
    {
      global $USER;
      
      if($USER->IsAuthorized())
      {
        if (in_array(12, $USER->GetUserGroupArray())) {
          $personType = 2;
        } else {
          $personType = 1;
        }
        if(empty($arUserResult['PAY_SYSTEM_ID']) && empty($arUserResult['DELIVERY_ID'])){
          foreach($arResult['PERSON_TYPE'] as $key => $type){
            if($type['CHECKED'] == 'Y'){
              $arResult['PERSON_TYPE'][$key]['CHECKED'] = '';
            }
          }
          
          $arResult['PERSON_TYPE'][$personType]['CHECKED'] = 'Y';
          $arUserResult['PERSON_TYPE_ID'] = $personType;
        }
        if($USER->IsAdmin()){
          //print_r('<pre>');
          //print_r($arUserResult);
          //print_r('</pre>');
        }
      }
    }
    
    public function OnSaleStatusOrder($orderId, $status)
    {
      Loader::includeModule('sale');
      $order = \CSaleOrder::GetByID($orderId);
      $email = $order['USER_EMAIL'];
      $name = urlencode($order['USER_LAST_NAME']);
      if ($orderId > 0 && $order['CANCELED'] == 'N') {
        $obProps = \Bitrix\Sale\Internals\OrderPropsValueTable::getList(array('filter' => array('ORDER_ID' => $orderId, 'CODE' => array('PHONE', 'EMAIL'))));
        while ($propOrder = $obProps->Fetch()) {
          if ($propOrder['CODE'] == 'PHONE') {
            $phone = urlencode($propOrder['VALUE']);
          }
          if ($propOrder['CODE'] == 'EMAIL') {
            $email = $propOrder['VALUE'];
          }
        }
        // Заказ считается оплаченным
        //if ($status == 'P') {
        $statusMas = \CSaleStatus::GetByID($status);
        $arEventField = [
          'USER_PHONE' => $phone,
          'ORDER_ID' => $orderId,
          'ORDER_STATUS' => $statusMas['NAME']
        ];
        $sms = new \Bitrix\Main\Sms\Event('SMS_CHANGE_ORDER_STATUS', $arEventField);
        $sms->setSite('s1');
        $sms->setLanguage('ru');
        $result = $sms->send();
        //}
      }
      //return $arFields;
    }
    public function customDeliveryCalculate(\Bitrix\Main\Event $event)
    {
      //Debug::startTimeLabel('load_customDeliveryCalculate_order');
      
      /** @var Delivery\CalculationResult $baseResult */
      $baseResult = $event->getParameter('RESULT');
      $shipment = $event->getParameter('SHIPMENT');
      
      logArlix('customDeliveryCalculate start', 'log_shipment.txt', __FILE__ .' :'. __LINE__);
      
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
      
      //Debug::endTimeLabel('load_customDeliveryCalculate_order');
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
      
      $dbResEvent = \Bitrix\Main\Mail\Internal\EventTable::getlist([
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
    static function sendModifyOrderMessage(\Bitrix\Sale\Order $order)
    {
      $arOrderVals = $order->getFields()->getValues();
      
      $propertyCollection = $order->getPropertyCollection();
      $arPropCollection = $propertyCollection->getArray();
      
      $userFio = $propertyCollection->getPayerName()->getValue();
      
      foreach ($arPropCollection['properties'] as $propCollection) {
        $arOrderProps[$propCollection['CODE']] = $propCollection['VALUE'][0];
      }
      
      $orderId = $order->getId();
      $orderDate = $order->getField('DATE_INSERT')->format('Y-m-d');
      $orderDate2 = $order->getField('DATE_INSERT')->format('H:i:s');
      
      $arStatus = \CSaleStatus::GetByID($arOrderVals['STATUS_ID']);
      $nameStatus = $arStatus['NAME'];
      
      $city = '';
      if ($arOrderProps['LOCATION']) {
        $arLocs = \CSaleLocation::GetByID($arOrderProps['LOCATION'], LANGUAGE_ID);
        $city = $arLocs['CITY_NAME'];
      }
      
      $arPaySys = \CSalePaySystem::GetByID($arOrderVals['PAY_SYSTEM_ID'], $arOrderVals['PERSON_TYPE_ID']);
      
      $resultDelivery = \Bitrix\Sale\Delivery\Services\Table::getList(array(
        'filter' => array('ID' => $arOrderVals['DELIVERY_ID']),
      
      ));
      if ($delivery = $resultDelivery->fetch()) {
        $deliveryName = $delivery['NAME'];
      }
      
      //Корзина
      $basket = $order->getBasket();
      
      $basketItemsStr = 'Состав заказа<br />' . PHP_EOL;
      
      $preorder = false;
      
      foreach ($basket as $basketItem) {
        $basketPropertyCollection = $basketItem->getPropertyCollection();
        $allPropProduct = $basketPropertyCollection->getPropertyValues();
        foreach ($allPropProduct as $prop) {
          switch ($prop['CODE']) {
            case 'PREORDER_PRODUCT':
              $preorder[$basketItem->getProductId()] = $prop['VALUE'] === 'Y';
              break;
          }
        }
      }
      $arProducts = [];
      foreach ($basket as $basketItem) {
        $mxResult = \CCatalogSku::GetProductInfo($basketItem->getField('PRODUCT_ID'));
        $res = \CIBlockElement::GetByID($mxResult['ID']);
        if ($ar_res = $res->GetNext()) {
          $itemProduct = $ar_res;
        }
        $size = '';
        $color = '';
        $article = '';
        
        $basketPropertyCollection = $basketItem->getPropertyCollection();
        foreach ($basketPropertyCollection->getPropertyValues() as $prop) {
          switch ($prop['CODE']) {
            case 'SIZES':
              $size = $prop;
              break;
            case 'COLOR':
              $color = $prop;
              break;
            case 'CML2_ARTICLE':
              $article = $prop;
              break;
          }
        }
        $props = '';
        if(!empty($color) && is_array($color)){
          $props .= mb_strtoupper($color['VALUE']).', ';
        }
        if(!empty($size) && is_array($size)){
          $props .= trim($size['VALUE']).', ';
        }
        if(!empty($article) && is_array($article)){
          $props .= 'арт. '.$article['VALUE'];
        }
        
        $basketItemsStr .= ($preorder[$basketItem->getField('PRODUCT_ID')] ? '[<b>ПРЕДЗАКАЗ</b>]&nbsp;' : '') . mb_strtoupper(trim($itemProduct['NAME'])) . ', ' . $props . ' &mdash; ' . round($basketItem->getField('QUANTITY')) . ' ' . $basketItem->getField('MEASURE_NAME') . '. &times; ' . number_format($basketItem->getField('PRICE'), 2, '.', ' ') . ' руб.<br>' . PHP_EOL;
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
      
      \CEvent::Send('ORDER_CHANGE', 's1', $arSend);
    }
  }
  function setOrderOutOfStock($orderId)
  {
    $order = Order::load($orderId);
    $basket = $order->getBasket();
    
    $arItemsForMail = [];
    
    foreach ($basket as $basketItem) {
      $productId = $basketItem->getProductId();
      
      // артикул размер
      
      \CBitrixComponent::includeComponentClass('arlix:product.subscribe');
      $isAvailable = \LikesComponent::isAvailable($productId);
      
      if (!$isAvailable) {
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
        
        if ($locationId == '0000073738' || $locationId == '0000103664') { // || $locationId == '0000445112'
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
    if (count($arItemsForMail)) {
      $siteUrl = SITE_SERVER_PROTOCOL . SITE_SERVER_NAME;
      
      foreach ($arItemsForMail as $arItem) {
        $strItems .= '<a href="' . $siteUrl . $arItem['DETAIL_PAGE_URL'] . '">' . $arItem['NAME'] . ' арт. ' . $arItem['CML2_ARTICLE'] . '</a><br>';
        $strItemsTgr .= "<a href=\"" . $siteUrl . $arItem['DETAIL_PAGE_URL'] . "\">" . $arItem['NAME'] . ' арт. ' . $arItem['CML2_ARTICLE'] . "</a>\n";
      }
      
      $arEventFields = array(
        'ORDER_ID' => $orderId,
        'ITEMS' => $strItems,
      );
      
      \CEvent::Send('SALE_NEW_ORDER_AVAILABLE_CHECK', SITE_ID, $arEventFields);
      
      telegramToChannel("Оформлен заказ <a href=\"" . $siteUrl . '/bitrix/admin/sale_order_view.php?ID=' . $arEventFields['ORDER_ID'] . "&lang=ru\">" . $arEventFields['ORDER_ID'] . "</a> с отсутствующими остатками:\n" . $strItemsTgr);
    }
  }