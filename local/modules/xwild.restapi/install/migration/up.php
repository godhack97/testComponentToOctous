<?php
    use Bitrix\Main\Application;
    use Bitrix\Main\Loader;
    use Xwild\Restapi\Foundation\Settings;

    Loader::includeModule('xwild.restapi');

    $sql = 'CREATE TABLE IF NOT EXISTS '.Settings::getInstance()->getTable()['request-response'].' 
        (
        `ID` INT(11) NOT NULL AUTO_INCREMENT,
        `DATETIME` DATETIME NOT NULL,
        `IP` CHAR(20),
        `METHOD` CHAR(10),
        `CLIENT_ID` CHAR(60),
        `REQUEST` MEDIUMTEXT,
        `RESPONSE` MEDIUMTEXT,
        PRIMARY KEY(`ID`),
        INDEX (`DATETIME`),
        INDEX (`IP`),
        INDEX (`CLIENT_ID`)
        )';

    Application::getConnection()->queryExecute($sql);

    $sql = 'CREATE TABLE IF NOT EXISTS '.Settings::getInstance()->getTable()['request-limit'].' 
        (
        `ID` INT(11) NOT NULL AUTO_INCREMENT,
        `DATETIME` DATETIME NOT NULL,
        `CLIENT_ID` CHAR(60),
        PRIMARY KEY(`ID`),
        INDEX (`DATETIME`),
        INDEX (`CLIENT_ID`)
        )';

    Application::getConnection()->queryExecute($sql);
