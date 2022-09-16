<?php

namespace App\Helpers;

class Images
{
    public static function getImageByLinkAndColor(string $link, string $color)
    {

        $arFilter = [
            "IBLOCK_ID"       => COLORS_ID,
            "ACTIVE_DATE"     => "Y",
            "ACTIVE"          => "Y",
            "PROPERTY_PARENT" => $link,
            "PROPERTY_COLOR"  => $color,
        ];

        $obColors = \CIBlockElement::GetList([], $arFilter, false, array("nPageSize" => 1), []);

        if ($obColor = $obColors->GetNextElement()) {
            $arFields = $obColor->GetProperties();
            $images = $arFields["IMAGES"]["VALUE"];
            if ($images && is_array($images) && count($images) > 0) {
                $arPhotoSmall = \CFile::ResizeImageGet(
                    $images[0],
                    ['width' => 800, 'height' => 1200],
                    BX_RESIZE_IMAGE_EXACT
                );
                return $arPhotoSmall["src"] ?: "";
            }
        }
    }
}
