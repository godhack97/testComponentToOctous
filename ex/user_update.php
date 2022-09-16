<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("User Update");
?>

<?


// UF_INSTAGRAM

//echo ConvertTimeStamp(strtotime('2016-03-01'));


$USER_ID = 2;
$user = new CUser;
$fields = Array(
  "PERSONAL_BIRTHDAY" => ConvertTimeStamp(strtotime('2016-03-01')),
  "UF_INSTAGRAM" => 'zenkot2', 
); 
$user->Update($USER_ID, $fields);


?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>