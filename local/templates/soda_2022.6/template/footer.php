<?

use \Bitrix\Main\Localization\Loc;
?>
<? if ($APPLICATION->GetCurDir() == SITE_DIR . 'order/' || $APPLICATION->GetCurDir() == SITE_DIR . 'personal/create/') : ?>
	<div id="content-loading" class="preloader loading">
		<div class="preloader__loader">
			<svg viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
				<circle class="preloader__base" cx="25" cy="25" r="20">
				</circle>
				<circle class="preloader__indicator" cx="25" cy="25" r="20">
				</circle>
			</svg>
		</div>
	</div>
<? endif ?>
</div>


</article>
</div>
</div>

<footer>
	<div class="footer">
		<div class="footer-logo-block">
			<a href="<?= SITE_DIR ?>" class="footer-logo hover"><img src="<?= SITE_TEMPLATE_PATH ?>/images/footer-logo.svg" alt="Logo SODAMODA"></a>
		</div>
		<div class="footer-content-block">
			<div class="wrapper">
				<div class="footer-content">
					<div class="footer-column">
						<p class="footer-title footer-item"><?= Loc::getMessage('FOOTER_MENU_CLIENT_SERVICE'); ?></p>
						<? $APPLICATION->IncludeComponent(
							"bitrix:menu",
							"footer_menu",
							array(
								"ALLOW_MULTI_SELECT" => "N",
								"CHILD_MENU_TYPE" => "footer_1",
								"DELAY" => "N",
								"MAX_LEVEL" => "1",
								"MENU_CACHE_GET_VARS" => array(""),
								"MENU_CACHE_TIME" => "3600",
								"MENU_CACHE_TYPE" => "N",
								"MENU_CACHE_USE_GROUPS" => "Y",
								"ROOT_MENU_TYPE" => "footer_1",
								"USE_EXT" => "N"
							)
						); ?>
					</div>
					<div class="footer-column">
						<p class="footer-title footer-item"><?= Loc::getMessage('FOOTER_MENU_APP'); ?></p>
						<p class="footer-item"><?= Loc::getMessage('FOOTER_MENU_APP_SUBTITLE'); ?></p>
						<a href="https://apps.apple.com/in/app/sodamoda/id1582756296" class="footer-item footer-app" target="_blank">
							<img src="<?= SITE_TEMPLATE_PATH ?>/images/icon-appstore.svg" alt="icon appstore">
						</a>
					</div>
					<div class="footer-column">
						<p class="footer-title footer-item"><?= Loc::getMessage('FOOTER_MENU_COMPANY'); ?></p>
						<? $APPLICATION->IncludeComponent(
							"bitrix:menu",
							"footer_menu",
							array(
								"ALLOW_MULTI_SELECT" => "N",
								"CHILD_MENU_TYPE" => "footer_2",
								"DELAY" => "N",
								"MAX_LEVEL" => "1",
								"MENU_CACHE_GET_VARS" => array(""),
								"MENU_CACHE_TIME" => "3600",
								"MENU_CACHE_TYPE" => "N",
								"MENU_CACHE_USE_GROUPS" => "Y",
								"ROOT_MENU_TYPE" => "footer_2",
								"USE_EXT" => "N"
							)
						); ?>
					</div>
					<div class="footer-column">
						<p class="footer-title footer-item"><?= Loc::getMessage('FOOTER_MENU_NEWS'); ?></p>
						<div class="footer-item footer-subscribe">
							<? $APPLICATION->IncludeComponent(
								"bitrix:sender.subscribe",
								"new-subscribe",
								array(
									"AJAX_MODE" => "N",
									"AJAX_OPTION_ADDITIONAL" => "",
									"AJAX_OPTION_HISTORY" => "N",
									"AJAX_OPTION_JUMP" => "N",
									"AJAX_OPTION_STYLE" => "Y",
									"CACHE_TIME" => "3600",
									"CACHE_TYPE" => "A",
									"CONFIRMATION" => "N",
									"HIDE_MAILINGS" => "N",
									"SET_TITLE" => "N",
									"SHOW_HIDDEN" => "N",
									"USER_CONSENT" => "N",
									"USER_CONSENT_ID" => "0",
									"USER_CONSENT_IS_CHECKED" => "Y",
									"USER_CONSENT_IS_LOADED" => "N",
									"USE_PERSONALIZATION" => "N",
									"COMPONENT_TEMPLATE" => "subscribe",
									"COMPOSITE_FRAME_MODE" => "Y",
									"COMPOSITE_FRAME_TYPE" => "DYNAMIC_WITHOUT_STUB"
								),
								false
							); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="footer-bot">
			<div class="wrapper">
				<div class="footer-langs"><?= strtoupper($dataLangCurrency['lang']) ?> : <?= strtoupper($dataLangCurrency['currency']) ?>
				</div>
			</div>
		</div>
	</div>
</footer>

</div>

<? $APPLICATION->IncludeComponent("tokit:basic.favorites.script", "", array()); ?>

<div class="overlay-content"></div>

<noindex>
	<div class="popups popup-search">
		<? $APPLICATION->IncludeComponent(
			"bitrix:search.title",
			"search_mobile",
			array(
				"CATEGORY_0" => array(
					0 => "iblock_catalogs",
				),
				"CATEGORY_0_TITLE" => "",
				"CATEGORY_0_forum" => array(
					0 => "all",
				),
				"CATEGORY_0_iblock_catalogs" => array(
					0 => "2",
				),
				"CATEGORY_0_main" => "",
				"CHECK_DATES" => "N",
				"CONTAINER_ID" => "title-search-mobile",
				"INPUT_ID" => "title-search-input-mobile",
				"NUM_CATEGORIES" => "1",
				"ORDER" => "date",
				"PAGE" => "#SITE_DIR#catalog/",
				"SHOW_INPUT" => "Y",
				"SHOW_OTHERS" => "N",
				"TOP_COUNT" => "5",
				"USE_LANGUAGE_GUESS" => "N",
				"COMPONENT_TEMPLATE" => "search_mobile"
			),
			false
		); ?>
	</div>


	<div class="popups popup-call">
		<div class="items">
			<? if (Loc::getCurrentLang() == 'ru') : ?>
				<div class="item"><a class="js-open-chat"><?= Loc::getMessage("FOOTER_POPUP_CALL_CHAT") ?></a></div>
			<? endif ?>
			<div class="item">
				<a href="whatsapp://send?phone=<?= PHONE_WHATSAPP ?>" target="_blank" title="<?= Loc::getMessage("FOOTER_POPUP_CALL_TITLE_WHATSAPP") ?>">
					<?= Loc::getMessage("FOOTER_POPUP_CALL_WHATSAPP") ?>
				</a>
			</div>
			<div class="item"><a href="tg://resolve?domain=sodamoda_chat"><?= Loc::getMessage("FOOTER_POPUP_CALL_TELEGRAM") ?></a></div>
			<div class="item"><a href="tel:<?= PHONE ?>"><?= Loc::getMessage("FOOTER_POPUP_CALL_CALL") ?></a></div>
		</div>
	</div>

	<?/*
	<div class="popups popup-menu">
		<div class="scroll-wrapper">
			<div class="wrapper">

				<? $APPLICATION->IncludeComponent(
					"bitrix:menu",
					"mobile_menu3",
					array(
						"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
						"CHILD_MENU_TYPE" => "catalog",	// Тип меню для остальных уровней
						"DELAY" => "N",	// Откладывать выполнение шаблона меню
						"MAX_LEVEL" => "1",	// Уровень вложенности меню
						"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
						"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
						"MENU_CACHE_TYPE" => "N",	// Тип кеширования
						"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
						"ROOT_MENU_TYPE" => "catalog",	// Тип меню для первого уровня
						"USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
						"COMPONENT_TEMPLATE" => ".default"
					),
					false
				); ?>

				<div class="bottom-section">
					<div class="item">
						<a href="<?= SITE_DIR ?>personal/favorites/"><?= Loc::getMessage("FOOTER_POPUP_MENU_FAVORITES") ?></a>
					</div>
					<div class="item">
						<a href="<?= SITE_DIR ?>personal/profile/"><?= Loc::getMessage("FOOTER_POPUP_MENU_LK") ?></a>
					</div>

					<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("footer_lang_currency"); ?>
					<? // if($USER->IsAdmin()):
					?>
					<?php
					$dataLangCurrency = getLangAndCurrency();
					?>
					<div class="item">
						<a class="toggle-menu-arrow show"><?= strtoupper($dataLangCurrency['lang']) ?> : <?= strtoupper($dataLangCurrency['currency']) ?></a>
					</div>
					<? //endif
					?>

					<div class="item change-langs">
						<div class="lots lots-lang">
							<div class="lot<? if ($dataLangCurrency['lang'] == 'RU') : ?> active<? endif ?>"><a href="?language=RU">Русский</a></div>
							<div class="lot<? if ($dataLangCurrency['lang'] == 'EN') : ?> active<? endif ?>"><a href="?language=EN">English</a></div>
						</div>
						<div class="lots lots-currency">
							<div class="lot<? if ($dataLangCurrency['currency'] == 'rub') : ?> active<? endif ?>"><a href="?currency=rub">RUB</a></div>
							<div class="lot<? if ($dataLangCurrency['currency'] == 'usd') : ?> active<? endif ?>"><a href="?currency=usd">USD</a></div>
							<div class="lot<? if ($dataLangCurrency['currency'] == 'aed') : ?> active<? endif ?>"><a href="?currency=aed">AED</a></div>
						</div>
					</div>
					<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("footer_lang_currency", ""); ?>
				</div>

			</div>
		</div>
	</div>
  */ ?>

	<div class="popups popup-new-menu">
		<div class="scroll-wrapper">
			<div class="menu-wrapper">

				<? $APPLICATION->IncludeComponent(
					"bitrix:menu",
					"new-mobile_menu",
					array(
						"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
						"CHILD_MENU_TYPE" => "catalog",	// Тип меню для остальных уровней
						"DELAY" => "N",	// Откладывать выполнение шаблона меню
						"MAX_LEVEL" => "1",	// Уровень вложенности меню
						"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
						"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
						"MENU_CACHE_TYPE" => "N",	// Тип кеширования
						"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
						"ROOT_MENU_TYPE" => "catalog",	// Тип меню для первого уровня
						"USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
						"COMPONENT_TEMPLATE" => ".default"
					),
					false
				); ?>

				<div class="langs-block">
					<p class="menu-title langs-title menu-toggle-btn active">
						<?= strtoupper($dataLangCurrency['lang']) ?> : <?= strtoupper($dataLangCurrency['currency']) ?>
					</p>
					<div class="langs-change menu-toggle-block">
						<div class="langs-change-wrap lang">
							<p class="langs-change-title">Language</p>
							<div class="langs-change-list">
								<div class="item<? if ($dataLangCurrency['lang'] == 'EN') : ?> active<? endif ?>"><a href="?language=EN">English</a></div>
								<div class="item<? if ($dataLangCurrency['lang'] == 'RU') : ?> active<? endif ?>"><a href="?language=RU">Русский</a></div>
							</div>
						</div>
						<div class="langs-change-wrap currency">
							<p class="langs-change-title">Currency</p>
							<div class="langs-change-list">
								<div class="item<? if ($dataLangCurrency['currency'] == 'usd') : ?> active<? endif ?>"><a href="?currency=usd">USD</a></div>
								<div class="item<? if ($dataLangCurrency['currency'] == 'aed') : ?> active<? endif ?>"><a href="?currency=aed">AED</a></div>
								<div class="item<? if ($dataLangCurrency['currency'] == 'rub') : ?> active<? endif ?>"><a href="?currency=rub">RUB</a></div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<? if (IS_PRODUCT) : ?>
		<div class="popups popup-product-size">
			<div class="indent">
				<div class="title"><?= Loc::getMessage("FOOTER_POPUP_PRODUCT_SIZE_TITLE") ?></div>
				<div class="info">
					<div class="modal-size-items">

					</div>
				</div>
				<a class="ibutton js-popup-close js-metrika-addtobasket-size"><?= Loc::getMessage("FOOTER_POPUP_PRODUCT_SIZE_CLOSE") ?></a>
			</div>
			<div class="close hover js-popup-close"><img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/close.svg"></div>
		</div>

		<div class="popups popup-product-cares">
			<div class="indent">
				<!-- div class="title">Состав и уход</div -->
				<div class="info"></div>
			</div>
			<div class="close hover js-popup-close"><img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/close.svg"></div>
		</div>

		<div class="popups popup-product-subscribe">
			<div class="indent">
				<div class="title"><?= Loc::getMessage("FOOTER_POPUP_PRODUCt_CUBSCRIBE_TITLE") ?></div>
				<div class="info"></div>
			</div>
			<div class="close hover js-popup-close"><img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/close.svg"></div>
		</div>

		<div class="popups popup-learn-receipt">
			<div class="indent">
				<div class="title"><?= Loc::getMessage("FOOTER_POPUP_RECEIPT_TITLE") ?></div>
				<div class="info">
					<p><?= Loc::getMessage("FOOTER_POPUP_RECEIPT_TEXT") ?></p>
					<form name="learn-receipt" method="post" class="form">
						<input type="hidden" name="productId" value="<?= '? article ?' ?>">
						<div class="field">
							<input type="text" class="field-style" name="phone" value="" placeholder=" " id="popup-learn-receipt-form-field-phone" required="required">
							<label class="placeholder" for="popup-learn-receipt-form-field-phone"><?= Loc::getMessage("FOOTER_POPUP_RECEIPT_PHONE") ?></label>
						</div>
						<input type="submit" class="ibutton js-learn-receipt-send" value="<?= Loc::getMessage('FOOTER_POPUP_RECEIPT_SEND') ?>" />
					</form>
					<p><?= Loc::getMessage("FOOTER_POPUP_RECEIPT_TEXT2", ['#ARTICLE#' => '? article ?']) ?></p>
				</div>
			</div>
			<div class="close hover js-popup-close"><img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/close.svg"></div>
		</div>

		<?
		/*<div class="popups popup-fitting">
			<div class="indent">
				<div class="title">Подбор со стилистом</div>
				<div class="info">
					<p>Персональный подбор образов с нашим стилистом</p>
					<form name="fitting" method="post" class="form">
						<input type="hidden" name="mode" value="fitting">
						<input type="hidden" name="page_name" value="<?= $APPLICATION->ShowTitle() ?>">
						<input type="hidden" name="page_url" value="<?= $APPLICATION->GetCurPageParam() ?>">
						<div class="field">
							<input type="text" class="field-style" name="name" value="" placeholder=" " id="popup-fitting-form-field-name" required="required">
							<label class="placeholder" for="popup-fitting-form-field-name">Ваше имя</label>
						</div>
						<div class="field">
							<input type="text" class="field-style js-mask-phone" name="phone" value="" placeholder=" " id="popup-fitting-form-field-phone" required="required">
							<label class="placeholder" for="popup-fitting-form-field-phone">Телефон</label>
						</div>
						<div class="field">
							<input type="text" class="field-style" name="date" value="" placeholder=" " id="popup-fitting-form-field-date">
							<label class="placeholder" for="popup-fitting-form-field-date">Дата и время записи</label>
						</div>
						<div class="field">
							<select name="city" class="field-style" required="required">
								<option value="">Выберите город</option>
								<option value="MSC">Москва</option>
								<option value="SPB">Санкт-Петербург</option>
								<option value="RND">Ростов-на-Дону</option>
								<option value="KZN">Казань</option>
								<option value="KLR">Калининград</option>
							</select>
						</div>
						<input type="submit" class="ibutton js-fitting-send" value="Записаться" />
					</form>
				</div>
			</div>
			<div class="close hover js-popup-close"><img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/close.svg"></div>
		</div>*/
		?>

		<div class="popups popup-delivery">
			<div class="indent">
				<div class="info">
					<?= Loc::getMessage("FOOTER_POPUP_DELIVERY") ?>
				</div>
			</div>
			<div class="close hover js-popup-close"><img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/close.svg"></div>
		</div>
	<? endif ?>

	<? if (in_array($APPLICATION->GetCurDir(), ['/order/', '/en/order/'])) : ?>
		<div class="popups popup-userinfo">
			<div class="indent">
				<div class="title">Получить кэшбэк</div>
				<div class="info">
					<p>Для оформления кэшбека, пожалуйста, поделитесь информацией о себе</p>
					<form name="userinfo" method="post" class="form">
						<input type="hidden" name="mode" value="userinfo">
						<input type="hidden" name="user" value="">
						<input type="hidden" name="order" value="">
						<div class="field">
							<input type="text" class="field-style" name="inst" value="" placeholder=" " id="popup-userinfo-form-field-inst" required="required">
							<label class="placeholder" for="popup-userinfo-form-field-inst">Ваш аккаунт instagram</label>
						</div>
						<div class="field">
							<input type="date" class="field-style" name="date" max="2010-12-31" value="" placeholder=" " id="popup-userinfo-form-field-date" required="required">
							<label class="placeholder" for="popup-userinfo-form-field-date">Дата рождения</label>
						</div>
						<input type="submit" class="ibutton js-userinfo-send" value="Отправить" />
					</form>
				</div>
			</div>
			<div class="close hover js-popup-close"><img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/close.svg"></div>
		</div>
	<? endif ?>

	<? if (in_array($APPLICATION->GetCurDir(), ['/vacancy/', '/en/vacancy/'])) : ?>
		<div class="popups popup-vacancy">
			<div class="indent">
				<div class="title">Ваш отклик</div>
				<div class="info">
					<p>Пожалуйста, расскажите о себе</p>
					<form name="vacancy" method="post" class="form">
						<input type="hidden" name="mode" value="vacancy">
						<input type="hidden" name="user" value="">
						<input type="hidden" name="vacancy" value=""><!-- Название выбранной вакансии -->
						<div class="field">
							<input type="text" class="field-style" name="name" value="" placeholder=" " id="popup-vacancy-form-field-name" required="required">
							<label class="placeholder" for="popup-vacancy-form-field-name"><?= Loc::getMessage("FOOTER_POPUP_MESSAGE_FIELD_NAME") ?></label>
						</div>
						<div class="field">
							<input type="text" class="field-style" name="email" value="" placeholder=" " id="popup-vacancy-form-field-email">
							<label class="placeholder" for="popup-vacancy-form-field-email"><?= Loc::getMessage("FOOTER_POPUP_MESSAGE_FIELD_EMAIL") ?></label>
						</div>
						<div class="field">
							<input type="text" class="field-style" name="phone" value="" placeholder=" " id="popup-vacancy-form-field-phone" required="required">
							<label class="placeholder" for="popup-vacancy-form-field-phone"><?= Loc::getMessage("FOOTER_POPUP_MESSAGE_FIELD_PHONE") ?></label>
						</div>
						<div class="field">
							<input type="file" class="field-style-DEL" name="resume" value="" placeholder=" " id="popup-vacancy-form-field-file">
						</div>
						<div class="field field-checkbox">
							<label>
								<input type="checkbox" value="Y" name="rule" required="required" oninvalid="this.setCustomValidity('Заполните это поле')" oninput="setCustomValidity('')">
								<div class="label">Соглашаюсь с <a href="/rule/" target="_blank" class="black">политикой конфиденциальности</a></div>
							</label>
						</div>
						<input type="submit" class="ibutton" value="Отправить" />
					</form>
				</div>
			</div>
			<div class="close hover js-popup-close"><img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/close.svg"></div>
		</div>
	<? endif ?>

	<div class="popups popup-message">
		<div class="indent">
			<div class="title"><?= Loc::getMessage("FOOTER_POPUP_MESSAGE_TITLE") ?></div>
			<div class="info">
				<form class="form" id="add_user_form">
					<div class="field">
						<input type="text" class="field-style js-check-exist-phone" name="phone" value="" placeholder=" " id="form-field-phone">
						<label class="placeholder" for="form-field-phone"><?= Loc::getMessage('FOOTER_POPUP_MESSAGE_FIELD_PHONE') ?></label>
					</div>
					<div class="field">
						<input type="text" class="field-style" name="name" value="" placeholder=" " id="form-field-name">
						<label class="placeholder" for="form-field-name"><?= Loc::getMessage("FOOTER_POPUP_MESSAGE_FIELD_NAME") ?></label>
					</div>
					<div class="field">
						<input type="text" class="field-style" name="email" value="" placeholder=" " id="form-field-email">
						<label class="placeholder" for="form-field-email"><?= Loc::getMessage("FOOTER_POPUP_MESSAGE_FIELD_EMAIL") ?></label>
					</div>
					<input class="ibutton" type="submit" value="<?= Loc::getMessage("FOOTER_POPUP_MESSAGE_BUTTON_CREATE") ?>" name="create">
				</form>
			</div>
		</div>
		<div class="close hover js-popup-close"><img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/close.svg"></div>
	</div>

	<div class="popups popup-choose-region">
		<div class="indent">
			<div class="title">SELECT LOCATION<? // Loc::getMessage("FOOTER_POPUP_MESSAGE_TITLE")
												?></div>
			<div class="info">
				<div class="lots">
					<div class="lot"><a href="">EUROPE</a></div>
					<div class="lot"><a href="">UNITED ARAB EMIRATES</a></div>
					<div class="lot"><a href="">UNITED STATES OF AMERICA</a></div>
					<div class="lot"><a href="">WORLDWIDE</a></div>
					<div class="lot"><a href="">DEUTSCHLAND</a></div>
					<div class="lot"><a href="">UNITED KINGDOM</a></div>
					<div class="lot"><a href="">РОССИЯ</a></div>
					<div class="lot"><a href="">КАЗАХСТАН</a></div>
				</div>
			</div>
		</div>
		<div class="close hover js-popup-close"><img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/close.svg"></div>
	</div>

	<div class="popups popup-table-sizes">
		<div class="indent">
			<div class="info">
				<div class="table-sizes">
					<?= Loc::getMessage("FOOTER_POPUP_TABLE_SIZE_VALUE") ?>
					<div class="block-image">
						<div class="bs-title"><?= Loc::getMessage("FOOTER_POPUP_TABLE_SIZE_LOOK") ?></div>
						<div class="look-wrap">
							<img class="look" src="<?= SITE_TEMPLATE_PATH ?>/images/look.png" alt="">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="close hover js-popup-close"><img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/close.svg"></div>
	</div>

	<div class="popups popup-filter"></div>

	<div class="popups popup-basket"></div>

	<div class="popups popup-calling"></div>

	<div class="popups popup-phone-check">
		<div class="indent">
			<div class="title"><?= Loc::getMessage("FOOTER_POPUP_SMS_TITLE") ?></div>
			<div class="info">
				<p class="result-message popup-phone-check-result-message"></p>
				<form id="verify_code_form" name="verify_code" method="post" class="form">
					<input type="hidden" id="action_code" name="mode" value="order_verify_phone" />
					<input type="hidden" id="popup-phone-field" name="phone" value="" />
					<div class="field">
						<input type="text" class="field-style" name="code_confirmed" value="" placeholder=" " id="code_confirmed" required="required">
						<label class="placeholder" for="code_confirmed"><?= Loc::getMessage("FOOTER_POPUP_SMS_LABEL") ?></label>
					</div>
					<input type="submit" class="ibutton" id="send_verify_code" value="Подтвердить">
				</form>
			</div>
		</div>
		<div class="close hover js-popup-close"><img src="/local/templates/sodamoda/images/icons/close.svg"></div>
	</div>

	<div class="popup-cookie" id="js-popup-cookie" style="display: none">
		<div class="wrapper">
			<div class="popup-cookie-content">
				<div class="description">
					<p><?= Loc::getMessage("FOOTER_POPUP_COOKIE_DESCRIPTION") ?>
						<a href="<?= SITE_DIR ?>privacy/" target="_blank" rel="noopener noreferrer" class="hover"><?= Loc::getMessage("FOOTER_POPUP_COOKIE_DESCRIPTION_LINK") ?></a>
					</p>
					<!-- p><?= Loc::getMessage("FOOTER_POPUP_COOKIE_NOTICE") ?></p -->
				</div>
				<!-- <div class="buttons"><button class="ibutton small" type="button">Понятно</button></div>-->
				<div class="close-button-wrap">
					<svg class="close-button-cookie hover" xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 14 14" height="15px" width="15px">
						<g fill="none" fill-rule="nonzero" stroke="#222222">
							<path d="M0 0l14 14M14 0L0 14"></path>
						</g>
					</svg>
				</div>
			</div>
		</div>
	</div>

	<? if (in_array($APPLICATION->GetCurDir(), ['/order/', '/en/order/'])) : ?>
		<div class="popups popup-order-change">
			<div class="indent">
				<div class="title"><?= Loc::getMessage("FOOTER_POPUP_MESSAGE_TITLE") ?></div>
				<div class="info"></div>
				<div class=buttons>
					<a href="/basket/" class="ibutton hollow"><?= Loc::getMessage("FOOTER_POPUP_ORDER_BACK") ?></a>
					<a href="javascript:void(0);" class="ibutton js-refresh-order"><?= Loc::getMessage("FOOTER_POPUP_ORDER_REFRESH") ?></a>
				</div>
			</div>
			<div class="close hover js-popup-close"><img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/close.svg"></div>
		</div>
	<? endif ?>

</noindex>


<!-- script>
      (function(w,d,u){
              var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
              var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
      })(window,document,'https://cdn.bitrix24.ru/b10180447/crm/site_button/loader_1_lkkyi6.js');
    </script -->


<!-- Yandex.Metrika counter -->
<script type="text/javascript">
	(function(m, e, t, r, i, k, a) {
		m[i] = m[i] || function() {
			(m[i].a = m[i].a || []).push(arguments)
		};
		m[i].l = 1 * new Date();
		k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
	})(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
	ym(54055981, "init", {
		clickmap: true,
		trackLinks: true,
		accurateTrackBounce: true,
		webvisor: true
	});
</script> <noscript>
	<div><img src="https://mc.yandex.ru/watch/54055981" style="position:absolute; left:-9999px;" alt="" /></div>
</noscript> <!-- /Yandex.Metrika counter -->

<script>
	BX.message({
		RETURN_REQUEST_SENT_SUCCESS: '<?= Loc::getMessage("FOOTER_JS_RETURN_REQUEST_SENT_SUCCESS") ?>',
		BASKET_URL_LANG: '<?= SITE_DIR ?>basket/',
	});
</script>

<? /*if(!$USER->IsAdmin()):?>

      <script id='chat-24-widget-code' type="text/javascript">
        !function (e) {
          var t = {};
          function n(c) { if (t[c]) return t[c].exports; var o = t[c] = {i: c, l: !1, exports: {}}; return e[c].call(o.exports, o, o.exports, n), o.l = !0, o.exports }
          n.m = e, n.c = t, n.d = function (e, t, c) { n.o(e, t) || Object.defineProperty(e, t, {configurable: !1, enumerable: !0, get: c}) }, n.n = function (e) {
            var t = e && e.__esModule ? function () { return e.default } : function () { return e  };
            return n.d(t, "a", t), t
          }, n.o = function (e, t) { return Object.prototype.hasOwnProperty.call(e, t) }, n.p = "/packs/", n(n.s = 0)
        }([function (e, t) {
          window.chat24WidgetCanRun = 1, window.chat24WidgetCanRun && function () {
            window.chat24ID = "15aad781394791d77a896302f721c2c9", window.chat24io_lang = "ru";
            var e = "https://livechat.chat2desk.com", t = document.createElement("script");
            t.type = "text/javascript", t.async = !0, fetch(e + "/packs/manifest.json?nocache=" + (new Date()).getTime()).then(function (e) {
              return e.json()
            }).then(function (n) {
              t.src = e + n["widget.js"];
              var c = document.getElementsByTagName("script")[0];
              c ? c.parentNode.insertBefore(t, c) : document.documentElement.firstChild.appendChild(t);
              var o = document.createElement("link");
              o.href = e + n["widget.css"], o.rel = "stylesheet", o.id = "chat-24-io-stylesheet", o.type = "text/css", document.getElementById("chat-24-io-stylesheet") || document.getElementsByTagName("head")[0].appendChild(o)
            })
          }()
        }]);
      </script>

    <?else: */ ?>
<!--      <script src="https://livechatv2.chat2desk.com/packs/ie-11-support.js"></script>-->
<script>
	window.chat24_token = "15aad781394791d77a896302f721c2c9";
	window.chat24_url = "https://livechatv2.chat2desk.com";
	window.chat24_socket_url = "wss://livechatv2.chat2desk.com/widget_ws_new";
	window.chat24_show_new_wysiwyg = "true";
	window.chat24_static_files_domain = "https://storage.chat2desk.com/";
	window.lang = "ru";
	window.fetch("".concat(window.chat24_url, "/packs/manifest.json?nocache=").concat(new Date().getTime())).then(function(res) {
		return res.json();
	}).then(function(data) {
		var chat24 = document.createElement("script");
		chat24.type = "text/javascript";
		chat24.async = true;
		chat24.src = "".concat(window.chat24_url).concat(data["application.js"]);
		document.body.appendChild(chat24);
	});
</script>
</body>
</html>

<?
if(isset($_SERVER["META_DATA"]) && LANGUAGE_ID == 'en' ) {
	if($_SERVER["META_DATA"]['ELEMENT'] == 'section') {
		$APPLICATION->SetPageProperty("title", $_SERVER["META_DATA"]['SECTION_PICTURE_FILE_TITLE']);
		$APPLICATION->SetPageProperty("description", $_SERVER["META_DATA"]['SECTION_PICTURE_FILE_ALT']);
	}
	if($_SERVER["META_DATA"]['ELEMENT'] == 'detail') {
		$APPLICATION->SetPageProperty("title", $_SERVER["META_DATA"]['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']);
		$APPLICATION->SetPageProperty("description", $_SERVER["META_DATA"]['ELEMENT_PREVIEW_PICTURE_FILE_ALT']);
	}
	unset($_SERVER["META_DATA"]);
}

