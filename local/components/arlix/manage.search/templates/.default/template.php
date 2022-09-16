<?php
  if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
  }

  /** @var CMain $APPLICATION */
  /** @var array $arParams */
  /** @var array $arResult */

  use Bitrix\Main\Web\Json;

  global $USER;

  $APPLICATION->RestartBuffer();
  
  $return = $arResult['RESPONSE'];
  
  if(!empty($arResult['ERRORS'])){
    $return = ['ERRORS' => $arResult['ERRORS']];
  }
  
  echo Json::encode($return);
?>