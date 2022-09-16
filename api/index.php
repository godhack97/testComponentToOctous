<?php
    header("Access-Control-Allow-Origin: http://localhost:8080");
    header("Access-Control-Allow-Methods: POST,GET");
    header("Access-Control-Allow-Headers: *");

    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

    use Bitrix\Main\Application,
        Bitrix\Main\Context;

    $application = Application::getInstance();
    $context = Context::getCurrent();
    $request = $context->getRequest();

    if (Bitrix\Main\Loader::includeModule('xwild.restapi')) {
        try {
            \Xwild\Restapi\Foundation\Core::getInstance()->run();
        }
        catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
