<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class News extends BaseController
{
    public function getNewsAction()
    {
        $response = [];

        $res = CIBlockElement::GetList(
            ['ACTIVE_FROM' => 'DESC'],
            ['IBLOCK_ID' => '2', 'ACTIVE' => 'Y'],
            false,
            ["nPageSize" => 50],
            [],
        );

        while ($ob = $res->GetNext()) {
            $ob['PREVIEW_PICTURE'] = \CFile::GetPath($ob['PREVIEW_PICTURE']);
            $response[] = [
                'id' => $ob['ID'],
                'subject' => $ob['NAME'],
                'created_at' => date('Y-m-d H:i:s', strtotime($ob["ACTIVE_FROM"])),
                'text' => $ob['PREVIEW_TEXT'],
                'full_text' => $ob['DETAIL_TEXT'],
                'banner_url' => 'https://rocketboy.ru' . $ob['PREVIEW_PICTURE'],
                'original_post_url' => 'https://rocketboy.ru' . $ob['DETAIL_PAGE_URL'],
            ];
        }

        return $response;
    }
}
