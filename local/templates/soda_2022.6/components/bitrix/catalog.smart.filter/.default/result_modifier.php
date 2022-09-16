<?php
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
	
	use Bitrix\Main\Web\Uri;
	use Bitrix\Main\Application;
	use \Bitrix\Main\Data\Cache;
	use \Bitrix\Main\Localization\Loc;
	use Bitrix\Main\Loader;
	use Bitrix\Highloadblock\HighloadBlockTable;
	
	$cache = Cache::createInstance(); // получаем экземпляр класса
	
	Loader::includeModule('highloadblock');
	
	$hlblock = HighloadBlockTable::getById(2)->fetch();
	$entity = HighloadBlockTable::compileEntity($hlblock);
	$PsuTaskControl = $entity->getDataClass();
//print_r('<pre>');
//print_r($arResult);
//print_r('</pre>');
	if ($cache->initCache(7200, 'offersMapFilter')) { // проверяем кеш и задаём настройки
		$offersMap = $cache->getVars(); // достаем переменные из кеша
	} elseif ($cache->startDataCache()) {
		$offersMap = [];
		$arSelect = ['ID', 'IBLOCK_ID', 'NAME', 'DATE_ACTIVE_FROM', 'PROPERTY_*'];
		$arFilter = ['IBLOCK_ID' => 8, 'ACTIVE_DATE' => 'Y', 'ACTIVE' => 'Y', '=AVAILABLE' => 'Y'];
		if (isset($GLOBALS[$arParams['PREFILTER_NAME']])) {
			$arFilter = array_merge($arFilter, $GLOBALS[$arParams['PREFILTER_NAME']]);
		}
		$res = CIBlockElement::GetList([], $arFilter, false, ['nPageSize' => 900], $arSelect);
		while ($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
			$arProps = $ob->GetProperties();
			foreach ($arProps as $prop) {
				switch ($prop['CODE']) {
					case 'SIZES':
						if (!empty($prop['VALUE'])) {
							if (!in_array($prop['VALUE_ENUM_ID'], $offersMap[$prop['CODE']])) {
								$offersMap[$prop['CODE']][$prop['VALUE_ENUM_ID']] = [
									'code' => $prop['CODE'],
									'name' => $prop['NAME'],
									'subcode' => $arProps['ROST']['VALUE_ENUM_ID'],
									'subcode_id' => $arProps['ROST']['ID'],
									'modName' => '',
								];
								if (!empty($arProps['ROST']['VALUE'])) {
									$offersMap[$prop['CODE']][$prop['VALUE_ENUM_ID']]['modName'] = $prop['VALUE'] . ' — ' . $arProps['ROST']['VALUE'] . ' ' . Loc::getMessage('RSM_MEASURE_SCU');
								} else {
									$offersMap[$prop['CODE']][$prop['VALUE_ENUM_ID']]['modName'] = $prop['VALUE'];
								}
							} else {
								if (!empty($arProps['ROST']['VALUE'])) {
									$offersMap[$prop['CODE']][$prop['VALUE_ENUM_ID']]['modName'] = $prop['VALUE'] . ' — ' . $arProps['ROST']['VALUE'] . ' ' . Loc::getMessage('RSM_MEASURE_SCU');
								}
							}
						}
						
						break;
					case 'COLOR':
						if (!empty($prop['VALUE'])) {
							$colorData = [];
							if (!in_array($prop['VALUE'], $offersMap[$prop['CODE']])) {
								$reslist = $PsuTaskControl::getList([
									'filter' => [
										'UF_XML_ID' => $prop['VALUE'],
									],
								]);
								if ($el = $reslist->fetch()) {
									
									$colorData = $el;
									//$colorData = (Loc::getCurrentLang() == 'en') ? $el['UF_NAME_EN'] : $el['UF_NAME'];
								}
								$offersMap[$prop['CODE']][$prop['VALUE']] = [
									'code' => $prop['CODE'],
									'name' => $prop['NAME'],
									'modName' => '',
									'value' => $colorData['UF_NAME'],
									'value_en' => $colorData['UF_NAME_EN'],
									'hex' => $colorData['UF_HEX'],
								];
							}
						}
						
						break;
				}
			}
		}
		$cache->endDataCache($offersMap); // записываем в кеш
	}
//$cache->cleanDir();
	
	if (isset($arParams["TEMPLATE_THEME"]) && !empty($arParams["TEMPLATE_THEME"])) {
		$arAvailableThemes = array();
		$dir = trim(preg_replace("'[\\\\/]+'", "/", dirname(__FILE__) . "/themes/"));
		if (is_dir($dir) && $directory = opendir($dir)) {
			while (($file = readdir($directory)) !== false) {
				if ($file != "." && $file != ".." && is_dir($dir . $file))
					$arAvailableThemes[] = $file;
			}
			closedir($directory);
		}
		
		if ($arParams["TEMPLATE_THEME"] == "site") {
			$solution = COption::GetOptionString("main", "wizard_solution", "", SITE_ID);
			if ($solution == "eshop") {
				$templateId = COption::GetOptionString("main", "wizard_template_id", "eshop_bootstrap", SITE_ID);
				$templateId = (preg_match("/^eshop_adapt/", $templateId)) ? "eshop_adapt" : $templateId;
				$theme = COption::GetOptionString("main", "wizard_" . $templateId . "_theme_id", "blue", SITE_ID);
				$arParams["TEMPLATE_THEME"] = (in_array($theme, $arAvailableThemes)) ? $theme : "blue";
			}
		} else {
			$arParams["TEMPLATE_THEME"] = (in_array($arParams["TEMPLATE_THEME"], $arAvailableThemes)) ? $arParams["TEMPLATE_THEME"] : "blue";
		}
	} else {
		$arParams["TEMPLATE_THEME"] = "blue";
	}
	
	$arParams["FILTER_VIEW_MODE"] = (isset($arParams["FILTER_VIEW_MODE"]) && toUpper($arParams["FILTER_VIEW_MODE"]) == "HORIZONTAL") ? "HORIZONTAL" : "VERTICAL";
	$arParams["POPUP_POSITION"] = (isset($arParams["POPUP_POSITION"]) && in_array($arParams["POPUP_POSITION"], array("left", "right"))) ? $arParams["POPUP_POSITION"] : "left";
	
	if ($arResult["FORM_ACTION"]) {
		$uri = new Uri($arResult["FORM_ACTION"]);
		$uri->deleteParams(array($arParams["FILTER_NAME"]));
		$arResult["FORM_ACTION"] = $uri->getUri();
	}
	
	$request = Application::getInstance()->getContext()->getRequest();
	$arResult["ALT_FORM_PROP"] = $request->get($arParams["FILTER_NAME"]);
	
	$newListItems = [];
	
	foreach ($arResult["ITEMS"] as $keyItem => $arItem) {
		$arItem["COUNT_CHECKED"] = 0;
		if (isset($arItem["PRICE"])) {
			if ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0) {
				continue;
			}
			$step_num = 5;
			$step = ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"]) / $step_num;
			$prices = array();
			if (Bitrix\Main\Loader::includeModule("currency")) {
				for ($i = 0; $i < $step_num; $i++) {
					$prices[$i] = CCurrencyLang::CurrencyFormat($arItem["VALUES"]["MIN"]["VALUE"] + $step * $i, $arItem["VALUES"]["MIN"]["CURRENCY"], false);
				}
				$prices[$step_num] = CCurrencyLang::CurrencyFormat($arItem["VALUES"]["MAX"]["VALUE"], $arItem["VALUES"]["MAX"]["CURRENCY"], false);
			} else {
				$precision = $arItem["DECIMALS"] ? $arItem["DECIMALS"] : 0;
				for ($i = 0; $i < $step_num; $i++) {
					$prices[$i] = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * $i, $precision, ".", "");
				}
				$prices[$step_num] = number_format($arItem["VALUES"]["MAX"]["VALUE"], $precision, ".", "");
			}
			
			for ($i = 1; $i <= $step_num; $i++) {
				if (in_array($prices[$i - 1] . "_" . $prices[$i], $arResult["ALT_FORM_PROP"]['price'][$arItem["ID"]])) {
					$arItem["CHECKED"] = true;
					$arItem["COUNT_CHECKED"]++;
				}
			}
		} else {
			foreach ($arItem["VALUES"] as $Values) {
				if ($Values["CHECKED"]) {
					$arItem["CHECKED"] = true;
					$arItem["COUNT_CHECKED"]++;
				}
			}
			
			usort($arItem["VALUES"], function ($item1, $item2) {
				if ($item2['CHECKED'] == $item1['CHECKED']) return 0;
				return $item2['CHECKED'] < $item1['CHECKED'] ? -1 : 1;
			});
		}
		
		switch ($arItem['CODE']) {
			case 'SIZES':
				foreach ($arItem['VALUES'] as $key => $item) {
					if (isset($offersMap[$arItem['CODE']][$item['FACET_VALUE']])) {
						$arItem['VALUES'][$key]['VALUE'] = $offersMap[$arItem['CODE']][$item['FACET_VALUE']]['modName'];
						$arItem['VALUES'][$key]['UPPER'] = $offersMap[$arItem['CODE']][$item['FACET_VALUE']]['modName'];
						if (!empty($offersMap[$arItem['CODE']][$item['FACET_VALUE']]['subcode'])) {
							$arItem['VALUES'][$key]['MOD'] = $arResult['ITEMS'][$offersMap[$arItem['CODE']][$item['FACET_VALUE']]['subcode_id']]['VALUES'][$offersMap[$arItem['CODE']][$item['FACET_VALUE']]['subcode']]['CONTROL_ID'];
						}
					}
				}
				$newListItems[$keyItem] = $arItem;
				break;
			case 'ROST':
				break;
			case 'COLOR':
				$newValues = [];
				foreach ($arItem['VALUES'] as $key => $item) {
					if (isset($offersMap[$arItem['CODE']][$item['URL_ID']])) {
						$newValues[$key] = $arItem['VALUES'][$key];
						$newValues[$key]['VALUE'] = Loc::getCurrentLang() == 'en' ? $offersMap[$arItem['CODE']][$item['URL_ID']]['value_en'] : $offersMap[$arItem['CODE']][$item['URL_ID']]['value'];
						$newValues[$key]['HEX'] = $offersMap[$arItem['CODE']][$item['URL_ID']]['hex'];
					}
				}
				$arItem['VALUES'] = $newValues;
				$newListItems[$keyItem] = $arItem;
				break;
			default:
				$newListItems[$keyItem] = $arItem;
				break;
		}
	}
	$arResult['ITEMS'] = $newListItems;
	
	foreach ($arResult["ITEMS"] as $arItem) {
		if ($arItem["CHECKED"]) {
			$arResult["CHECKED"] = true;
		}
	}

