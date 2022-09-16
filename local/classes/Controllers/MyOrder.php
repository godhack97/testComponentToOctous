<?php

namespace App\Controllers;

use Bitrix\Sale;
use App\Helpers\User;
use App\Helpers\Color;
use Bitrix\Sale\Order;
use Bitrix\Main\Context;
use App\Controllers\BaseController;
use Bitrix\Sale\Location\LocationTable;
use Bitrix\Sale\Internals\StatusLangTable;

class MyOrder extends BaseController
{
    public function getListAction()
    {
        $arResult = [];
        $userId = User::getUserIdByToken($this->token);
        $arStatuses = $this->getStatuses();

        $dbRes = Order::getList([
            "select" => ["ID"],
            "filter" => [
                "USER_ID" => $userId,
            ],
            "order" => ["ID" => "DESC"]
        ]);

        while ($arOrder = $dbRes->fetch()) {
            $order = Order::load($arOrder["ID"]);
            $basket = $order->getBasket();

            $arResult["items"][] = [
                "id" => $order->getId(),
                "order_id" => "№" . $order->getId(),
                "price" => $order->getPrice(),
                "quantity" => array_sum($basket->getQuantityList()),
                "status" => $arStatuses[$order->getField("STATUS_ID")],
            ];
        }

        return $arResult;
    }

    public function getByIdAction()
    {
        $arResult = [];

        $request = Context::getCurrent()->getRequest();
        $id = $request->get("id");
        $userId = User::getUserIdByToken($this->token);
        $arStatuses = $this->getStatuses();

        $dbRes = Order::getList([
            "select" => ["ID"],
            "filter" => [
                "USER_ID" => $userId,
                "ID" => $id,
            ]
        ]);

        if ($arOrder = $dbRes->fetch()) {
            $order = Order::load($arOrder["ID"]);
            $basket = Sale\Basket::loadItemsForOrder($order);

            $propertyCollection = $order->getPropertyCollection();
            $shipmentCollection = $order->getShipmentCollection();

            $name = $propertyCollection->getPayerName()->getValue();
            $phone = $propertyCollection->getPhone()->getValue();
            $email = $propertyCollection->getUserEmail()->getValue();
            $locationId = $propertyCollection->getDeliveryLocation()->getValue();

            foreach ($shipmentCollection as $shipment) {
                $deliveryName = $shipment->getDeliveryName();
            }

            $user = "$name \n $phone \n $email";

            $arResult = [
                "id" => $order->getId(),
                "order_id" => "№" . $order->getId(),
                "price_without_discount" => $basket->getPrice(),
                "price_with_discount" => $basket->getBasePrice(),
                "delivery_price" => $order->getDeliveryPrice(),
                "total_price" => $order->getPrice(),
                "quantity" => array_sum($basket->getQuantityList()),
                "status" => $arStatuses[$order->getField("STATUS_ID")],
                "user_data" => $user,
                "address" => self::getLocationByCode($locationId),
                "delivery_name" => $deliveryName,
            ];

            foreach ($basket as $basketItem) {
                $arProps = [];

                $obProps = \CIBlockElement::GetProperty(
                    self::OFFERS_ID,
                    $basketItem->getProductId(),
                    false,
                    ["CODE" => ["COLOR", "SIZE"]]
                );

                while ($arProp = $obProps->Fetch()) {
                    $arProps[$arProp["CODE"]] = $arProp["VALUE_ENUM"] ?: $arProp["VALUE"];
                }

                $color = Color::getColorById($arProps["COLOR"]);

                $arFilter = [
                    "IBLOCK_ID" => COLORS_ID,
                    "ACTIVE_DATE" => "Y",
                    "ACTIVE" => "Y",
                    "PROPERTY_PARENT" => $arProps["CML2_LINK"],
                    "PROPERTY_COLOR" => $arProps["COLOR"],
                ];

                $res = \CIBlockElement::GetList([], $arFilter, false, ["nPageSize" => 1], []);

                if ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetProperties();
                    $images = $arFields["IMAGES"]["VALUE"];
                    if ($images && is_array($images) && count($images) > 0) {
                        $arPhotoSmall = \CFile::ResizeImageGet(
                            $images[0],
                            [
                                "width" => 124,
                                "height" => 184
                            ],
                            BX_RESIZE_IMAGE_EXACT
                        );
                        $photo = $arPhotoSmall["src"];
                    }
                }

                $mxResult = \CCatalogSku::GetProductInfo($basketItem->getProductId());

                $arResult["items"][] = [
                    "id" => $mxResult["ID"],
                    "productId" => $basketItem->getProductId(),
                    "name" => $basketItem->getField("NAME"),
                    "price" => $basketItem->getFinalPrice(),
                    "color" => $color["UF_NAME"],
                    "size" => $arProps["SIZES"],
                    "artnumber" => $arProps["CML2_ARTICLE"],
                    "photo" => $photo
                ];
            }
        }

        return $arResult;
    }

    public function getStatuses(): array
    {
        $arResult = [];

        $statusResult = StatusLangTable::getList([
            "order" => ["STATUS.SORT" => "ASC"],
            "filter" => ["STATUS.TYPE" => "O", "LID" => LANGUAGE_ID],
            "select" => ["STATUS_ID", "NAME", "DESCRIPTION"],
        ]);

        while ($status = $statusResult->fetch()) {
            $arResult[$status["STATUS_ID"]] = $status["NAME"];
        }

        return $arResult;
    }

    public static function getLocationByCode($code)
    {
        $name = "";

        if ($code) {
            $parameters = [];
            $parameters["filter"]["=CODE"] = $code;
            $parameters["filter"]["NAME.LANGUAGE_ID"] = "ru";

            $parameters["limit"] = 1;
            $parameters["select"] = ["LNAME" => "NAME.NAME"];

            $arVal = LocationTable::getList($parameters)->fetch();

            if ($arVal && strlen($arVal["LNAME"]) > 0) {
                $name = $arVal["LNAME"];
            }
        }
        return $name;
    }
}
