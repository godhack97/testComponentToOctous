<?php
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true){
		die();
	}

	use Bitrix\Main\Web\Json;
	$return = $arResult['RESPONSE'];

	if(!empty($arResult['ERRORS'])){
		$return = array('ERRORS' => $arResult['ERRORS']);
	}

	echo Json::encode($return);