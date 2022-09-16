<?php
    use Bitrix\Main\Loader;
    use Bitrix\Main\Application;
		
    Application::getConnection()->queryExecute('DROP TABLE IF EXISTS network_order');
