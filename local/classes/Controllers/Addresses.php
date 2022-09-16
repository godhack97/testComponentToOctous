<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\HlBlock;
use App\Helpers\User;
use Bitrix\Main\Context;

class Addresses extends BaseController
{
    public function getListAction()
    {
        $userId = User::getUserIdByToken($this->token);

        $entityDataClass = HlBlock::getDataClass("Addresses");

        $arData = $entityDataClass::getList([
            "filter" => ["UF_USER_ID" => $userId]
        ])->fetch();

        return ["items" => unserialize($arData["UF_ADDRESSES"])];
    }

    public function getAction()
    {
        $userId = User::getUserIdByToken($this->token);

        $request = Context::getCurrent()->getRequest();
        $id = $request->get("id");

        $entityDataClass = HlBlock::getDataClass("Addresses");

        $arData = $entityDataClass::getList([
            "filter" => ["UF_USER_ID" => $userId]
        ])->fetch();

        $arAddresses = unserialize($arData["UF_ADDRESSES"]);

        foreach ($arAddresses as $address) {
            if ($address["id"] === $id) {
                return $address;
            }
        }
    }

    public function addAction()
    {
        $userId = User::getUserIdByToken($this->token);

        $request = Context::getCurrent()->getRequest();
        $uniqid = uniqid();

        $arFields[] = [
            "id"      => $uniqid,
            "name"    => $request->get("name"),
            "street"  => $request->get("street"),
            "city"    => $request->get("city"),
            "zipcode" => $request->get("zipcode"),
            "country" => $request->get("country"),
            "phone"   => $request->get("phone")
        ];

        $entityDataClass = HlBlock::getDataClass("Addresses");

        $arData = $entityDataClass::getList([
            "filter" => ["UF_USER_ID" => $userId]
        ])->fetch();

        if ($arData && $arData["UF_ADDRESSES"]) {
            $arAddresses = unserialize($arData["UF_ADDRESSES"]);
            $arAddresses[] = $arFields[0];

            $entityDataClass::update($arData["ID"], [
                "UF_ADDRESSES" => serialize($arAddresses)
            ]);
        } else {
            $entityDataClass::add([
                "UF_USER_ID"   => $userId,
                "UF_ADDRESSES" => serialize($arFields),
            ]);
        }

        return ["id" => $uniqid];
    }

    public function editAction()
    {
        $userId = User::getUserIdByToken($this->token);

        $request = Context::getCurrent()->getRequest();
        $id = $request->get("id");

        $entityDataClass = HlBlock::getDataClass("Addresses");

        $arData = $entityDataClass::getList([
            "filter" => ["UF_USER_ID" => $userId]
        ])->fetch();

        $arAddresses = unserialize($arData["UF_ADDRESSES"]);

        foreach ($arAddresses as $key => $address) {
            if ($address["id"] === $id) {
                $arAddresses[$key] = [
                    "id"      => $address["id"],
                    "name"    => $request->get("name"),
                    "street"  => $request->get("street"),
                    "city"    => $request->get("city"),
                    "zipcode" => $request->get("zipcode"),
                    "country" => $request->get("country"),
                    "phone"   => $request->get("phone")
                ];
            }
        }

        $entityDataClass::update($arData["ID"], [
            "UF_ADDRESSES" => serialize($arAddresses)
        ]);
    }

    public function deleteAction()
    {
        $userId = User::getUserIdByToken($this->token);

        $request = Context::getCurrent()->getRequest();
        $id = $request->get("id");

        $entityDataClass = HlBlock::getDataClass("Addresses");

        $arData = $entityDataClass::getList([
            "filter" => ["UF_USER_ID" => $userId]
        ])->fetch();

        $arAddresses = unserialize($arData["UF_ADDRESSES"]);

        foreach ($arAddresses as $key => $address) {
            if ($address["id"] === $id) {
                unset($arAddresses[$key]);
            }
        }

        $arAddresses = array_values($arAddresses);

        $entityDataClass::update($arData["ID"], [
            "UF_ADDRESSES" => serialize($arAddresses)
        ]);
    }
}
