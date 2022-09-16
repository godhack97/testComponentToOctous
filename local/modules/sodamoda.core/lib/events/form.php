<?php
  namespace Sodamoda\Core\Events;

  use Bitrix\Main\Loader;
  use \Bitrix\Sale\Order;
  use \Bitrix\Main\Localization\Loc;
  use \Bitrix\Highloadblock\HighloadBlockTable;
  use Bitrix\Main\Mail\Event;

  class Form{
    public function my_onBeforeResultAdd($WEB_FORM_ID, &$arFields, &$arrVALUES)
    {
      //global $APPLICATION;

      // действие обработчика распространяется только на форму с ID=6
      if ($WEB_FORM_ID == 3) {
        //AddMessage2Log('$arrVALUES '. print_r($arrVALUES, 1));

        $URL_DOC = 'https://script.google.com/macros/s/AKfycbwtJAus_iY1rNtUGFXrOQNwgJgsxgVZbHbw4p9-uo9v8vRYHtQ/exec';

        $arForm['type'] = urlencode($arrVALUES['form_dropdown_SIMPLE_QUESTION_549']);
        $arForm['who'] = urlencode($arrVALUES['form_dropdown_SIMPLE_QUESTION_158']);
        $arForm['name'] = urlencode($arrVALUES['form_text_28']);
        $arForm['phone'] = urlencode($arrVALUES['form_text_29']);;
        $arForm['mail'] = urlencode($arrVALUES['form_text_30']);
        $arForm['message'] = urlencode($arrVALUES['form_text_31']);;

        $urlCurl = $URL_DOC . '?p5=' . $arForm['mail'] . '&p1=' . $arForm['type'] . '&p2=' . $arForm['who'] . '&p3=' . $arForm['name'] . '&p4=' . $arForm['phone'] . '&p6=' . $arForm['message'];

        //AddMessage2Log('$arForm '. print_r($arForm, 1));

        $options = array(
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HEADER => false,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_ENCODING => '',
          CURLOPT_USERAGENT => 'spider',
          CURLOPT_AUTOREFERER => true,
          CURLOPT_CONNECTTIMEOUT => 120,
          CURLOPT_TIMEOUT => 120,
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_SSL_VERIFYPEER => false
        );

        $ch = curl_init($urlCurl);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;

        //    if(!empty($header['errno']))
        //      AddMessage2Log('CURL'. print_r($header, 1) );
      }
    }
  }