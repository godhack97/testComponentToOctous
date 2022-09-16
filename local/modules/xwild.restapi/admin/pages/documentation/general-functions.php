<?php
    use Xwild\Restapi\Foundation\Page;
    use Bitrix\Main\Localization\Loc;

    Bitrix\Main\Loader::includeModule('xwild.restapi');

    Page::getInstance()->loadLanguage(__FILE__);
    $methods = get_class_methods('\Xwild\Restapi\Foundation\Response');
    sort($methods);
    $listMethods = '';
    foreach ($methods as $method) {
        if ($method === 'getInstance' || $method === 'json' || $method === '__call')
            continue;
        $listMethods .= '<div class="row"><div class="cell">Request::getInstance()->'.$method.'()</div></div>';
    }
    echo getMessageModule('XwildRestContent', ['#METHODS#' => $listMethods]);
