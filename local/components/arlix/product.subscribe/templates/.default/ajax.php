<?
  
  use \Bitrix\Main\Loader,
    \Bitrix\Main\Localization\Loc,
    \Bitrix\Highloadblock\HighloadBlockTable,
    \Bitrix\Main\Application,
    Bitrix\Main\UserPhoneAuthTable,
    \Bitrix\Main\Web\Cookie;
  
  define('STOP_STATISTICS', true);
  define('NO_AGENT_CHECK', true);
  define("NO_KEEP_STATISTIC", true);
  define("NO_AGENT_STATISTIC", "Y");
  
  $siteId = isset($_REQUEST['site_id']) && is_string($_REQUEST['site_id']) ? $_REQUEST['site_id'] : '';
  $siteId = substr(preg_replace('/[^a-z0-9_]/i', '', $siteId), 0, 2);
  if (!empty($siteId) && is_string($siteId)) {
    define('SITE_ID', $siteId);
  }
  
  require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
  
  $request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
  $request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);
  
  if (!check_bitrix_sessid() || !$request->isPost() || !Loader::includeModule('highloadblock')) {
    die();
  }
  
  global $USER;
  $result = [];
  
  
  if ($request->get('event') == 'subscribe') {
    
    $arProp = [];
    if (!empty($request->get('props'))) {
      $strProp = $request->get('props');
      parse_str($strProp, $arProp);
    }
    
    if (intval($arProp['productId']) > 0 && (!empty($arProp['phone']) || !empty($arProp['email']))) {
      $hlblock = HighloadBlockTable::getById(HL_SUBSCRIBE)->fetch();
      $entity = HighloadBlockTable::compileEntity($hlblock);
      $hlSubscribeTable = $entity->getDataClass();

      if (!empty(htmlspecialcharsbx($arProp['phone']))) {
        $countElements = $hlSubscribeTable::getlist([
          'filter' => ['UF_PRODUCT_ID' => intval($arProp['productId']), 'UF_PHONE' => htmlspecialcharsbx($arProp['phone'])]
        ])->getSelectedRowsCount();
      }
      else {
        $countElements = $hlSubscribeTable::getlist([
          'filter' => ['UF_PRODUCT_ID' => intval($arProp['productId']), 'UF_EMAIL' => htmlspecialcharsbx($arProp['email'])]
        ])->getSelectedRowsCount();
      }

      if ($countElements == 0) {
        $arFields = [
          'UF_PRODUCT_ID' => intval($arProp['productId']),
          'UF_PHONE' => htmlspecialcharsbx($arProp['phone']),
          'UF_EMAIL' => htmlspecialcharsbx($arProp['email']),
          'UF_SITE_ID' => $siteId
        ];
        $hlResult = $hlSubscribeTable::add($arFields);
        if ($hlResult->isSuccess()) {
          $result['message'] = Loc::getMessage("APS_SUBSCRIBE_SUCCESS");
          
          $isExistProductId = false;
          $arProductSession = unserialize($_SESSION['PRODUCT_SUBSCRIBE']);
          foreach ($arProductSession as $arProduct) {
            foreach ($arProduct as $productId => $email)
              if ($productId == intval($arProp['productId'])) {
                $isExistProductId = true;
              }
          }
          if (!$isExistProductId) {
            $userData = [];
            if(!empty($arProp['phone'])){
              $userData[] = ['name' => 'phone', 'value' => htmlspecialcharsbx($arProp['phone'])];
            }
            if(!empty($arProp['email'])){
              $userData[] = ['name' => 'email', 'value' => htmlspecialcharsbx($arProp['email'])];
            }
            $arProductSession[] = [intval($arProp['productId']) => $userData];
            $_SESSION['PRODUCT_SUBSCRIBE'] = serialize($arProductSession);
          }
        } else {
          $arError = $hlResult->getErrorMessages();
          $result['message'] = $arError[0];
        }
      }
      else {
        $result['message'] = Loc::getMessage("APS_SUBSCRIBE_ALREADY");
        $arProductSession = unserialize($_SESSION['PRODUCT_SUBSCRIBE']);
        foreach ($arProductSession as $arProduct) {
          foreach ($arProduct as $productId => $email)
            if ($productId == intval($arProp['productId'])) {
              $isExistProductId = true;
            }
        }
        if (!$isExistProductId) {
          $userData = [];
          if(!empty($arProp['phone'])){
            $userData[] = ['name' => 'phone', 'value' => htmlspecialcharsbx($arProp['phone'])];
          }
          if(!empty($arProp['email'])){
            $userData[] = ['name' => 'email', 'value' => htmlspecialcharsbx($arProp['email'])];
          }
          $arProductSession[] = [intval($arProp['productId']) => $userData];
          $_SESSION['PRODUCT_SUBSCRIBE'] = serialize($arProductSession);
        }
      }
    }
    
    echo json_encode($result);
    
  }
  elseif ($request->get('event') == 'subscribe-user') {
    
    if (!empty($request->get('product_id'))) {
      $productId = intval($request->get('product_id'));
    }
    
    $currentUserEmail = $USER->GetEmail();
    $currentUserPhone = '';
    
    if ($productId > 0 && !empty($currentUserEmail)) {
      $hlblock = HighloadBlockTable::getById(HL_SUBSCRIBE)->fetch();
      $entity = HighloadBlockTable::compileEntity($hlblock);
      $hlSubscribeTable = $entity->getDataClass();

      $userPhone = UserPhoneAuthTable::getList(['filter' => ['USER_ID' => $USER->GetID()]])->fetch();

      if(isset($userPhone['PHONE_NUMBER'])){
        $currentUserPhone = str_replace('+', '', $userPhone['PHONE_NUMBER']);
        $countElements = $hlSubscribeTable::getlist([
          'filter' => [
            'UF_PRODUCT_ID' => $productId,
            'UF_PHONE' => $currentUserPhone
          ]
        ])->getSelectedRowsCount();
      }
      else{
        $countElements = $hlSubscribeTable::getlist([
          'filter' => [
            'UF_PRODUCT_ID' => $productId,
            'UF_EMAIL' => $currentUserEmail
          ]
        ])->getSelectedRowsCount();
      }
      
      if ($countElements == 0) {
        $arFields = [
          'UF_PRODUCT_ID' => $productId,
          'UF_EMAIL' => $currentUserEmail,
          'UF_PHONE' => $currentUserPhone,
          'UF_SITE_ID' => $siteId
        ];
        $hlResult = $hlSubscribeTable::add($arFields);
        if ($hlResult->isSuccess()) {
          $result['message'] = Loc::getMessage("APS_SUBSCRIBE_SUCCESS");
        } else {
          $arError = $hlResult->getErrorMessages();
          $result['message'] = $arError[0];
        }
      } else {
        $result['message'] = Loc::getMessage("APS_SUBSCRIBE_ALREADY");
      }
    }
    
    echo json_encode($result);
    
  }
  elseif ($request->get('event') == 'unsubscribe-user') {
    
    if (!empty($request->get('product_id'))) {
      $productId = intval($request->get('product_id'));
    }
    
    $currentUserEmail = $USER->GetEmail();
    $currentUserPhone = '';
    $arElementsList = [];
    
    if ($productId > 0 && !empty($currentUserEmail)) {
      $hlblock = HighloadBlockTable::getById(HL_SUBSCRIBE)->fetch();
      $entity = HighloadBlockTable::compileEntity($hlblock);
      $hlSubscribeTable = $entity->getDataClass();

      $userPhone = UserPhoneAuthTable::getList(['filter' => ['USER_ID' => $USER->GetID()]])->fetch();
      if(isset($userPhone['PHONE_NUMBER'])){
        $currentUserPhone = str_replace('+', '', $userPhone['PHONE_NUMBER']);
        $arElements = $hlSubscribeTable::getlist([
          'filter' => ['UF_PRODUCT_ID' => $productId, 'UF_PHONE' => $currentUserPhone],
          'select' => ['ID']
        ])->fetchAll();

        foreach ($arElements as $arElement){
          if(!in_array($arElement['ID'], $arElementsList)){
            $arElementsList[] = $arElement['ID'];
          }
        }
      }
      if(isset($userPhone['PHONE_NUMBER'])){
        $arElements = $hlSubscribeTable::getlist([
          'filter' => ['UF_PRODUCT_ID' => $productId, 'UF_EMAIL' => $currentUserEmail],
          'select' => ['ID']
        ])->fetchAll();

        foreach ($arElements as $arElement){
          if(!in_array($arElement['ID'], $arElementsList)){
            $arElementsList[] = $arElement['ID'];
          }
        }
      }
      
      foreach ($arElementsList as $arVal) {
        $hlSubscribeTable::delete($arVal);
      }
      
      $result['message'] = Loc::getMessage("APS_UNSUBSCRIBE_SUCCESS");
    }
    
    echo json_encode($result);
    
  }
  elseif ($request->get('event') == 'unsubscribe-not-auth') {
    
    if (!empty($request->get('product_id'))) {
      $productId = intval($request->get('product_id'));
    }
    
    $arProductSession = unserialize($_SESSION['PRODUCT_SUBSCRIBE']);

    foreach ($arProductSession as $key => $arProduct) {
      foreach ($arProduct as $sessionProductId => $dataUser)
        if ($sessionProductId == $productId) {
          $currentUserContact = [];
          foreach ($dataUser as $userContact){
            $currentUserContact[$userContact['name']] = $userContact['value'];
          }
          unset($arProductSession[$key]);
          $_SESSION['PRODUCT_SUBSCRIBE'] = serialize($arProductSession);
        }
    }
    if ($productId > 0 && count($currentUserContact) > 0) {
      $hlblock = HighloadBlockTable::getById(HL_SUBSCRIBE)->fetch();
      $entity = HighloadBlockTable::compileEntity($hlblock);
      $hlSubscribeTable = $entity->getDataClass();
      $arElementsList = [];
      //$filter = [];
      foreach ($currentUserContact as $contactType => $contactData){
        switch ($contactType){
          case 'phone':
            $filter = ['=UF_PRODUCT_ID' => $productId, '=UF_PHONE' => $contactData];
            break;
          case 'email':
            $filter = ['=UF_PRODUCT_ID' => $productId, '=UF_EMAIL' => $contactData];
            break;
        }
        $arElements = $hlSubscribeTable::getlist([
          'filter' => $filter,
          'select' => ['ID']
        ])->fetchAll();

        foreach ($arElements as $arElement){
          if(!in_array($arElement['ID'], $arElementsList)){
            $arElementsList[] = $arElement['ID'];
          }
        }
      }
      foreach ($arElementsList as $arVal) {
        $hlSubscribeTable::delete($arVal);
      }
      
      $result['message'] = Loc::getMessage("APS_UNSUBSCRIBE_SUCCESS");
    }
    
    echo json_encode($result);
    
  }
  elseif ($request->get('event') == 'subscribe-phone') {
    $arProp = [];
    if (!empty($request->get('props'))) {
      $strProp = $request->get('props');
      parse_str($strProp, $arProp);
    }
    
    if (intval($arProp['productId']) > 0 && !empty($arProp['phone']) && Loader::includeModule('form')) {
      $arValues = [
        'form_text_21' => htmlspecialcharsbx($arProp['phone']),
        'form_text_22' => intval($arProp['productId']),
      ];
      
      $RESULT_ID = CFormResult::Add(2, $arValues);
      CFormResult::SetEvent($RESULT_ID);
      CFormResult::Mail($RESULT_ID);
    }
  }