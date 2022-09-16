<?


/**
 * Use: telegramToChannel("Отправка<br>сообщения"); 
 * @param type $message
 */
function telegramToChannel($message, $channel = TELEGRAM_CHANNAL_MAIN)
{
  $message = str_replace(['<br>', '<br/>', '<br />', '</br>'], ['\n '], $message);
  
  //$telegram_channel = '@sodamoda_channel'; // Публичный, тестовый канал
  //$telegram_channel = '-1001436057583'; // Частный, тестовый канал
  //$telegram_channel = '-1001265308640';

  $option = ["chat_id" => $channel, "text" => $message, "parse_mode" => "HTML", "disable_web_page_preview" => True];
  $answer = telegramSend("sendMessage", $option);
}


function telegramSend($type, $option) 
{
  $token = "1750163265:AAH2TyZyIkdRTRejdQ5AeEzZAH0OoKUPHrw";
  $zapros = curl_init();
  curl_setopt_array(
      $zapros,
      array(
          CURLOPT_URL => 'https://api.telegram.org/bot'. $token .'/'. $type,
          CURLOPT_RETURNTRANSFER => TRUE, 
          CURLOPT_TIMEOUT => 10, 
          CURLOPT_POST => TRUE,
          CURLOPT_POSTFIELDS => $option,
      )
  );
  $answer = curl_exec($zapros);	
  curl_close($zapros);
  $answer = json_decode($answer, true);	
  // Записать ответ от телеграма в txt файл
  file_put_contents($_SERVER['DOCUMENT_ROOT'] .'/upload/log_telegram.txt', date("d.m.Y H:i:s") ."\n". print_r($answer, true) ."\n\n", FILE_APPEND);
  
  return $answer;
}


/**
 * Логирование
 */
function logArlix($value, $fileName = 'log_arlix.txt', $fileCall = __FILE__)
{
  return false;
  
  global $USER;
  
  //if(in_array($USER->GetID(), [313, 2]))
  //{
    $value = 'USER->'. $USER->GetID() .' '. $value;
  
    file_put_contents($_SERVER['DOCUMENT_ROOT'] .'/upload/'. $fileName, date("d.m.Y H:i:s") ."\t". str_replace($_SERVER['DOCUMENT_ROOT'], '', $fileCall) ."\n". print_r($value, true) ."\n\n", FILE_APPEND);
  //}
}

/**
 * Только для одного пользователя
 */
function dbgz($var, $exit = false) {
	global $USER;

	if ($USER->IsAdmin() && in_array($USER->GetID(), Array(2, 3)))
		dbg($var, $exit);
}

/**
 * Функция для вывода отладочной информации
 */
function dbg($var, $exit = false) {
	echo "<pre>";
	if (is_array($var))
		print_r($var);
	else
		echo $var;
	echo "</pre>";

	if ($exit)
		exit;
}

/**
 * Функция очистки номера телефона от посторонних символов.
 * Возвращает номер российского телефона без кода страны (8 или +7)
 */
function clearPhone($phone) {
  return substr(preg_replace('![^0-9]+!', '', $phone), 1);
}


/**
 * По ссылке на ютуб получаем SRC для iframe
 */
function getYoutubeSrc($url) {
  if (stristr($url, 'youtu.be/'))
    $videoSrc = substr($url, strrpos($url, '/') + 1);

  if (stristr($url, 'watch')) {
    $query = parse_url($url, PHP_URL_QUERY);
    $end = strpos($query, '&') !== false ? strpos($query, '&') - 2 : false;

    if ($end)
      $videoSrc = substr($query, strpos($query, 'v=')+2, $end);
    else
      $videoSrc = substr($query, strpos($query, 'v=')+2);
  }
  
  return 'https://www.youtube.com/embed/'. $videoSrc;
}



/**
 * Проверяет, является ли раздел конечным каталогом
 * @param $iblockID - ID инфоблока
 * @param $sectionID - ID раздела
 * @return bool
 */
function isParentSection($iblockID, $sectionID)
{
  $isParent = true;
  if (\Bitrix\Main\Loader::includeModule('catalog')) {
    $arFilter = [
      "IBLOCK_ID" => $iblockID,
      "SECTION_ID" => $sectionID,
      'ACTIVE' => 'Y'
    ];

    if (CIBlockSection::GetCount($arFilter) > 0) {
      $isParent = false;
    }
  }

  return $isParent;
}


function getBodyClasses()
{
  global $APPLICATION;
  $page = false;
  
  if(IS_MAIN)
    $page = 'main';
  
  if(IS_CATALOG)
    $page = 'catalog';
  
  if(IS_PRODUCT)
    $page = 'product';
  
  if(!$page)
    $page = 'content';
  
  $result = 'page-'. $page;
  
  if($APPLICATION->GetCurDir() == '/order/')
    $result .= ' page-order';
  
  if(IS_ASIDE_HOVER)
    $result .= ' mode-aside-hover';
          
  $result .=  ' site-'. SITE_ID;
  
  return $result;
}


/**
 * Форматированный вывод цены
 */
function priceFormat($price) {
  return number_format($price, 0, '', ' ').'&nbsp;руб.';
}


/**
 * Получить название доставки
 */
function getDeliveryName($arDelivery, $ORDER_DELIVERY_ID)
{
  if($ORDER_DELIVERY_ID == 'sdek:courier')
    return 'СДЭК доставка до двери';

  if($ORDER_DELIVERY_ID == 'sdek:pickup')
    return 'СДЭК доставка до пункта выдачи';

  if($ORDER_DELIVERY_ID == 'new41:profile')
    return 'Dostavista доставка до двери';

  if($ORDER_DELIVERY_ID == 'new39:profile')
    return 'Почта России';

  if($ORDER_DELIVERY_ID == 'new40:profile')
    return 'EMS';
  
  if($ORDER_DELIVERY_ID == 'new65:profile') // Нестандартная посылка
    return 'Почта России';
  
  if($ORDER_DELIVERY_ID == 'new66:profile') // Посылка 1 класса
    return 'Почта России';
  
  if($ORDER_DELIVERY_ID == 'new80:profile') // ЕМС международная
    return 'EMS';
  
  if($ORDER_DELIVERY_ID == 'pickpoint:postamat')
    return 'PickPoint';
  
  if($ORDER_DELIVERY_ID == 'ipolh_dpd:COURIER')
    return 'DPD доставка до двери';
    
  if($ORDER_DELIVERY_ID == 'ipolh_dpd:PICKUP')
    return 'DPD доставка до пункта выдачи';
  
  if($ORDER_DELIVERY_ID == 'new76:profile')
    return 'DHL';
  
  if($ORDER_DELIVERY_ID == 'new1:profile')
    return 'Без доставки';
    
  if($ORDER_DELIVERY_ID == 'ups:express_saver')
    return 'UPS';
    
  if(empty($arDelivery['NAME']))
    $arDelivery['NAME'] = $ORDER_DELIVERY_ID;
          
  return $arDelivery['NAME'];
}


/**
 * Морфология
 * wordMorphos(40, "комментарий", "комментария", "комментариев"); 
 */
function wordMorphos($n, $n1, $n2, $n5) 
{
  if($n >= 11 and $n <= 19)
    return $n5;
  
  $n = $n % 10;
  
  if($n == 1) 
    return $n1;
  
  if($n >= 2 and $n <= 4) 
    return $n2;
  
  return $n5;
}


function getForColorProductPhoto($productsIds)
{
  $arSelectOffers = Array("ID", "DETAIL_PICTURE","PROPERTY_CML2_LINK", "PROPERTY_SIZES", "PROPERTY_COLOR");
  $arFilterOffers = Array("IBLOCK_ID" => CATALOG_OFFERS_ID, "ID" => $productsIds, 'DETAIL_PICTURE' => false);
  $resOffers = CIBlockElement::GetList(Array(), $arFilterOffers, false, Array(), $arSelectOffers);
  while ($obOffers = $resOffers->GetNextElement()) {
      $arFieldsOffers = $obOffers->GetFields();
      $arNoPhotoProducts[$arFieldsOffers['ID']]['CML2_LINK'] = $arFieldsOffers['PROPERTY_CML2_LINK_VALUE'];
      $arNoPhotoProducts[$arFieldsOffers['ID']]['COLOR'] = $arFieldsOffers['PROPERTY_COLOR_VALUE'];
      $arNoPhotoProducts[$arFieldsOffers['ID']]['SIZE'] = $arFieldsOffers['PROPERTY_SIZES_VALUE'];
      $parentProducts[] = $arFieldsOffers['PROPERTY_CML2_LINK_VALUE'];
  }

  $arSelectOffers = Array("ID", "IBLOCK_ID", "DETAIL_PICTURE", "PROPERTY_CML2_LINK", "PROPERTY_SIZES", "PROPERTY_COLOR", "PROPERTY_MORE_PHOTO", "PROPERTY_*");
  $arFilterOffers = Array(
      "IBLOCK_ID" => CATALOG_OFFERS_ID,
      "PROPERTY_CML2_LINK"=>$parentProducts,
      array(
          'LOGIC'=>'OR',
          '!DETAIL_PICTURE'=>false,
          '!PROPERTY_MORE_PHOTO'=>false,
      ),
  );
  $resOffers = CIBlockElement::GetList(Array(), $arFilterOffers, false, Array(), $arSelectOffers);
  while ($obOffers = $resOffers->GetNextElement()) {
      $arFieldsOffers = $obOffers->GetFields();
      $arProps = $obOffers->GetProperties();

      if(!$arFieldsOffers['DETAIL_PICTURE']){
          $morePhoto = current($arProps['MORE_PHOTO']['VALUE']);
          $arFieldsOffers['DETAIL_PICTURE'] = $morePhoto;
      }

      $arWithPhotoProducts[$arFieldsOffers['PROPERTY_CML2_LINK_VALUE']][$arFieldsOffers['PROPERTY_COLOR_VALUE']] = array(
          'COLOR'=>$arFieldsOffers['PROPERTY_COLOR_VALUE'],
          'DETAIL_PICTURE'=>$arFieldsOffers['DETAIL_PICTURE'],
          'CML2_LINK'=>$arFieldsOffers['PROPERTY_CML2_LINK_VALUE'],
      );

  }

  return array(
      'NO_PHOTO_PRODUCTS'=>$arNoPhotoProducts,
      'WITH_PHOTO_PRODUCTS'=>$arWithPhotoProducts,
  );
}


function isLangDir()
{
  $directory = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getRequestedPageDirectory();
  $arDirectory = explode('/', $directory);
  if($arDirectory[1] == 'en') {
    return true;
  }

  return false;
}


/**
 * Уменьшение исходных картинок в каталоге товаров
 */
function imagesResizeOriginal(&$arFields) 
{
  if ($arFields["IBLOCK_ID"] == CATALOG_ID || $arFields["IBLOCK_ID"] == COLORS_ID) 
  {
    // Максимальные значения длины и ширины картинки
    $PictureMaxWidth = 2048;
    $PictureMaxHeight = 3072;

    // Каталог
    if ($arFields["IBLOCK_ID"] == CATALOG_ID) 
    {
      // Картинка для анонса
      /*if ($arFields['PREVIEW_PICTURE']['tmp_name']) {
          //изменяем размеры картинки:
          $arNewFile = CIBlock::ResizePicture($arFields['PREVIEW_PICTURE'], array(
              "WIDTH" => $PictureMaxWidth,
              "HEIGHT" => $PictureMaxHeight,
              "METHOD" => "resample",
          ));
          $arFields['PREVIEW_PICTURE'] = $arNewFile;
      }*/
      
      // Детальная картинка
      if ($arFields['DETAIL_PICTURE']['tmp_name']) 
      {
        //изменяем размеры картинки:
        $arNewFile = CIBlock::ResizePicture($arFields['DETAIL_PICTURE'], array(
            "WIDTH" => $PictureMaxWidth,
            "HEIGHT" => $PictureMaxHeight,
            "METHOD" => "resample",
        ));
        $arFields['DETAIL_PICTURE'] = $arNewFile;
      }

      // Свойство "видео превью"
      $prop_id_photo = 11;
      if (isset($arFields['PROPERTY_VALUES'][$prop_id_photo])){
        foreach ($arFields['PROPERTY_VALUES'][$prop_id_photo] as $key => $moreimg) {
          if ($arFields['PROPERTY_VALUES'][$prop_id_photo][$key]['VALUE']['tmp_name']) {
            $arNewFile = CIBlock::ResizePicture($arFields['PROPERTY_VALUES'][$prop_id_photo][$key]['VALUE'], array(
              'WIDTH' => $PictureMaxWidth,
              'HEIGHT' => $PictureMaxHeight,
              'METHOD' => 'resample',
            ));
            $arFields['PROPERTY_VALUES'][$prop_id_photo][$key]['VALUE'] = $arNewFile;
          }
        }
      }
    }

    // Цвета
    if ($arFields["IBLOCK_ID"] == COLORS_ID) 
    {
      // Картинка для анонса
      /*if ($arFields['PREVIEW_PICTURE']['tmp_name']) {
          //изменяем размеры картинки:
          $arNewFile = CIBlock::ResizePicture($arFields['PREVIEW_PICTURE'], array(
              "WIDTH" => $PictureMaxWidth,
              "HEIGHT" => $PictureMaxHeight,
              "METHOD" => "resample",
          ));
          $arFields['PREVIEW_PICTURE'] = $arNewFile;
      }*/
      
      // Детальная картинка
      if ($arFields['DETAIL_PICTURE']['tmp_name']) 
      {
        //изменяем размеры картинки:
        $arNewFile = CIBlock::ResizePicture($arFields['DETAIL_PICTURE'], array(
            "WIDTH" => $PictureMaxWidth,
            "HEIGHT" => $PictureMaxHeight,
            "METHOD" => "resample",
        ));
        $arFields['DETAIL_PICTURE'] = $arNewFile;
      }

      // Свойство "видео превью"
      $prop_id_photo = 118;
      foreach ($arFields["PROPERTY_VALUES"][$prop_id_photo] as $key => $moreimg) 
      {
        if ($arFields["PROPERTY_VALUES"][$prop_id_photo][$key]['VALUE']['tmp_name']) 
        {
          $arNewFile = CIBlock::ResizePicture($arFields["PROPERTY_VALUES"][$prop_id_photo][$key]['VALUE'], array(
              "WIDTH" => $PictureMaxWidth,
              "HEIGHT" => $PictureMaxHeight,
              "METHOD" => "resample",
          ));
          $arFields["PROPERTY_VALUES"][$prop_id_photo][$key]['VALUE'] = $arNewFile;
        }
      }
      
      $prop_id_photo = 212;
      foreach ($arFields["PROPERTY_VALUES"][$prop_id_photo] as $key => $moreimg) 
      {
        if ($arFields["PROPERTY_VALUES"][$prop_id_photo][$key]['VALUE']['tmp_name']) 
        {
          $arNewFile = CIBlock::ResizePicture($arFields["PROPERTY_VALUES"][$prop_id_photo][$key]['VALUE'], array(
              "WIDTH" => $PictureMaxWidth,
              "HEIGHT" => $PictureMaxHeight,
              "METHOD" => "resample",
          ));
          $arFields["PROPERTY_VALUES"][$prop_id_photo][$key]['VALUE'] = $arNewFile;
        }
      }
    }
  }
}