<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Bitrix\Iblock\SectionTable;
use Exception;

class Sections extends BaseController
{
    protected array $response = [];

    public function getSectionsAction(): array
    {
        $sections = SectionTable::Query()
            ->setSelect(["ID", "NAME"])
            ->setFilter(["IBLOCK_ID" => self::CATALOG_ID, "DEPTH_LEVEL" => "1", "ACTIVE" => "Y"])
            ->setOrder(["SORT" => "ASC"])
            ->exec()
            ->fetchCollection();

        foreach ($sections as $section) {
            $this->response["items"][] = [
                "id"   => (int) $section->getId(),
                "name" => $section->getName()
            ];
        }

        $this->response["total_items"] = count($this->response["items"]);

        return $this->response;
    }
}
