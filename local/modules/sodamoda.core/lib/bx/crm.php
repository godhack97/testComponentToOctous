<?php
  
  namespace Sodamoda\Core\Bx;
  
  class Crm
  {
    private static $instance = null;
    
    function addProductToDeal($dealId, $productId, $newDeal = false)
    {
      $products = Core::dealProductrowsGet($dealId)['result'] ?: array();
      $newProductList = [];
      
      if (count($products) > 0) {
        foreach ($products as $kProd => $vProd) {
          $newProductList[] = ['ID' => $vProd['ID'], 'PRODUCT_ID' => $productId, 'PRICE' => $vProd['PRICE'], 'QUANTITY' => 1];
        }
        Core::dealProductrowsSet($dealId, $newProductList);
      }
      //else {
        //$product = ['PRODUCT_ID' => $productId, 'PRICE' => $productPrice, 'QUANTITY' => 1];
        //$newProductList[] = $product;
        //Core::dealProductrowsSet($dealId, $newProductList);
      //}
      
      $latest_product_title = '';
      $latest_products = Core::dealProductrowsGet($dealId)['result'];
      $latest_products = array_reverse($latest_products);
      
      foreach ($latest_products as $product) {
        if ($product['PRODUCT_ID'] == $productId) {
          $latest_product_title = $product['PRODUCT_NAME'];
          break;
        }
      }
      
      if (!$newDeal) {
        Core::crmTimelineCommentAdd([
          'fields' => [
            'ENTITY_ID' => $dealId,
            'ENTITY_TYPE' => 'deal',
            'COMMENT' => 'Внимание: автоматически добавлен в сделку новый товар - ' . $latest_product_title
          ]
        ]);
      }
    }
    
    function updateDeal($dealId, $data)
    {
      if (!empty($data['phone'])) {
        $data['phone'] = str_replace(['+', ' ', '.', '-', '(', ')'], '', $data['phone']);
      }
      
      if (isset($data['email']) && $data['email']) {
        $dataDeal['EMAIL'] = [['VALUE' => $data['email'], 'VALUE_TYPE' => 'WORK']];
        $contact = Core::contactList([
          'filter' => ['EMAIL' => $data['email']]
        ])['result'];
      }
      
      if (!$contact) {
        $contact = Core::contactList([
          'filter' => ['PHONE' => $data['phone']]
        ])['result'];
      }
      
      $names = explode(' ', trim($data['name']));
      if (count($names) > 0) {
        $data['first_name'] = $names[0];
        
        if (isset($names[1])) {
          $data['last_name'] = $names[1];
        }
      }
      else {
        $data['first_name'] = $data['name'];
      }
      
      $dataDeal = [
        'NAME' => $data['name'],
        'HAS_PHONE' => 'Y',
      ];
      
      $deal = Core::dealGet($dealId)['result'];
      
      if (!empty($deal['PHONE'])) {
        $dataDeal['PHONE'] = [['ID' => $deal['PHONE'][0]['ID'], 'VALUE' => $data['phone'], 'VALUE_TYPE' => 'WORK']];
      }
      else {
        $dataDeal['PHONE'] = [['VALUE' => $data['phone'], 'VALUE_TYPE' => 'WORK']];
      }
      if (!empty($deal['EMAIL'])) {
        $dataDeal['EMAIL'] = [['ID' => $deal['EMAIL'][0]['ID'], 'VALUE' => $data['email'], 'VALUE_TYPE' => 'WORK']];
      }
      else {
        $dataDeal['EMAIL'] = [['VALUE' => $data['email'], 'VALUE_TYPE' => 'WORK']];
      }
      
      if (isset($data['last_name'])) {
        $dataDeal['LAST_NAME'] = $data['last_name'];
      }
      if ($contact) {
        $dataDeal['CONTACT_ID'] = $contact[0]['ID'];
      }
      
      foreach ($data['fields'] as $field) {
        switch ($field['code']) {
          case 'payment':
            $dataDeal['UF_CRM_1628621924030'] = $field['value'];
            break;
          case 'payment_type':
            $dataDeal['UF_CRM_1629895839'] = $field['value'];
            break;
          case 'payment_credit':
            $dataDeal['UF_CRM_1629895910'] = $field['value'];
            break;
          case 'status':
            $dataDeal['STAGE_ID'] = $field['value'];
            $dataDeal['CATEGORY_ID'] = 3;
            break;
          case 'fieldDiscount':
            $dataDeal['UF_CRM_1634209271'] = $field['value'];
            break;
          case 'fieldPartialPay':
            $dataDeal['UF_CRM_1634209333'] = $field['value'];
            break;
          case 'order_price':
            $dataDeal['UF_CRM_1635270433196'] = $field['value'];
            break;
        }
      }
      //if (isset($data['addProduct'])) {
        //$products = Core::dealProductrowsGet($dealId)['result'] ?: array();
        //$this->addProductToDeal($dealId, $this->getCrmProductID($data['addProduct']), true);
      //}
      $dealUpdate = Core::dealUpdata(
        $dealId,
        $dataDeal
      )['result'];
      
      if ($dealUpdate) {
        return array('result' => true);
      }
    }
    
    function createDeal($title, $order)
    {
      $data = [];
      $propertyCollection = $order->getPropertyCollection();
      $phonePropValue = $propertyCollection->getPhone()->getValue();
      $emailPropValue = $propertyCollection->getUserEmail()->getValue();
      $basket = $order->getBasket();
      if (!empty($phonePropValue)) {
        $data['phone'] = str_replace(['+', ' ', '.', '-', '(', ')'], '', $phonePropValue);
      }
      if (isset($emailPropValue) && $emailPropValue) {
        $data['email'] = $emailPropValue;
        $contact = Core::contactList([
          'filter' => ['EMAIL' => $emailPropValue]
        ])['result'];
        
        if (!$contact) {
          $contact = Core::contactList([
            'filter' => ['PHONE' => $data['phone']]
          ])['result'];
        }
        if ($contact) {
          $contact = $contact[0];
        }
      }
  
      $fioValue = $propertyCollection->getItemByOrderPropertyId(7)->getValue();
      $names = explode(' ', trim($fioValue));
      if (count($names) > 0) {
        $data['first_name'] = $names[0];
        
        if (isset($names[1])) {
          $data['last_name'] = $names[1];
        }
      }
      else {
        $data['first_name'] = $fioValue;
      }
  
      $basketComments = '';
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
              $size = $prop['VALUE'];
              break;
            case 'COLOR':
              $color = $prop['VALUE'];
              break;
            case 'CML2_ARTICLE':
              $article = $prop['VALUE'];
              break;
          }
        }
  
        $basketComments .= '
        '.$itemProduct['NAME'] . ', ' . $color . ', ' . $size . ', ' . $article . ' — ' . $basketItem->getQuantity() . ' шт. x ' . number_format($basketItem->getPrice(), 0, '', ' ') . ' руб.';
      }
      
      $categoryID = 0;
      $dataDeal = [
        'CATEGORY_ID' => $categoryID,
        'STAGE_ID' => 'NEW',
        'TITLE' => $title,
        'NAME' => $data['first_name'],
        'SOURCE_ID' => 'STORE',
        'HAS_PHONE' => 'Y',
        'UF_CRM_1643621776' => HTMLToTxt($basketComments, 'https://sodamoda.ru'),
      ];
      
      if (isset($data['last_name'])) {
        $dataDeal['LAST_NAME'] = $data['last_name'];
      }
      
      if (!$contact) {
        $contactId = Core::contactAdd([
          'NAME' => $data['first_name'],
          'LAST_NAME' => $data['last_name'],
          'OPENED' => 'Y',
          'ASSIGNED_BY_ID' => 15,
          'TYPE_ID' => 'CLIENT',
          'SOURCE_ID' => 'STORE',
          'EMAIL' => [['VALUE' => $emailPropValue, 'VALUE_TYPE' => 'HOME']],
          'PHONE' => [['VALUE' => $data['phone'], 'VALUE_TYPE' => 'WORK']]
        ])['result'];
        
        $contact = Core::contactGet($contactId)['result'];
      }
      
      $dataDeal['ASSIGNED_BY_ID'] = 15;
      
      /*
      if ($contact) {
        $dealsByContact = Core::dealList([
          'filter' => ['STAGE_ID' => 'NEW', 'CONTACT_ID' => $contact['ID'], 'CATEGORY_ID' => $categoryID],
          'select' => ['ID']
        ])['result'];
        
        if (count($dealsByContact) > 0) {
          //$this->addProductToDeal($dealsByContact[0]['ID'], $this->getCrmProductID($productIDStart));
          $crmData = [
            'orderId' => $title,
            'name' => $fioValue,
            'phone' => $data['mobilephone'],
            'email' => $data['email'],
            'fields' => [],
          ];
          $crmDealID = $dealsByContact[0]['ID'];
          
          $this->updateDeal($crmDealID, $crmData);
          return;
        }
      }
      */
      
      $dataDeal['CONTACT_ID'] = $contact['ID'];
      
      //var_dump($dataDeal);
      $dealId = Core::dealAdd($dataDeal)['result'];
      $deal = Core::dealGet($dealId)['result'];
  
      $text = 'Сделка ' . $dealId . ' создан автоматически с сайта. <a href="https://crmsoda.ru/crm/deal/details/' . $dealId . '/">Подробнее</a>';
      Core::imNotify($deal['ASSIGNED_BY_ID'], $text);
    }
    
    //base singleton
    public static function getInstance()
    {
      if (null === self::$instance) {
        self::$instance = new self();
      }
      return self::$instance;
    }
    
    private function __clone(){}
    private function __construct(){}
    private function __sleep(){}
    private function __wakeup(){}
  }