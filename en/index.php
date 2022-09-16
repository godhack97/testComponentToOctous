<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
// // $APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
use \Bitrix\Main\Localization\Loc;
$APPLICATION->SetTitle("SODAMODA");
?>


<? $APPLICATION->IncludeComponent(
  "arlix:home.premiere_top",
  "",
  [
    "IBLOCK_ID" => 29,
    "COUNT" => 5,
  ],
  false
); ?>

<? $APPLICATION->IncludeComponent(
  "arlix:home.premiere_complect",
  "big_left",
  [
    "IBLOCK_ID" => 26,
    "COUNT" => 4,
  ],
  false
); ?>
<? $APPLICATION->IncludeComponent(
  "arlix:home.premiere_one",
  "big_one",
  [
    "IBLOCK_ID" => 25,
  ],
  false
); ?>
<? $APPLICATION->IncludeComponent(
  "arlix:home.premiere_complect",
  "big_right",
  [
    "IBLOCK_ID" => 27,
    "COUNT" => 4,
  ],
  false
); ?>
<? $APPLICATION->IncludeComponent(
  "arlix:home.premiere_carousel",
  "",
  [
    "IBLOCK_ID" => 28,
    "COUNT" => 6,
  ],
  false
); ?>





<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>