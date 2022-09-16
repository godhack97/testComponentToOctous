<?
  require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

  use \Bitrix\Main\UserTable;
  use \Bitrix\Main\Context;

  $result = [];

  $request = Context::getCurrent()->getRequest();
  $term = trim($request->get("term"));

  if (strlen($term) > 0) {
    $rsFIO = explode(' ', $term);
    if(count($rsFIO) > 1){
      $dbUser = UserTable::getList([
        'limit' => 10,
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
          "LOGIC" => "AND",
          'NAME' => "%" . $rsFIO[1] . "%",
          'LAST_NAME' => "%" . $rsFIO[0] . "%",
        ],
      ]);
    }
    else{
      $dbUser = UserTable::getList([
        'limit' => 10,
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
          "LOGIC" => "OR",
          'NAME' => "%" . $term . "%",
          'LAST_NAME' => "%" . $term . "%",
          'SECOND_NAME' => "%" . $term . "%",
          'EMAIL' => "%" . $term . "%",
          'LOGIN' => "%" . $term . "%",
          'PERSONAL_PHONE' => "%" . $term . "%",
        ],
      ]);
    }

    while ($arRes = $dbUser->fetch()) {
      $value = [];
      $arRes["PERSONAL_PHONE"] = '+' . $arRes["PERSONAL_PHONE"];

      if ($arRes["LAST_NAME"])
        $value[] = $arRes["LAST_NAME"];

      if ($arRes["NAME"])
        $value[] = $arRes["NAME"];

      if ($arRes["SECOND_NAME"])
        $value[] = $arRes["SECOND_NAME"];

      if (count($value) <= 0)
        $value[] = $arRes["LOGIN"];

      $result[] = [
        'id' => $arRes["ID"],
        'name' => $arRes["NAME"],
        'last_name' => $arRes["LAST_NAME"],
        'second_name' => $arRes["SECOND_NAME"],
        'email' => $arRes["EMAIL"],
        'login' => $arRes["LOGIN"],
        'phone' => $arRes["PERSONAL_PHONE"],
        'label' => '<span class="uif-name">' . implode($value, " ") . '</span> <span class="uif-mail">' . $arRes["EMAIL"] . '</span> <span class="uif-phone">' . $arRes["PERSONAL_PHONE"] . '</span>',
        'value' => implode($value, " "),
      ];
    }
  }

  echo json_encode($result);

  require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
