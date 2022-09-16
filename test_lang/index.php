<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Test");

// Создаем массив с адресами для каждого языкового кода
$sites = array(
	"ru" => "/",
	"en" => "/en/",
);

// получаем язык
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2); // вырезаем первые две буквы

// проверяем язык
if (!in_array($lang, array_keys($sites))){
  $lang = 'ru';
}

// перенаправление на субдомен
//header('Location: ' . $sites[$lang]);
?>


<?
$page = $APPLICATION->GetCurPage();
if($lang == "en"){
	$redirect = "https://" . $_SERVER[HTTP_HOST]. "/en" . $page;
	$link = "https://" . $_SERVER[HTTP_HOST]. $page;
}
else{
	$redirect = "https://" . $_SERVER[HTTP_HOST] . $page;
	$link = "https://" . $_SERVER[HTTP_HOST]. "/en" . $page;
}
?>

Язык в браузере - <?=$lang?><br>
Ссылка перенаправления при первом открытии - <?=$redirect?><br>




<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>