<?php
    use Bitrix\Main\IO\File;
    use Bitrix\Main\Localization\Loc;

    $connectStr = 'if (Bitrix\Main\Loader::includeModule(\'' . getMessageModule('XwildRestModuleId') . '\')) \Xwild\Restapi\Foundation\Core::getInstance()->run();';

    $APPLICATION->SetTitle(getMessageModule('XwildRestUninstallPageTitle', ['#MODULE_NAME#' => $config['module']['name']]));

    $textPage = getMessageModule('XwildRestUninstallMessageHeader');
    $textPage .= getMessageModule('XwildRestUninstallMessageBody', ['#MODULE_NAME#' => $config['module']['name']]);

    $bitrixDir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/init.php';
    $localDir = $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/init.php';

    if (is_file($localDir)) {
        $path = $localDir;
    }
    elseif (is_file($bitrixDir)) {
        $path = $bitrixDir;
    }

    if ($path) {
        $content = File::getFileContents($path);
        if (stripos($content, $connectStr) !== false) {
            $content = str_replace($connectStr, '// '.$connectStr, $content);
            File::putFileContents($path, $content);
            $textPage .= getMessageModule('XwildRestUninstallModuleDisconnected', ['#PATH#' => str_replace($_SERVER['DOCUMENT_ROOT'], '', $path)]);
        }
    }
    $textPage .= getMessageModule('XwildRestUninstallMessageFooter');

    echo CAdminMessage::ShowNote($textPage);