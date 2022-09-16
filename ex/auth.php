<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Auth");
?>
<?
//$login = 'zenkot@yandex.ru';
//$password = '';
//
//// За место e-mail подставляем логин
//$rsUsers = CUser::GetList(($by = 'LAST_LOGIN'), ($order = 'DESC'), Array('EMAIL' => $login));
////var_dump($rsUsers);
//if($rsUsers->SelectedRowsCount() > 0)
//{
//  while($arUser = $rsUsers->Fetch())
//  {
//    dbg($arUser);
//    break;
//  }
//  
//  $res = $USER->Login($arUser['LOGIN'], $password, 'Y');
//  $APPLICATION->arAuthResult = $res;
//
//  var_dump($res);
//  
//}
?>
  
  <? /*$APPLICATION->IncludeComponent(
	"bitrix:main.auth.form",
	"",
	Array(
		"AUTH_FORGOT_PASSWORD_URL" => "",
		"AUTH_REGISTER_URL" => "",
		"AUTH_SUCCESS_URL" => ""
	)
); */ ?>
  
  
  
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>