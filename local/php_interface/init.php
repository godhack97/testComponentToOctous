<?php

/**
 * Module REST API
 *
 * @package  xwild.restapi
 * @author   zTx <nexsysis@gmail.com>
 * @website  https://xwild-dev.ru/
 */
// if (Bitrix\Main\Loader::includeModule('xwild.restapi')) \Xwild\Restapi\Foundation\Core::getInstance()->run();

require_once($_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/init.php');
local\Helpers\SetConst::init();
//local\Helpers\SetEvents::init();

use Bitrix\Main\Loader;
use Bitrix\Sale;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main;
use Bitrix\Main\UserPhoneAuthTable;

Loader::includeModule('sodamoda.core');

App\Locale::setLocaleAndCurency();

//AddEventHandler("sale", "OnSaleOrderBeforeSaved", 'saleOrderBeforeSaved');
//Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleOrderBeforeSaved', 'saleOrderBeforeSaved');
//Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleOrderSaved', 'OnSaleOrderSaved');
//Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleOrderSaved', 'OnSaleOrderSaved');

//Main\EventManager::getInstance()->addEventHandler("main", "OnBeforeUserAdd", Array("SmsEvent", "OnBeforeUserAdd"));
//AddEventHandler("main", "OnAfterUserAdd", Array("SmsEvent", "OnAfterUserAdd"));
//AddEventHandler("main", "OnAfterUserSimpleRegister", Array("SmsEvent", "OnAfterUserAdd"));
//AddEventHandler("sale", "OnSaleStatusOrder", Array("SmsEvent", "OnSaleStatusOrder"));

// Перед добавлением результата формы
//AddEventHandler('form', 'onBeforeResultAdd', 'my_onBeforeResultAdd');


//AddEventHandler('sale', 'OnSaleComponentOrderProperties', 'OnSaleComponentOrderPropertiesEvent');
//AddEventHandler("sale", "OnSaleComponentOrderOneStepPersonType", "selectSavedPersonType");


// Событие для отправки файла в аттач письма
//AddEventHandler('main', 'OnBeforeEventAdd', ['MailEventHandler', 'onBeforeEventAddHandler']);

// Обработчик для отправки файла в аттач письма
// Чтобы добавить ещё - добавьте типы почтовых событий и идентификаторы почтовых шаблонов в $events и $messageIds

class MailEventHandler
{
    static function onBeforeEventAddHandler(&$event, &$lid, &$arFields, &$message_id, &$files)
    {
        // Названия типов почтовых событий и идентификаторы почтовых шаблонов, по которым будет проходить фильтрация при отлавливании события
        $events = ['FORM_FILLING_SIMPLE_FORM_6'];
        $messageIds = ['131'];

        if (in_array($event, $events) && in_array($message_id, $messageIds)) {
            // Определяем массив прикрепляемых к письму идентификаторов файлов, если не задан
            if (!is_array($files))
                $files = [];

            // Перебираем поля письма
            foreach ($arFields as $field)
                if ($link = self::getLinkFromField($field))     // Если находим ссылку на файл, передаем ее дальше
                    if ($fileID = self::getFileFromLink($link))   // Если получаем идентификатор файла, сохраняем его в массив идентификаторов файлов письма
                        $files[] = $fileID;
        }
    }


    // Метод возвращает ссылку на файл
    private static function getLinkFromField($field)
    {
        preg_match("/(https\:.*form_show_file.*action\=download)/", $field, $out);
        return ($out[1] ?: false);               // Если поле соответствует маске пути к файлу, возвращаем путь
    }


    // Метод возвращает идентификатор файла
    private static function getFileFromLink($link)
    {
        $uri = new \Bitrix\Main\Web\Uri($link);                  // Создаем новый объект, в который записываем ссылку
        parse_str($uri->getQuery(), $query);                  // Разбираем строку URI и создаем массив из параметров запроса    
        $arFile = CFormResult::GetFileByHash($query["rid"], $query["hash"]);  // Метод возвращает массив свойств файла по идентификатору результата веб-формы и хэшу    

        return $arFile['FILE_ID'];                        // Возвращаем идентификатор файла
    }
}



function OnSaleComponentOrderPropertiesEvent(&$arUserResult, \Bitrix\Main\HttpRequest $request, &$arParams, &$arResult)
{
    global $USER;

    if ($USER->IsAuthorized()) {
        $userEmail = $request->get('ORDER_PROP_6');
        if (!empty($userEmail)) {
            $filter = [
                "EMAIL"               => $userEmail,
            ];
            $rsUsers = CUser::GetList(($by = "personal_country"), ($order = "desc"), $filter); // выбираем пользователей
            while ($arUser = $rsUsers->Fetch()) {
                $userObj = $arUser;
            };
            $userFN = $userObj['NAME']; //Имя
            $userLN = $userObj['LAST_NAME']; //Фамилия
            $userSN = $userObj['SECOND_NAME']; //Отчество

            if (empty($userLN)) {
                $fioField = $request->get('ORDER_PROP_7');
                if (!empty($fioField)) {
                    $parseFIO = explode(' ', $fioField); //0 - Фамилия, 1 - Имя, 2 - Отчество

                    $fields = [];
                    if (empty($userLN)) {
                        $fields['LAST_NAME'] = $parseFIO[0];
                    }
                    if (count($parseFIO) > 1 && empty($userFN)) {
                        $fields['NAME'] = $parseFIO[1];
                    }
                    if (count($parseFIO) > 2 && empty($userSN)) {
                        $fields['SECOND_NAME'] = $parseFIO[2];
                    }
                    if (count($fields) > 0) {
                        $user = new CUser;
                        $user->Update($userObj['ID'], $fields);
                    }
                }
            }
        }
    }
    foreach ($arUserResult['ORDER_PROP'] as $kProp => $vProp) {
        switch ($kProp) {
            case 3:
                if ($request->get('ORDER_PROP_3') && !empty($request->get('ORDER_PROP_3'))) {
                    $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_3');
                }
                break;
            case 4:
                if ($request->get('ORDER_PROP_4') && !empty($request->get('ORDER_PROP_4'))) {
                    $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_4');
                }
                break;
            case 2:
                if ($request->get('ORDER_PROP_2') && !empty($request->get('ORDER_PROP_2'))) {
                    $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_2');
                }
                break;
            case 5:
                if ($request->get('ORDER_PROP_5') && !empty($request->get('ORDER_PROP_5'))) {
                    $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_5');
                }
                break;
            case 6:
                if ($request->get('ORDER_PROP_6') && !empty($request->get('ORDER_PROP_6'))) {
                    $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_6');
                }
                break;
            case 7:
                if ($request->get('ORDER_PROP_7') && !empty($request->get('ORDER_PROP_7'))) {
                    $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_7');
                }
                break;
            case 14:
                if ($request->get('ORDER_PROP_14') && !empty($request->get('ORDER_PROP_14'))) {
                    $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_14');
                }
                break;
            case 15:
                if ($request->get('ORDER_PROP_15') && !empty($request->get('ORDER_PROP_15'))) {
                    $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_15');
                }
            case 17:
                if (preg_match('/^375/', $request->get('ORDER_PROP_17'))) {
                    if ($request->get('ORDER_PROP_17') && !empty($request->get('ORDER_PROP_17'))) {
                        $arUserResult['ORDER_PROP'][$kProp] = '+' . $request->get('ORDER_PROP_17');
                    }
                } else {
                    if ($request->get('ORDER_PROP_17') && !empty($request->get('ORDER_PROP_17'))) {
                        $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_17');
                    }
                }
                break;
            case 38:
                if ($request->get('ORDER_PROP_38') && !empty($request->get('ORDER_PROP_38'))) {
                    $arUserResult['ORDER_PROP'][$kProp] = $request->get('ORDER_PROP_38');
                }
                break;
        }
    }
}


function selectSavedPersonType(&$arResult, &$arUserResult)
{
    global $USER;

    if ($USER->IsAuthorized()) {
        if (in_array(12, $USER->GetUserGroupArray())) {
            $personType = 2;
        } else {
            $personType = 1;
        }
        if (empty($arUserResult['PAY_SYSTEM_ID']) && empty($arUserResult['DELIVERY_ID'])) {
            foreach ($arResult['PERSON_TYPE'] as $key => $type) {
                if ($type['CHECKED'] == 'Y') {
                    $arResult['PERSON_TYPE'][$key]['CHECKED'] = '';
                }
            }

            $arResult['PERSON_TYPE'][$personType]['CHECKED'] = 'Y';
            $arUserResult['PERSON_TYPE_ID'] = $personType;
        }
        if ($USER->IsAdmin()) {
            //print_r('<pre>');
            //print_r($arUserResult);
            //print_r('</pre>');
        }
    }
}


function checkBonus($phone)
{
    $ch = curl_init();
    $user_agent = "Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)";

    $url = "http://d1130cc05bce.sn.mynetname.net:3306/ushp_obn/hs/testContur/info/" . $phone;

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $token = 'KKU';
    $secret = '5BlW2N&s%@';
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "$token:$secret");
    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
    $resp = json_decode($resp, true);
    if (!empty($resp)) {
        $rs['BONUS'] = true;
        $rs['BONUS_BALANCE'] = $resp['BonusActual'];
        $rs['BONUS_DATE_UPDATE'] = $resp['DateActual'];
    } else {
        $rs['BONUS'] = false;
    }

    return $rs;
}


function checkBonusBalanceUserAgent()
{
    $order = array('sort' => 'asc');
    $tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
    $rsUsers = CUser::GetList($order, $tmp, [], ['SELECT' => ['UF_DATE_UPDATE_BONUS']]);
    while ($rsUser = $rsUsers->Fetch()) {
        $userPhoneCheck = UserPhoneAuthTable::getList(["filter" =>  ["=USER_ID" => $rsUser['ID']]])->fetch();
        $arResult['PHONE_CONFIRMED'] = $userPhoneCheck['CONFIRMED'] === 'Y';

        if ($arResult['PHONE_CONFIRMED']) {
            $bonusSite = CSaleUserAccount::GetByUserID($rsUser['ID'], 'RUB');
            if (!$bonusSite) {
                $arFields = ["USER_ID" => $rsUser['ID'], "CURRENCY" => "RUB", "CURRENT_BUDGET" => 0];
                $accountID = CSaleUserAccount::Add($arFields);
            } else {
                $accountID = $bonusSite['ID'];
            }

            $checkedBonus = checkBonus(str_replace('+7', '7', $userPhoneCheck['PHONE_NUMBER']));
            if ($checkedBonus['BONUS']) {
                if (!empty($checkedBonus['BONUS_DATE_UPDATE']) && (strtotime($checkedBonus['BONUS_DATE_UPDATE']) !== strtotime($rsUser['UF_DATE_UPDATE_BONUS']))) {
                    $arFields = ['USER_ID' => $rsUser['ID'], 'CURRENCY' => 'RUB', 'CURRENT_BUDGET' => $checkedBonus['BONUS_BALANCE']];
                    $accountID = CSaleUserAccount::Update($accountID, $arFields);

                    $user = new \CUser();
                    $user->Update($rsUser['ID'], [
                        'UF_DATE_UPDATE_BONUS' => date('d.m.Y H:i:s', strtotime($checkedBonus['BONUS_DATE_UPDATE'])),
                    ]);
                }
            }
        }
    }
    return "checkBonusBalanceUserAgent();";
}


function MyFormatCurrency($fSum, $strCurrency)
{
    if (!isset($fSum) || strlen($fSum) <= 0)
        return "";

    $arCurFormat = CCurrencyLang::GetCurrencyFormat($strCurrency);

    if (!isset($arCurFormat["DECIMALS"]))
        $arCurFormat["DECIMALS"] = 0;

    $arCurFormat["DECIMALS"] = IntVal($arCurFormat["DECIMALS"]);

    if (!isset($arCurFormat["DEC_POINT"]))
        $arCurFormat["DEC_POINT"] = ".";

    if (!isset($arCurFormat["THOUSANDS_SEP"]))
        $arCurFormat["THOUSANDS_SEP"] = "\\" . "xA0";

    if (!isset($arCurFormat["FORMAT_STRING"]))
        $arCurFormat["FORMAT_STRING"] = "#";


    $num = number_format(
        ceil(($fSum) / 10) * 10,
        $arCurFormat["DECIMALS"],
        $arCurFormat["DEC_POINT"],
        $arCurFormat["THOUSANDS_SEP"]
    );

    return str_replace(
        "#",
        $num,
        $arCurFormat["FORMAT_STRING"]
    );
}


function my_onBeforeResultAdd($WEB_FORM_ID, &$arFields, &$arrVALUES)
{
    //global $APPLICATION;

    // действие обработчика распространяется только на форму с ID=6
    if ($WEB_FORM_ID == 3) {
        //AddMessage2Log('$arrVALUES '. print_r($arrVALUES, 1));

        $URL_DOC = 'https://script.google.com/macros/s/AKfycbwtJAus_iY1rNtUGFXrOQNwgJgsxgVZbHbw4p9-uo9v8vRYHtQ/exec';

        $arForm['type']     = urlencode($arrVALUES['form_dropdown_SIMPLE_QUESTION_549']);
        $arForm['who']      = urlencode($arrVALUES['form_dropdown_SIMPLE_QUESTION_158']);
        $arForm['name']     = urlencode($arrVALUES['form_text_28']);
        $arForm['phone']    = urlencode($arrVALUES['form_text_29']);;
        $arForm['mail']     = urlencode($arrVALUES['form_text_30']);
        $arForm['message']  = urlencode($arrVALUES['form_text_31']);;

        $urlCurl = $URL_DOC . '?p5=' . $arForm['mail'] . '&p1=' . $arForm['type'] . '&p2=' . $arForm['who'] . '&p3=' . $arForm['name'] . '&p4=' . $arForm['phone'] . '&p6=' . $arForm['message'];

        //AddMessage2Log('$arForm '. print_r($arForm, 1));

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

        $ch = curl_init($urlCurl);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $err     = curl_errno($ch);
        $errmsg  = curl_error($ch);
        $header  = curl_getinfo($ch);
        curl_close($ch);

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;

        //    if(!empty($header['errno']))
        //      AddMessage2Log('CURL'. print_r($header, 1) );
    }
}


//// После добавления результат формы
//AddEventHandler('form', 'onAfterResultAdd', 'my_onAfterResultAddUpdate');
//
//function my_onAfterResultAddUpdate($WEB_FORM_ID, $RESULT_ID)
//{
//  // Форма Напишите нам
//  if ($WEB_FORM_ID == 3)
//  {
//    // запишем в дополнительное поле 'user_ip' IP-адрес пользователя
//    //CFormResult::SetField($RESULT_ID, 'user_ip', $_SERVER["REMOTE_ADDR"]);
//    AddMessage2Log();
//  }
//}


function generateCoupon($ID, $val)
{
    $sectionSertificateID = 121;
    if ($val === 'Y') {
        if (!Loader::includeModule("sale") || !Loader::includeModule('iblock')) return;

        $orderId = $ID;
        $order = Sale\Order::load($orderId);
        $basket = $order->getBasket();

        $arProductIDs = $arBasketItems = [];

        foreach ($basket as $basketItem) {
            $arProductIDs[] = $basketItem->getProductId();
            $arBasketItems[$basketItem->getProductId()] = [
                'PRICE' => $basketItem->getPrice(),
                'QUANTITY' => $basketItem->getQuantity()
            ];
        }

        $order = $basket->getOrder();
        $siteId = $order->getSiteId();
        $currency = $order->getCurrency();

        $rsElements = \Bitrix\Iblock\ElementTable::getList([
            'select' => ['ID'],
            'filter' => ['ID' => $arProductIDs, 'IBLOCK_SECTION_ID' => $sectionSertificateID]
        ]);
        while ($arElements = $rsElements->Fetch()) {
            $arActions["CLASS_ID"] = "CondGroup";
            $arActions["DATA"]["All"] = "AND";
            $arActions["CLASS_ID"] = "CondGroup";
            $arActions["CHILDREN"][0]["CLASS_ID"] = "ActSaleBsktGrp";
            $arActions["CHILDREN"][0]["DATA"]["Type"] = "Discount";
            $arActions["CHILDREN"][0]["DATA"]["Value"] = $arBasketItems[$arElements['ID']]['PRICE'];
            $arActions["CHILDREN"][0]["DATA"]["Unit"] = "CurAll";
            $arActions["CHILDREN"][0]["DATA"]["All"] = "OR";
            $arActions["CHILDREN"][0]["DATA"]["True"] = "True";

            $arConditions["CLASS_ID"] = "CondGroup";
            $arConditions["DATA"]["All"] = "AND";
            $arConditions["DATA"]["True"] = "True";
            $arConditions["CHILDREN"] = "";

            $arFields = [
                "LID" => $siteId,
                "NAME" => 'Скидка по сертификату ' . $arBasketItems[$arElements['ID']]['PRICE'] . 'руб.',
                "PRIORITY" => 900,
                "CURRENCY" => $currency,
                "ACTIVE" => "Y",
                "USER_GROUPS" => [2],
                'ACTIONS' => $arActions,
                "CONDITIONS" => $arConditions,
                "LAST_LEVEL_DISCOUNT" => 'Y'
            ];
            if ($siteId == 's2') {
                $arFields['NAME'] = 'Certificate discount ' . $arBasketItems[$arElements['ID']]['PRICE'] . " $";
            }

            $discountID = CSaleDiscount::Add($arFields);
            if ($discountID > 0) {
                for ($i = 0; $i < $arBasketItems[$arElements['ID']]['QUANTITY']; $i++) {
                    $coupon = \Bitrix\Sale\Internals\DiscountCouponTable::generateCoupon(true);
                    $activeFrom = new \Bitrix\Main\Type\DateTime();
                    $activeTo = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime("+1 years"));
                    if ($siteId == 's2') {
                        $description = 'For order №' . $orderId;
                    } else {
                        $description = 'Для заказа №' . $orderId;
                    }
                    $addDb = \Bitrix\Sale\Internals\DiscountCouponTable::add([
                        'DISCOUNT_ID' => $discountID,
                        'COUPON' => $coupon,
                        'TYPE' => \Bitrix\Sale\Internals\DiscountCouponTable::TYPE_ONE_ORDER,
                        'ACTIVE_FROM' => '',
                        'ACTIVE_TO' => '',
                        'MAX_USE' => 1,
                        'DESCRIPTION' => $description,
                        'ACTIVE_FROM' => $activeFrom,
                        'ACTIVE_TO' => $activeTo
                    ]);

                    if ($addDb->isSuccess()) {
                        $propertyCollection = $order->getPropertyCollection();
                        $email = $propertyCollection->getUserEmail()->getValue();

                        $arEventFields = [
                            "EMAIL_ORDER" => $email,
                            "COUPON" => $coupon,
                            "COUPON_PRICE" => number_format($arBasketItems[$arElements['ID']]['PRICE'], 0, '', ' '),
                            "IMAGE_URL" => $arBasketItems[$arElements['ID']]['PRICE'],
                            'ACTIVE_TO' => FormatDate('d F Y года', $activeTo->getTimestamp(), time() + CTimeZone::GetOffset())
                        ];

                        CEvent::Send("GENERATE_COUPON", $siteId, $arEventFields, true, '', [], ($siteId == 's2') ? 'en' : 'ru');
                    }
                }
            }
        }
    }
}

function OnStoreProductUpdateHandler($ID, $arFields)
{
    //$arStoregeList = [7, 8, 11, 12];
    $arStoregeList = explode('.', STORAGE_LIST);

    if (in_array($arFields['STORE_ID'], $arStoregeList) !== false) {
        if ($arFields['AMOUNT'] > 0) {
            $mxResult = CCatalogSku::GetProductInfo($arFields['PRODUCT_ID']);
            if (is_array($mxResult)) {
                $rsItems = \Bitrix\Iblock\ElementTable::getList([
                    'filter' => ['ID' => $arFields['PRODUCT_ID']],
                    'select' => ['*', 'NAME', 'DETAIL_PAGE_URL' => 'IBLOCK.DETAIL_PAGE_URL']
                ]);

                while ($arItem = $rsItems->fetch()) {
                    $productLink = CIBlock::ReplaceDetailUrl($arItem['DETAIL_PAGE_URL'], $arItem, false, 'E');
                    $productTitle = $arItem['NAME'];
                }
                $shortExist = false;
                $rsData = CBXShortUri::GetList([], []);
                while ($arRes = $rsData->Fetch()) {
                    if ($arRes['URI'] == $productLink) {
                        $str_SHORT_URI = $arRes['SHORT_URI'];
                        $shortExist = true;
                    }
                }

                $hlblock = HighloadBlockTable::getById(HL_SUBSCRIBE)->fetch();
                $entity = HighloadBlockTable::compileEntity($hlblock);
                $hlSubscribeTable = $entity->getDataClass();

                $rsElements = $hlSubscribeTable::getlist([
                    'filter' => ['UF_PRODUCT_ID' => $arFields['PRODUCT_ID']],
                ]);

                while ($arElement = $rsElements->fetch()) {
                    if (!empty($arElement['UF_EMAIL'])) {
                        $arEventFields = [
                            'PRODUCT_URL' => $productLink,
                            'USER_EMAIL' => $arElement['UF_EMAIL']
                        ];
                        CEvent::Send('PRODUCT_SUBSCRIBE', $arElement['UF_SITE_ID'], $arEventFields, true, '', [], ($arElement['UF_SITE_ID'] == 's2') ? 'en' : 'ru');

                        $arFilter = [
                            'EMAIL' => $arElement['UF_EMAIL']
                        ];
                        $res = Bitrix\Main\UserTable::getList([
                            'select' => ['ID'],
                            'filter' => $arFilter,
                        ]);

                        while ($arRes = $res->fetch()) {
                            $userPhone = UserPhoneAuthTable::getList(['filter' => ['=USER_ID' => $arRes['ID']]])->fetch();
                            if (!empty($userPhone)) {
                                if ($shortExist) {
                                    $arEventField = [
                                        'USER_PHONE' => $userPhone['PHONE_NUMBER'],
                                        'PRODUCT_NAME' => str_replace([' (', ')'], [', ', ''], $productTitle),
                                        'PRODUCT_LINK' => '/' . $str_SHORT_URI,
                                    ];
                                } else {
                                    $str_SHORT_URI = CBXShortUri::GenerateShortUri();
                                    $str_SHORT_URI = strtolower($str_SHORT_URI);
                                    $arFields = [
                                        'URI' => $productLink,
                                        'SHORT_URI' => $str_SHORT_URI,
                                        'STATUS' => '301',
                                    ];
                                    CBXShortUri::Add($arFields); //добавляем ссылку
                                    $arEventField = [
                                        'USER_PHONE' => $userPhone['PHONE_NUMBER'],
                                        'PRODUCT_NAME' => str_replace([' (', ')'], [', ', ''], $productTitle),
                                        'PRODUCT_LINK' => '/' . $str_SHORT_URI,
                                    ];
                                }
                                $sms = new \Bitrix\Main\Sms\Event('SMS_CHANGE_STORE_SUBSCRIBE', $arEventField);
                                $sms->setSite('s1');
                                $sms->setLanguage('ru');
                                $result = $sms->send();
                            }
                        }
                    } else {
                        if ($arElement['UF_PHONE']) {
                            if ($shortExist) {
                                $arEventField = [
                                    'USER_PHONE' => $arElement['UF_PHONE'],
                                    'PRODUCT_NAME' => str_replace([' (', ')'], [', ', ''], $productTitle),
                                    'PRODUCT_LINK' => '/' . $str_SHORT_URI,
                                ];
                            } else {
                                $str_SHORT_URI = CBXShortUri::GenerateShortUri();
                                $str_SHORT_URI = strtolower($str_SHORT_URI);
                                $arFields = [
                                    'URI' => $productLink,
                                    'SHORT_URI' => $str_SHORT_URI,
                                    'STATUS' => '301',
                                ];
                                CBXShortUri::Add($arFields); //добавляем ссылку
                                $arEventField = [
                                    'USER_PHONE' => $arElement['UF_PHONE'],
                                    'PRODUCT_NAME' => str_replace([' (', ')'], [', ', ''], $productTitle),
                                    'PRODUCT_LINK' => '/' . $str_SHORT_URI,
                                ];
                            }
                            $sms = new \Bitrix\Main\Sms\Event('SMS_CHANGE_STORE_SUBSCRIBE', $arEventField);
                            $sms->setSite('s1');
                            $sms->setLanguage('ru');
                            $result = $sms->send();
                        }
                    }

                    $hlSubscribeTable::delete($arElement['ID']);
                }
            }

            ////////Рассылка менеджерам
            $dbRes = \Bitrix\Sale\Order::getList([
                'select' => ["ID"],
                'filter' => ['=PROPERTY_VAL.CODE' => 'ORDER_OUT_OF_STOCK', '=PROPERTY_VAL.VALUE' => 'Y', 'BASKET.PRODUCT_ID' => $arFields['PRODUCT_ID'], '!STATUS_ID' => 'N'],
                'runtime' => [
                    new \Bitrix\Main\Entity\ReferenceField(
                        'PROPERTY_VAL',
                        '\Bitrix\sale\Internals\OrderPropsValueTable',
                        ["=this.ID" => "ref.ORDER_ID"],
                        ["join_type" => "left"]
                    ),
                ]
            ]);

            while ($arOrder = $dbRes->fetch()) {
                $order = Sale\Order::load($arOrder['ID']);
                $propertyCollection = $order->getPropertyCollection();
                foreach ($propertyCollection as $propertyItem) {
                    switch ($propertyItem->getField("CODE")) {
                        case 'ORDER_OUT_OF_STOCK':
                            $propertyItem->setField("VALUE", 'N');
                            $order->save();
                            break;
                    }
                }

                $arProduct = CCatalogProduct::GetByIDEx($mxResult['ID']);

                $orderId = $arOrder['ID'];
                $order = Sale\Order::load($orderId);
                $propertyCollection = $order->getPropertyCollection();

                $arDate = explode(' ', $order->getField("DATE_INSERT"));

                $payed = ($order->getField("PAYED") == 'Y') ? 'Оплачен' : 'Не оплачен';
                $STATUS_INFO = 'Заказ номер: <b>' . $orderId . '</b><br>'
                    . 'Дата: ' . $arDate[0] . '<br>'
                    . 'Время: ' . $arDate[1] . '<br><br>'
                    . 'Статус заказа: <b>' . $payed . '</b><br>';

                $deliveryRes = \Bitrix\Sale\Delivery\Services\Table::getList(array(
                    'filter' => ['ACTIVE' => 'Y', 'ID' => $order->getDeliveryIdList()],
                    'select' => ['NAME']
                ))->fetchAll()[0];

                $paymentRes = \Bitrix\Sale\Paysystem\Manager::getList(array(
                    'filter' => ['ACTIVE' => 'Y', 'ID' => $order->getPaySystemIdList()],
                    'select' => ['NAME']
                ))->fetchAll()[0];

                $locPropValue = $propertyCollection->getDeliveryLocation();

                if ($locPropValue->getValue()) {
                    $arLocs = CSaleLocation::GetByID($locPropValue->getValue(), LANGUAGE_ID);
                    $city = $arLocs['CITY_NAME'];
                }

                $DETAIL_INFO = '<br>'
                    . 'Стоимость товара: ' . number_format($order->getPrice() - $order->getDeliveryPrice(), 2, '.', ' ') . ' руб.<br>'
                    . 'Стоимость доставки: ' . number_format($order->getDeliveryPrice(), 2, '.', ' ') . ' руб.<br>'
                    . '<b>Итого: ' . number_format($order->getPrice(), 2, '.', ' ') . ' руб.</b><br>'
                    . '<br>'
                    . 'Способ оплаты: <b>' . $paymentRes['NAME'] . '</b><br>'
                    . 'Способ доставки: <b>' . $deliveryRes['NAME'] . '</b><br>'
                    . 'Детальная информация:<br>'
                    . 'Имя: <b>' . $propertyCollection->getPayerName()->getValue() . '</b><br>'
                    . 'Телефон: <b>' . phoneFormat($propertyCollection->getPhone()->getValue()) . '</b><br>'
                    . 'E-mail: <b>' . $propertyCollection->getUserEmail()->getValue() . '</b><br>'
                    . '<br>'
                    . 'Город доставки: <b>' . $city . '</b><br>'
                    . 'Адрес доставки: <b>' . $propertyCollection->getAddress() . '</b><br>'
                    . 'Индекс: <b>' . $propertyCollection->getDeliveryLocationZip() . '</b><br>'
                    . '<br>';

                $basket = $order->getBasket();
                $ORDER_LIST = 'Состав заказа:<br>';
                foreach ($basket as $basketItem) {
                    $ORDER_LIST .= $basketItem->getField('NAME') . ' - ' . $basketItem->getQuantity() . 'шт. x ' . $basketItem->getPrice() . ' ' . $order->getCurrency() . '<br />';
                }

                $arEventFields = [
                    'ORDER_URL' => 'https://sodamoda.ru/bitrix/admin/sale_order_view.php?ID=' . $arOrder['ID'],
                    'ORDER_ID' => $arOrder['ID'],
                    'TITLE' => $productTitle,
                    'ARTICLE' => $arProduct['PROPERTIES']['CML2_ARTICLE']['VALUE'],
                    'STATUS_INFO' => $STATUS_INFO,
                    'DETAIL_INFO' => $DETAIL_INFO,
                    'ORDER_LIST' => $ORDER_LIST
                ];

                CEvent::Send("ORDER_AVAILABLE", 's1', $arEventFields, true, '', [], 'ru');
            }
        }
    }
}


function onSaleAdminOrderInfoBlockShowHandler(\Bitrix\Main\Event $event)
{
    $order = $event->getParameter("ORDER");

    if ($order->isPaid()) {
        $payMethod = '';
        $propertyCollection = $order->getPropertyCollection();
        $arProperties = $propertyCollection->getArray();
        foreach ($arProperties['properties'] as $arValue) {
            if ($arValue['CODE']  == 'PAY_METHOD') {
                $payMethod = $arValue['OPTIONS'][$arValue['VALUE'][0]];
            }
        }

        if (!empty($payMethod)) {
            return new \Bitrix\Main\EventResult(
                \Bitrix\Main\EventResult::SUCCESS,
                array(
                    array(
                        'TITLE' => 'Метод оплаты:',
                        'VALUE' => $payMethod,
                        'ID' => 'pay_method'
                    ),
                )
            );
        }
    }
}

//function registerSmscService() {
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
//}


class SmsEvent
{
    function OnBeforeUserAdd(&$arFields)
    {
        if (isset($arFields['PHONE_NUMBER'])) {
            $arFields["LOGIN"] = $arFields["PHONE_NUMBER"];
            /*            $arEventField = [
                "LOGIN" => $arFields["LOGIN"],
                "EMAIL" => $arFields["EMAIL"],
                "USER_PHONE" => $arFields["PHONE_NUMBER"],
                "PASSWORD" => $arFields["PASSWORD"],
            ];
            $sms = new \Bitrix\Main\Sms\Event("SMS_USER_REGISTER", $arEventField);
            $sms->setSite('s1');
            $sms->setLanguage('ru');
*/            //$result = $sms->send();
        }
        return $arFields;
    }

    //    function OnAfterUserAdd(&$arFields)
    //    {
    //        if (isset($arFields['PHONE_NUMBER'])) {
    //            $arFields["LOGIN"] = $arFields["PHONE_NUMBER"];
    //            $arEventField = [
    //                "LOGIN" => $arFields["LOGIN"],
    //                "EMAIL" => $arFields["EMAIL"],
    //                "USER_PHONE" => $arFields["PERSONAL_PHONE"],
    //                "PASSWORD" => $arFields["CONFIRM_PASSWORD"],
    //            ];
    //            $sms = new \Bitrix\Main\Sms\Event("SMS_USER_REGISTER", $arEventField);
    //            $sms->setSite('s1');
    //            $sms->setLanguage('ru');
    //            //$result = $sms->send();
    //        }
    //        return $arFields;
    //    }

    function OnSaleStatusOrder($orderId, $status)
    {
        CModule::IncludeModule('sale');
        $order = CSaleOrder::GetByID($orderId);
        $email = $order['USER_EMAIL'];
        $name  = urlencode($order['USER_LAST_NAME']);
        if ($orderId > 0 && $order['CANCELED'] == "N") {
            $obProps = Bitrix\Sale\Internals\OrderPropsValueTable::getList(array('filter' => array('ORDER_ID' => $orderId, 'CODE' => array('PHONE', 'EMAIL'))));
            while ($propOrder = $obProps->Fetch()) {
                if ($propOrder['CODE'] == 'PHONE') {
                    $phone = urlencode($propOrder['VALUE']);
                }
                if ($propOrder['CODE'] == 'EMAIL') {
                    $email = $propOrder['VALUE'];
                }
            }
            // Заказ считается оплаченным
            //if ($status == 'P') {
            $statusMas = CSaleStatus::GetByID($status);
            $arEventField = [
                "USER_PHONE" => $phone,
                "ORDER_ID"   =>  $orderId,
                "ORDER_STATUS" => $statusMas['NAME']
            ];
            $sms = new \Bitrix\Main\Sms\Event("SMS_CHANGE_ORDER_STATUS", $arEventField);
            $sms->setSite('s1');
            $sms->setLanguage('ru');
            $result = $sms->send();
            //}
        }
        return $arFields;
    }
}



function OnSaleOrderSaved(Main\Event $event)
{
    /** @var \Bitrix\Sale\Order $order */
    $order = $event->getParameter("ENTITY");

    $paymentCollection = $order->getPaymentCollection();
    $propertyCollection = $order->getPropertyCollection();
    if ($paymentCollection->isExistsInnerPayment()) {
        if (count($paymentCollection) < 2) {
            $payment = $paymentCollection[0];

            $bonusPay = (($order->getPrice() / 100) * PERCENT_PAY_BONUS);
            $payment->setField('SUM', $bonusPay);
            $payment->setPaid('Y');

            $service = \Bitrix\Sale\PaySystem\Manager::getObjectById(14);
            $newPayment = $paymentCollection->createItem($service);
            $newPayment->setField('SUM', $order->getPrice() - $bonusPay);

            $bonusPayProp = $propertyCollection->getItemByOrderPropertyId(37);
            $bonusPayProp->setValue($bonusPay);
            //$bonusPay = $propertyCollection->getField("ORDER_BONUS_PAY");
            //$bonusPay->setValue($bonusPay);
            $order->save();
        }
    }
}


function saleOrderSaved(Main\Event $event)
{
    /** @var \Bitrix\Sale\Order $order */
    $order = $event->getParameter("ENTITY");
    $paymentCollection = $order->getPaymentCollection();
    print_r('<pre>');
    print_r($order);
    print_r('</pre>');

    die();
}
