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

if (empty($arResult["ALL_ITEMS"]))
	return;

CUtil::InitJSCore();

if (file_exists($_SERVER["DOCUMENT_ROOT"] . $this->GetFolder() . '/themes/' . $arParams["MENU_THEME"] . '/colors.css'))
	$APPLICATION->SetAdditionalCSS($this->GetFolder() . '/themes/' . $arParams["MENU_THEME"] . '/colors.css');

$menuBlockId = "catalog_menu_" . $this->randString();
?>


<div class="main-menu bx-top-nav bx-<?= $arParams["MENU_THEME"] ?>" id="<?= $menuBlockId ?>">
	<nav class="bx-top-nav-container" id="cont_<?= $menuBlockId ?>">
		<ul class="bx-nav-list-1-lvl js-header-menu" id="ul_<?= $menuBlockId ?>">

			<?
			$activeFlag = false;
			if (strpos($APPLICATION->GetCurDir(), 'catalog/new/') || strpos($APPLICATION->GetCurDir(), 'en/catalog/new/'))
				$activeFlag = true;
			?>

			<li class="bx-nav-1-lvl bx-nav-list-1-col <? if ($activeFlag) : ?>active<? endif ?> bx-nav-parent" onmouseover="BX.CatalogMenu.itemOver(this);" onmouseout="BX.CatalogMenu.itemOut(this)" data-role="bx-menu-item" onclick="if (BX.hasClass(document.documentElement, 'bx-touch')) obj_<?= $menuBlockId ?>.clickInMobile(this, event);">
				<a href="<? if (Loc::getCurrentLang() == 'en') : ?>/en<? endif ?>/catalog/new/">
					<span><?= Loc::getMessage("MENU_NEW_IN") ?></span>
				</a>

				<div class="bx-nav-2-lvl-container">
					<div class="wrapper">
						<ul class="bx-nav-list-2-lvl">
							<? foreach ($arResult["CATALOG_MENU"] as $itemID => $arItem) :
								if ($arItem['DEPTH_LEVEL'] > 1) continue; ?>

								<? if ($arItem["LINK"] == '/catalog/dzhinsy/') { ?>
									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Одежда</p>
									<? } ?>

									<li class="bx-nav-2-lvl">
										<a href="<?= $arItem["LINK"] ?>">
											<span class="hover<? if ($arItem["SELECTED"]) : ?> active<? endif ?>"><?= $arItem["TEXT"] ?></span>
										</a>
									</li>

									<? if ($arItem["LINK"] == '/catalog/verkhnyaya-odezhda/') { ?>
									</div>

									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Аксессуары</p>
									<? } ?>

									<? if ($arItem["LINK"] == '/catalog/odezhda-dlya-kosmetologov/') { ?>
									</div>
									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Аксессуары 2</p>
									<? } ?>

								<? endforeach; ?>
									</div>
						</ul>

						<div class="spotlight-on">
							<?
							$id_iblock = 2; // ID инфоблока
							$id_section = 20; // ID раздела

							$sql_section = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id_iblock, 'ID' => $id_section, 'ACTIVE' => 'Y', 'DEPTH_LEVEL' => 1));
							while ($result_section = $sql_section->GetNext()) {
								$res['IMG_SRC'] = CFile::GetPath($result_section['PICTURE']);
								$res['NAME'] = $result_section['NAME'];
								$res['LINK'] = $result_section['SECTION_PAGE_URL'];
							}
							?>

							<? if (!empty($res['IMG_SRC'])) { ?>
								<a href="<?= $res['LINK'] ?>" class="spotlight-on__img">
									<img src="<?= $res['IMG_SRC'] ?>" alt="spotlight img">
								</a>
							<? } ?>
							<div class="spotlight-on__desc">
								<p class="spotlight-on__text"><?= Loc::getMessage("MENU_SPOTIGHT_TEXT") ?></p>
								<a class="spotlight-on__link hover" href="<?= $res['LINK'] ?>"><?= $res['NAME'] ?></a>
								<a href="<?= $res['LINK'] ?>" class="spotlight-on__btn hover">
									<span><?= Loc::getMessage("MENU_SPOTIGHT_BTN") ?></span>
								</a>

							</div>


						</div>
					</div>
				</div>
			</li>
			<li class="bx-nav-1-lvl bx-nav-list-1-col <? if ($arItem["SELECTED"]) : ?>bx-active<? endif ?> bx-nav-parent" onmouseover="BX.CatalogMenu.itemOver(this);" onmouseout="BX.CatalogMenu.itemOut(this)" data-role="bx-menu-item" onclick="if (BX.hasClass(document.documentElement, 'bx-touch')) obj_<?= $menuBlockId ?>.clickInMobile(this, event);">
				<a href="<? if (Loc::getCurrentLang() == 'en') : ?>/en<? endif ?>/catalog/new/">
					<span>Clothing</span>
				</a>

				<div class="bx-nav-2-lvl-container">
					<div class="wrapper">
						<ul class="bx-nav-list-2-lvl">
							<? foreach ($arResult["CATALOG_MENU"] as $itemID => $arItem) :
								if ($arItem['DEPTH_LEVEL'] > 1) continue; ?>

								<? if ($arItem["LINK"] == '/catalog/dzhinsy/') { ?>
									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Одежда</p>
									<? } ?>

									<li class="bx-nav-2-lvl">
										<a href="<?= $arItem["LINK"] ?>">
											<span class="hover<? if ($arItem["SELECTED"]) : ?> active<? endif ?>"><?= $arItem["TEXT"] ?></span>
										</a>
									</li>

									<? if ($arItem["LINK"] == '/catalog/verkhnyaya-odezhda/') { ?>
									</div>

									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Аксессуары</p>
									<? } ?>

									<? if ($arItem["LINK"] == '/catalog/odezhda-dlya-kosmetologov/') { ?>
									</div>
									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Аксессуары 2</p>
									<? } ?>

								<? endforeach; ?>
									</div>
						</ul>

						<div class="spotlight-on">
							<?
							$id_iblock = 2; // ID инфоблока
							$id_section = 20; // ID раздела

							$sql_section = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id_iblock, 'ID' => $id_section, 'ACTIVE' => 'Y', 'DEPTH_LEVEL' => 1));
							while ($result_section = $sql_section->GetNext()) {
								$res['IMG_SRC'] = CFile::GetPath($result_section['PICTURE']);
								$res['NAME'] = $result_section['NAME'];
								$res['LINK'] = $result_section['SECTION_PAGE_URL'];
							}
							?>

							<? if (!empty($res['IMG_SRC'])) { ?>
								<a href="<?= $res['LINK'] ?>" class="spotlight-on__img">
									<img src="<?= $res['IMG_SRC'] ?>" alt="spotlight img">
								</a>
							<? } ?>
							<div class="spotlight-on__desc">
								<p class="spotlight-on__text"><?= Loc::getMessage("MENU_SPOTIGHT_TEXT") ?></p>
								<a class="spotlight-on__link hover" href="<?= $res['LINK'] ?>"><?= $res['NAME'] ?></a>
								<a href="<?= $res['LINK'] ?>" class="spotlight-on__btn hover">
									<span><?= Loc::getMessage("MENU_SPOTIGHT_BTN") ?></span>
								</a>

							</div>


						</div>
					</div>
				</div>
			</li>
			<li class="bx-nav-1-lvl bx-nav-list-1-col <? if ($arItem["SELECTED"]) : ?>bx-active<? endif ?> bx-nav-parent" onmouseover="BX.CatalogMenu.itemOver(this);" onmouseout="BX.CatalogMenu.itemOut(this)" data-role="bx-menu-item" onclick="if (BX.hasClass(document.documentElement, 'bx-touch')) obj_<?= $menuBlockId ?>.clickInMobile(this, event);">
				<a href="<? if (Loc::getCurrentLang() == 'en') : ?>/en<? endif ?>/catalog/new/">
					<span>Accesories</span>
				</a>

				<div class="bx-nav-2-lvl-container">
					<div class="wrapper">
						<ul class="bx-nav-list-2-lvl">
							<? foreach ($arResult["CATALOG_MENU"] as $itemID => $arItem) :
								if ($arItem['DEPTH_LEVEL'] > 1) continue; ?>

								<? if ($arItem["LINK"] == '/catalog/dzhinsy/') { ?>
									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Одежда</p>
									<? } ?>

									<li class="bx-nav-2-lvl">
										<a href="<?= $arItem["LINK"] ?>">
											<span class="hover<? if ($arItem["SELECTED"]) : ?> active<? endif ?>"><?= $arItem["TEXT"] ?></span>
										</a>
									</li>

									<? if ($arItem["LINK"] == '/catalog/verkhnyaya-odezhda/') { ?>
									</div>

									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Аксессуары</p>
									<? } ?>

									<? if ($arItem["LINK"] == '/catalog/odezhda-dlya-kosmetologov/') { ?>
									</div>
									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Аксессуары 2</p>
									<? } ?>

								<? endforeach; ?>
									</div>
						</ul>

						<div class="spotlight-on">
							<?
							$id_iblock = 2; // ID инфоблока
							$id_section = 20; // ID раздела

							$sql_section = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id_iblock, 'ID' => $id_section, 'ACTIVE' => 'Y', 'DEPTH_LEVEL' => 1));
							while ($result_section = $sql_section->GetNext()) {
								$res['IMG_SRC'] = CFile::GetPath($result_section['PICTURE']);
								$res['NAME'] = $result_section['NAME'];
								$res['LINK'] = $result_section['SECTION_PAGE_URL'];
							}
							?>

							<? if (!empty($res['IMG_SRC'])) { ?>
								<a href="<?= $res['LINK'] ?>" class="spotlight-on__img">
									<img src="<?= $res['IMG_SRC'] ?>" alt="spotlight img">
								</a>
							<? } ?>
							<div class="spotlight-on__desc">
								<p class="spotlight-on__text"><?= Loc::getMessage("MENU_SPOTIGHT_TEXT") ?></p>
								<a class="spotlight-on__link hover" href="<?= $res['LINK'] ?>"><?= $res['NAME'] ?></a>
								<a href="<?= $res['LINK'] ?>" class="spotlight-on__btn hover">
									<span><?= Loc::getMessage("MENU_SPOTIGHT_BTN") ?></span>
								</a>

							</div>


						</div>
					</div>
				</div>
			</li>
			<li class="bx-nav-1-lvl bx-nav-list-1-col <? if ($arItem["SELECTED"]) : ?>bx-active<? endif ?> bx-nav-parent" onmouseover="BX.CatalogMenu.itemOver(this);" onmouseout="BX.CatalogMenu.itemOut(this)" data-role="bx-menu-item" onclick="if (BX.hasClass(document.documentElement, 'bx-touch')) obj_<?= $menuBlockId ?>.clickInMobile(this, event);">
				<a href="<? if (Loc::getCurrentLang() == 'en') : ?>/en<? endif ?>/catalog/new/">
					<span>Shoes</span>
				</a>

				<div class="bx-nav-2-lvl-container">
					<div class="wrapper">
						<ul class="bx-nav-list-2-lvl">
							<? foreach ($arResult["CATALOG_MENU"] as $itemID => $arItem) :
								if ($arItem['DEPTH_LEVEL'] > 1) continue; ?>

								<? if ($arItem["LINK"] == '/catalog/dzhinsy/') { ?>
									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Одежда</p>
									<? } ?>

									<li class="bx-nav-2-lvl">
										<a href="<?= $arItem["LINK"] ?>">
											<span class="hover<? if ($arItem["SELECTED"]) : ?> active<? endif ?>"><?= $arItem["TEXT"] ?></span>
										</a>
									</li>

									<? if ($arItem["LINK"] == '/catalog/verkhnyaya-odezhda/') { ?>
									</div>

									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Аксессуары</p>
									<? } ?>

									<? if ($arItem["LINK"] == '/catalog/odezhda-dlya-kosmetologov/') { ?>
									</div>
									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Аксессуары 2</p>
									<? } ?>

								<? endforeach; ?>
									</div>
						</ul>

						<div class="spotlight-on">
							<?
							$id_iblock = 2; // ID инфоблока
							$id_section = 20; // ID раздела

							$sql_section = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id_iblock, 'ID' => $id_section, 'ACTIVE' => 'Y', 'DEPTH_LEVEL' => 1));
							while ($result_section = $sql_section->GetNext()) {
								$res['IMG_SRC'] = CFile::GetPath($result_section['PICTURE']);
								$res['NAME'] = $result_section['NAME'];
								$res['LINK'] = $result_section['SECTION_PAGE_URL'];
							}
							?>

							<? if (!empty($res['IMG_SRC'])) { ?>
								<a href="<?= $res['LINK'] ?>" class="spotlight-on__img">
									<img src="<?= $res['IMG_SRC'] ?>" alt="spotlight img">
								</a>
							<? } ?>
							<div class="spotlight-on__desc">
								<p class="spotlight-on__text"><?= Loc::getMessage("MENU_SPOTIGHT_TEXT") ?></p>
								<a class="spotlight-on__link hover" href="<?= $res['LINK'] ?>"><?= $res['NAME'] ?></a>
								<a href="<?= $res['LINK'] ?>" class="spotlight-on__btn hover">
									<span><?= Loc::getMessage("MENU_SPOTIGHT_BTN") ?></span>
								</a>

							</div>


						</div>
					</div>
				</div>
			</li>
			<li class="bx-nav-1-lvl bx-nav-list-1-col <? if ($arItem["SELECTED"]) : ?>bx-active<? endif ?> bx-nav-parent" onmouseover="BX.CatalogMenu.itemOver(this);" onmouseout="BX.CatalogMenu.itemOut(this)" data-role="bx-menu-item" onclick="if (BX.hasClass(document.documentElement, 'bx-touch')) obj_<?= $menuBlockId ?>.clickInMobile(this, event);">
				<a href="<? if (Loc::getCurrentLang() == 'en') : ?>/en<? endif ?>/catalog/new/">
					<span>Bags</span>
				</a>

				<div class="bx-nav-2-lvl-container">
					<div class="wrapper">
						<ul class="bx-nav-list-2-lvl">
							<? foreach ($arResult["CATALOG_MENU"] as $itemID => $arItem) :
								if ($arItem['DEPTH_LEVEL'] > 1) continue; ?>

								<? if ($arItem["LINK"] == '/catalog/dzhinsy/') { ?>
									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Одежда</p>
									<? } ?>

									<li class="bx-nav-2-lvl">
										<a href="<?= $arItem["LINK"] ?>">
											<span class="hover<? if ($arItem["SELECTED"]) : ?> active<? endif ?>"><?= $arItem["TEXT"] ?></span>
										</a>
									</li>

									<? if ($arItem["LINK"] == '/catalog/verkhnyaya-odezhda/') { ?>
									</div>

									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Аксессуары</p>
									<? } ?>

									<? if ($arItem["LINK"] == '/catalog/odezhda-dlya-kosmetologov/') { ?>
									</div>
									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Аксессуары 2</p>
									<? } ?>

								<? endforeach; ?>
									</div>
						</ul>

						<div class="spotlight-on">
							<?
							$id_iblock = 2; // ID инфоблока
							$id_section = 20; // ID раздела

							$sql_section = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id_iblock, 'ID' => $id_section, 'ACTIVE' => 'Y', 'DEPTH_LEVEL' => 1));
							while ($result_section = $sql_section->GetNext()) {
								$res['IMG_SRC'] = CFile::GetPath($result_section['PICTURE']);
								$res['NAME'] = $result_section['NAME'];
								$res['LINK'] = $result_section['SECTION_PAGE_URL'];
							}
							?>

							<? if (!empty($res['IMG_SRC'])) { ?>
								<a href="<?= $res['LINK'] ?>" class="spotlight-on__img">
									<img src="<?= $res['IMG_SRC'] ?>" alt="spotlight img">
								</a>
							<? } ?>
							<div class="spotlight-on__desc">
								<p class="spotlight-on__text"><?= Loc::getMessage("MENU_SPOTIGHT_TEXT") ?></p>
								<a class="spotlight-on__link hover" href="<?= $res['LINK'] ?>"><?= $res['NAME'] ?></a>
								<a href="<?= $res['LINK'] ?>" class="spotlight-on__btn hover">
									<span><?= Loc::getMessage("MENU_SPOTIGHT_BTN") ?></span>
								</a>

							</div>


						</div>
					</div>
				</div>
			</li>
			<li class="bx-nav-1-lvl bx-nav-list-1-col <? if ($arItem["SELECTED"]) : ?>bx-active<? endif ?> bx-nav-parent" onmouseover="BX.CatalogMenu.itemOver(this);" onmouseout="BX.CatalogMenu.itemOut(this)" data-role="bx-menu-item" onclick="if (BX.hasClass(document.documentElement, 'bx-touch')) obj_<?= $menuBlockId ?>.clickInMobile(this, event);">
				<a href="<? if (Loc::getCurrentLang() == 'en') : ?>/en<? endif ?>/catalog/new/">
					<span>Jewellery</span>
				</a>

				<div class="bx-nav-2-lvl-container">
					<div class="wrapper">
						<ul class="bx-nav-list-2-lvl">
							<? foreach ($arResult["CATALOG_MENU"] as $itemID => $arItem) :
								if ($arItem['DEPTH_LEVEL'] > 1) continue; ?>

								<? if ($arItem["LINK"] == '/catalog/dzhinsy/') { ?>
									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Одежда</p>
									<? } ?>

									<li class="bx-nav-2-lvl">
										<a href="<?= $arItem["LINK"] ?>">
											<span class="hover<? if ($arItem["SELECTED"]) : ?> active<? endif ?>"><?= $arItem["TEXT"] ?></span>
										</a>
									</li>

									<? if ($arItem["LINK"] == '/catalog/verkhnyaya-odezhda/') { ?>
									</div>

									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Аксессуары</p>
									<? } ?>

									<? if ($arItem["LINK"] == '/catalog/odezhda-dlya-kosmetologov/') { ?>
									</div>
									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Аксессуары 2</p>
									<? } ?>

								<? endforeach; ?>
									</div>
						</ul>

						<div class="spotlight-on">
							<?
							$id_iblock = 2; // ID инфоблока
							$id_section = 20; // ID раздела

							$sql_section = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id_iblock, 'ID' => $id_section, 'ACTIVE' => 'Y', 'DEPTH_LEVEL' => 1));
							while ($result_section = $sql_section->GetNext()) {
								$res['IMG_SRC'] = CFile::GetPath($result_section['PICTURE']);
								$res['NAME'] = $result_section['NAME'];
								$res['LINK'] = $result_section['SECTION_PAGE_URL'];
							}
							?>

							<? if (!empty($res['IMG_SRC'])) { ?>
								<a href="<?= $res['LINK'] ?>" class="spotlight-on__img">
									<img src="<?= $res['IMG_SRC'] ?>" alt="spotlight img">
								</a>
							<? } ?>
							<div class="spotlight-on__desc">
								<p class="spotlight-on__text"><?= Loc::getMessage("MENU_SPOTIGHT_TEXT") ?></p>
								<a class="spotlight-on__link hover" href="<?= $res['LINK'] ?>"><?= $res['NAME'] ?></a>
								<a href="<?= $res['LINK'] ?>" class="spotlight-on__btn hover">
									<span><?= Loc::getMessage("MENU_SPOTIGHT_BTN") ?></span>
								</a>

							</div>


						</div>
					</div>
				</div>
			</li>
			<li class="bx-nav-1-lvl bx-nav-list-1-col <? if ($arItem["SELECTED"]) : ?>bx-active<? endif ?> bx-nav-parent" onmouseover="BX.CatalogMenu.itemOver(this);" onmouseout="BX.CatalogMenu.itemOut(this)" data-role="bx-menu-item" onclick="if (BX.hasClass(document.documentElement, 'bx-touch')) obj_<?= $menuBlockId ?>.clickInMobile(this, event);">
				<a href="<? if (Loc::getCurrentLang() == 'en') : ?>/en<? endif ?>/catalog/new/">
					<span>Sale</span>
				</a>

				<div class="bx-nav-2-lvl-container">
					<div class="wrapper">
						<ul class="bx-nav-list-2-lvl">
							<? foreach ($arResult["CATALOG_MENU"] as $itemID => $arItem) :
								if ($arItem['DEPTH_LEVEL'] > 1) continue; ?>

								<? if ($arItem["LINK"] == '/catalog/dzhinsy/') { ?>
									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Одежда</p>
									<? } ?>

									<li class="bx-nav-2-lvl">
										<a href="<?= $arItem["LINK"] ?>">
											<span class="hover<? if ($arItem["SELECTED"]) : ?> active<? endif ?>"><?= $arItem["TEXT"] ?></span>
										</a>
									</li>

									<? if ($arItem["LINK"] == '/catalog/verkhnyaya-odezhda/') { ?>
									</div>

									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Аксессуары</p>
									<? } ?>

									<? if ($arItem["LINK"] == '/catalog/odezhda-dlya-kosmetologov/') { ?>
									</div>
									<div class="bx-nav-list-item">
										<p class="bx-nav-list-title">Аксессуары 2</p>
									<? } ?>

								<? endforeach; ?>
									</div>
						</ul>

						<div class="spotlight-on">
							<?
							$id_iblock = 2; // ID инфоблока
							$id_section = 20; // ID раздела

							$sql_section = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id_iblock, 'ID' => $id_section, 'ACTIVE' => 'Y', 'DEPTH_LEVEL' => 1));
							while ($result_section = $sql_section->GetNext()) {
								$res['IMG_SRC'] = CFile::GetPath($result_section['PICTURE']);
								$res['NAME'] = $result_section['NAME'];
								$res['LINK'] = $result_section['SECTION_PAGE_URL'];
							}
							?>

							<? if (!empty($res['IMG_SRC'])) { ?>
								<a href="<?= $res['LINK'] ?>" class="spotlight-on__img">
									<img src="<?= $res['IMG_SRC'] ?>" alt="spotlight img">
								</a>
							<? } ?>
							<div class="spotlight-on__desc">
								<p class="spotlight-on__text"><?= Loc::getMessage("MENU_SPOTIGHT_TEXT") ?></p>
								<a class="spotlight-on__link hover" href="<?= $res['LINK'] ?>"><?= $res['NAME'] ?></a>
								<a href="<?= $res['LINK'] ?>" class="spotlight-on__btn hover">
									<span><?= Loc::getMessage("MENU_SPOTIGHT_BTN") ?></span>
								</a>

							</div>


						</div>
					</div>
				</div>
			</li>

			<?
			$activeFlag = false;
			if (strpos($APPLICATION->GetCurDir(), 'company/') || strpos($APPLICATION->GetCurDir(), 'en/company/'))
				$activeFlag = true;
			?>
			<li class="bx-nav-1-lvl bx-nav-list-1-col <? if ($activeFlag) : ?>active<? endif ?> bx-nav-parent" onmouseover="BX.CatalogMenu.itemOver(this);" onmouseout="BX.CatalogMenu.itemOut(this)" data-role="bx-menu-item" onclick="if (BX.hasClass(document.documentElement, 'bx-touch')) obj_<?= $menuBlockId ?>.clickInMobile(this, event);">

				<a href="<? if (Loc::getCurrentLang() == 'en') : ?>/en<? endif ?>/company/">
					<span><?= Loc::getMessage("MENU_INFO") ?></span>
				</a>

				<div class="bx-nav-2-lvl-container">
					<div class="wrapper">
						<ul class="bx-nav-list-2-lvl">
							<div class="bx-nav-list-item">
								<p class="bx-nav-list-title">О нас</p>
								<? foreach ($arResult["INFO_MENU"] as $itemID => $arItem) :
									if ($arItem['DEPTH_LEVEL'] > 1) continue; ?>

									<li class="bx-nav-2-lvl">
										<a href="<?= $arItem["LINK"] ?>">
											<span class="hover<? if ($arItem["SELECTED"]) : ?> active<? endif ?>"><?= $arItem["TEXT"] ?></span>
										</a>
									</li>

								<? endforeach; ?>
							</div>
						</ul>
					</div>
				</div>
			</li>
		</ul>

	</nav>
</div>


<script>
	BX.ready(function() {
		window.obj_<?= $menuBlockId ?> = new BX.Main.MenuComponent.CatalogHorizontal('<?= CUtil::JSEscape($menuBlockId) ?>', <?= CUtil::PhpToJSObject($arResult["ITEMS_IMG_DESC"]) ?>);
	});
</script>