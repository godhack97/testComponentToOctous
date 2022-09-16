<?php
    use Bitrix\Main\Loader;
    use Bitrix\Main\Application;
    use Xwild\Restapi\Foundation\Settings;

    Loader::includeModule('xwild.restapi');

    Application::getConnection()->queryExecute('DROP TABLE IF EXISTS '.Settings::getInstance()->getTable()['request-response']);
    Application::getConnection()->queryExecute('DROP TABLE IF EXISTS '.Settings::getInstance()->getTable()['request-limit']);
