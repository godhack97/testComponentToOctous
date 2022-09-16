<?php

namespace local\Helpers;

use Bitrix\Main\Localization\Loc,
    Bitrix\Iblock,
    Bitrix\Main\Loader,
    Bitrix\Highloadblock\HighloadBlockTable;

class UserTypeElemColors extends \CAllIBlockProperty
{
    function GetUserTypeDescription()
    {
        return array(
            "USER_TYPE" => "UserTypeElemColors",
            'PROPERTY_TYPE'  => Iblock\PropertyTable::TYPE_ELEMENT,
            "DESCRIPTION" => "Элемент с цветом",
            'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
            //"VIEW_CALLBACK" => array(__CLASS__, 'GetPublicView'),
            //"EDIT_CALLBACK" => array(__CLASS__, 'GetPublicEdit'),
        );
    }

    function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
      
        $arItem = Array(
            "ID" => 0,
            "IBLOCK_ID" => 0,
            "NAME" => ""
        );

        if(intval($value["VALUE"]) > 0)
        {
            $arFilter = Array(
                "ID" => intval($value["VALUE"]),
                "IBLOCK_ID" => $arProperty["LINK_IBLOCK_ID"],
            );
            $arItem = \CIBlockElement::GetList(Array(), $arFilter, false, false, Array("ID", "IBLOCK_ID", "NAME","PROPERTY_COLOR"))->Fetch();

            if($arItem["PROPERTY_COLOR_VALUE"]){
                Loader::includeModule("highloadblock");

                $hlblock = HighloadBlockTable::getById(2)->fetch();
                $entity = HighloadBlockTable::compileEntity($hlblock);
                $PsuTaskControl = $entity->getDataClass();

                $reslist=$PsuTaskControl::getList([
                    'filter'=>[
                        "UF_XML_ID"=>$arItem["PROPERTY_COLOR_VALUE"],
                    ],
                ]);
                if ($el=$reslist->fetch())
                {
                    $arItem["PROPERTY_COLOR_VALUE"]=$el["UF_NAME"];
                }
            }


        }

        $html = '<input name="'.$strHTMLControlName["VALUE"].'" id="'.$strHTMLControlName["VALUE"].'" value="'.htmlspecialcharsex($value["VALUE"]).'" size="5" type="text">';
        $html .= '<input type="button" value="..." onclick="jsUtils.OpenWindow(\'/local/php_interface/iblock_element_search.php?lang='.LANG.'&IBLOCK_ID='.$arProperty["LINK_IBLOCK_ID"].'&n='.$strHTMLControlName["VALUE"].'\', 600, 500);">';
        $html .= ' <span id="sp_'.$strHTMLControlName["VALUE"].'">'.$arItem["NAME"]." - ".$arItem['PROPERTY_COLOR_VALUE'].'</span>';
        return  $html;
    }

}