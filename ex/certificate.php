<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Sertificate");
?><?

//Ваш электронный сертификат на сумму #COUPON_PRICE# руб.<br>
//<br>
//Для использования на сайте воспользуйтесь промокодом:<br>
//<br>
//<span style="font-size:18px"><b>#COUPON#</b><br></span>
//<br>
//Сертификат действителен до #ACTIVE_TO#<br>
//<br>
//<img src="https://#SERVER_NAME#/upload/sert/sert5000.jpg" style="width:450px;max-width:100%;"><br>
//<br>
//Сертификат является одноразовым, скидка распространяется на сумму товаров в корзине в рамках одного заказа, доставка оплачивается отдельно.<br>
//<br>
//С уважением,<br>
//<a href="https://#SERVER_NAME#/">#SITE_NAME#</a>


//$arEventFields = array(
//    "COUPON_PRICE"  => '10 000',
//    "COUPON"        => 'SL-I SSH9-DHXPJVC',
//    "ACTIVE_TO"     => '29.04.2023 г.',
//    "SERVER_NAME"   => 'sodamoda.ru',
//    "SITE_NAME"     => 'SODAMODA',
//    "EMAIL_ORDER"   => 'kotl yarovdl1997@icloud.com', // 'zenkot@yandex.ru',
//);
//$rs = CEvent::Send("GENERATE_COUPON", SITE_ID, $arEventFields);
//
//var_dump($rs);


?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>