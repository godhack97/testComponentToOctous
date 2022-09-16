<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers;
use Bitrix\Main;
use Bitrix\Main\Loader;

class Location extends BaseController
{
    public function getAction()
    {
        Loader::includeModule('sale');

        require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/bitrix/sale.location.selector.search/class.php");

        try {
            \CUtil::JSPostUnescape();
            $request = Main\Context::getCurrent()->getRequest();
            $query = $request->get("query");

            $params = [
                "select" => [
                    1 => "CODE",
                    "DISPLAY" => "NAME.NAME",
                ],
                "additionals" => [
                    1 => "PATH"
                ],
                "filter" => [
                    "=PHRASE" => $query,
                    "=NAME.LANGUAGE_ID" => "ru",
                    "=SITE_ID" => "s1"
                ],
                "version" => "2",
                "PAGE_SIZE" => "20",
                "PAGE" => "0"
            ];

            $resutl = \CBitrixLocationSelectorSearchComponent::processSearchRequestV2($params);

            if ($resutl) {
                unset($resutl["ETC"]);
            }

            return  Helpers\Arrays::arrayChangeKeyCaseRecursive($resutl, CASE_LOWER);
        } catch (Main\SystemException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
