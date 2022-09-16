<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
<?php $frame = $this->createFrame("sender-subscribe")->begin(""); ?>

<div class="catalog-new-filter" data-name="smart_filter_sodamoda_form" data-action="<?= $arResult["FORM_ACTION"] ?>">
	<div class="wrapper">
		<div class="line">
			<div class="breadcrumb-list">
				<a href="<?=SITE_DIR?>" class="breadcrumb-item"><span><?=Loc::getCurrentLang() === 'en' ? 'Home' : 'Главная';?></span></a>
			<?php
				$arFilterSect = array('IBLOCK_ID' => CATALOG_ID, 'ID' => $arParams['SECTION_ID']);
				$db_list_sect = CIBlockSection::GetList([], $arFilterSect, true, array('CODE', 'NAME', 'UF_NAME_EN'));
				if ($ar_result_sect = $db_list_sect->GetNext()) {
					?>
					<span>/</span>
					<a href="<?=SITE_DIR?>catalog/<?=$ar_result_sect['CODE'];?>/" class="breadcrumb-item"><span><?=Loc::getCurrentLang() === 'en' ? $ar_result_sect['UF_NAME_EN'] : $ar_result_sect['NAME'];?></span></a>
					<?php
				}
				else{
					?>
					<span>/</span>
					<a href="<?=SITE_DIR?>catalog/new/" class="breadcrumb-item"><span><?=Loc::getCurrentLang() === 'en' ? 'New' : 'Новинки';?></span></a>
					<?php
				}
			?>
			</div>
			<?php

			if (isset($arParams['SECTION_ID']) && !empty($arParams['SECTION_ID'])) {
				$currentSectionID = $arParams['SECTION_ID'];
				$subsectiosID = $arParams['SECTION_ID'];
			} else {
				$currentSectionID = 9999;
				$subsectiosID = 9999;
			}


			$arFilterSect = array('IBLOCK_ID' => CATALOG_ID, 'ID' => $currentSectionID);
			$db_list_sect = CIBlockSection::GetList([$by => $order], $arFilterSect, true, array('DEPTH_LEVEL', 'IBLOCK_SECTION_ID'));
			if ($ar_result_sect = $db_list_sect->GetNext()) {
				if ($ar_result_sect['DEPTH_LEVEL'] == 2) {
					$subsectiosID = $ar_result_sect['IBLOCK_SECTION_ID'];
					$activeTopSection = $currentSectionID;
				}
			}
			?>

			<?php $APPLICATION->IncludeComponent(
				"bitrix:catalog.section.list",
				"subsection",
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


			<div class="filter-list">
				<?php
				foreach($arResult["ITEMS"] as $key => $arItem){
					switch ($arItem['CODE']){
						case 'SIZES':
							?>
							<div class="filter-item js-filter-item filter-item__size">
								<span class="filter-title"><?=Loc::getMessage("SMART_FILTER_SIZE")?></span>

								<div class="filter-choose">
									<span class="filter-choose-title"></span>
									<span class="filter-choose-close hover" data-filter-clear>&times;</span>
								</div>

								<div class="filter-content filter-content__size">
									<?php
										foreach ($arItem["VALUES"] as $prop){
											?>
											<label class="filter-content-item hover">
												<input data-item-prop type="checkbox" <?=$prop["CHECKED"]? 'checked="checked"': '' ?> value="<?=$prop["HTML_VALUE"] ?>" name="<?=$prop["CONTROL_NAME"] ?>" id="<?=$prop["CONTROL_ID"] ?>">
												<span class="filter-content-title"><?=$prop["VALUE"];?></span>
											</label>
											<?php
										}
									?>
								</div>
							</div>
							<?php
							break;
					}
				}
					foreach($arResult["ITEMS"] as $key => $arItem){
						switch ($arItem['CODE']){
							case 'COLOR':
								?>
								<div class="filter-item js-filter-item filter-item__color">
									<span class="filter-title"><?=Loc::getMessage("SMART_FILTER_COLOR")?></span>

									<div class="filter-choose">
										<span class="filter-choose-color-box"></span>
										<span class="filter-choose-title"></span>
										<span class="filter-choose-close hover" data-filter-clear>&times;</span>
									</div>

									<div class="filter-content filter-content__color">
										<?php
											foreach ($arItem["VALUES"] as $prop){
												?>
												<label class="filter-content-item hover">
													<span class="filter-color-box" style="background-color: <?=$prop["HEX"];?>;"></span>
													<input data-item-prop type="checkbox" <?=$prop["CHECKED"]? 'checked="checked"': '' ?> value="<?=$prop["HTML_VALUE"] ?>" name="<?=$prop["CONTROL_NAME"] ?>" id="<?=$prop["CONTROL_ID"] ?>">
													<span class="filter-content-title"><?=$prop["VALUE"];?></span>
												</label>
												<?php
											}
										?>
									</div>
								</div>
								<?php
								break;
						}
					}
				?>
			</div>
		</div>
	</div>
</div>

<?php $frame->end(); ?>