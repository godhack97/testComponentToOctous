<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("EX");
?>

<a href="https://t.me/SODAMODA_CHAT" target="_blank">telegram</a> <br>

<a href="tg://resolve?domain=sodamoda_chat" target="_blank">Telegram</a>
  
<?

exit;


function get_web_page( $url )
{
  $options = array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HEADER         => false,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_ENCODING       => "",
      CURLOPT_USERAGENT      => "spider",
      CURLOPT_AUTOREFERER    => true,
      CURLOPT_CONNECTTIMEOUT => 120,
      CURLOPT_TIMEOUT        => 120,
      CURLOPT_MAXREDIRS      => 10,  
      CURLOPT_SSL_VERIFYPEER => false  
  );

  $ch = curl_init( $url );
  curl_setopt_array( $ch, $options );
  $content = curl_exec( $ch );
  $err     = curl_errno( $ch );
  $errmsg  = curl_error( $ch );
  $header  = curl_getinfo( $ch );
  curl_close( $ch );

  $header['errno']   = $err;
  $header['errmsg']  = $errmsg;
  $header['content'] = $content;
  
  return $header;
}

$URL = 'https://script.google.com/macros/s/AKfycbwtJAus_iY1rNtUGFXrOQNwgJgsxgVZbHbw4p9-uo9v8vRYHtQ/exec';

$email    = 'e-mail@mail.ru';
$type     = urlencode('Тип обращения');
$who      = 'who';
$name     = 'name';
$phone    = '79163400966';
$message  = 'message';

get_web_page($URL .'?p1='. $email .'&p2='. $type .'&p3='. $who .'&p4='. $name .'&p5='. $phone .'&p6='. $message);




// <a href="//irc247.ru/?utm=hui" target="_blank" rel="nofollow">ссылка</a>


//$USER->Authorize(2);
//exit();

//global $USER;
//$arUserGroups = CUser::GetUserGroup($USER->GetID());
//if(!in_array(10, $arUserGroups))
//{
//  
//  echo 'in';

//$statusResult = \Bitrix\Sale\Internals\StatusLangTable::getList(array(
//
//    'order' => array('STATUS.SORT'=>'ASC'),
//
//    'filter' => array('STATUS.TYPE'=>'D','LID'=>LANGUAGE_ID),
//
//    'select' => array('STATUS_ID','NAME','DESCRIPTION','NOTIFY'=>'STATUS.NOTIFY'),
//
//));
//
//
//
//while($status=$statusResult->fetch())
//
//{
//
//    dbg($status);
//
//}

//$result = \Bitrix\Main\UserGroupTable::getList(array(
//  'filter' => array('USER_ID' => $USER->GetID(),'GROUP.ACTIVE'=>'Y'),
//  'select' => array('GROUP_ID','GROUP_CODE'=>'GROUP.STRING_ID'), // выбираем идентификатор группы и символьный код группы
//  'order' => array('GROUP.C_SORT'=>'ASC'), // сортируем в соответствии с сортировкой групп
//));
//
//$isAdmin = false;
//while ($arGroup = $result->fetch())
//{
//  if(in_array($arGroup['GROUP_ID'], Array(10, 14)))
//    $isAdmin = true;
//}
//
//if(!$isAdmin)
//{
//
//  // \Bitrix\Main\Loader::IncludeModule("sale");
//  $statusResult = \Bitrix\Sale\Internals\StatusTable::getList(array(
//    'order' => array('SORT'=>'ASC'),
//    'filter' => array('TYPE'=>'O'),
//  ));
//
//  while($status = $statusResult->fetch())
//    $arStatus[] = $status;
//}


//}
//else
//  echo 'else';


//  $arMeasure = \Bitrix\Catalog\MeasureTable::getList()->fetchAll();
//  foreach ($arMeasure as $key => $value) 
//  {
//    // $info = CCatalogMeasureClassifier::getMeasureInfoByCode($value["CODE"]);
//    $symbol = CCatalogMeasureClassifier::getMeasureTitle($value["CODE"], 'SYMBOL_RUS');
//    $mTitle = CCatalogMeasureClassifier::getMeasureTitle($value["CODE"], 'MEASURE_TITLE');
//    $arMeasure[$key]['SYMBOL'] = $symbol;
//    $arMeasure[$key]['MEASURE_TITLE'] = $mTitle;
//  }
//  
//  dbg($arMeasure);



//$arOrder = CSaleOrder::GetByID(3523);
//dbg($arOrder);

//$BCC = 'info@sodamoda.ru, zenkot@yandex.ru, #BCC#';
//$arBcc = array_merge(Array('paid@sodamoda.ru'), explode(',', $BCC));
//
//dbg(implode(', ', $arBcc));

//$arFields['ORDER_ID'] = 1823;
//$arOrder = CSaleOrder::GetByID($arFields['ORDER_ID']);
//$arPaySys = CSalePaySystem::GetByID($arOrder['PAY_SYSTEM_ID'], $arOrder['PERSON_TYPE_ID']);
//dbg($arPaySys);

//$res = in_array(147, Array('147', '148'));
//var_dump($res);


//$ORDER_ID = 1635;
//
//$arOrder = CSaleOrder::GetByID($ORDER_ID);
//if(empty($arOrder['RESPONSIBLE_ID']) && $USER->IsAuthorized())
//{
//  $int = CSaleOrder::Update($ORDER_ID, Array('RESPONSIBLE_ID' => $USER->GetID()));
//  var_dump($int);
//}
//else
//  dbg($arOrder['RESPONSIBLE_ID']);
//// RESPONSIBLE_ID



?><?$APPLICATION->IncludeComponent(
	"bitrix:form.result.new",
	"",
	Array(
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CHAIN_ITEM_LINK" => "",
		"CHAIN_ITEM_TEXT" => "",
		"EDIT_URL" => "result_edit.php",
		"IGNORE_CUSTOM_TEMPLATE" => "N",
		"LIST_URL" => "result_list.php",
		"SEF_MODE" => "N",
		"SUCCESS_URL" => "",
		"USE_EXTENDED_ERRORS" => "N",
		"VARIABLE_ALIASES" => Array("RESULT_ID"=>"RESULT_ID","WEB_FORM_ID"=>"WEB_FORM_ID"),
		"WEB_FORM_ID" => "1"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>