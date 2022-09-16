<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Color;
use App\Helpers\User;
use Bitrix\Main\Context;
use Bitrix\Sale;
use Bitrix\Main\Loader;
use Bitrix\Catalog\Product;
use Bitrix\Iblock\Elements\ElementOffersTable;
use Bitrix\Sale\DiscountCouponsManager;

class Basket extends BaseController
{
    public function getBasketAction()
    {
        $arResult = [];

        $rsUser = \CUser::GetByID(User::getUserIdByToken($this->token));
        $arUser = $rsUser->Fetch();

        if ($arUser["UF_API_COUPON"]) {
            DiscountCouponsManager::add($arUser["UF_API_COUPON"]);
            \CSaleBasket::UpdateBasketPrices(\CSaleBasket::GetBasketUserID(), SITE_ID);

            $dbBasketItems = \CSaleBasket::GetList(array(), array(
                "FUSER_ID" => \CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ), false, false, array());

            while ($arItem = $dbBasketItems->Fetch()) {
                $arOrder["BASKET_ITEMS"][] = $arItem;
            }
            $arOrder['SITE_ID'] = SITE_ID;

            \CSaleDiscount::DoProcessOrder($arOrder, array(), $arErrors);
        }

        $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Context::getCurrent()->getSite());

        $arResult = [
            "price" => $basket->getPrice(),
            "fullPrice" => $arOrder["ORDER_PRICE"] ?? $basket->getBasePrice()
        ];

        foreach ($basket as $basketItem) {
            $element = ElementOffersTable::getByPrimary($basketItem->getProductId(), [
                "select" => ["COLOR", "SIZES.ITEM", "CML2_ARTICLE", "CML2_LINK"],
            ])->fetchObject();

            $arColor = Color::getColorById($element->getColor()->getValue());

            $arFilter = [
                "IBLOCK_ID" => COLORS_ID,
                "ACTIVE_DATE" => "Y",
                "ACTIVE" => "Y",
                "PROPERTY_PARENT" => $element->getCml2Link()->getValue(),
                "PROPERTY_COLOR" => $element->getColor()->getValue(),
            ];

            $res = \CIBlockElement::GetList([], $arFilter, false, array("nPageSize" => 1), []);

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
                "id"        => $mxResult["ID"],
                "productId" => $basketItem->getProductId(),
                "name"      => $basketItem->getField("NAME"),
                "photo"     => $photo,
                "price"     => $basketItem->getPrice() ?? "",
                "quantity"  => $basketItem->getQuantity(),
                "color"     => $arColor["UF_NAME"],
                "size"      => ($element->getSizes()->getItem()) ? $element->getSizes()->getItem()->getValue() : "",
                "articul"   => $element->getCml2Article()->getValue(),
            ];
        }

        return $arResult;
    }

    public function addToBasketAction()
    {
        Loader::includeModule("catalog");

        $request = Context::getCurrent()->getRequest();
        $id = $request->get("id");

        $fields = [
            "PRODUCT_ID" => $id,
            "QUANTITY" => 1,
        ];

        $result = Product\Basket::addProduct($fields);

        if (!$result->isSuccess()) {
            throw new \Exception(implode("; ", $result->getErrorMessages()));
        }
    }

    public function decreaseBasketAction()
    {
        Loader::includeModule("catalog");

        $request = Context::getCurrent()->getRequest();
        $id = $request->get("id");
        $quantity = $request->get("quantity");

        $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Context::getCurrent()->getSite());

        foreach ($basket as $basketItem) {
            if ($basketItem->getProductId() == $id) {
                $basketItem->setField("QUANTITY", $basketItem->getQuantity() - $quantity);
            }
        }

        $basket->save();
    }

    public function deleteInBasketAction()
    {
        Loader::includeModule("catalog");

        $request = Context::getCurrent()->getRequest();
        $id = $request->get("id");

        $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Context::getCurrent()->getSite());

        foreach ($basket as $basketItem) {
            if ($basketItem->getProductId() == $id) {
                $basket->getItemById($basketItem->getId())->delete();
                $basket->save();
            }
        }
    }

    public function applyCouponAction()
    {
        Loader::includeModule("sale");
        Loader::IncludeModule("catalog");

        $request = Context::getCurrent()->getRequest();
        $sCode = $request->get("coupon");

        //Применяем купон
        DiscountCouponsManager::add($sCode);

        /* Обновляем скидки */
        \CSaleBasket::UpdateBasketPrices(\CSaleBasket::GetBasketUserID(), SITE_ID);
        $dbBasketItems = \CSaleBasket::GetList(array(), array(
            "FUSER_ID" => \CSaleBasket::GetBasketUserID(),
            "LID" => SITE_ID,
            "ORDER_ID" => "NULL"
        ), false, false, array());

        while ($arItem = $dbBasketItems->Fetch()) {
            $arOrder["BASKET_ITEMS"][] = $arItem;
        }
        $arOrder['SITE_ID'] = SITE_ID;

        \CSaleDiscount::DoProcessOrder($arOrder, array(), $arErrors);

        if (!$arErrors) {
            $user = new \CUser();
            $user->Update(User::getUserIdByToken($this->token), ['UF_API_COUPON' => $sCode]);
        }

        return [
            "fullPrice" => $arOrder["ORDER_PRICE"],
        ];
    }

    public function deleteCouponAction()
    {
        $user = new \CUser();
        $user->Update(User::getUserIdByToken($this->token), ['UF_API_COUPON' => ""]);
    }
}
