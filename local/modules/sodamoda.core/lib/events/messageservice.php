<?php
  namespace Sodamoda\Core\Events;

  use Bitrix\Main\Loader;
  use \Bitrix\Sale\Order;
  use \Bitrix\Main\Localization\Loc;
  use \Bitrix\Highloadblock\HighloadBlockTable;
  use Bitrix\Main\Mail\Event;

  class Messageservice{
    function onGetSmsSenders() {
	    return [
	      // Класс нашего СМС-сервиса
	      new \Sodamoda\Core\Sms\Beeline(),
		  ];
//  global $USER;
//  if ($USER->isAdmin()) {
//    require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/classes/Smsc.php');
//    return [
//      // Класс нашего СМС-сервиса
//      new Smsc(),
//    ];
//  }
////  require_once($_SERVER['DOCUMENT_ROOT'].'/local/class/Helpers/Smsc.php');
////  require 'Smsc.php';
////  $f = fopen($_SERVER['DOCUMENT_ROOT']."/myfile.txt", "a");
////  fwrite($f, print_r('asdassadasdsasad', 1));
////  fclose($f);
////  return [
////    // Класс нашего СМС-сервиса
////    new Smsc(),
////  ];
		}
  }