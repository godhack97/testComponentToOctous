<?php

namespace Sodamoda\Core\Events;

use Bitrix\Main\Config\Option;
use \Bitrix\Main\Loader;
use \Bitrix\Highloadblock\HighloadBlockTable;
use \Bitrix\Main\UserPhoneAuthTable;
use \Bitrix\Sale;
use \Bitrix\Catalog\StoreProductTable;

class Catalog
{

    public function OnSuccessCatalogImport1C()
    {
        if (Loader::includeModule('catalog')) {
            $arFilter = array('IBLOCK_ID' => CATALOG_ID, 'ACTIVE' => 'Y', 'INCLUDE_SUBSECTIONS' => 'Y');
            $rs = \CIBlockElement::GetList(array(), $arFilter, false, array('nPageSize' => 100000), array('ID', 'PROPERTY_ATT_WEIGHT'));
            while ($ob = $rs->GetNextElement()) {
                $arFields = $ob->GetFields();
                $offers = \CCatalogSKU::getOffersList($arFields['ID']);

                if (!empty($offers[$arFields['ID']])) {
                    foreach ($offers[$arFields['ID']] as $offer) {
                        $arOfferFields = array('WEIGHT' => $arFields['PROPERTY_ATT_WEIGHT_VALUE']);
                        \CCatalogProduct::Update($offer['ID'], $arOfferFields);
                    }
                }
            }

            $rsElement = \CIBlockElement::GetList([], ['IBLOCK' => COLORS_ID, '!PROPERTY_PREORDER' => false, '!PROPERTY_PARENT' => false, 'ACTIVE' => 'Y'], false, false, ['ID']);

            while ($arElement = $rsElement->Fetch()) {
                $props = \CIBlockElement::GetByID($arElement['ID'])->GetNextElement()->GetProperties();
                $colorProductXML_ID = $props['COLOR']['VALUE'];
                $parentProduct = $props['PARENT']['VALUE'];

                $res = \CCatalogSKU::getOffersList($parentProduct, 0, ['PROPERTY_COLOR' => $colorProductXML_ID, 'ACTIVE' => 'Y']);
                foreach ($res[$parentProduct] as $offer) {
                    $updateProduct = ['QUANTITY_TRACE' => 'N'];
                    \CCatalogProduct::Update($offer['ID'], $updateProduct);
                }
            }
        }
    }
    public function OnStoreProductUpdate($ID, $arFields)
    {
        //$arStoregeList = [7, 8, 11, 12];
        $arStoregeList = explode('.', STORAGE_LIST);
        global $USER;
        if ($USER->GetID() != 25) {
            if (in_array($arFields['STORE_ID'], $arStoregeList) !== false) {
                if ($arFields['AMOUNT'] > 0) {
                    $mxResult = \CCatalogSku::GetProductInfo($arFields['PRODUCT_ID']);
                    if (is_array($mxResult)) {
                        $rsItems = \Bitrix\Iblock\ElementTable::getList([
                            'filter' => ['ID' => $arFields['PRODUCT_ID']],
                            'select' => ['*', 'NAME', 'DETAIL_PAGE_URL' => 'IBLOCK.DETAIL_PAGE_URL']
                        ]);
                        $colorID = false;
                        while ($arItem = $rsItems->fetch()) {
                            $productLink = \CIBlock::ReplaceDetailUrl($arItem['DETAIL_PAGE_URL'], $arItem, false, 'E');
                            $productTitle = $arItem['NAME'];

                            $res = \CIBlockElement::GetProperty(8, $arFields['PRODUCT_ID'], 'sort', 'asc', array('CODE' => 'COLOR'));
                            while ($ob = $res->GetNext()) {
                                $colorID = $ob['VALUE'];
                            }
                            if ($colorID) {
                                $productLink .= '?setColor=' . $colorID;
                            }
                        }
                        $shortExist = false;
                        $rsData = \CBXShortUri::GetList([], []);
                        while ($arRes = $rsData->Fetch()) {
                            if ($arRes['URI'] == $productLink) {
                                $str_SHORT_URI = $arRes['SHORT_URI'];
                                $shortExist = true;
                            }
                        }

                        //AddMessage2Log(print_r($productLink, true), 'main');
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
                                \CEvent::Send('PRODUCT_SUBSCRIBE', $arElement['UF_SITE_ID'], $arEventFields, true, '', [], ($arElement['UF_SITE_ID'] == 's2') ? 'en' : 'ru');

                                $arFilter = [
                                    'EMAIL' => $arElement['UF_EMAIL']
                                ];
                                $res = \Bitrix\Main\UserTable::getList([
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
                                            $str_SHORT_URI = \CBXShortUri::GenerateShortUri();
                                            $str_SHORT_URI = strtolower($str_SHORT_URI);
                                            $arFields = [
                                                'URI' => $productLink,
                                                'SHORT_URI' => $str_SHORT_URI,
                                                'STATUS' => '301',
                                            ];
                                            \CBXShortUri::Add($arFields); //добавляем ссылку
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
                                        $str_SHORT_URI = \CBXShortUri::GenerateShortUri();
                                        $str_SHORT_URI = strtolower($str_SHORT_URI);
                                        $arFields = [
                                            'URI' => $productLink,
                                            'SHORT_URI' => $str_SHORT_URI,
                                            'STATUS' => '301',
                                        ];
                                        \CBXShortUri::Add($arFields); //добавляем ссылку
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
                        'select' => ['ID'],
                        'filter' => ['=PROPERTY_VAL.CODE' => 'ORDER_OUT_OF_STOCK', '=PROPERTY_VAL.VALUE' => 'Y', 'BASKET.PRODUCT_ID' => $arFields['PRODUCT_ID'], '!STATUS_ID' => 'N'],
                        'runtime' => [
                            new \Bitrix\Main\Entity\ReferenceField(
                                'PROPERTY_VAL',
                                '\Bitrix\sale\Internals\OrderPropsValueTable',
                                ['=this.ID' => 'ref.ORDER_ID'],
                                ['join_type' => 'left']
                            ),
                        ]
                    ]);

                    while ($arOrder = $dbRes->fetch()) {
                        $order = Sale\Order::load($arOrder['ID']);
                        $propertyCollection = $order->getPropertyCollection();
                        foreach ($propertyCollection as $propertyItem) {
                            switch ($propertyItem->getField('CODE')) {
                                case 'ORDER_OUT_OF_STOCK':
                                    $propertyItem->setField('VALUE', 'N');
                                    $order->save();
                                    break;
                            }
                        }

                        $arProduct = \CCatalogProduct::GetByIDEx($mxResult['ID']);

                        $orderId = $arOrder['ID'];
                        $order = Sale\Order::load($orderId);
                        $propertyCollection = $order->getPropertyCollection();

                        $arDate = explode(' ', $order->getField('DATE_INSERT'));

                        $payed = ($order->getField('PAYED') == 'Y') ? 'Оплачен' : 'Не оплачен';
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
                            $arLocs = \CSaleLocation::GetByID($locPropValue->getValue(), LANGUAGE_ID);
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

                        \CEvent::Send('ORDER_AVAILABLE', 's1', $arEventFields, true, '', [], 'ru');
                    }
                }
            }
        }
    }
    public function OnBeforeStoreProductUpdate($ID, $arFields)
    {
        $storeIDS = explode(',', Option::get('sodamoda.core', 'store_lists'));
        if (in_array($arFields['STORE_ID'], $storeIDS) !== false) {
            $rsProduct = self::checkAvailableOffers($arFields['PRODUCT_ID']);
            $updateFields = ['QUANTITY' => $rsProduct['AMOUNT']];
            \CCatalogProduct::Update($arFields['PRODUCT_ID'], $updateFields);
        }
    }
    public function OnBeforeProductUpdate($ID, $arFields)
    {
        $productInfo = \CCatalogSku::GetProductInfo($ID);
        if (is_array($productInfo)) {
            $rsElement = \CIBlockElement::GetList([], ['IBLOCK' => COLORS_ID, '!PROPERTY_PREORDER' => false, '!PROPERTY_PARENT' => $productInfo['ID'], 'ACTIVE' => 'Y'], false, false, ['ID']);

            while ($arElement = $rsElement->Fetch()) {
                $arFields['QUANTITY_TRACE'] = 'N';
            }
        }
    }

    public static function checkAvailableOffers($productID)
    {
        $storeList = explode(',', \Bitrix\Main\Config\Option::get('sodamoda.core', 'store_lists'));
        $rsStore = [
            'AMOUNT' => 0,
            'AVAILABLE' => false,
        ];
        if (!empty($productID)) {
            $rsStoreProduct = StoreProductTable::getList(array(
                'filter' => ['PRODUCT_ID' => $productID, 'STORE.ACTIVE' => 'Y', 'STORE_ID' => $storeList],
                'select' => ['AMOUNT', 'STORE_ID', 'PRODUCT_ID'],
            ));
            while ($arStoreProduct = $rsStoreProduct->fetch()) {
                $rsStore['AMOUNT'] += $arStoreProduct['AMOUNT'];
            }
            if ($rsStore['AMOUNT'] > 0) {
                $rsStore['AVAILABLE'] = true;
            }
        }

        return $rsStore;
    }

    function currencyGetOptimalPrice($productID, $quantity = 1, $arUserGroups = [], $renewal = "Y", $arPrices = [], $siteID = SITE_ID, $arDiscountCoupons = false)
    {
        $currency = "RUB";

        if ($_COOKIE["CATALOG_CURRENCY"]) {
            $currency = $_COOKIE["CATALOG_CURRENCY"];
        }

        $arOptPrices = \CCatalogProduct::GetByIDEx($productID);

        switch ($currency) {
            case 'RUB':
                return [
                    'PRICE' => [
                        "ID" => $productID,
                        'CATALOG_GROUP_ID' => 1,
                        'PRICE' => $arOptPrices['PRICES'][1]['PRICE'],
                        'CURRENCY' => "RUB",
                        'ELEMENT_IBLOCK_ID' => $productID,
                        'VAT_INCLUDED' => "Y",
                    ],
                    'DISCOUNT' => [
                        'VALUE' => 0,
                        'CURRENCY' => "RUB",
                    ],
                ];
            case 'USD':
                return [
                    'PRICE' => [
                        "ID" => $productID,
                        'CATALOG_GROUP_ID' => 6,
                        'PRICE' => $arOptPrices['PRICES'][6]['PRICE'],
                        'CURRENCY' => "USD",
                        'ELEMENT_IBLOCK_ID' => $productID,
                        'VAT_INCLUDED' => "Y",
                    ],
                    'DISCOUNT' => [
                        'VALUE' => 0,
                        'CURRENCY' => "USD",
                    ],
                ];
            case 'AED':
                return [
                    'PRICE' => [
                        "ID" => $productID,
                        'CATALOG_GROUP_ID' => 7,
                        'PRICE' => $arOptPrices['PRICES'][7]['PRICE'],
                        'CURRENCY' => "AED",
                        'ELEMENT_IBLOCK_ID' => $productID,
                        'VAT_INCLUDED' => "Y",
                        "VAT_RATE" => 0,
                    ],
                    'DISCOUNT' => [
                        'VALUE' =>  $arOptPrices['PRICES'][7]['PRICE'],
                        'CURRENCY' => "AED",
                    ],
                    // "RESULT_PRICE" => [
                    //     "PRICE_TYPE_ID" => 7,
                    //     "BASE_PRICE" => $arOptPrices['PRICES'][7]['PRICE'],
                    //     "CURRENCY" => "AED",
                    // ],
                    // "PRODUCT_ID" => $arOptPrices["ID"]

                ];
            default:
                if ($siteID === 's1') {
                    return [
                        'PRICE' => [
                            "ID" => $productID,
                            'CATALOG_GROUP_ID' => 1,
                            'PRICE' => $arOptPrices['PRICES'][1]['PRICE'],
                            'CURRENCY' => "RUB",
                            'ELEMENT_IBLOCK_ID' => $productID,
                            'VAT_INCLUDED' => "Y",
                        ],
                        'DISCOUNT' => [
                            'VALUE' => 0,
                            'CURRENCY' => "RUB",
                        ],
                    ];
                } elseif ($siteID === 's2') {
                    return [
                        'PRICE' => [
                            "ID" => $productID,
                            'CATALOG_GROUP_ID' => 6,
                            'PRICE' => $arOptPrices['PRICES'][6]['PRICE'],
                            'CURRENCY' => "USD",
                            'ELEMENT_IBLOCK_ID' => $productID,
                            'VAT_INCLUDED' => "Y",
                        ],
                        'DISCOUNT' => [
                            'VALUE' => 0,
                            'CURRENCY' => "USD",
                        ],
                    ];
                } else {
                    return [
                        'PRICE' => [
                            "ID" => $productID,
                            'CATALOG_GROUP_ID' => 1,
                            'PRICE' => $arOptPrices['PRICES'][1]['PRICE'],
                            'CURRENCY' => "RUB",
                            'ELEMENT_IBLOCK_ID' => $productID,
                            'VAT_INCLUDED' => "Y",
                        ],
                        'DISCOUNT' => [
                            'VALUE' => 0,
                            'CURRENCY' => "RUB",
                        ],
                    ];
                }
        }
    }
}
