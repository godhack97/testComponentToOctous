<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';
    use Xwild\Restapi\Foundation\Page;

    Bitrix\Main\Loader::includeModule('xwild.restapi');
    Page::getInstance()->checkAccess('accessDocumentation');

    die;
