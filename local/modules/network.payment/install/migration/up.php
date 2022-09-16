<?php
    use Bitrix\Main\Application;
    use Bitrix\Main\Loader;
	
    $sql = 'CREATE TABLE IF NOT EXISTS network_order
    (
        `ID` INT(11) NOT NULL AUTO_INCREMENT,
        `BITRIX_ORDER_ID` int(10) UNSIGNED NOT NULL,
        `ORDER_ID` varchar(55) NOT NULL,
        `AMOUNT` decimal(12,4) UNSIGNED NOT NULL,
        `CURRENCY` varchar(3) NOT NULL,
        `REFERENCE` varchar(20) NOT NULL,
        `STATE` varchar(20) NULL,
        `STATUS` varchar(50) NOT NULL,
        `ACTION` text NOT NULL,
        `PAYMENT_ID` text NOT NULL,
        `PAYMENT_LINK` text NOT NULL,
        `CAPTURED_AMT` decimal(12,4) UNSIGNED NULL,
        `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY(`ID`),
        UNIQUE KEY `NGENIUS_ONLINE_ORDER_ID` (`ORDER_ID`)
    )';

    Application::getConnection()->queryExecute($sql);
