<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("EX");
?>
<?

// ссылки 
//<a href="https://t.me/SODAMODA_CHAT" target="_blank">telegram</a> <br>
//<a href="tg://resolve?domain=sodamoda_chat" target="_blank">Telegram</a>


//telegramToChannel('Тест из сайта');

/*
чтоб запустить код напишите в любом месте: 
telegram("Текст");
*/

//telegramToChannel("Отправка<br>сообщения"); 
//
//
//function telegramToChannel($message) 
//{
//  $message = str_replace(['<br>', '<br/>', '<br />', '</br>'], ['\n'], $message);
//
//  //$telegram_channel = '@sodamoda_channel'; // Публичный
//  $telegram_channel = '-1001436057583'; // Частный
//
//  $option = ["chat_id" => $telegram_channel, "text" => $message, "parse_mode" => "HTML", "disable_web_page_preview" => True];
//  $answer = telegramSend("sendMessage", $option);
//  
//  //    echo '<pre>';
//  //    print_r($answer);
//  //    echo '</pre>';
//}
//
//
//function telegramSend($type, $option) 
//{
//  $token = "1750163265:AAH2TyZyIkdRTRejdQ5AeEzZAH0OoKUPHrw";
//  $zapros = curl_init();
//  curl_setopt_array(
//      $zapros,
//      array(
//          CURLOPT_URL => 'https://api.telegram.org/bot'. $token .'/'. $type,
//          CURLOPT_RETURNTRANSFER => TRUE, 
//          CURLOPT_TIMEOUT => 10, 
//          CURLOPT_POST => TRUE,
//          CURLOPT_POSTFIELDS => $option,
//      )
//  );
//  $answer = curl_exec($zapros);	
//  curl_close($zapros);
//  file_put_contents('telegram.txt', $answer); //записать ответ от телеграма в txt файл
//  $answer = json_decode($answer, true);	
//  
//  return $answer;
//}
