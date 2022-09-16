<?php
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }
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

    if (empty($arResult["ALL_ITEMS"])) {
        return;
    }

    CUtil::InitJSCore();

    $menuBlockId = "catalog_menu_" . $this->randString();
?>

<div class="main-menu bx-top-nav bx-<?= $arParams["MENU_THEME"] ?>" id="<?= $menuBlockId ?>">
	<nav class="bx-top-nav-container" id="cont_<?= $menuBlockId ?>">
		<ul class="bx-nav-list-1-lvl js-header-menu" id="ul_<?= $menuBlockId ?>">
			<?php
            $activeFlag = false;
            if (strpos($APPLICATION->GetCurDir(), 'catalog/new/') || strpos($APPLICATION->GetCurDir(), 'en/catalog/new/')) {
                $activeFlag = true;
            }
            ?>
			<li class="bx-nav-1-lvl bx-nav-list-1-col <?=$activeFlag ? 'active' :  ''?>" data-role="bx-menu-item" onclick="if (BX.hasClass(document.documentElement, 'bx-touch')) obj_<?= $menuBlockId ?>.clickInMobile(this, event);">
				<a href="<?=Loc::getCurrentLang() == 'en' ? '/en' : '' ?>/catalog/new/">
					<span><?= Loc::getMessage("MENU_NEW_IN") ?></span>
				</a>
			</li>
			<?php
            $activeFlag = false;
            if (strpos($APPLICATION->GetCurDir(), 'verkhnyaya-odezhda/') || strpos($APPLICATION->GetCurDir(), 'en/verkhnyaya-odezhda/')) {
                $activeFlag = true;
            }
            ?>
			<li class="bx-nav-1-lvl bx-nav-list-1-col <?=$activeFlag ? 'active' :  ''?> bx-nav-parent" data-role="bx-menu-item" onclick="if (BX.hasClass(document.documentElement, 'bx-touch')) obj_<?= $menuBlockId ?>.clickInMobile(this, event);">
				<a href="<?=Loc::getCurrentLang() == 'en' ? '/en' : '' ?>/catalog/verkhnyaya-odezhda/">
					<span><?= Loc::getMessage("MENU_CLOTHES") ?></span>
				</a>
				<div class="bx-nav-2-lvl-container">
					<div class="wrapper">
						<ul class="bx-nav-list-2-lvl">
							<div class="bx-nav-list-item">
							<?php
                                foreach ($arResult["CATALOG_MENU"] as $itemID => $arItem) {
                                    if ($arItem['DEPTH_LEVEL'] > 1) {
                                        continue;
                                    }

                                    switch ($arItem["LINK"]) {
                                        case '/catalog/dzhinsy/':
                                        case '/en/catalog/dzhinsy/':
                                            ?>
											<p class="bx-nav-list-title"><?= Loc::getMessage("MENU_CLOTHES") ?></p>
											<?php
                                            break;
                                        case '/catalog/verkhnyaya-odezhda/':
                                        case '/en/catalog/verkhnyaya-odezhda/':
                                            ?>
											</div>
											<div class="bx-nav-list-item">
												<p class="bx-nav-list-title"><?= Loc::getMessage("MENU_OTHER") ?></p>
											<?php
                                            break;
                                        case '/catalog/odezhda-dlya-kosmetologov/':
                                        case '/en/catalog/odezhda-dlya-kosmetologov/':
                                            ?>
											</div>
											<div class="bx-nav-list-item">
												<p class="bx-nav-list-title"><?= Loc::getMessage("MENU_ACCESSORIES") ?></p>
											<?php
                                            break;
                                    } ?>
									<li class="bx-nav-2-lvl">
										<a href="<?= $arItem["LINK"] ?>">
											<span class="hover <?=$arItem["SELECTED"] ? 'active' : '' ?>"><?= $arItem["TEXT"] ?></span>
										</a>
									</li>
									<?php
                                }
                            ?>
							</div>
						</ul>

						<div class="spotlight-on">
							<?php
                            $id_iblock = 2; // ID инфоблока
                            $id_section = 20; // ID раздела

                            $sql_section = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id_iblock, 'ID' => $id_section, 'ACTIVE' => 'Y', 'DEPTH_LEVEL' => 1), false, ['NAME', 'UF_NAME_EN', 'PICTURE', 'SECTION_PAGE_URL']);
                            while ($result_section = $sql_section->GetNext()) {
                                $res['IMG_SRC'] = CFile::GetPath($result_section['PICTURE']);
                                $res['NAME'] = Loc::getCurrentLang() === 'en' ? $result_section['UF_NAME_EN'] : $result_section['NAME'];
                                $res['LINK'] = Loc::getCurrentLang() === 'en' ? SITE_DIR.$result_section['SECTION_PAGE_URL'] : $result_section['SECTION_PAGE_URL'];
                            }
                            ?>

							<?php if (!empty($res['IMG_SRC'])) { ?>
								<a href="<?= $res['LINK'] ?>" class="spotlight-on__img">
									<img src="<?= $res['IMG_SRC'] ?>" alt="spotlight img">
								</a>
							<?php } ?>
							<div class="spotlight-on__desc">
								<p class="spotlight-on__text"><?= Loc::getMessage("MENU_SPOTIGHT_TEXT") ?></p>
								<a class="spotlight-on__link hover" href="<?= $res['LINK'] ?>"><?= $res['NAME'] ?></a>
								<a href="<?= $res['LINK'] ?>" class="ibutton big hollow">
									<span><?= Loc::getMessage("MENU_SPOTIGHT_BTN") ?></span>
								</a>
							</div>
						</div>
					</div>
				</div>
			</li>

			<?php
            $activeFlag = false;
            if (strpos($APPLICATION->GetCurDir(), 'aksessuary/') || strpos($APPLICATION->GetCurDir(), 'en/aksessuary/')) {
                $activeFlag = true;
            }
            ?>
			<li class="bx-nav-1-lvl bx-nav-list-1-col <?=$activeFlag ? 'active' :  '' ?> bx-nav-parent" data-role="bx-menu-item" onclick="if (BX.hasClass(document.documentElement, 'bx-touch')) obj_<?= $menuBlockId ?>.clickInMobile(this, event);">
				<a href="<?=Loc::getCurrentLang() == 'en' ? '/en' : '' ?>/catalog/aksessuary/">
					<span><?= Loc::getMessage("MENU_ACCESSORIES") ?></span>
				</a>

				<div class="bx-nav-2-lvl-container">
					<div class="wrapper">
						<ul class="bx-nav-list-2-lvl">
							<div class="bx-nav-list-item">
								<?php
                                    foreach ($arResult["CATALOG_MENU"] as $itemID => $arItem) {
                                        if ($arItem['DEPTH_LEVEL'] > 1) {
                                            continue;
                                        }

                                        switch ($arItem["LINK"]) {
                                            case '/catalog/dzhinsy/':
                                            case '/en/catalog/dzhinsy/':
                                                ?>
												<p class="bx-nav-list-title"><?= Loc::getMessage("MENU_CLOTHES") ?></p>
												<?php
                                                break;
                                            case '/catalog/verkhnyaya-odezhda/':
                                            case '/en/catalog/verkhnyaya-odezhda/':
                                                ?>
												</div>
												<div class="bx-nav-list-item">
													<p class="bx-nav-list-title"><?= Loc::getMessage("MENU_OTHER") ?></p>
												<?php
                                                break;
                                            case '/catalog/odezhda-dlya-kosmetologov/':
                                            case '/en/catalog/odezhda-dlya-kosmetologov/':
                                                ?>
												</div>
												<div class="bx-nav-list-item">
													<p class="bx-nav-list-title"><?= Loc::getMessage("MENU_ACCESSORIES") ?></p>
												<?php
                                                break;
                                        } ?>
										<li class="bx-nav-2-lvl">
											<a href="<?= $arItem["LINK"] ?>">
												<span class="hover <?=$arItem["SELECTED"] ? 'active' : '' ?>"><?= $arItem["TEXT"] ?></span>
											</a>
										</li>
										<?php
                                    }
                                ?>
							</div>
						</ul>

						<div class="spotlight-on">
							<?php
                            $id_iblock = 2; // ID инфоблока
                            $id_section = 20; // ID раздела

                            $sql_section = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id_iblock, 'ID' => $id_section, 'ACTIVE' => 'Y', 'DEPTH_LEVEL' => 1), false, ['NAME', 'UF_NAME_EN', 'PICTURE', 'SECTION_PAGE_URL']);
                            while ($result_section = $sql_section->GetNext()) {
                                $res['IMG_SRC'] = CFile::GetPath($result_section['PICTURE']);
                                $res['NAME'] = Loc::getCurrentLang() === 'en' ? $result_section['UF_NAME_EN'] : $result_section['NAME'];
                                $res['LINK'] = Loc::getCurrentLang() === 'en' ? SITE_DIR.$result_section['SECTION_PAGE_URL'] : $result_section['SECTION_PAGE_URL'];
                            }
                            ?>

							<?php if (!empty($res['IMG_SRC'])) { ?>
								<a href="<?= $res['LINK'] ?>" class="spotlight-on__img">
									<img src="<?= $res['IMG_SRC'] ?>" alt="spotlight img">
								</a>
							<?php } ?>
							<div class="spotlight-on__desc">
								<p class="spotlight-on__text"><?= Loc::getMessage("MENU_SPOTIGHT_TEXT") ?></p>
								<a class="spotlight-on__link hover" href="<?= $res['LINK'] ?>"><?= $res['NAME'] ?></a>
								<a href="<?= $res['LINK'] ?>" class="ibutton big hollow">
									<span><?= Loc::getMessage("MENU_SPOTIGHT_BTN") ?></span>
								</a>

							</div>


						</div>
					</div>
				</div>
			</li>
			<li class="bx-nav-1-lvl bx-nav-list-1-col"  data-role="bx-menu-item" onclick="if (BX.hasClass(document.documentElement, 'bx-touch')) obj_<?= $menuBlockId ?>.clickInMobile(this, event);">
				<a href="<?=Loc::getCurrentLang() == 'en' ? '/en' : '' ?>/catalog/sale/">
					<span><?= Loc::getMessage("MENU_SALE") ?></span>
				</a>
			</li>

			<?php
            $activeFlag = false;
            if (strpos($APPLICATION->GetCurDir(), 'company/') || strpos($APPLICATION->GetCurDir(), 'en/company/')) {
                $activeFlag = true;
            }
            ?>
		</ul>

	</nav>
</div>
