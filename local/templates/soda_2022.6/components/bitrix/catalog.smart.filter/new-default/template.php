<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);
//dbgz($arParams);
?>
<? $frame = $this->createFrame("sender-subscribe")->begin(""); ?>
<div class="catalog-filter" data-name="smart_filter_sodamoda_form" data-action="<? echo $arResult["FORM_ACTION"] ?>">
	<div class="wrapper">
		<div class="line">
			<?
			if (isset($arParams['SECTION_ID']) && !empty($arParams['SECTION_ID'])) {
				$currentSectionID = $arParams['SECTION_ID'];
				$subsectiosID = $arParams['SECTION_ID'];
			} else {
				$currentSectionID = 9999;
				$subsectiosID = 9999;
			}


			$arFilterSect = array('IBLOCK_ID' => CATALOG_ID, 'ID' => $currentSectionID);
			$db_list_sect = CIBlockSection::GetList(array($by => $order), $arFilterSect, true, array('DEPTH_LEVEL', 'IBLOCK_SECTION_ID'));
			if ($ar_result_sect = $db_list_sect->GetNext()) {
				if ($ar_result_sect['DEPTH_LEVEL'] == 2) {
					$subsectiosID = $ar_result_sect['IBLOCK_SECTION_ID'];
					$activeTopSection = $currentSectionID;
				}
			}
			?>

			<? $APPLICATION->IncludeComponent(
				"bitrix:catalog.section.list",
				"top",
				array(
					"ADD_SECTIONS_CHAIN" => "Y",	// Включать раздел в цепочку навигации
					"CACHE_FILTER" => "N",	// Кешировать при установленном фильтре
					"CACHE_GROUPS" => "Y",	// Учитывать права доступа
					"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
					"CACHE_TYPE" => "A",	// Тип кеширования
					"COMPOSITE_FRAME_MODE" => "A",	// Голосование шаблона компонента по умолчанию
					"COMPOSITE_FRAME_TYPE" => "AUTO",	// Содержимое компонента
					"COUNT_ELEMENTS" => "N",	// Показывать количество элементов в разделе
					"COUNT_ELEMENTS_FILTER" => "CNT_ACTIVE",	// Показывать количество
					"FILTER_NAME" => "sectionsFilter",	// Имя массива со значениями фильтра разделов
					"IBLOCK_ID" => "2",	// Инфоблок
					"IBLOCK_TYPE" => "catalogs",	// Тип инфоблока
					"SECTION_CODE" => "",	// Код раздела
					"SECTION_FIELDS" => array(	// Поля разделов
						0 => "",
						1 => "",
					),
					"SECTION_ID" => $subsectiosID,	// ID раздела
					"SECTION_URL" => "",	// URL, ведущий на страницу с содержимым раздела
					"SECTION_USER_FIELDS" => array(	// Свойства разделов
						0 => "UF_NAME_EN",
						1 => "",
					),
					"SHOW_PARENT_NAME" => "Y",	// Показывать название раздела
					"TOP_DEPTH" => "2",	// Максимальная отображаемая глубина разделов
					"VIEW_MODE" => "LINE",	// Вид списка подразделов
					"ACTIVE_TOP_SECTION" => $activeTopSection
				),
				$component
			); ?>

			<div data-filter-items class="items <? if ($arResult["CHECKED"]) { ?>is-applied<? } ?>">
				<div id="range-slider" class="range-slider" data-view-mode="<?= isset($_COOKIE['viewMode']) ? $_COOKIE['viewMode'] : '1'; ?>"></div>
				<div class="item-filter hover js-c-filter-display"><?= Loc::getMessage("SMART_FILTER_FILTERS") ?><span class="count">[ <span data-filter-number class="number"></span> ]</span></div>
				<div data-filter-clear class="item-clear hover"><?= Loc::getMessage("SMART_FILTER_CLEAR") ?></div>
			</div>
		</div>

		<div class="c-filter">
			<div class="scroll-wrapper">
				<div class="blocks">
					<?
					global $USER;
					foreach ($arResult["ITEMS"] as $key => $arItem) //prices
					{
						$key = $arItem["ENCODED_ID"];
						if (isset($arItem["PRICE"])) {
							/*
                        if ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0)
                            continue;
                        $step_num = 5;
                        $step = ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"]) / $step_num;
                        $prices = array();
                        if (Bitrix\Main\Loader::includeModule("currency"))
                        {
                            for ($i = 0; $i < $step_num; $i++)
                            {
                                $prices[$i] = CCurrencyLang::CurrencyFormat($arItem["VALUES"]["MIN"]["VALUE"] + $step*$i, $arItem["VALUES"]["MIN"]["CURRENCY"], false);
                            }
                            $prices[$step_num] = CCurrencyLang::CurrencyFormat($arItem["VALUES"]["MAX"]["VALUE"], $arItem["VALUES"]["MAX"]["CURRENCY"], false);
                        }
                        else
                        {
                            $precision = $arItem["DECIMALS"]? $arItem["DECIMALS"]: 0;
                            for ($i = 0; $i < $step_num; $i++)
                            {
                                $prices[$i] = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step*$i, $precision, ".", "");
                            }
                            $prices[$step_num] = number_format($arItem["VALUES"]["MAX"]["VALUE"], $precision, ".", "");
                        }
                        ?>
                        <div class="block">
                            <div class="caption">Цена</div>
                            <div class="items">
                                <?for($i = 1; $i <= $step_num; $i++){?>
                                <div class="item <?echo ($i>3 && !in_array($prices[$i-1]."_".$prices[$i],$arResult["ALT_FORM_PROP"]['price'][$arItem["ID"]])) ? 'disabled': '' ?>">
                                    <input data-item-prop <? echo in_array($prices[$i-1]."_".$prices[$i],$arResult["ALT_FORM_PROP"]['price'][$arItem["ID"]]) ? 'checked="checked"': '' ?> type="checkbox" value="<?=$prices[$i-1]?>_<?=$prices[$i]?>" name="<?=$arParams["FILTER_NAME"]?>[price][<?=$arItem["ID"]?>][<?=$i?>]" id="filter_price_<?=$arItem["ID"]?>_<?=$i?>">
                                    <label  <? echo in_array($prices[$i-1]."_".$prices[$i],$arResult["ALT_FORM_PROP"]['price'][$arItem["ID"]]) ? 'class="hover"': '' ?> data-role="label_filter_price_<?=$arItem["ID"]?>_<?=$i?>" for="filter_price_<?=$arItem["ID"]?>_<?=$i?>">До <?=$prices[$i]?> руб</label>
                                </div>
                                <?}?>
                            </div>
                            <?if($step_num>3 && $arItem["COUNT_CHECKED"]<$step_num){?>
                            <a class="more hover js-c-filter-more" data-many="Посмотреть больше" data-small="Свернуть">Посмотреть больше</a>
                            <?}?>
                        </div>
                      <?
                      */
						} else {
							if (empty($arItem["VALUES"]) || isset($arItem["PRICE"]))
								continue;

							if ($arItem["DISPLAY_TYPE"] == "A" && ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0))
								continue;

					?>
							<div class="block">
								<? if ($arItem['CODE'] == 'SIZES') : ?>
									<div class="caption"><?= Loc::getMessage("SMART_FILTER_SIZE") ?></div>
								<? else : ?>
									<div class="caption"><?= $arItem["NAME"] ?></div>
								<? endif; ?>
								<?
								$arCur = current($arItem["VALUES"]);
								switch ($arItem["DISPLAY_TYPE"]) {
									default: //CHECKBOXES
								?>
										<div class="items">
											<?
											$i = 1;
											$iLimit = 10;
											?>
											<? foreach ($arItem["VALUES"] as $val => $ar) : ?>
												<div class="item <? echo ($i > $iLimit && !$ar["CHECKED"]) ? 'disabled' : '' ?>">
													<input data-item-prop <? echo $ar["CHECKED"] ? 'checked="checked"' : '' ?> type="checkbox" value="<?= $ar["HTML_VALUE"] ?>" name="<?= $ar["CONTROL_NAME"] ?>" id="<?= $ar["CONTROL_ID"] ?>" <?= isset($ar['MOD']) ? 'data-mod-id=' . $ar['MOD'] : ''; ?> onclick="">
													<label <?= $ar["CHECKED"] ? 'class="hover"' : '' ?> data-role="label_<?= $ar["CONTROL_ID"] ?>" for="<?= $ar["CONTROL_ID"] ?>"><?= $ar["VALUE"]; ?></label>
												</div>
												<? $i++; ?>
											<? endforeach ?>
										</div>
										<? if (count($arItem["VALUES"]) > $iLimit && $arItem["COUNT_CHECKED"] < count($arItem["VALUES"])) : ?>
											<a class="more hover js-c-filter-more" data-many="<?= Loc::getMessage("SMART_FILTER_VIEW_MORE") ?>" data-small="<?= Loc::getMessage("SMART_FILTER_COLLAPSE") ?>"><?= Loc::getMessage("SMART_FILTER_VIEW_MORE") ?></a>
								<? endif;
								}
								?>
							</div>
					<?
						}
					}
					?>
				</div>
			</div>
		</div>

	</div>
</div>
<? $frame->end(); ?>