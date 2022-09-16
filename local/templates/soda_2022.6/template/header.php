<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}
$APPLICATION->SetPageProperty('og:image', SITE_SERVER_PROTOCOL . SITE_SERVER_NAME . SITE_TEMPLATE_PATH . '/images/LogoForShare300.jpg');

use \Bitrix\Main\Localization\Loc;

$isActionBannerDisplay = false;
//if($APPLICATION->get_cookie('ACTION_BANNER_SECTION') != 'HIDE')
//{
//  $isActionBannerDisplay = true;
//
//  if((IS_CATALOG && !IS_PRODUCT) || IS_MAIN)
//    $isActionBannerDisplay = true;
//  else
//    $isActionBannerDisplay = false;
//
//  if(strpos($APPLICATION->GetCurDir(), 'catalog/sale'))
//    $isActionBannerDisplay = false;
//}
?>
<!DOCTYPE html>
<html lang="<?= Loc::getCurrentLang() ?>">

<head>
	<title><?= $APPLICATION->ShowTitle(); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="robots" content="index, follow" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<!-- meta name="author" content="arlix.ru" -->
	<meta name="yandex-verification" content="b80e557c3c39287e" />
	<meta name="google-site-verification" content="PM2lf4t-exq8qfKB79D80QgpU5NgIRp6ofVfWdQv6ck" />
	<meta name="facebook-domain-verification" content="37xyg7zo3t2xxyp6jp2yf8zg489xam" />

	<meta property="og:url" content="<?= SITE_SERVER_PROTOCOL . SITE_SERVER_NAME . $APPLICATION->GetCurPage() ?>">
	<meta property="og:type" content="website">
	<meta property="og:title" content="<?php $APPLICATION->ShowProperty("title") ?>">
	<meta property="og:description" content="<?= $APPLICATION->ShowProperty("description") ?>">
	<meta property="og:image" content="<?= $APPLICATION->ShowProperty("og:image") ?>">

	<link rel="apple-touch-icon-precomposed" sizes="57x57" href="<?= SITE_TEMPLATE_PATH ?>/images/favicon/apple-touch-icon-57x57.png" />
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?= SITE_TEMPLATE_PATH ?>/images/favicon/apple-touch-icon-114x114.png" />
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?= SITE_TEMPLATE_PATH ?>/images/favicon/apple-touch-icon-72x72.png" />
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?= SITE_TEMPLATE_PATH ?>/images/favicon/apple-touch-icon-144x144.png" />
	<link rel="apple-touch-icon-precomposed" sizes="60x60" href="<?= SITE_TEMPLATE_PATH ?>/images/favicon/apple-touch-icon-60x60.png" />
	<link rel="apple-touch-icon-precomposed" sizes="120x120" href="<?= SITE_TEMPLATE_PATH ?>/images/favicon/apple-touch-icon-120x120.png" />
	<link rel="apple-touch-icon-precomposed" sizes="76x76" href="<?= SITE_TEMPLATE_PATH ?>/images/favicon/apple-touch-icon-76x76.png" />
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?= SITE_TEMPLATE_PATH ?>/images/favicon/apple-touch-icon-152x152.png" />
	<link rel="icon" type="image/png" href="<?= SITE_TEMPLATE_PATH ?>/images/favicon/favicon-196x196.png" sizes="196x196" />
	<link rel="icon" type="image/png" href="<?= SITE_TEMPLATE_PATH ?>/images/favicon/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/png" href="<?= SITE_TEMPLATE_PATH ?>/images/favicon/favicon-32x32.png" sizes="32x32" />
	<link rel="icon" type="image/png" href="<?= SITE_TEMPLATE_PATH ?>/images/favicon/favicon-16x16.png" sizes="16x16" />
	<link rel="icon" type="image/png" href="<?= SITE_TEMPLATE_PATH ?>/images/favicon/favicon-128.png" sizes="128x128" />
	<meta name="application-name" content="<?= Loc::getMessage("HEADER_META_APPLICATION_NAME") ?>" />
	<meta name="msapplication-TileColor" content="#FFFFFF" />
	<meta name="msapplication-TileImage" content="<?= SITE_TEMPLATE_PATH ?>/images/favicon/mstile-144x144.png" />
	<meta name="msapplication-square70x70logo" content="<?= SITE_TEMPLATE_PATH ?>/images/favicon/mstile-70x70.png" />
	<meta name="msapplication-square150x150logo" content="<?= SITE_TEMPLATE_PATH ?>/images/favicon/mstile-150x150.png" />
	<meta name="msapplication-wide310x150logo" content="<?= SITE_TEMPLATE_PATH ?>/images/favicon/mstile-310x150.png" />
	<meta name="msapplication-square310x310logo" content="<?= SITE_TEMPLATE_PATH ?>/images/favicon/mstile-310x310.png" />


	<?php $APPLICATION->SetAdditionalCss(SITE_TEMPLATE_PATH . "/css/reset.css"); ?>
	<?php $APPLICATION->SetAdditionalCss(SITE_TEMPLATE_PATH . "/css/fonts.css"); ?>
	<?php
	if (IS_MAIN) {
		$APPLICATION->SetAdditionalCss(SITE_TEMPLATE_PATH . "/css/main-page.css");
	}
	?>
	<?php $APPLICATION->SetAdditionalCss(SITE_TEMPLATE_PATH . "/css/owl.carousel.css"); ?>
	<?php $APPLICATION->SetAdditionalCss(SITE_TEMPLATE_PATH . "/css/tiny-slider.css"); ?>
	<?php $APPLICATION->SetAdditionalCss(SITE_TEMPLATE_PATH . "/fancybox/jquery.fancybox.css"); ?>
	<?php $APPLICATION->SetAdditionalCss(SITE_TEMPLATE_PATH . "/css/jquery-ui.structure.css"); ?>
	<?php $APPLICATION->SetAdditionalCss(SITE_TEMPLATE_PATH . "/css/jquery-ui.css"); ?>
	<?php $APPLICATION->SetAdditionalCss("https://unpkg.com/swiper@8/swiper-bundle.min.css"); ?>

	<?php $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/jquery-3.3.1.min.js');
	?>

	<?php $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/jquery-ui.min.js") ?>
	<?php $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/jquery.validate/jquery.validate.js") ?>

	<?php $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/inputmask-multi/jquery.inputmask.bundle.min.js") ?>
	<?php $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/inputmask-multi/jquery.inputmask-multi.js") ?>
	<?php $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/owl.carousel.min.js") ?>
	<?php $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/tiny-slider.js") ?>
	<?php $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/fancybox/jquery.fancybox.min.js") ?>
	<?php $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/disable-body-scroll.js") ?>
	<?php $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/jquery.sticky-kit.js") ?>
	<?php $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/jquery.cookie.js") ?>
	<?php $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/scripts.js") ?>
	<?php $APPLICATION->AddHeadScript('https://unpkg.com/swiper@8/swiper-bundle.min.js') ?>

	<?php $APPLICATION->ShowHead(); ?>
	<script>
		window.catalogItems = {};
	</script>
</head>

<body class="<?= getBodyClasses() ?><?php if ($USER->IsAdmin()) : ?> is-admin<?php endif ?><?php if ($isActionBannerDisplay) : ?> action-banner-visible<?php endif ?>" data-mobile="<?= IS_MOBILE; ?>" data-tablet="<?= IS_TABLET; ?>">

	<?php if (!IS_PRODUCT) : ?>
		<?php $APPLICATION->ShowPanel(); ?>
	<?php endif ?>
	<?php if (preg_match('/(manager|personal|partner)/', $APPLICATION->GetCurDir())) : ?>
		<div id="content-loading">
			<!--			<img src="<?= SITE_TEMPLATE_PATH ?>/images/loading.svg">-->
			<div class="preloader__loader">
				<svg viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
					<circle class="preloader__base" cx="25" cy="25" r="20"></circle>
					<circle class="preloader__indicator" cx="25" cy="25" r="20"></circle>
				</svg>
			</div>
		</div>
	<?php endif ?>

	<div class="indent-header-fixed"></div>

	<header class="header">
		<div class="wrapper">
			<div class="header-top">

				<div class="header-icon-mobile">
					<div class="hamburger hamburger--squeeze hover js-mobile-menu">
						<div class="hamburger-box">
							<div class="hamburger-inner"></div>
						</div>
						<?php if (IS_ASIDE_HOVER) : ?>
							<div class="hover-area"></div>
						<?php endif ?>
					</div>
					<div class="search-mobile hover js-mobile-open-search">
						<img src="<?= SITE_TEMPLATE_PATH ?>/images/new-header/header-search.svg" alt="">
					</div>
				</div>

				<div class="header-catalog">
					<a href="<?= SITE_DIR ?>catalog/new/" class="header-catalog__link link-women hover"><?= Loc::getMessage('HEADER_MENU_NEW'); ?></a>
				</div>
				<a href="<?= SITE_DIR ?>" class="header-logo">
					<img class="hover" src="<?= SITE_TEMPLATE_PATH ?>/images/new-header-logo.svg" alt="Logo SODAMODA" />
				</a>
				<div class="header-info">
					<div class="header-info__settings">
						<?php // chouse currency & lang
						?>
						<?php Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("lang"); ?>
						<?php
						$page = $APPLICATION->GetCurPageParam("language=EN", array("language"));
						$linken = $_SERVER['HTTP_HOST'] . $page;
						$page2 = str_replace('/en', '', $linken);
						$page2 = str_replace('language=EN', 'language=RU', $page2);

						$dataLangCurrency = App\Locale::getLangAndCurrency();
						global $USER;
						?>

						<div class="info-settings__item info-settings__currency item js-langs-display">
							<div class="item hover"><?= strtoupper($dataLangCurrency['lang']) ?><span class="inner-currency"> : <?= strtoupper($dataLangCurrency['currency']) ?></span></div>

							<div class="langs-window">
								<div class="change-langs">
									<div class="lots lots-lang">
										<div class="lot<?php if ($dataLangCurrency['lang'] == 'RU') : ?> active<?php endif ?>"><a href="<?= $APPLICATION->GetCurPageParam("language=RU"); ?>">Русский</a></div>
										<div class="lot<?php if ($dataLangCurrency['lang'] == 'EN') : ?> active<?php endif ?>"><a href="<?= $APPLICATION->GetCurPageParam("language=EN"); ?>">English</a></div>
									</div>
									<div class="lots lots-currency">
										<div class="lot<?php if ($dataLangCurrency['currency'] == 'rub') : ?> active<?php endif ?>"><a href="<?= $APPLICATION->GetCurPageParam("currency=rub"); ?>">RUB</a></div>
										<div class="lot<?php if ($dataLangCurrency['currency'] == 'usd') : ?> active<?php endif ?>"><a href="<?= $APPLICATION->GetCurPageParam("currency=usd"); ?>">USD</a></div>
										<div class="lot<?php if ($dataLangCurrency['currency'] == 'aed') : ?> active<?php endif ?>"><a href="<?= $APPLICATION->GetCurPageParam("currency=aed"); ?>">AED</a></div>
									</div>
								</div>
							</div>
						</div>
						<?php Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("lang", ""); ?>

						<div class="info-settings__item info-settings__chat item js-calling-display">
							<span class="item hover"><?= Loc::getMessage("HEADER_INFO_MENU_CHAT") ?></span>

							<div class="calling-window">
								<div class="list">
									<?php if (Loc::getCurrentLang() == 'ru') : ?>
										<div class="item-list"><a class="hover js-open-chat"><?= Loc::getMessage("HEADER_POPUP_CALLING_CHAT") ?></a></div>
									<?php endif ?>
									<div class="item-list">
										<a class="hover" href="whatsapp://send?phone=<?= PHONE_WHATSAPP ?>" target="_blank" title="<?= Loc::getMessage("HEADER_POPUP_CALLING_TITLE_WHATSAPP") ?>">
											<?= Loc::getMessage("HEADER_POPUP_CALLING_WHATSAPP") ?>
										</a>
									</div>
									<div class="item-list"><a class="hover" href="tg://resolve?domain=sodamoda_chat"><?= Loc::getMessage("HEADER_POPUP_CALLING_TELEGRAM") ?></a></div>
									<div class="item-list"><a class="hover" href="tel:<?= PHONE ?>"><?= Loc::getMessage("HEADER_POPUP_CALLING_CALL") ?></a></div>
								</div>
							</div>
						</div>
					</div>

					<div class="header-info__nav">
						<div class="info-nav__item info-nav__profile hover">
							<?php $APPLICATION->IncludeComponent(
								"bitrix:system.auth.form",
								"auth",
								array(
									"FORGOT_PASSWORD_URL" => SITE_DIR . "personal/index.php?forgotpassword=yes",
									"PROFILE_URL" => SITE_DIR . "personal/profile/",
									"REGISTER_URL" => SITE_DIR . "personal/profile/",
									"SHOW_ERRORS" => "N",
									"COMPONENT_TEMPLATE" => "new-auth",
									"COMPOSITE_FRAME_MODE" => "Y",
									"COMPOSITE_FRAME_TYPE" => "DYNAMIC_WITHOUT_STUB"
								),
								false
							); ?>
						</div>

						<div class="info-nav__item info-nav__chat-mobile hover js-mobile-open-call"><?= Loc::getMessage("HEADER_INFO_MENU_CHAT") ?></div>

						<?php $APPLICATION->IncludeComponent(
							'tokit:basic.favorites.count',
							'template',
							array(
								'COMPOSITE_FRAME_MODE' => 'A',
								'COMPOSITE_FRAME_TYPE' => 'AUTO'
							)
						); ?>

						<div id="basket-mini-wr" class="info-nav__item info-nav__basket basket js-basket-display">
							<?php $staticHTMLCache = \Bitrix\Main\Data\StaticHTMLCache::getInstance();
							$staticHTMLCache->disableVoting(); ?>
							<?php $APPLICATION->IncludeComponent(
								"bitrix:sale.basket.basket.line",
								"basket_line",
								array(
									"HIDE_ON_BASKET_PAGES" => "Y",
									"PATH_TO_AUTHORIZE" => "",
									"PATH_TO_BASKET" => SITE_DIR . "basket/",
									"PATH_TO_ORDER" => SITE_DIR . "order/",
									"PATH_TO_PERSONAL" => SITE_DIR . "personal/",
									"PATH_TO_PROFILE" => SITE_DIR . "personal/",
									"PATH_TO_REGISTER" => SITE_DIR . "login/",
									"POSITION_FIXED" => "N",
									"SHOW_AUTHOR" => "N",
									"SHOW_EMPTY_VALUES" => "Y",
									"SHOW_NUM_PRODUCTS" => "Y",
									"SHOW_PERSONAL_LINK" => "Y",
									"SHOW_PRODUCTS" => "Y",
									"SHOW_REGISTRATION" => "N",
									"SHOW_TOTAL_PRICE" => "Y",
									"COMPONENT_TEMPLATE" => "basket_line",
									"SHOW_DELAY" => "N",
									"SHOW_NOTAVAIL" => "N",
									"SHOW_IMAGE" => "Y",
									"SHOW_PRICE" => "Y",
									"SHOW_SUMMARY" => "Y",
									"MAX_IMAGE_SIZE" => "70",
									"COMPOSITE_FRAME_MODE" => "Y",
									"COMPOSITE_FRAME_TYPE" => "DYNAMIC_WITHOUT_STUB"
								),
								false
							); ?>
							<?php $staticHTMLCache->enableVoting(); ?>
						</div>
					</div>

				</div>
			</div>

			<div class="header-bot">
				<div class="header-menu">
					<?php $APPLICATION->IncludeComponent(
						"bitrix:menu",
						"header",
						array(
							"ALLOW_MULTI_SELECT" => "N",
							"CHILD_MENU_TYPE" => "catalog",
							"DELAY" => "N",
							"MAX_LEVEL" => "2",
							"MENU_CACHE_GET_VARS" => array(),
							"MENU_CACHE_TIME" => "3600",
							"MENU_CACHE_TYPE" => "N",
							"MENU_CACHE_USE_GROUPS" => "Y",
							"ROOT_MENU_TYPE" => "catalog",
							"USE_EXT" => "Y",
							"COMPONENT_TEMPLATE" => "header",
							"MENU_THEME" => "site"
						),
						false
					); ?>
				</div>
				<div class="header-search">
					<?php if ($APPLICATION->GetDirProperty("ORDER_MANAGER") == "Y") : ?>

						<?php if ($APPLICATION->GetCurDir() == '/manager/store/') : ?>

							<?php $APPLICATION->IncludeComponent("bitrix:search.title", "search", array(
								"CATEGORY_0" => array(	// Ограничение области поиска
									0 => "iblock_catalogs",
								),
								"CATEGORY_0_TITLE" => "",	// Название категории
								"CATEGORY_0_forum" => array(
									0 => "all",
								),
								"CATEGORY_0_iblock_catalogs" => array(	// Искать в информационных блоках типа "iblock_catalogs"
									0 => "2",
								),
								"CATEGORY_0_main" => "",
								"CHECK_DATES" => "N",	// Искать только в активных по дате документах
								"CONTAINER_ID" => "title-search",	// ID контейнера, по ширине которого будут выводиться результаты
								"INPUT_ID" => "title-search-input",	// ID строки ввода поискового запроса
								"NUM_CATEGORIES" => "1",	// Количество категорий поиска
								"ORDER" => "date",	// Сортировка результатов
								"PAGE" => SITE_DIR . "catalog/",	// Страница выдачи результатов поиска (доступен макрос #SITE_DIR#)
								"SHOW_INPUT" => "Y",	// Показывать форму ввода поискового запроса
								"SHOW_OTHERS" => "N",	// Показывать категорию "прочее"
								"TOP_COUNT" => "5",	// Количество результатов в каждой категории
								"USE_LANGUAGE_GUESS" => "N",	// Включить автоопределение раскладки клавиатуры
								"COMPONENT_TEMPLATE" => ".default"
							), false); ?>

						<?php else : ?>

							<?php $APPLICATION->IncludeComponent(
                "arlix:order.search", 
                "search.vue", 
                array(
                  "COMPONENT_TEMPLATE" => "search.vue",
                  "COMPOSITE_FRAME_MODE" => "A",
                  "COMPOSITE_FRAME_TYPE" => "AUTO"
                ),
                false
              ); ?>

						<?php endif ?>

					<?php else : ?>

						<?php $APPLICATION->IncludeComponent("bitrix:search.title", "search", array(
							"QUERY" => htmlspecialcharsbx($_REQUEST['q']),
							"CATEGORY_0" => array(	// Ограничение области поиска
								0 => "iblock_catalogs",
							),
							"CATEGORY_0_TITLE" => "",	// Название категории
							"CATEGORY_0_forum" => array(
								0 => "all",
							),
							"CATEGORY_0_iblock_catalogs" => array(	// Искать в информационных блоках типа "iblock_catalogs"
								0 => "2",
							),
							"CATEGORY_0_main" => "",
							"CHECK_DATES" => "N",	// Искать только в активных по дате документах
							"CONTAINER_ID" => "title-search",	// ID контейнера, по ширине которого будут выводиться результаты
							"INPUT_ID" => "title-search-input",	// ID строки ввода поискового запроса
							"NUM_CATEGORIES" => "1",	// Количество категорий поиска
							"ORDER" => "date",	// Сортировка результатов
							"PAGE" => SITE_DIR . "catalog/",	// Страница выдачи результатов поиска (доступен макрос #SITE_DIR#)
							"SHOW_INPUT" => "Y",	// Показывать форму ввода поискового запроса
							"SHOW_OTHERS" => "N",	// Показывать категорию "прочее"
							"TOP_COUNT" => "5",	// Количество результатов в каждой категории
							"USE_LANGUAGE_GUESS" => "N",	// Включить автоопределение раскладки клавиатуры
							"COMPONENT_TEMPLATE" => ".default"
						), false); ?>

					<?php endif ?>
				</div>
			</div>
		</div>
	</header>

	<?php $APPLICATION->ShowViewContent('filter'); ?>
	<div class="blur-wrap">

		<?php if (IS_MAIN) : ?>

			<?php $APPLICATION->IncludeComponent(
				"bitrix:news.list",
				"slider_mobile",
				array(
					"ACTIVE_DATE_FORMAT" => "d.m.Y",
					"ADD_SECTIONS_CHAIN" => "N",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_ADDITIONAL" => "",
					"AJAX_OPTION_HISTORY" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "N",
					"CACHE_FILTER" => "N",
					"CACHE_GROUPS" => "N",
					"CACHE_TIME" => "36000000",
					"CACHE_TYPE" => "A",
					"CHECK_DATES" => "Y",
					"DETAIL_URL" => "",
					"DISPLAY_BOTTOM_PAGER" => "N",
					"DISPLAY_DATE" => "N",
					"DISPLAY_NAME" => "N",
					"DISPLAY_PICTURE" => "N",
					"DISPLAY_PREVIEW_TEXT" => "N",
					"DISPLAY_TOP_PAGER" => "N",
					"FIELD_CODE" => array(
						0 => "DETAIL_PICTURE",
						1 => "",
					),
					"FILTER_NAME" => "",
					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
					"IBLOCK_ID" => (SITE_ID == 's2') ? HISTORY_EN_ID : HISTORY_ID,
					"IBLOCK_TYPE" => "content",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"INCLUDE_SUBSECTIONS" => "N",
					"MESSAGE_404" => "",
					"NEWS_COUNT" => "15",
					"PAGER_BASE_LINK_ENABLE" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
					"PAGER_SHOW_ALL" => "N",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_TEMPLATE" => ".default",
					"PAGER_TITLE" => Loc::getMessage("HEADER_META_APPLICATION_NAME"),
					"PARENT_SECTION" => "",
					"PARENT_SECTION_CODE" => "",
					"PREVIEW_TRUNCATE_LEN" => "",
					"PROPERTY_CODE" => array(
						0 => "LINK",
						1 => "LINK_SECTION",
					),
					"SET_BROWSER_TITLE" => "N",
					"SET_LAST_MODIFIED" => "N",
					"SET_META_DESCRIPTION" => "N",
					"SET_META_KEYWORDS" => "N",
					"SET_STATUS_404" => "N",
					"SET_TITLE" => "N",
					"SHOW_404" => "N",
					"SORT_BY1" => "SORT",
					"SORT_BY2" => "ID",
					"SORT_ORDER1" => "DESC",
					"SORT_ORDER2" => "DESC",
					"STRICT_SECTION_CHECK" => "N",
					"COMPONENT_TEMPLATE" => "slider_mobile"
				),
				false
			); ?>

		<?php endif ?>

		<?php
		$flagMainWorkarea = false;
		if ($APPLICATION->GetCurDir() == '/') {
			$flagMainWorkarea = true;
		} elseif ($APPLICATION->GetCurDir() == '/en/') {
			$flagMainWorkarea = true;
		}
		?>

		<div class="workarea page<?php if ($flagMainWorkarea) : ?> workarea-main<?php endif ?>">
			<div class="wrapper content">
				<article>
					<div data-content <?php if (IS_PRODUCT) : ?> id="products-wrap" <?php endif ?>>