<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Создание заказа");?>
  
<?$APPLICATION->IncludeComponent(
	"arlix:order.page",
	"",
Array(),
false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>