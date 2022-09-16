<? 
$siteId = '';
if (isset($_REQUEST['site_id']) && is_string($_REQUEST['site_id']))
  $siteId = substr(preg_replace('/[^a-z0-9_]/i', '', $_REQUEST['site_id']), 0, 2);

if ($siteId) {
  define('SITE_ID', $siteId);
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("form");
  
  use \Bitrix\Main\Localization\Loc;
  use \Bitrix\Main\UserTable;
  use \Bitrix\Main\UserPhoneAuthTable;
  use \Bitrix\Main\Loader;
  use \Bitrix\Sale;
  use Bitrix\Highloadblock\HighloadBlockTable;

global $USER, $APPLICATION;
if (!is_object($USER)) {
  $USER = new CUser;
}

$result = array();
$mode = htmlspecialcharsbx($_POST['mode']);
$result['mode'] = $mode;
$result['status'] = false;
$result['message'] = Loc::getMessage('AJAX_ERROR_MESSAGE');


switch($mode)
{
  //////////////////////////////////////////////////////////////////////////////
  // Запись на примерку
  case 'userinfo':

    //    [mode] => userinfo
    //    [user] => 2
    //    [inst] => wer
    //    [date] => 2016-03-01
    //    [site_id] => s1
    
    $USER_ID = htmlspecialcharsbx($_POST['user']);
    if(!empty($USER_ID))
    {
      $user = new CUser;
      $arFields = Array(
        "PERSONAL_BIRTHDAY" => ConvertTimeStamp(strtotime(htmlspecialcharsbx($_POST['date']))),
        "UF_INSTAGRAM" => htmlspecialcharsbx($_POST['inst']), 
      ); 
      $user->Update($USER_ID, $arFields);
      
      
      $arValues = Array(
        'form_text_39' => $arFields['UF_INSTAGRAM'],
        'form_date_40' => $arFields['PERSONAL_BIRTHDAY'],
        'form_text_41' => $USER_ID,
        'form_text_42' => htmlspecialcharsbx($_POST['order']),
      );
      
      if($RESULT_ID = CFormResult::Add(5, $arValues))
      {
        CFormResult::Mail($RESULT_ID);
        
        $url = SITE_SERVER_PROTOCOL . SITE_SERVER_NAME .'/bitrix/admin/sale_order_view.php?lang=ru&ID='. $arValues['form_text_42'];
        $messageTlg  = 'В дополнение к оформленному заказу номер <a href="'. $url .'">'. $arValues['form_text_42'] .'</a>, пользователь отправил свои данные. ';
        $messageTlg .= 'Instagram <a href="https://www.instagram.com/'. $arFields['UF_INSTAGRAM'] .'/">@'. $arFields['UF_INSTAGRAM'] .'</a>. ';
        $messageTlg .= 'День рождения '. $arFields['PERSONAL_BIRTHDAY'] .'.';
        telegramToChannel($messageTlg);
      }
      
      $result['status'] = true;
    }
    else
      $result['message'] = 'Ошибка. Не указан пользователь';
    
    $result['post'] = print_r($_POST, 1);
    break;
  
    
  //////////////////////////////////////////////////////////////////////////////
  // Запись на примерку
  case 'fitting':
    
    $pageName = htmlspecialcharsbx($_POST['page_name']);
    $pageUrl = SITE_SERVER_PROTOCOL . SITE_SERVER_NAME . htmlspecialcharsbx($_POST['page_url']);
    
    $arValues = Array(
      'form_text_34' => htmlspecialcharsbx($_POST['name']),
      'form_text_35' => phoneFormat(htmlspecialcharsbx($_POST['phone'])), // phoneFormat()
      'form_text_36' => htmlspecialcharsbx($_POST['date']),
      'form_text_37' => $pageName .' '. $pageUrl,
      'form_text_38' => htmlspecialcharsbx($_POST['city']),
    );
    

    if($RESULT_ID = CFormResult::Add(4, $arValues))
    {
      CFormResult::Mail($RESULT_ID);
      $result['status'] = true;
      
      $arCities = [
          'MSC' => [
              'NAME' => 'Москва',
              'TELEGRAM' => TELEGRAM_USER_MSC,
          ],
          'SPB' => [
              'NAME' => 'Санкт-Петербург',
              'TELEGRAM' => TELEGRAM_USER_SPB,
          ],
          'RND' => [
              'NAME' => 'Ростов-на-Дону',
              'TELEGRAM' => TELEGRAM_USER_RND,
          ],
          'KZN' => [
              'NAME' => 'Казать',
              'TELEGRAM' => TELEGRAM_USER_KZN,
          ],
          'KLR' => [
              'NAME' => 'Калининград',
              'TELEGRAM' => TELEGRAM_USER_KLR,
          ],
      ];

      $message = 'Заявка на примерку';
      $message .= !empty($arValues['form_text_34']) ? ' от '. $arValues['form_text_34'] .'.' : '.';
      $message .= !empty($arValues['form_text_35']) ? ' Телефон '. $arValues['form_text_35'] .'.' : '';
      $message .= !empty($arValues['form_text_36']) ? ' Дата визита: '. $arValues['form_text_36'] .'.' : '';
      $message .= !empty($arValues['form_text_38']) ? ' Город '. $arCities[ $arValues['form_text_38'] ]['NAME'] .'.' : '';
      $message .= ' Страница оформления заявки: <a href="'. $pageUrl .'">'. $pageName .'</a>';

      //telegramToChannel($message, '-1001436057583');
      telegramToChannel($message, $arCities[ $arValues['form_text_38'] ]['TELEGRAM']);
    }
    else
    {
      global $strError;
      $result['message'] = $strError;
    }
    
    break;
  
  
  //////////////////////////////////////////////////////////////////////////////
  // Форма возврата
  case 'return':
    CModule::IncludeModule("form");

    if(!htmlspecialcharsbx($_POST['USER_PHONE']))
      $result['message'] = Loc::getMessage('AJAX_ENTER_PHONE');
    else
    {
      $arValues = Array(
        'form_text_1' => htmlspecialcharsbx($_POST['name']),
        'form_text_2' => phoneFormat(htmlspecialcharsbx($_POST['USER_PHONE'])),
        'form_textarea_3' => implode('<br>', $_POST['product']),
        'form_text_4' => htmlspecialcharsbx($_POST['REASONS']),
        //'form_text_5' => htmlspecialcharsbx($_POST['INN']),
        'form_text_6' => htmlspecialcharsbx($_POST['ACCOUNT']),
        'form_text_7' => htmlspecialcharsbx($_POST['BIC']),
        'form_text_8' => htmlspecialcharsbx($_POST['BANK']),
        'form_text_9' => htmlspecialcharsbx($_POST['ORDER_ID']),
        'form_text_12' => htmlspecialcharsbx($_POST['TC_NAME']),    // Название транспортной компании
        'form_text_13' => htmlspecialcharsbx($_POST['TC_TRACK']),   // Номер трека (отправления)
        'form_text_14' => htmlspecialcharsbx($_POST['TC_DATE']),    // Дата отправления
        'form_text_15' => htmlspecialcharsbx($_POST['RETURN_TYPE']),// Способ возврата товара
        'form_text_16' => htmlspecialcharsbx($_POST['CARD']),
        'form_text_17' => htmlspecialcharsbx($_POST['PASSPORT_SERIES']),
        'form_text_18' => htmlspecialcharsbx($_POST['PASSPORT_NUMBER']),
        'form_text_19' => htmlspecialcharsbx($_POST['PASSPORT_ISSUED']),
        'form_text_20' => htmlspecialcharsbx($_POST['PASSPORT_ADDRESS']),
      );

      $date = htmlspecialcharsbx($_POST['date']);
      $price = htmlspecialcharsbx($_POST['price']);
      $name = htmlspecialcharsbx($_POST['name']);
      $phone = phoneFormat(htmlspecialcharsbx($_POST['USER_PHONE']));
      $products = implode('<br>', $_POST['product']);
      $reason = htmlspecialcharsbx($_POST['REASONS']);
      //$inn = htmlspecialcharsbx($_POST['INN']);
      $account = htmlspecialcharsbx($_POST['ACCOUNT']);
      $bik = htmlspecialcharsbx($_POST['BIC']);
      $bank = htmlspecialcharsbx($_POST['BANK']);
      $CARD = htmlspecialcharsbx($_POST['CARD']);
      $order_id = htmlspecialcharsbx($_POST['ORDER_ID']);
      $TC_NAME = htmlspecialcharsbx($_POST['TC_NAME']);
      $TC_TRACK = htmlspecialcharsbx($_POST['TC_TRACK']);
      $TC_DATE = htmlspecialcharsbx($_POST['TC_DATE']);
      $RETURN_TYPE = htmlspecialcharsbx($_POST['RETURN_TYPE']);

      $passportSeries = htmlspecialcharsbx($_POST['PASSPORT_SERIES']);
      $passportNumber = htmlspecialcharsbx($_POST['PASSPORT_NUMBER']);
      $passportIssued = htmlspecialcharsbx($_POST['PASSPORT_ISSUED']);
      $passportAddress = htmlspecialcharsbx($_POST['PASSPORT_ADDRESS']);

      $dateNow = htmlspecialcharsbx($_POST['date_now']);

      // создадим новый результат
      if($RESULT_ID = CFormResult::Add(1, $arValues))
      {
        // pdf
        require_once($_SERVER["DOCUMENT_ROOT"].'/local/lib/tcpdf/tcpdf.php');
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetCreator('SODAMODA');
        $pdf->SetAuthor('SODAMODA');
        $pdf->SetTitle('Заявление на возврат');
        $pdf->SetSubject('Заявление на возврат');
        $pdf->SetKeywords('Заявление на возврат');

        $pdf->SetMargins(8, 10, 10, 5);
        $fontname = TCPDF_FONTS::addTTFfont($_SERVER["DOCUMENT_ROOT"].'/local/lib/tcpdf/fonts/HelveticaNeueCyr-Roman.ttf', 'TrueTypeUnicode', '', 32);
        $pdf->SetFont($fontname, '', 9, '', false);

        $pdf->AddPage();
        $pdf->SetDrawColor(210, 100, 0);
        $pdf->SetTextColor(71, 71, 71);

        $items = '';
        $aItems = explode("+", $_POST["items"]);
        $aItemsForMessage = Array();
        foreach ($aItems as $value) {
        	$aTmp = explode(";", $value);
          $aItemsForMessage[] = $aTmp[1].' &mdash; '.$aTmp[3].'х'.$aTmp[4];
        	$items .= '<tr class="items"><td>'.$aTmp[0].'</td><td>'.$aTmp[1].'</td><td>'.$aTmp[2].'</td><td>'.$aTmp[3].'</td><td>'.$aTmp[4].'</td></tr>';
        }

        $additional = '';
        if ($TC_NAME) {
          $additional .= 'Название транспортной компании: '.$TC_NAME.'<br>';
        }
        if ($TC_TRACK) {
          $additional .= 'Номер трека (отправления): '.$TC_TRACK.'<br>';
        }
        if ($TC_DATE) {
          $additional .= 'Дата отправления: '.$TC_DATE.'';
        }

        $html = '
        <style>
          .basket-table {
            border-collapse: collapse; 
            margin-top: 40px;
            margin-bottom: 40px;
          }
          .basket-table tr td {
            border-bottom: 1px solid #CDCDCD;
          }
          .basket-table tr.head td {
            text-align: left;
            color: #444;
            padding-bottom: 12px;
            margin-bottom: 12px;
            text-transform: uppercase;
          }
          .basket-table tr.items td {
            padding: 18px 0;
            vertical-align: middle;
          }
          .bottom-table {
            border-collapse: collapse;
            border: 0 none;
            border-color:#ffffff;
          }
          .bottom-table td {
            color: #444;
            border: 0 none;
            border-color:#ffffff;
          }
        </style>';

        $html .= '<table class="bottom-table" cellpadding="0" cellspacing="0" width="100%" style="width:100%">
        	<tr>
        	<td width="20%" style="vertical-align:top;"><br><br><br><img src="/local/resource/images/logo.svg"></td>
        	<td width="80%" style="text-align:right;vertical-align:top;">
        	<p style="margin-top:0;padding-top:0;">Индивидуальному предпринимателю Боджгуа Д.В.<br />
            (интернет-магазин www.sodamoda.ru)<br />
            123104, г. Москва, ул. М. Бронная, дом № 19А, пом. 3</p>';
        $html .= '<p>'.$name.'<br>'.$phone.'</p>';

        $html .= '<p>Паспорт серии '. $passportSeries .' номер '. $passportNumber .'<br>Выдан '. $passportIssued .'<br>Проживающему по адресу '. $passportAddress .'</p>';
        $html .= '</td>
        	</tr>
        </table>
        <h1>ЗАЯВЛЕНИЕ НА ВОЗВРАТ ТОВАРА</h1>
        <p>'. $date .' я приобрел(а) в интернет-магазине «www.sodamoda.ru» в соответствии с заказом № '.$order_id.' на сумму '.$price.' В соответствии с п. 2 ст. 25 Закона РФ от 07.02.1992 № 2300-1 (ред. от 18.03.2019) «О защите прав потребителей» я отказываюсь от исполнения договора купли-продажи и прошу произвести возврат следующих артикулов из заказа:</p>
        <table class="basket-table" cellpadding="4" cellspacing="0" width="100%" style="width:100%">
          <tr class="head">
            <td width="10%">Артикул</td>
            <td width="50%">Название</td>
            <td width="15%">Размер</td>
            <td width="12.5%">Количество</td>
            <td width="12.5%">Сумма</td>
          </tr>
          '. $items .'
        </table>
        <p>Причина возврата: '. $reason .'</p>
        <h3>Реквизиты для возврата денежных средств</h3>
        <p>'; // <p>ИНН: '. $inn .'<br>';

        $html .= 'Номер лицевого счета получателя: '. $account .'<br>';
        $html .= 'БИК Банка: '. $bik .'<br>';
        $html .= 'Название Банка: '. $bank .'<br>';
        $html .= 'Последние 4 цифры карты с которой производилась оплата: '. $CARD .'<br><br>';
        $html .= 'Способ возврата товара: '. $RETURN_TYPE .'<br>';
        $html .=  $additional .'</p>
        <div>
          <p>Я проинформирован, что в соответствии со ст.25 Закона РФ «О защите прав потребителя» Обмен / Возврат непродовольственного товара надлежащего качества проводится, если указанный товар не был в употреблении, сохранены его товарный вид, потребительские свойства, пломбы, фабричные ярлыки.<br><br>';
          $html .= 'Я проинформирован, что возврат денежных средств осуществляется тем же способом, каким были получены за товар.<br><br>';
          $html .= 'Я проинформирован о том, что я обязан приложить копию своего паспорта к настоящему заявлению для рассмотрения настоящего заявления по существу.<br><br>';
          $html .= 'Я проинформирован, что возврат денежных средств осуществляется в течении 5 банковских дней, с момента получения товара продавцом, при наличии оригинала заявления.<br><br><br></p>
        </div>
        ';

        $html .= '<table class="bottom-table" cellpadding="4" cellspacing="0" width="100%" style="width:100%"><tr><td width="50%">'. $dateNow .'</td><td width="50%" style="text-align:right;">Подпись _________ / '.$name.' /</td></tr></table>';


        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf_filename = '/upload/pdf/return_'.$order_id.'.pdf';
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].$pdf_filename, 'F');

        $_POST['product'] = explode("##", $_POST['product']);

        $aEventFields = Array(
          "SIMPLE_QUESTION_207" => htmlspecialcharsbx($_POST['ORDER_ID']),
          "SIMPLE_QUESTION_592" => htmlspecialcharsbx($_POST['name']),
          "SIMPLE_QUESTION_112" => phoneFormat(htmlspecialcharsbx($_POST['USER_PHONE'])),
          "SIMPLE_QUESTION_954" => implode('<br>', $aItemsForMessage),
          "SIMPLE_QUESTION_280" => htmlspecialcharsbx($_POST['REASONS']),
          //"SIMPLE_QUESTION_584" => htmlspecialcharsbx($_POST['INN']),
          "SIMPLE_QUESTION_565" => htmlspecialcharsbx($_POST['ACCOUNT']),
          "SIMPLE_QUESTION_636" => htmlspecialcharsbx($_POST['BIC']),
          "SIMPLE_QUESTION_199" => htmlspecialcharsbx($_POST['BANK']),
          "SIMPLE_QUESTION_656" => htmlspecialcharsbx($_POST['RETURN_TYPE']),
          "SIMPLE_QUESTION_830" => htmlspecialcharsbx($_POST['TC_NAME']),
          "SIMPLE_QUESTION_556" => htmlspecialcharsbx($_POST['TC_TRACK']),
          "SIMPLE_QUESTION_571" => htmlspecialcharsbx($_POST['TC_DATE']),
        );
        CEvent::Send("FORM_FILLING_SIMPLE_FORM_1", "s1", $aEventFields, "Y", 91, array($pdf_filename));
        
        //CFormResult::Mail($RESULT_ID);
        $result['order_id'] = $arValues['form_text_9'];
        $result['status'] = true;
      }
      else
      {
        global $strError;
        $result['message'] = $strError;
      }
    }
    break;

  case 'returnNotAuth':
    CModule::IncludeModule("form");

    $product = $aItems = '';
    for($i = 0; $i < count($_POST['article']); $i++) {
      $product .= $_POST['article'][$i].'##'.$_POST['nameProduct'][$i].'##'.$_POST['size'][$i].'##'.$_POST['quantity'][$i].'##'.$_POST['price'][$i];
      $aItems .= $_POST['article'][$i].';'.$_POST['nameProduct'][$i].';'.$_POST['size'][$i].';'.$_POST['quantity'][$i].';'.$_POST['price'][$i];
      if($i !== count($_POST['article']) - 1) {
        $product .= '<br>';
        $aItems .= '+';
      }
    }

    if(!htmlspecialcharsbx($_POST['USER_PHONE']))
      $result['message'] = Loc::getMessage('AJAX_ENTER_PHONE');
    else
    {
      $arValues = Array(
        'form_text_1' => htmlspecialcharsbx($_POST['name']),
        'form_text_2' => phoneFormat(htmlspecialcharsbx($_POST['USER_PHONE'])),
        'form_textarea_3' => $product,
        'form_text_4' => htmlspecialcharsbx($_POST['REASONS']),
        //'form_text_5' => htmlspecialcharsbx($_POST['INN']),
        'form_text_6' => htmlspecialcharsbx($_POST['ACCOUNT']),
        'form_text_7' => htmlspecialcharsbx($_POST['BIC']),
        'form_text_8' => htmlspecialcharsbx($_POST['BANK']),
        'form_text_9' => htmlspecialcharsbx($_POST['orderId']),
        'form_text_12' => htmlspecialcharsbx($_POST['TC_NAME']),    // Название транспортной компании
        'form_text_13' => htmlspecialcharsbx($_POST['TC_TRACK']),   // Номер трека (отправления)
        'form_text_14' => htmlspecialcharsbx($_POST['TC_DATE']),    // Дата отправления
        'form_text_15' => htmlspecialcharsbx($_POST['RETURN_TYPE']),// Способ возврата товара
        'form_text_16' => htmlspecialcharsbx($_POST['CARD']),
        'form_text_17' => htmlspecialcharsbx($_POST['PASSPORT_SERIES']),
        'form_text_18' => htmlspecialcharsbx($_POST['PASSPORT_NUMBER']),
        'form_text_19' => htmlspecialcharsbx($_POST['PASSPORT_ISSUED']),
        'form_text_20' => htmlspecialcharsbx($_POST['PASSPORT_ADDRESS']),
      );

      $date = FormatDate('"d" F Y', MakeTimeStamp($_POST['dateSale']), time() + CTimeZone::GetOffset());
      $price = htmlspecialcharsbx($_POST['totalPrice']).' '.$_POST['currency'];
      $name = htmlspecialcharsbx($_POST['name']);
      $phone = phoneFormat(htmlspecialcharsbx($_POST['USER_PHONE']));
      $products = $product;
      $reason = htmlspecialcharsbx($_POST['REASONS']);
      //$inn = htmlspecialcharsbx($_POST['INN']);
      $account = htmlspecialcharsbx($_POST['ACCOUNT']);
      $bik = htmlspecialcharsbx($_POST['BIC']);
      $bank = htmlspecialcharsbx($_POST['BANK']);
      $CARD = htmlspecialcharsbx($_POST['CARD']);
      $order_id = htmlspecialcharsbx($_POST['orderId']);
      $TC_NAME = htmlspecialcharsbx($_POST['TC_NAME']);
      $TC_TRACK = htmlspecialcharsbx($_POST['TC_TRACK']);
      $TC_DATE = htmlspecialcharsbx($_POST['TC_DATE']);
      $RETURN_TYPE = htmlspecialcharsbx($_POST['RETURN_TYPE']);

      $passportSeries = htmlspecialcharsbx($_POST['PASSPORT_SERIES']);
      $passportNumber = htmlspecialcharsbx($_POST['PASSPORT_NUMBER']);
      $passportIssued = htmlspecialcharsbx($_POST['PASSPORT_ISSUED']);
      $passportAddress = htmlspecialcharsbx($_POST['PASSPORT_ADDRESS']);

      $dateNow = htmlspecialcharsbx($_POST['date_now']);

      // создадим новый результат
      if($RESULT_ID = CFormResult::Add(1, $arValues))
      {
        // pdf
        require_once($_SERVER["DOCUMENT_ROOT"].'/local/lib/tcpdf/tcpdf.php');
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetCreator('SODAMODA');
        $pdf->SetAuthor('SODAMODA');
        $pdf->SetTitle('Заявление на возврат');
        $pdf->SetSubject('Заявление на возврат');
        $pdf->SetKeywords('Заявление на возврат');

        $pdf->SetMargins(8, 10, 10, 5);
        $fontname = TCPDF_FONTS::addTTFfont($_SERVER["DOCUMENT_ROOT"].'/local/lib/tcpdf/fonts/HelveticaNeueCyr-Roman.ttf', 'TrueTypeUnicode', '', 32);
        $pdf->SetFont($fontname, '', 9, '', false);

        $pdf->AddPage();
        $pdf->SetDrawColor(210, 100, 0);
        $pdf->SetTextColor(71, 71, 71);

        $returnPrice = 0;
        $items = '';
        $aItems = explode("+", $aItems);
        $aItemsForMessage = Array();
        foreach ($aItems as $value) {
          $aTmp = explode(";", $value);
          $aItemsForMessage[] = $aTmp[1].' &mdash; '.$aTmp[3].'х'.$aTmp[4];
          $items .= '<tr class="items"><td>'.$aTmp[0].'</td><td>'.$aTmp[1].'</td><td>'.$aTmp[2].'</td><td>'.$aTmp[3].'</td><td>'.$aTmp[4].'</td></tr>';
          $returnPrice += preg_replace('~\D+~','', $aTmp[4]);
        }

        $additional = '';
        if ($TC_NAME) {
          $additional .= 'Название транспортной компании: '.$TC_NAME.'<br>';
        }
        if ($TC_TRACK) {
          $additional .= 'Номер трека (отправления): '.$TC_TRACK.'<br>';
        }
        if ($TC_DATE) {
          $additional .= 'Дата отправления: '.$TC_DATE.'';
        }

        $html = '
        <style>
          .basket-table {
            border-collapse: collapse; 
            margin-top: 40px;
            margin-bottom: 40px;
          }
          .basket-table tr td {
            border-bottom: 1px solid #CDCDCD;
          }
          .basket-table tr.head td {
            text-align: left;
            color: #444;
            padding-bottom: 12px;
            margin-bottom: 12px;
            text-transform: uppercase;
          }
          .basket-table tr.items td {
            padding: 18px 0;
            vertical-align: middle;
          }
          .bottom-table {
            border-collapse: collapse;
            border: 0 none;
            border-color:#ffffff;
          }
          .bottom-table td {
            color: #444;
            border: 0 none;
            border-color:#ffffff;
          }
        </style>';

        $html .= '<table class="bottom-table" cellpadding="0" cellspacing="0" width="100%" style="width:100%">
        	<tr>
        	<td width="20%" style="vertical-align:top;"><br><br><br><img src="/local/resource/images/logo.svg"></td>
        	<td width="80%" style="text-align:right;vertical-align:top;">
        	<p style="margin-top:0;padding-top:0;">Индивидуальному предпринимателю Боджгуа Д.В.<br />
            (интернет-магазин www.sodamoda.ru)<br />
            123104, г. Москва, ул. М. Бронная, дом № 19А, пом. 3</p>';
        $html .= '<p>'.$name.'<br>'.$phone.'</p>';

        $html .= '<p>Паспорт серии '. $passportSeries .' номер '. $passportNumber .'<br>Выдан '. $passportIssued .'<br>Проживающему по адресу '. $passportAddress .'</p>';
        $html .= '</td>
        	</tr>
        </table>
        <h1>ЗАЯВЛЕНИЕ НА ВОЗВРАТ ТОВАРА</h1>
        <p>'. $date .' я приобрел(а) в интернет-магазине «www.sodamoda.ru» в соответствии с заказом № '.$order_id.' на сумму '.$price.' В соответствии с п. 2 ст. 25 Закона РФ от 07.02.1992 № 2300-1 (ред. от 18.03.2019) «О защите прав потребителей» я отказываюсь от исполнения договора купли-продажи и прошу произвести возврат следующих артикулов из заказа:</p>
        <table class="basket-table" cellpadding="4" cellspacing="0" width="100%" style="width:100%">
          <tr class="head">
            <td width="10%">Артикул</td>
            <td width="50%">Название</td>
            <td width="15%">Размер</td>
            <td width="12.5%">Количество</td>
            <td width="12.5%">Сумма</td>
          </tr>
          '. $items .'
        </table>
        <p>Итого для возврата: '.number_format($returnPrice, 0, '', ' ').' '.$_POST['currency'].'</p>
        <p>Причина возврата: '. $reason .'</p>
        <h3>Реквизиты для возврата денежных средств</h3>
        <p>'; //<p>ИНН: '. $inn .'<br>';

        $html .= 'Номер лицевого счета получателя: '. $account .'<br>';
        $html .= 'БИК Банка: '. $bik .'<br>';
        $html .= 'Название Банка: '. $bank .'<br>';
        $html .= 'Последние 4 цифры карты с которой производилась оплата: '. $CARD .'<br><br>';
        $html .= 'Способ возврата товара: '. $RETURN_TYPE .'<br>';
        $html .=  $additional .'</p>
        <div>
          <p>Я проинформирован, что в соответствии со ст.25 Закона РФ «О защите прав потребителя» Обмен / Возврат непродовольственного товара надлежащего качества проводится, если указанный товар не был в употреблении, сохранены его товарный вид, потребительские свойства, пломбы, фабричные ярлыки.<br><br>';
        $html .= 'Я проинформирован, что возврат денежных средств осуществляется тем же способом, каким были получены за товар.<br><br>';
        $html .= 'Я проинформирован о том, что я обязан приложить копию своего паспорта к настоящему заявлению для рассмотрения настоящего заявления по существу.<br><br>';
        $html .= 'Я проинформирован, что возврат денежных средств осуществляется в течении 5 банковских дней, с момента получения товара продавцом, при наличии оригинала заявления.<br><br><br></p>
        </div>
        ';

        $html .= '<table class="bottom-table" cellpadding="4" cellspacing="0" width="100%" style="width:100%;"><tr><td width="50%">'. $dateNow .'</td><td width="50%" style="text-align:right;">Подпись _________ / '.$name.' /</td></tr></table>';


        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf_filename = '/upload/pdf/return_'.$order_id.'.pdf';
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].$pdf_filename, 'F');

        $aEventFields = Array(
          "SIMPLE_QUESTION_207" => $order_id,
          "SIMPLE_QUESTION_291" => $passportSeries,
          "SIMPLE_QUESTION_125" => $passportNumber,
          "SIMPLE_QUESTION_283" => $passportIssued,
          "SIMPLE_QUESTION_156" => $passportAddress,
          "SIMPLE_QUESTION_592" => htmlspecialcharsbx($_POST['name']),
          "SIMPLE_QUESTION_112" => phoneFormat(htmlspecialcharsbx($_POST['USER_PHONE'])),
          "SIMPLE_QUESTION_954" => implode('<br>', $aItemsForMessage),
          "RETURN_PRICE" => 'Итого для возврата: '.number_format($returnPrice, 0, '', ' ').' '.$_POST['currency'],
          "SIMPLE_QUESTION_280" => htmlspecialcharsbx($_POST['REASONS']),
          //"SIMPLE_QUESTION_584" => htmlspecialcharsbx($_POST['INN']),
          "SIMPLE_QUESTION_565" => htmlspecialcharsbx($_POST['ACCOUNT']),
          "SIMPLE_QUESTION_636" => htmlspecialcharsbx($_POST['BIC']),
          "SIMPLE_QUESTION_199" => htmlspecialcharsbx($_POST['BANK']),
          "SIMPLE_QUESTION_656" => htmlspecialcharsbx($_POST['RETURN_TYPE']),
          "SIMPLE_QUESTION_830" => htmlspecialcharsbx($_POST['TC_NAME']),
          "SIMPLE_QUESTION_556" => htmlspecialcharsbx($_POST['TC_TRACK']),
          "SIMPLE_QUESTION_571" => htmlspecialcharsbx($_POST['TC_DATE']),
        );
        CEvent::Send("FORM_FILLING_SIMPLE_FORM_1", "s1", $aEventFields, "Y", 91, array($pdf_filename));

        //CFormResult::Mail($RESULT_ID);
        $result['status'] = true;
      }
      else
      {
        global $strError;
        $result['message'] = $strError;
      }
    }
    break;


  //////////////////////////////////////////////////////////////////////////////
  // Авторизация пользователя
  case 'authorize':
    $login = htmlspecialcharsbx($_POST['USER_LOGIN']);
    $password = htmlspecialcharsbx($_POST['USER_PASSWORD']);
    
    $result['form']['login'] = $login;
    $result['form']['pass'] = $password;
    
    if($login)
    {
      if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
        // За место e-mail подставляем логин
        $rsUsers = CUser::GetList(($by = 'LAST_LOGIN'), ($order = 'DESC'), Array('EMAIL' => $login));
        if($rsUsers->SelectedRowsCount() > 0)
        {
          while($arUser = $rsUsers->Fetch())
          {
            if(!empty($arUser['LOGIN']))
              $login = $arUser['LOGIN'];
            break;
          }
        }
      }
      else{
        $login = UserPhoneAuthTable::normalizePhoneNumber($login);
        $userPhone = UserPhoneAuthTable::getList(["filter" => ["PHONE_NUMBER" => $login]])->fetch();
          //var_dump($userPhone['CONFIRMED']);
          //die();
        if (isset($userPhone['USER_ID'])) {
          //if ($userPhone['CONFIRMED'] === 'Y') {
            $rsUsers = CUser::GetList(($by = 'LAST_LOGIN'), ($order = 'DESC'), Array('ID' => $userPhone['USER_ID']));
            if($rsUsers->SelectedRowsCount() > 0)
            {
              while($arUser = $rsUsers->Fetch())
              {
                if(!empty($arUser['LOGIN']))
                    $login = $arUser['LOGIN'];
                break;
              }
            }
          //}
        }
      }
      $res = $USER->Login($login, $password, 'Y');
      $APPLICATION->arAuthResult = $res;
      
      $result['form']['message'] = $res;
      
      if(empty($res['MESSAGE'])) 
      {
        $result['status'] = true;
        $result['message'] = "";
      } 
      else 
      {
        $result['message'] = str_replace('<br>', ' ', $res['MESSAGE']);
      }
    }
    else
      $result['message'] = Loc::getMessage('AJAX_ENTER_EMAIL');
    break;


  //////////////////////////////////////////////////////////////////////////////
  // Регистрация пользователя
  case 'register':
    if(!$USER->IsAuthorized())
    {
      if(htmlspecialcharsbx($_POST['rule']) != 'Y')
      {
        $result['message'] = Loc::getMessage('AJAX_AGREEMENT_PRIVACY');
        break;
      }

      $email = htmlspecialcharsbx($_POST['USER_EMAIL']);
      $password = htmlspecialcharsbx($_POST['USER_PASSWORD']);
      $password_confirm = htmlspecialcharsbx($_POST['USER_CONFIRM_PASSWORD']);
      $userName = htmlspecialcharsbx($_POST['USER_NAME']);
      $lastUserName = htmlspecialcharsbx($_POST['USER_LAST_NAME']);
      $phone = htmlspecialcharsbx($_POST['USER_PHONE']);

      if($password !== $password_confirm) {
        $result['message'] = Loc::getMessage('AJAX_PASSWORD_NOT_MATCH');
        break;
      }

      $res = $USER->Register(
        $email,
        $userName,
        $lastUserName,
        $password,
        $password_confirm,
        $email,
        false,
        '',
        0,
        false,
        $phone
      );

      if($res['TYPE'] == 'ERROR')
        $result['message'] = str_replace('<br>', ' ', $res['MESSAGE']);
      else 
      {
        $result['status'] = true;
        if(htmlspecialcharsbx($_POST['sender']) == 'Y') {
          if(CModule::IncludeModule("sender")) {
            \Bitrix\Sender\Subscription::add($email, []);
          }
        }

        if($res["ID"] > 0 && !empty($phone)) {
          $user = new CUser;
          $user->Update($res["ID"], ["PERSONAL_PHONE" => $phone]);
        }
      }

    }
    else {
      $result['message'] = "";
      $result['status'] = true;
    }
    break;
      
      
    //////////////////////////////////////////////////////////////////////////////
    // Регистрация. Проверка телефона
    case 'register-check-phone':
      $userPhone = htmlspecialcharsbx($_POST['USER_PHONE']);
      $userPhone = str_replace([' ', '-'], ['', ''], $userPhone);
      $verifyCode = htmlspecialcharsbx($_POST['SMS_CODE']);
      if ($userId = CUser::VerifyPhoneCode($userPhone, $verifyCode)) {
        $user = new CUser();
        $user->Update($userId, ["ACTIVE" => "Y"]);
        // authorize user
        $USER->Authorize($userId);

        $result['message'] = "";
        $result['status'] = true;
      }
      else {
        $result['message'] = $userId;
        $result['status'] = true;
      }
      break;
      
      
    //////////////////////////////////////////////////////////////////////////////
    // Заказ. Проверка телефона
    case 'order-check-phone':
      $userPhone = htmlspecialcharsbx($_POST['phone']);

      if($USER->IsAuthorized()){
          $userID = $USER->GetID();
          $userPhoneCheck = UserPhoneAuthTable::getList(["filter" => ["=USER_ID" => $userID]])->fetch();
          if($userPhoneCheck !== false && $userPhoneCheck['PHONE_NUMBER'] !== '+'.$userPhone){
              UserPhoneAuthTable::delete($userPhoneCheck['USER_ID']);
              UserPhoneAuthTable::add([
                  "USER_ID" => $userID,
                  "PHONE_NUMBER" => $userPhone,
              ]);
              $userPhoneCheck = UserPhoneAuthTable::getList(["filter" => ["=PHONE_NUMBER" => '+'.$userPhone]])->fetch();
          }
          else{
              UserPhoneAuthTable::add([
                  "USER_ID" => $userID,
                  "PHONE_NUMBER" => $userPhone,
              ]);
              $userPhoneCheck = UserPhoneAuthTable::getList(["filter" => ["=PHONE_NUMBER" => '+'.$userPhone]])->fetch();
          }
      }
    else
    {
      $userPhoneCheck = UserPhoneAuthTable::getList(["filter" => ["=PHONE_NUMBER" => '+'.$userPhone]])->fetch();
    }

    if (isset($userPhoneCheck['USER_ID'])) {
      CUser::SendPhoneCode($userPhone, 'SMS_USER_CONFIRM_NUMBER');
      $result['message'] = $userPhoneCheck['USER_ID'];
      $result['status'] = true;
    } else {
      $result['message'] = Loc::getMessage('AJAX_SMS_ERROR_PHONE');
      $result['status'] = false;
    }
    
    break;
      
      
    //////////////////////////////////////////////////////////////////////////////
    // Заказ. Проверка СМС кода и дальнейшая авторизация пользователя
    case 'order_verify_phone':
      $userPhone = htmlspecialcharsbx($_POST['phone']);
      $userPhone = str_replace([' ', '-'], ['', ''], $userPhone);
      $verifyCode = htmlspecialcharsbx($_POST['code_confirmed']);
      $userId = CUser::VerifyPhoneCode($userPhone, $verifyCode);
      if ($userId) {
        $USER->Authorize($userId);

      $result['message'] = "";
      $result['status'] = true;
    }
    else {
      $result['message'] = Loc::getMessage('AJAX_SMS_ERROR_CODE');
      $result['status'] = false;
    }
    break;

      
    //////////////////////////////////////////////////////////////////////////////
    // ЛК. Проверка телефона
    case 'profile-check-phone':
      $userPhone = htmlspecialcharsbx($_POST['PHONE_NUMBER']);
      $userID = htmlspecialcharsbx($_POST['ID']);
      $userPhoneCheck = UserPhoneAuthTable::getList(["filter" => ["=PHONE_NUMBER" => '+'.$userPhone]])->fetch();
      if (isset($userPhoneCheck['USER_ID'])) {
        CUser::SendPhoneCode($userPhone, 'SMS_USER_CONFIRM_NUMBER');
        $result['message'] = $userPhoneCheck['USER_ID'];
        $result['status'] = true;
      }
      else {
        $userPhoneCheck = UserPhoneAuthTable::getList(["filter" => ["=USER_ID" => $userID]])->fetch();
        if('+'.$userPhone != $userPhoneCheck['PHONE_NUMBER']){
          UserPhoneAuthTable::delete($userPhoneCheck['USER_ID']);
          UserPhoneAuthTable::add([
            "USER_ID" => $userID,
            "PHONE_NUMBER" => $userPhone,
          ]);
          CUser::SendPhoneCode($userPhone, 'SMS_USER_CONFIRM_NUMBER');
          $result['message'] = $userID;
          $result['status'] = true;
        }
        else{
          $result['message'] = $userPhoneCheck;
          $result['status'] = false;
        }
      }
      break;


    //////////////////////////////////////////////////////////////////////////////
    // ЛК. Проверка подтверждения телефона
    case 'personal-check-confirm-phone':
      $userPhone = htmlspecialcharsbx($_POST['phone']);
      $userPhoneCheck = UserPhoneAuthTable::getList(["filter" => ["=PHONE_NUMBER" => '+'.$userPhone]])->fetch();

      if (isset($userPhoneCheck['USER_ID'])) 
      {
        if($userPhoneCheck['CONFIRMED'] === 'Y') 
        {
          $result['message'] = $userPhoneCheck['USER_ID'];
          $result['status'] = true;
        }
        else 
        {
          $result['message'] = $userPhoneCheck['USER_ID'];
          $result['status'] = false;
        }
      }
      else 
      {
        $result['message'] = $userPhoneCheck;
        $result['status'] = false;
      }
      break;

      
    case 'vacancy':

      $arLegalExt = ['txt','doc','docx','pdf','xls','xlsx','zip','rar','7zip','gzip','gz'];
      $result['ext_status'] = true;

      // Если есть файл - надо сохранить его на сервер и использовать этот путь
      $legalExt = $arFile = false;
      if(!empty($_FILES['resume']['tmp_name'])) 
      {
        $name = $_FILES['resume']['name'];
        $ext = explode('.', $name);
        $ext = end($ext);
        
        if(in_array($ext, $arLegalExt))
          $legalExt = true;

        if($legalExt)
        {
          $uploads_dir = $_SERVER['DOCUMENT_ROOT'].'/upload/resume/';
          $filePath = $uploads_dir.$name;
          $is_moved = move_uploaded_file($_FILES['resume']['tmp_name'], $filePath);

          $arFile = CFile::MakeFileArray($filePath);
        }
      }
      else
        $legalExt = true;	// Файл не был отправлен юзером сознательно - остальная форма должна отправиться

      if(!$legalExt)
      {
        $result['status'] = true;
        $result['ext_status'] = false;
        $result['ext_message'] = 'Заявка не отправлена: неверный тип файла резюме. Используйте один из допустимых типов: '.implode(', ', $arLegalExt);
      }
      else
      {
        $arValues = [
          'form_text_43'  => htmlspecialcharsbx($_POST['name']),    // ФИО
          'form_email_44' => htmlspecialcharsbx($_POST['email']),   // Email
          'form_text_45'  => htmlspecialcharsbx($_POST['phone']),   // Phone
          'form_text_47'  => htmlspecialcharsbx($_POST['vacancy']),	// Вакансия
          'form_file_46'  => $arFile
        ];

        if($RESULT_ID = CFormResult::Add(6, $arValues))
        {
          CFormResult::Mail($RESULT_ID);

          if($arFile)
            unlink($filePath);

          $result['status'] = true;
        }
        else
          $result['status'] = false;
      }
      
      break;
    // Заявка от потенциального партнёра на проверку его данных
    case 'new-partner':

      if($USER->IsAuthorized())
      {
        $arData = [];

        // Проверка данных
        $arData['WORK_COMPANY'] = htmlspecialcharsbx($_POST['WORK_COMPANY']);
        $arData['UF_INN'] = htmlspecialcharsbx($_POST['UF_INN']);
        $arData['UF_PSRNIE'] = htmlspecialcharsbx($_POST['UF_PSRNIE']);
        $arData['WORK_STREET'] = htmlspecialcharsbx($_POST['WORK_STREET']);
        $arData['PERSONAL_STREET'] = htmlspecialcharsbx($_POST['PERSONAL_STREET']);
        $arData['UF_OKVED'] = htmlspecialcharsbx($_POST['UF_OKVED']);
        $arData['UF_BANK'] = htmlspecialcharsbx($_POST['UF_BANK']);
        $arData['UF_SETTLEMENT_ACCOUNT'] = htmlspecialcharsbx($_POST['UF_SETTLEMENT_ACCOUNT']);
        $arData['UF_CORRESPONDENT_ACCOUNT'] = htmlspecialcharsbx($_POST['UF_CORRESPONDENT_ACCOUNT']);
        $arData['UF_IBIKNN'] = htmlspecialcharsbx($_POST['UF_IBIKNN']);
        $arData['UF_TAXATION'] = htmlspecialcharsbx($_POST['UF_TAXATION']);

        // Апдейт данных
        $userId = $USER->GetId();
        $user = new CUser;
        $user->Update($userId, $arData);

        if($error = $user->LAST_ERROR)
        {
          $result['status'] = false;
          $result['message'] = $error;
        }
        $userPhone = UserPhoneAuthTable::getList(['filter' => ['=USER_ID' => $USER->GetId()]])->fetch();
        if($userPhone){
          $arData['USER_PHONE'] = $userPhone['PHONE_NUMBER'];
        }
        // Отправить письмо на почту сайта
        $arData['USER_ID'] = $USER->GetId();
        $arData['USER_FIO'] = $USER->GetFullName();
        $arData['USER_EMAIL'] = $USER->GetEmail();

        CEvent::Send('USER_PARTNER_NEW', SITE_ID, $arData);

        if(in_array(17, CUser::GetUserGroup($USER->GetId()))){
          $result['message'] = 'Данные успешно обновлены';
        }
        else{
          $result['message'] = 'Данные отправлены. После рассмотрения заявки, наши менеджеры свяжутся с Вами';
        }
        $result['status'] = true;
      }

      break;
  
  // Получение корзины пользователя
  case 'user-basket':
    Loader::includeModule('sale');
    CBitrixComponent::includeComponentClass('arlix:product.subscribe');
    Loader::includeModule('highloadblock');
    
    $hlblock = HighloadBlockTable::getById(2)->fetch();
    $entity = HighloadBlockTable::compileEntity($hlblock);
    $PsuTaskControl = $entity->getDataClass();
    
    if(isset($_POST['orderID'])){
      $basket = Sale\Order::load($_POST['orderID'])->getBasket();
    }
    else{
      $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
    }
    $messages = [];
    $result['status'] = true;
    foreach ($basket as $basketItem) {
      $props = $basketItem->getPropertyCollection();
      $propsValue = $props->getPropertyValues();
      $mxResult = \CCatalogSku::GetProductInfo($basketItem->getProductId());
      $res = \CIBlockElement::GetByID($mxResult['ID']);
      $isPreorder = false;
      $colorXMLID = false;
      $reslist = $PsuTaskControl::getList([
        'filter' => [
          'UF_NAME' => $propsValue['COLOR']['VALUE'],
        ],
      ]);
      if ($el = $reslist->fetch()) {
        $colorXMLID = $el['UF_XML_ID'];
      }
      $productStatus = [];
      $rsElement = CIBlockElement::GetList([], ['IBLOCK_ID' => COLORS_ID, 'PROPERTY_PARENT' => $mxResult['ID'], 'PROPERTY_COLOR' => $colorXMLID], false, false, ['PROPERTY_PREORDER']);
      while ($arElement = $rsElement->GetNext()) {
        $isPreorder = !empty($arElement['PROPERTY_PREORDER_ENUM_ID']) && $arElement['PROPERTY_PREORDER_ENUM_ID'] !== 'N';
      }
      $productStatus[$basketItem->getProductId()] = [
        'id' => $basketItem->getProductId(),
        'preorder' => $isPreorder,
        'available' => LikesComponent::checkAvailableOffers($basketItem->getProductId())['AVAILABLE'],
      ];
      if (!LikesComponent::checkAvailableOffers($basketItem->getProductId())['AVAILABLE'] || $isPreorder) {
        if ($ar_res = $res->GetNext()) {
          $itemProduct = $ar_res;
        }
        $messages[] = [
          'product' => $basketItem->getProductId(),
          'name' => $itemProduct['NAME'],
          'props' => $props->getPropertyValues(),
          'available' => LikesComponent::checkAvailableOffers($basketItem->getProductId())['AVAILABLE'],
          'stocks' => LikesComponent::checkAvailableOffers($basketItem->getProductId()),
        ];
      }
      if ($result['status'] && !LikesComponent::checkAvailableOffers($basketItem->getProductId())['AVAILABLE'] && !$isPreorder) {
        $result['status'] = false;
      }
    }
    foreach ($productStatus as $product){
      if(!$result['status']){
        $result['status'] = ($isPreorder || LikesComponent::checkAvailableOffers($basketItem->getProductId())['AVAILABLE']);
      }
    }
    $result['message'] = 'Корзина проверена.';
    $result['messages'] = $messages;
    break;
  
  // Получение корзины пользователя
  case 'user-check-basket':
    Loader::includeModule('sale');
    CBitrixComponent::includeComponentClass('arlix:product.subscribe');
    Loader::includeModule('highloadblock');
    
    $hlblock = HighloadBlockTable::getById(2)->fetch();
    $entity = HighloadBlockTable::compileEntity($hlblock);
    $PsuTaskControl = $entity->getDataClass();
    
    if(isset($_POST['orderID'])){
      $basket = Sale\Order::load($_POST['orderID'])->getBasket();
    }
    else{
      $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
    }
    $messages = [];
    $result['status'] = true;
    $productStatus = [];
    foreach ($basket as $basketItem) {
      $props = $basketItem->getPropertyCollection();
      $propsValue = $props->getPropertyValues();
      $mxResult = \CCatalogSku::GetProductInfo($basketItem->getProductId());
      $res = \CIBlockElement::GetByID($mxResult['ID']);
      $isPreorder = false;
      $colorXMLID = false;
      $reslist = $PsuTaskControl::getList([
        'filter' => [
          'UF_NAME' => $propsValue['COLOR']['VALUE'],
        ],
      ]);
      if ($el = $reslist->fetch()) {
        $colorXMLID = $el['UF_XML_ID'];
      }
      $rsElement = CIBlockElement::GetList([], ['IBLOCK_ID' => COLORS_ID, 'PROPERTY_PARENT' => $mxResult['ID'], 'PROPERTY_COLOR' => $colorXMLID], false, false, ['PROPERTY_PREORDER']);
      while ($arElement = $rsElement->GetNext()) {
        if(!$isPreorder){
          $isPreorder = !empty($arElement['PROPERTY_PREORDER_ENUM_ID']) && $arElement['PROPERTY_PREORDER_ENUM_ID'] !== 'N';
        }
      }
      if ($ar_res = $res->GetNext()) {
        $itemProduct = $ar_res;
      }
      if (!LikesComponent::checkAvailableOffers($basketItem->getProductId())['AVAILABLE']) {
        if($isPreorder){
          $messages['available'][] = [
            'product' => $basketItem->getProductId(),
            'name' => $itemProduct['NAME'],
            'props' => $props->getPropertyValues(),
            'available' => LikesComponent::checkAvailableOffers($basketItem->getProductId())['AVAILABLE'],
            'stocks' => LikesComponent::checkAvailableOffers($basketItem->getProductId()),
          ];
        }
        else{
          $messages['not_available'][] = [
            'product' => $basketItem->getProductId(),
            'name' => $itemProduct['NAME'],
            'props' => $props->getPropertyValues(),
            'available' => LikesComponent::checkAvailableOffers($basketItem->getProductId())['AVAILABLE'],
            'stocks' => LikesComponent::checkAvailableOffers($basketItem->getProductId()),
          ];
        }
      }
      else{
        $messages['available'][] = [
          'product' => $basketItem->getProductId(),
          'name' => $itemProduct['NAME'],
          'props' => $props->getPropertyValues(),
          'available' => LikesComponent::checkAvailableOffers($basketItem->getProductId())['AVAILABLE'],
          'stocks' => LikesComponent::checkAvailableOffers($basketItem->getProductId()),
        ];
      }
      $productStatus[$basketItem->getProductId()] = [
        'id' => $basketItem->getProductId(),
        'preorder' => $isPreorder,
        'available' => LikesComponent::checkAvailableOffers($basketItem->getProductId())['AVAILABLE'],
      ];
      if ($result['status'] && !LikesComponent::checkAvailableOffers($basketItem->getProductId())['AVAILABLE'] && !$isPreorder) {
        $result['status'] = false;
      }
    }
    foreach ($productStatus as $product){
      if(!$result['status']){
        $result['status'] = ($product['preorder'] || $product['available']);
      }
    };
    $result['test'] = $productStatus;
    $result['message'] = 'Корзина проверена.';
    $result['messages'] = $messages;
    break;
	
	// Получение корзины пользователя
	case 'user-clear-basket':
		if (isset($_POST["BasketClear"]) && $_POST["BasketClear"] === 'Y' && Loader::includeModule("sale")) {
			CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID());
		}
		break;
}


// Очистка буфера
//ob_end_clean();
		
//header('Content-Type: application/json');
//echo json_encode($result, JSON_UNESCAPED_UNICODE);
//exit();

exit(json_encode($result));