<?php
  use Bitrix\Main\Localization\Loc;
  use Bitrix\Main\Loader,
    Bitrix\Highloadblock\HighloadBlockTable;

  $siteId = '';
  if (isset($_REQUEST['site_id']) && is_string($_REQUEST['site_id']))
    $siteId = substr(preg_replace('/[^a-z0-9_]/i', '', $_REQUEST['site_id']), 0, 2);

  if ($siteId) {
    define('SITE_ID', $siteId);
  }

  require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

  if (isset($_REQUEST['search_field']) && !empty($_REQUEST['search_field'])){
    $APPLICATION->IncludeComponent('arlix:manage.search', '', [
      'REQUEST' => trim(htmlspecialcharsbx($_REQUEST['search_field'])),
      'IS_OWNER' => trim(htmlspecialcharsbx($_REQUEST['is_owner'])),
    ], false);
  }
  else{
    $APPLICATION->IncludeComponent('bitrix:news.list', 'search_products', [
      'IBLOCK_ID' => CATALOG_ID,
      'FIELD_CODE' => ['CODE', 'NAME', 'IBLOCK_ID', 'IBLOCK_CODE', 'IBLOCK_EXTERNAL_ID', 'DETAIL_PICTURE', '*'],
      'FILTER_NAME' => 'product_owner',
      'PROPERTY_CODE' => ['PROPERTY_IMAGES', 'PROPERTY_PRODUCT_OWNER'],
      'ACTIVE_DATE_FORMAT' => 'd.m.Y',
      'ADD_SECTIONS_CHAIN' => 'N',
      'AJAX_MODE' => 'N',
      'AJAX_OPTION_ADDITIONAL' => '',
      'AJAX_OPTION_HISTORY' => 'N',
      'AJAX_OPTION_JUMP' => 'N',
      'AJAX_OPTION_STYLE' => 'N',
      'CACHE_FILTER' => 'N',
      'CACHE_GROUPS' => 'Y',
      'CACHE_TIME' => '36000000',
      'CACHE_TYPE' => 'A',
      'CHECK_DATES' => 'Y',
      'DETAIL_URL' => '/catalog/#SECTION_CODE#/#ELEMENT_CODE#/',
      'DISPLAY_BOTTOM_PAGER' => 'N',
      'DISPLAY_DATE' => 'N',
      'DISPLAY_NAME' => 'N',
      'DISPLAY_PICTURE' => 'N',
      'DISPLAY_PREVIEW_TEXT' => 'N',
      'DISPLAY_TOP_PAGER' => 'N',
      'HIDE_LINK_WHEN_NO_DETAIL' => 'N',
      'IBLOCK_TYPE' => 'catalogs',
      'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
      'INCLUDE_SUBSECTIONS' => 'N',
      'MESSAGE_404' => '',
      'NEWS_COUNT' => '-1',
      'PAGER_BASE_LINK_ENABLE' => 'N',
      'PAGER_DESC_NUMBERING' => 'N',
      'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
      'PAGER_SHOW_ALL' => 'N',
      'PAGER_SHOW_ALWAYS' => 'N',
      'PAGER_TEMPLATE' => 'ajax_product_load',
      'PAGER_TITLE' => 'Мои товары',
      'PARENT_SECTION' => '',
      'PARENT_SECTION_CODE' => '',
      'PREVIEW_TRUNCATE_LEN' => '',
      'SET_BROWSER_TITLE' => 'N',
      'SET_LAST_MODIFIED' => 'N',
      'SET_META_DESCRIPTION' => 'N',
      'SET_META_KEYWORDS' => 'N',
      'SET_STATUS_404' => 'N',
      'SET_TITLE' => 'N',
      'SHOW_404' => 'N',
      'SORT_BY1' => 'ACTIVE_FROM',
      'SORT_BY2' => 'SORT',
      'SORT_ORDER1' => 'DESC',
      'SORT_ORDER2' => 'ASC',
      'STRICT_SECTION_CHECK' => 'N',
      'COMPONENT_TEMPLATE' => 'catalog',
      'IS_OWNER' => CSite::InGroup([1, 8, 9, 15, 16]) ? 'N' : 'Y',
    ], false);
  }
  die();
?>