<?php
    use Xwild\Restapi\Foundation\Page;
    use Bitrix\Main\Localization\Loc;

    Bitrix\Main\Loader::includeModule('xwild.restapi');
    Page::getInstance()->loadLanguage(__FILE__);
    echo getMessageModule('XwildRestContent', ['#LANG#' => LANG, '#MODULE_ID#' => \Xwild\Restapi\Foundation\Settings::getInstance()->getModule()['id']]);
