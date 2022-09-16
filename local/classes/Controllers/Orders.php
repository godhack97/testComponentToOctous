<?php

namespace App\Controllers;

use Bitrix\Sale;
use App\Helpers\Qiwi;
use App\Helpers\User;
use Bitrix\Sale\Order;
use App\Helpers\PayPal;
use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\PaySystem;
use Bitrix\Main\Config\Option;
use App\Controllers\BaseController;
use Bitrix\Sale\DiscountCouponsManager;

class Orders extends BaseController
{
    const EN_DELIVERY        = [83];
    const RU_DELIVERY        = [3, 4, 5, 7, 40, 47, 66, 67, 71, 72];
    const RU_DELIVERY_MOSCOW = [3, 4, 7, 47];
    const RU_DELIVERY_SPB    = [5, 7, 47, 67];

    const EN_PAYMENT         = [15, 16];
    const RU_PAYMENT         = [10, 14];

    const MOSCOW_SPB_ID      = [
        "0000073738",
        "0000078911",
        "0000103664",
    ];

    public function getAction()
    {
        $request = Context::getCurrent()->getRequest();

        $deliveryId  = (int)$request->get("delivery_id");
        $cityId      = (int)$request->get("city_id");
        $location_id = (int)$request->get("location_id");

        $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Context::getCurrent()->getSite());

        $result = \Bitrix\Sale\Delivery\Services\Table::getList([
            "filter" => ["ACTIVE" => "Y"],

        ]);

        $arDelivery = [];

        while ($delivery = $result->fetch()) {
            if ($this->lang === "EN" && !in_array($delivery["ID"], self::EN_DELIVERY)) {
                continue;
            }

            if ($this->lang === "RU" && !in_array($delivery["ID"], self::RU_DELIVERY)) {
                continue;
            }

            $arDelivery[] = [
                "id"    => (int)$delivery["ID"],
                "name"  => $delivery["NAME"],
                "price" => $this->getDeliveryPriceForProduct($delivery["ID"], $cityId)
            ];
        }

        $deliveryPrice = 0;
        $total = 0;

        if ($request->get("delivery_id")) {
            foreach ($arDelivery as $delivery) {
                if ((int)$delivery["id"] === $deliveryId) {
                    $total = (int)$basket->getPrice() + (int)$delivery["price"];
                    $deliveryPrice = $delivery["price"];
                }
            }
        }


        $paySystemResult = \Bitrix\Sale\PaySystem\Manager::getList([
            "filter"  => ["ACTIVE" => "Y"]
        ]);

        $arPayments = [];

        while ($paySystem = $paySystemResult->fetch()) {
            if ($this->lang === "EN" && !in_array((int)$paySystem["ID"], self::EN_PAYMENT)) {
                continue;
            }

            if ($this->lang === "RU" && !in_array((int)$paySystem["ID"], self::RU_PAYMENT)) {
                continue;
            }

            if ($this->lang === "RU"
                && in_array((int)$location_id, self::MOSCOW_SPB_ID)
                && (int)$paySystem["ID"] !== 14) {
                continue;
            }

            $arPayments[] = [
                "id"    => (int)$paySystem["ID"],
                "name"  => $paySystem["NAME"],
            ];

            $dbRestriction = \Bitrix\Sale\Internals\ServiceRestrictionTable::getList([
                "select" => ["PARAMS"],
                "filter" => [
                    "SERVICE_ID" => $paySystem["ID"],
                    "SERVICE_TYPE" => \Bitrix\Sale\Services\PaySystem\Restrictions\Manager::SERVICE_TYPE_PAYMENT
                ]
            ]);

            $restrictions = [];

            while ($restriction = $dbRestriction->fetch()) {
                if (is_array($restriction["PARAMS"])) {
                    $restrictions = array_merge($restrictions, $restriction["PARAMS"]);
                }
            }
            $restriction = \Bitrix\Sale\Services\PaySystem\Restrictions\Delivery::prepareParamsValues([], $paySystem["ID"]);
            $restrictions["DELIVERY"] = $restriction["DELIVERY"];
        }

        return [
            "price"          => $basket->getBasePrice(),
            "subtotal"       => $basket->getPrice(),
            "delivery_price" => $deliveryPrice,
            "total"          => $total,
            "deliverys"      => $arDelivery,
            "payments"       => $arPayments
        ];
    }

    public function createAction()
    {
        if (!Loader::IncludeModule("sale")) {
            die();
        }

        function getPropertyByCode($propertyCollection, $code)
        {
            foreach ($propertyCollection as $property) {
                if ($property->getField("CODE") == $code) {
                    return $property;
                }
            }
        }

        $siteId = \Bitrix\Main\Context::getCurrent()->getSite();
        $request = Context::getCurrent()->getRequest();

        $location   = $request->get("location");
        $address    = $request->get("address");
        $name       = $request->get("name");
        $email      = $request->get("email");
        $phone      = $request->get("phone");
        $comment    = $request->get("comment");
        $deliveryId = $request->get("delivery_id");
        $paymentId  = $request->get("payment_id");

        if ($this->lang === "RU") {
            $currencyCode = Option::get("sale", "default_currency", "RUB");
        } else {
            $currencyCode = Option::get("sale", "default_currency", "USD");
        }

        DiscountCouponsManager::init();

        if ($this->token) {
            $userId = User::getUserIdByToken($this->token);
        } else {
            $userId = Sale\Fuser::getId();
        }

        $order = Order::create($siteId, $userId);

        $order->setPersonTypeId(1);
        $basket = Sale\Basket::loadItemsForFUser(\CSaleBasket::GetBasketUserID(), $siteId)->getOrderableItems();

        //Действия над товарами
        $basketItems = $basket->getBasketItems();
        foreach ($basketItems as $basketItem) {
        }

        $order->setBasket($basket);

        /*Shipment*/
        $shipmentCollection = $order->getShipmentCollection();
        $shipment = $shipmentCollection->createItem();
        $service = Delivery\Services\Manager::getById($deliveryId);

        $shipment->setFields([
            "DELIVERY_ID" => $service["ID"],
            "DELIVERY_NAME" => $service["NAME"],
        ]);

        $shipmentItemCollection = $shipment->getShipmentItemCollection();
        foreach ($order->getBasket() as $item) {
            $shipmentItem = $shipmentItemCollection->createItem($item);
            $shipmentItem->setQuantity($item->getQuantity());
        }

        /*Payment*/
        $paymentCollection = $order->getPaymentCollection();
        $payment = $paymentCollection->createItem();
        $paySystemService = PaySystem\Manager::getObjectById($paymentId);
        $payment->setFields([
            "PAY_SYSTEM_ID" => $paySystemService->getField("PAY_SYSTEM_ID"),
            "PAY_SYSTEM_NAME" => $paySystemService->getField("NAME"),
        ]);

        $payment->setField("SUM", $order->getPrice());

        $order->doFinalAction(true);
        $propertyCollection = $order->getPropertyCollection();

        $locationProperty = getPropertyByCode($propertyCollection, "LOCATION");
        $locationProperty->setValue($location);

        $addressProperty = getPropertyByCode($propertyCollection, "ADDRESS");
        $addressProperty->setValue($address);

        $nameProperty = getPropertyByCode($propertyCollection, "CONTACT_PERSON");
        $nameProperty->setValue($name);

        $emailProperty = getPropertyByCode($propertyCollection, "EMAIL");
        $emailProperty->setValue($email);

        $phoneProperty = getPropertyByCode($propertyCollection, "PHONE");
        $phoneProperty->setValue($phone);

        $order->setField("CURRENCY", $currencyCode);
        $order->setField("USER_DESCRIPTION", $comment);

        $order->save();

        $result = [
            "id" => $order->GetId(),
            "link" => null,
        ];

        if (in_array($paymentId, [14])) {
            $result["link"] = self::getPaymentLink($order->GetId());
        }

        $result["link"] = self::getPaymentLink($order->GetId());
        return $result;
    }

    protected static function getPaymentLink($orederId)
    {
        $link = "";

        $orderObj  = Sale\Order::load($orederId);
        $paymentCollection  =  $orderObj->getPaymentCollection();

        $payment  =  $paymentCollection[0];
        $service  = Sale\PaySystem\Manager::getObjectById($payment->getPaymentSystemId());
        $context  = \Bitrix\Main\Application::getInstance()->getContext();
        $service->initiatePay($payment, $context->getRequest());

        $initResult = $service->initiatePay($payment, $context->getRequest(), \Bitrix\Sale\PaySystem\BaseServiceHandler::STRING);

        if ((int)$payment->getPaymentSystemId() === 15) {
            $link = Qiwi::getUrlByTemplate($initResult->getTemplate());
        } else {
            $link = $initResult->getPaymentUrl();
        }
        
        return $link;
    }

    public function getDeliveryPriceForProduct($deliveryId, $userCityId)
    {
        $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());
        $virtualOrder = Order::create(\Bitrix\Main\Context::getCurrent()->getSite(), Sale\Fuser::getId());
        $virtualOrder->setPersonTypeId(1);
        $virtualOrder->setBasket($basket);

        /** @var \Bitrix\Sale\PropertyValueCollection $orderProperties */
        $orderProperties = $virtualOrder->getPropertyCollection();

        /** @var \Bitrix\Sale\PropertyValue $orderDeliveryLocation */
        $orderDeliveryLocation = $orderProperties->getDeliveryLocation();
        $orderDeliveryLocation->setValue($userCityId); // В какой город "доставляем" (куда доставлять).

        /** @var \Bitrix\Sale\ShipmentCollection $shipmentCollection */
        $shipmentCollection = $virtualOrder->getShipmentCollection();

        $delivery = Sale\Delivery\Services\Manager::getObjectById($deliveryId);

        /** @var \Bitrix\Sale\Shipment $shipment */
        $shipment = $shipmentCollection->createItem($delivery);

        $calcResult = $shipment->calculateDelivery();
        $deliveryPeriodDescription = $calcResult->getPeriodDescription();

        /** @var \Bitrix\Sale\ShipmentItemCollection $shipmentItemCollection */
        $shipmentItemCollection = $shipment->getShipmentItemCollection();

        /** @var \Bitrix\Sale\BasketItem $basketItem */
        foreach ($basket as $basketItem) {
            $item = $shipmentItemCollection->createItem($basketItem);
            $item->setQuantity($basketItem->getQuantity());
        }

        return $virtualOrder->getDeliveryPrice();
    }
}
