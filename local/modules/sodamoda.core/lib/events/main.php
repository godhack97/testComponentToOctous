<?php

namespace Sodamoda\Core\Events;

use Bitrix\Main\Application;
use \Bitrix\Main\Loader;
use \Bitrix\Sale;
use \Bitrix\Main\Mail\Event;
use \Bitrix\Main\Service\GeoIp;
use Bitrix\Main\Diag;
use Bitrix\Main\Context;

class Main
{
    function doBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
    {
        /*
			foreach ($aModuleMenu as $k => $v) {
				if ($v['text'] == 'Клуб') {
					foreach ($v['items'] as $k1 => $v1) {
						if ($v1['text'] == 'Состав') {
							unset($aModuleMenu[$k]['items'][$k1]);
						}
					}
				}
			}
			
			$arRes = [
				"global_menu_hockey"     => [
					"menu_id"      => "hockey",
					"page_icon"    => "services_title_icon",
					"index_icon"   => "services_page_icon",
					"text"         => "Хоккейный клуб",
					"title"        => "Хоккейный клуб",
					"sort"         => 120,
					"items_id"     => "global_menu_hockey",
					"help_section" => "custom",
					"items"        => []
				],
				"global_menu_hockey_khl" => [
					"menu_id"      => "hockey_khl",
					"page_icon"    => "services_title_icon",
					"index_icon"   => "services_page_icon",
					"text"         => "КХЛ Меню (Test)",
					"title"        => "КХЛ Меню (Test)",
					"sort"         => 120,
					"items_id"     => "global_menu_hockey_khl",
					"help_section" => "custom",
					"items"        => []
				],
				"global_menu_quiz"       => [
					"menu_id"      => "quiz",
					"page_icon"    => "services_title_icon",
					"index_icon"   => "services_page_icon",
					"text"         => "QUIZ",
					"title"        => "QUIZ",
					"sort"         => 120,
					"items_id"     => "global_menu_quiz",
					"help_section" => "custom",
					"items"        => []
				],
			];
			
			return $arRes;
			*/
    }
    function OnBeforeUserAdd(&$arFields)
    {
        if (isset($arFields['PHONE_NUMBER'])) {
            $arFields['LOGIN'] = $arFields['PHONE_NUMBER'];
            /*  $arEventField = [
              "LOGIN" => $arFields["LOGIN"],
              "EMAIL" => $arFields["EMAIL"],
              "USER_PHONE" => $arFields["PHONE_NUMBER"],
              "PASSWORD" => $arFields["PASSWORD"],
            ];
            $sms = new \Bitrix\Main\Sms\Event("SMS_USER_REGISTER", $arEventField);
            $sms->setSite('s1');
            $sms->setLanguage('ru');
        */
            //$result = $sms->send();
        }
        return $arFields;
    }
    function onBeforeUserRegister(&$arFields)
    {
        /*
        if (!isset($arFields["USER_PHONE"]) || strlen($arFields["USER_PHONE"])<=0)
        {
            $GLOBALS['APPLICATION']->ThrowException('Поле телефон обязательно для регистрации');
            return false;
        }
        */
    }
    function onBeforeUserUpdate(&$arFields)
    {
        $arGroups = \CUser::GetUserGroup($arFields['ID']);
        $isPartner = false;
        if (!in_array(17, $arGroups)) {
            if (is_array($arFields['GROUP_ID']) && count($arFields['GROUP_ID'])) {
                foreach ($arFields['GROUP_ID'] as $group) {
                    if (intval($group['GROUP_ID']) === 17) {
                        $isPartner = true;
                    }
                }
            }
        }
        if ($isPartner) {
            $rsUser = \CUser::GetByID($arFields['ID'])->Fetch();

            Event::send([
                'EVENT_NAME' => 'USER_PARTNER_ACTIVATE',
                'LID' => 's1',
                'C_FIELDS' => [
                    'USER_FIO' => $rsUser['NAME'] . ' ' . $rsUser['LAST_NAME'],
                    'USER_EMAIL' => $rsUser['EMAIL'],
                ],
            ]);
        }
    }

    function onAfterUserLogin(&$arFields)
    {
    }
    function OnAfterUserAdd(&$arFields)
    {
        if ($arFields['EXTERNAL_AUTH_ID'] == 'socservices' && $arFields['ID']) {
            if (Loader::includeModule('sale')) {
                \CSaleUserAccount::UpdateAccount($arFields['ID'], 500, 'RUB', 'Бонус за регистрацию через соц. сети');
            }
        }
    }

    function OnBeforeEventSend(&$arFields, &$arTemplate)
    {
        //logArlix('OnBeforeEventSendHandler start', 'log_create_order.txt', __FILE__ .' :'. __LINE__);

        // Форма возврата, редактируем для письма список товаров
        if ($arFields['RS_FORM_SID'] == 'SIMPLE_FORM_1') {
            $arProducts = explode('<br>', $arFields['SIMPLE_QUESTION_954']);
            $arFields['SIMPLE_QUESTION_954'] = '';

            foreach ($arProducts as $product) {
                $arProduct = explode('##', $product);
                $arFields['SIMPLE_QUESTION_954'] .= $arProduct[2] . ' — ' . $arProduct[3] . 'x' . number_format($arProduct[4], 2, '.', ' ') . ' руб.<br>';
            }

            $arFields['SIMPLE_QUESTION_954'] = trim($arFields['SIMPLE_QUESTION_954'], '<br>');
            $arFields['SIMPLE_QUESTION_954_RAW'] = $arFields['SIMPLE_QUESTION_954'];
        }


        if ($arTemplate['EVENT_NAME'] == 'USER_INFO' && $arFields['USER_ID']) {
            logArlix('OnBeforeEventSendHandler start USER_INFO', 'log_create_order.txt', __FILE__ . ' :' . __LINE__);

            $password = randString(6, ['123456789']); // uniqid();
            $checkword = randString(20);
            $user = new \CUser;
            $fields = array(
                'PASSWORD' => $password,
                'CONFIRM_PASSWORD' => $password,
                'CHECKWORD' => $checkword,
            );
            $user->Update($arFields['USER_ID'], $fields);

            $arFields['USER_PASSWORD'] = $_SESSION['USER_PASS_FOR_ORDER_INFO'] = $password;

            if (preg_match('#^[0-9]+$#', $arFields['LOGIN'])) {
                $arEventField = [
                    'LOGIN' => $arFields['LOGIN'],
                    'EMAIL' => $arFields['EMAIL'],
                    'USER_PHONE' => $arFields['LOGIN'],
                    'PASSWORD' => $password,
                ];
                $sms = new \Bitrix\Main\Sms\Event('SMS_USER_REGISTER', $arEventField);
                $sms->setSite('s1');
                $sms->setLanguage('ru');
                $result = $sms->send();
            }
        }


        if ($arTemplate['EVENT_NAME'] == 'SALE_NEW_ORDER' && !empty($arFields['USER_LOGIN'])) {
            logArlix('OnBeforeEventSendHandler start SALE_NEW_ORDER ' . $_SESSION['USER_PASS_FOR_ORDER_INFO'], 'log_create_order.txt', __FILE__ . ' :' . __LINE__);

            if (!empty($_SESSION['USER_PASS_FOR_ORDER_INFO'])) {
                $arFields['NEW_USER_INFO'] = '<b>Данные для входа в личный кабинет:</b><br>
                                        Логин: ' . $arFields['USER_LOGIN'] . '<br>
                                        Пароль: ' . $_SESSION['USER_PASS_FOR_ORDER_INFO'] . '<br><br>';
                $_SESSION['USER_PASS_FOR_ORDER_INFO'] = '';
            }

            $userID = \Bitrix\Main\UserTable::getList(array(
                'select' => ['ID'],
                'filter' => ['LOGIN' => $arFields['USER_LOGIN']],
            ))->fetch()['ID'];

            logArlix('OnBeforeEventSendHandler start SALE_NEW_ORDER $userID=' . $userID, 'log_create_order.txt', __FILE__ . ' :' . __LINE__);

            if ($userID > 0) {
                $res = \Bitrix\Main\UserGroupTable::getList(array(
                    'select' => ['*'],
                    'filter' => ['USER_ID' => $userID, 'GROUP_ID' => 15],
                ));
                if ($res->getSelectedRowsCount() > 0) {
                    $arFields['ADDITIONAL_INFO'] = '<br><b>Заказ был создан менеджером</b>';
                    logArlix('OnBeforeEventSendHandler start SALE_NEW_ORDER $userID=' . $userID . ' ' . $arFields['ADDITIONAL_INFO'], 'log_create_order.txt', __FILE__ . ' :' . __LINE__);
                }
            }
        }


        // Номер отправления вашего заказа
        if ($arTemplate['EVENT_NAME'] == 'SALE_ORDER_TRACKING_NUMBER' && $arFields['FLAG_CRON_PICKPOINT'] != 'Y') {
            Loader::includeModule('sale');

            //AddMessage2Log('OnBeforeEventSendHandler START $arFields '. print_r($arFields, 1) .' $arTemplate '. print_r($arTemplate, 1));

            $arDate = explode(' ', $arFields['ORDER_DATE']);
            $arFields['ORDER_DATE_DAY'] = $arDate[0];
            $arFields['ORDER_DATE_TIME'] = $arDate[1];
            $arFields['LINK_TRACKING'] = '';
            $arOrder = \CSaleOrder::GetByID($arFields['ORDER_ID']);

            //AddMessage2Log('OnBeforeEventSendHandler $arOrder '. print_r($arOrder, 1));

            if (stristr($arOrder['DELIVERY_ID'], 'sdek')) {
                $arFields['LINK_TRACKING'] = 'Отследить заказ:<br>'
                    . '<a href="https://cdek.ru/tracking?order_id=' . $arFields['ORDER_TRACKING_NUMBER'] . '">https://cdek.ru/tracking?order_id=' . $arFields['ORDER_TRACKING_NUMBER'] . '</a><br>'
                    . '<br>';
            }

            if (stristr($arOrder['DELIVERY_ID'], 'pickpoint')) {
                $arFields['LINK_TRACKING'] = 'Отследить заказ:<br>'
                    . '<a href="https://pickpoint.ru/monitoring/">https://pickpoint.ru/monitoring/</a><br>'
                    . '<br>';
            }

            if (stristr($arOrder['DELIVERY_ID'], 'dpd')) {
                $arFields['LINK_TRACKING'] = 'Отследить заказ:<br>'
                    . '<a href="https://www.dpd.ru/ols/trace2/standard.do2">https://www.dpd.ru/ols/trace2/standard.do2</a><br>'
                    . '<br>';
            }

            if (stristr($arOrder['DELIVERY_ID'], 'new40') || stristr($arOrder['DELIVERY_ID'], 'new65') || stristr($arOrder['DELIVERY_ID'], 'new66')) // EMS и Нестандартная посылка
            {
                $arFields['LINK_TRACKING'] = 'Отследить заказ:<br>'
                    . '<a href="https://www.pochta.ru/tracking#' . $arFields['ORDER_TRACKING_NUMBER'] . '">https://www.pochta.ru/tracking#' . $arFields['ORDER_TRACKING_NUMBER'] . '</a><br>'
                    . '<br>';
            }

            $arDelivery = \CSaleDelivery::GetByID($arOrder['DELIVERY_ID']);
            $arFields['DELIVERY_NAME'] = getDeliveryName($arDelivery, $arOrder['DELIVERY_ID']);
        }

        logArlix('OnBeforeEventSendHandler finish', 'log_create_order.txt', __FILE__ . ' :' . __LINE__);
    }
    static function onBeforeEventAdd(&$event, &$lid, &$arFields, &$message_id, &$files)
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
                if ($link = self::getLinkFromField($field))         // Если находим ссылку на файл, передаем ее дальше
                    if ($fileID = self::getFileFromLink($link))     // Если получаем идентификатор файла, сохраняем его в массив идентификаторов файлов письма
                        $files[] = $fileID;
        }
    }

    // Отключаем вкладку "Реклама" при редактировании элемента RemoveYandexDirectTab
    function OnAdminTabControlBegin(&$TabControl)
    {
        if ($GLOBALS['APPLICATION']->GetCurPage() == '/bitrix/admin/iblock_element_edit.php') {
            foreach ($TabControl->tabs as $Key => $arTab) {
                if ($arTab['DIV'] == 'seo_adv_seo_adv') {
                    //unset($TabControl->tabs[$Key]);
                    $TabControl->tabs[$Key] = '';
                }
            }
        }
    }
    function checkManagerResponsibility($params)
    {
        // Если в админке заказ смотрит/редактирует менеджер из группы 15, то он автоматически проставляется ответственным
        if ($params['ID'] > 0) {
            $order = Sale\Order::load($params['ID']);

            $responsibleId = $order->getField('RESPONSIBLE_ID');
            $currentUserId = $GLOBALS['USER']->GetID();
            if (!$responsibleId && $currentUserId) {
                $result = \Bitrix\Main\UserGroupTable::getList(array(
                    'filter' => array('USER_ID' => $currentUserId, 'GROUP.ACTIVE' => 'Y', 'GROUP_ID' => 15),
                    'select' => array('GROUP_ID')
                ));

                if ($arGroup = $result->fetch()) {
                    $order->setField('RESPONSIBLE_ID', $currentUserId);
                    $order->save();
                    LocalRedirect($GLOBALS['APPLICATION']->GetCurUri());
                }
            }
        }
    }


    public function OnPageStart()
    {
        $request = Context::getCurrent()->getRequest();

        $currentPage     = $_SERVER["REQUEST_URI"];
        $currentLanguage = $_COOKIE["MANUAL_LANGUAGE"];
        $currentCurrency = $_COOKIE["CATALOG_CURRENCY"];

        if (str_contains($currentPage, "/api/v1/")) {
            return;
        }

        $isAdminSection = str_contains($currentPage, "/bitrix/");
        $isAjax = str_contains($currentPage, "/ajax/") || str_contains($currentPage, "/include/");
        $siteId = str_contains($currentPage, "/en/") ? "s2" : "s1";

        if ($request->get("currency")) {
            \App\Locale::setCookie("CATALOG_CURRENCY", $request->get("currency"));
        }

        if (defined("CHK_EVENT") || $isAdminSection || $isAjax) {
            return;
        }

        //если установили язык через гет-параметр, то считаем, что уже находимся на нужном сайте
        if ($request->get("language") && in_array($request->get("language"), ["RU", "EN"])) {
            \App\Locale::setCookie("MANUAL_LANGUAGE", $request->get("language"));
        } elseif ($currentLanguage) {
            if ($currentLanguage === "RU" && $siteId !== "s1") {
                \App\Locale::setCookie("AUTO_REDIRECT_BY_COUNTRY", true);
                $redirectPage = preg_replace("/\/en/", "", $currentPage, 1);
                \LocalRedirect($redirectPage);
            }

            if ($currentLanguage === "EN" && $siteId !== "s2") {
                \App\Locale::setCookie("AUTO_REDIRECT_BY_COUNTRY", true);
                \LocalRedirect("/en" . $currentPage);
            }
        }

        if (!$currentLanguage) {
            $language = \App\IpWhoIs::getCountryCode();
            //массив кодов русскоговорящих стран
            $arRusCountryCodes = ["AZ", "AM", "BY", "KG", "KZ", "RU", "TM", "TJ", "UA"];

            if (in_array($language, $arRusCountryCodes)) {
                \App\Locale::setCookie("MANUAL_LANGUAGE", "RU");
                if ($currentCurrency) {
                    \App\Locale::setCookie("CATALOG_CURRENCY", $currentCurrency);
                } else {
                    \App\Locale::setCookie("CATALOG_CURRENCY", "rub");
                }
            } else {
                if ($siteId !== "s2") {
                    \App\Locale::setCookie("MANUAL_LANGUAGE", "EN");
                    \App\Locale::setCookie("CATALOG_CURRENCY", "usd");

                    if ($language === "AE") {
                        \App\Locale::setCookie("CATALOG_CURRENCY", "aed");
                    }
                    
                    \LocalRedirect("/en" . $currentPage);
                }
            }
        }
    }
}
