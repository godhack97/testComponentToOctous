<?


telegram('Test', 1);



function telegram($text, $adress = false) 
{

    $id[1] = 157266205;
    $id[2] = 1749404939;
    
    //$telegram_channel = '@sodamoda_channel';
    $telegram_channel = '-1001436057583';

    if (!$adress) {
        $chat_id = $telegram_channel;
    } else if ($id[$adress]) {
        $chat_id = $id[$adress];
    }
    
    if ($chat_id) {
        $text = str_replace("</br>", "\n", $text); 
        $option = ["chat_id" => $chat_id, "text" => $text, "parse_mode" => "HTML", "disable_web_page_preview" => True];
        $answer = telegram_send("sendMessage", $option);
    }
}


function telegram_send($type, $option) 
{
    $token = "1750163265:AAH2TyZyIkdRTRejdQ5AeEzZAH0OoKUPHrw";									
    $zapros = curl_init();
    curl_setopt_array(
        $zapros,
        array(
            CURLOPT_URL => 'https://api.telegram.org/bot'.$token.'/'.$type,
            CURLOPT_RETURNTRANSFER => TRUE, 
            CURLOPT_TIMEOUT => 10, 
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $option,
        )
    );
    $answer = curl_exec($zapros);	
    curl_close($zapros);
    //file_put_contents('telegram.txt', $answer);//записать ответ от телеграма в txt файл
    $answer = json_decode($answer, true);	
    return $answer;
}
