<?
  require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
  
  use \Bitrix\Main\UserPhoneAuthTable;
  use \Bitrix\Main\UserTable;
  use \Bitrix\Main\Context;
  
  $result = [];
  
  $request = Context::getCurrent()->getRequest();
  
  $action = trim($request->get('action'));
  $fio = trim($request->get('name'));
  $email = trim($request->get('email'));
  $phone = trim($request->get('phone'));
  switch ($action) {
    case 'search_user_by_phone':
      $userPhoneCheck = UserPhoneAuthTable::getList(['filter' => ['=PHONE_NUMBER' => UserPhoneAuthTable::normalizePhoneNumber($phone)]])->fetch();
      if (isset($userPhoneCheck['USER_ID'])) {
        $dbUser = UserTable::getList([
          'limit' => 1,
          'select' => [
            'ID',
            'NAME',
            'LAST_NAME',
            'SECOND_NAME',
            'EMAIL',
            'LOGIN',
            'PERSONAL_PHONE',
          ],
          'filter' => [
            '=ID' => $userPhoneCheck['USER_ID'],
          ],
        ]);
        if ($arRes = $dbUser->fetch()) {
          $value = [];
          if ($arRes['LAST_NAME']) {
            $value[] = $arRes['LAST_NAME'];
          }
          if ($arRes['NAME']) {
            $value[] = $arRes['NAME'];
          }
          if ($arRes['SECOND_NAME']) {
            $value[] = $arRes['SECOND_NAME'];
          }
          if (count($value) <= 0) {
            $value[] = $arRes['LOGIN'];
          }
          $result = [
            'id' => $arRes['ID'],
            'name' => $arRes['NAME'],
            'last_name' => $arRes['LAST_NAME'],
            'second_name' => $arRes['SECOND_NAME'],
            'email' => $arRes['EMAIL'],
            'login' => $arRes['LOGIN'],
            'phone' => $arRes['PERSONAL_PHONE'],
            'label' => implode($value, ' '),
            'value' => implode($value, ' '),
          ];
        }
      }
      break;
    default:
      $arrFio = explode(' ', $fio);
      $name = $arrFio[0];
      $lastName = '';
      $secondName = '';
      
      switch (count($arrFio)) {
        case 2:
          {
            $lastName = $arrFio[1];
          }
          break;
        case 3:
          {
            $lastName = $arrFio[1];
            $secondName = $arrFio[2];
          }
          break;
      }
      
      $login = $email;
      
      $new_password = \Bitrix\Main\Security\Random::getString(7);
      
      $dbUser = UserTable::getList([
        'limit' => 1,
        'select' => [
          'ID',
          'NAME',
          'LAST_NAME',
          'SECOND_NAME',
          'EMAIL',
          'LOGIN',
          'PERSONAL_PHONE',
        ],
        'filter' => [
          'LOGIC' => 'OR',
          'EMAIL' => $email,
          'LOGIN' => $login,
        ],
      ]);
      if ($arRes = $dbUser->fetch()) {
        $value = [];
        if ($arRes['LAST_NAME'])
          $value[] = $arRes['LAST_NAME'];
        if ($arRes['NAME'])
          $value[] = $arRes['NAME'];
        if ($arRes['SECOND_NAME'])
          $value[] = $arRes['SECOND_NAME'];
        if (count($value) <= 0)
          $value[] = $arRes['LOGIN'];
        $result = [
          'id' => $arRes['ID'],
          'name' => $arRes['NAME'],
          'last_name' => $arRes['LAST_NAME'],
          'second_name' => $arRes['SECOND_NAME'],
          'email' => $arRes['EMAIL'],
          'login' => $arRes['LOGIN'],
          'phone' => $arRes['PERSONAL_PHONE'],
          'label' => implode($value, ' '),
          'value' => implode($value, ' '),
        ];
      } else {
        $userPhoneCheck = UserPhoneAuthTable::getList(['filter' => ['=PHONE_NUMBER' => UserPhoneAuthTable::normalizePhoneNumber($phone)]])->fetch();
        
        if (isset($userPhoneCheck['USER_ID'])) {
          $dbUser = UserTable::getList([
            'limit' => 1,
            'select' => [
              'ID',
              'NAME',
              'LAST_NAME',
              'SECOND_NAME',
              'EMAIL',
              'LOGIN',
              'PERSONAL_PHONE',
            ],
            'filter' => [
              '=ID' => $userPhoneCheck['USER_ID'],
            ],
          ]);
          if ($arRes = $dbUser->fetch()) {
            $user = new CUser;
            $fields = [
              'NAME' => $name,
              'LAST_NAME' => $lastName,
              'SECOND_NAME' => $secondName,
            ];
            if ($user->Update($arRes['ID'], $fields)) {
              $value = [];
              $value[] = $lastName;
              $value[] = $name;
              $value[] = $secondName;
              if (count($value) <= 0) {
                $value[] = $arRes['LOGIN'];
              }
              $result = [
                'id' => $arRes['ID'],
                'name' => $name,
                'last_name' => $lastName,
                'second_name' => $secondName,
                'email' => $arRes['EMAIL'],
                'login' => $arRes['LOGIN'],
                'phone' => $arRes['PERSONAL_PHONE'],
                'label' => implode($value, ' '),
                'value' => implode($value, ' '),
              ];
            } else {
              $value = [];
              if ($arRes['LAST_NAME']) {
                $value[] = $arRes['LAST_NAME'];
              }
              if ($arRes['NAME']) {
                $value[] = $arRes['NAME'];
              }
              if ($arRes['SECOND_NAME']) {
                $value[] = $arRes['SECOND_NAME'];
              }
              if (count($value) <= 0) {
                $value[] = $arRes['LOGIN'];
              }
              $result = [
                'id' => $arRes['ID'],
                'name' => $arRes['NAME'],
                'last_name' => $arRes['LAST_NAME'],
                'second_name' => $arRes['SECOND_NAME'],
                'email' => $arRes['EMAIL'],
                'login' => $arRes['LOGIN'],
                'phone' => $arRes['PERSONAL_PHONE'],
                'label' => implode($value, ' '),
                'value' => implode($value, ' '),
              ];
            }
          }
        } else {
          $user = new \CUser;
          $arFields = [
            'NAME' => $name,
            'LAST_NAME' => $lastName,
            'SECOND_NAME' => $secondName,
            'EMAIL' => $email,
            'LOGIN' => $login,
            'PERSONAL_PHONE' => $phone,
            'PHONE_NUMBER' => $phone,
            'LID' => 'ru',
            'ACTIVE' => 'Y',
            'GROUP_ID' => array(3, 6, 4),
            'PASSWORD' => $new_password,
            'CONFIRM_PASSWORD' => $new_password,
          ];
          $ID = $user->Add($arFields);
          if (intval($ID) > 0) {
            $result = [
              'id' => $ID,
              'name' => $name,
              'last_name' => $lastName,
              'second_name' => $secondName,
              'email' => $email,
              'login' => $login,
              'phone' => $phone,
              'label' => $fio,
              'value' => $fio,
            ];
          } else {
            var_dump($user->LAST_ERROR);
          }
        }
      }
      break;
  }
  
  echo json_encode($result);
  
  
  require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');