<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Bitrix\Iblock\Elements\ElementStaticContentTable;
use Bitrix\Main\Context;

class Content extends BaseController
{
    public function getListAction($id)
    {
        $arResult = [];

        $elements = ElementStaticContentTable::query()
            ->addSelect("ID")
            ->addSelect("NAME")
            ->fetchCollection();

        foreach ($elements as $element) {
            $arResult[] = [
                "id" => $element["ID"],
                "title" => $element["NAME"],
            ];
        }

        return $arResult;
    }

    public function getByIdAction($is)
    {
        $element = ElementStaticContentTable::query()
            ->addFilter("ID", $id)
            ->addSelect("ID")
            ->addSelect("NAME")
            ->addSelect("DETAIL_TEXT")
            ->fetchObject();

        return [
            "id" => $element->getId(),
            "name" => $element->getName(),
            "text" => $element->getDetailText(),
        ];
    }
}
