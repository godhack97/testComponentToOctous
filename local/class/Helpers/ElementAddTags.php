<?php

namespace local\Helpers;


class ElementAddTags
{
  function AddTagsFromSectionTitle(&$arFields)
  {
//    if($arFields['IBLOCK_ID'] == CATALOG_ID) {
//      $tagsStr = '';
//
//      if(!empty($arFields['IBLOCK_SECTION'])) {
//        $dbSections = \Bitrix\Iblock\SectionTable::getList([
//          'select' => ['NAME'],
//          'filter' => ['ID' => $arFields['IBLOCK_SECTION']]
//        ]);
//
//        $firstTags = false;
//        while($arSections = $dbSections->fetch()) {
//          if($firstTags == false) {
//            $tagsStr .= $arSections['NAME'];
//            $firstTags = true;
//          } else {
//            $tagsStr .= ", ".$arSections['NAME'];
//          }
//        }
//      }
//    }
//
//    $arFields['TAGS'] = $tagsStr;
  }
}