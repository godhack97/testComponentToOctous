<?php

    use Bitrix\Main\IO\File;
    use Bitrix\Main\Localization\Loc;
    use Xwild\Restapi\Foundation\Settings;

    $APPLICATION->SetTitle(getMessageModule('XwildRestInstallPageTitle', ['#MODULE_NAME#' => Settings::getInstance()->getModule()['name']]));
    $textPage = getMessageModule('XwildRestInstallMessageHeader');
    $textPage .= getMessageModule('XwildRestInstallMessageBody', ['#MODULE_NAME#' => Settings::getInstance()->getModule()['name']]);
    $bitrixDir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/init.php';
    $localDir = $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/init.php';
    $textConnection = "<?php".PHP_EOL.PHP_EOL;
    $textConnection .= "/**".PHP_EOL;
    $textConnection .= " * Module ".Settings::getInstance()->getModule()['name'].PHP_EOL;
    $textConnection .= " *".PHP_EOL;
    $textConnection .= " * @package  ".Settings::getInstance()->getModule()['id'].PHP_EOL;
    $textConnection .= " * @author   ".Settings::getInstance()->getAuthor()['name']." <".Settings::getInstance()->getAuthor()['email'].">".PHP_EOL;
    $textConnection .= " * @website  ".Settings::getInstance()->getAuthor()['website'].PHP_EOL;
    $textConnection .= " */".PHP_EOL;
    $textConnection .= Settings::getInstance()->getModule()['connectionString'].PHP_EOL;

    if (is_file($localDir)) {
        $path = $localDir;
    }
    elseif (is_file($bitrixDir)) {
        $path = $bitrixDir;
    }

    if (!$path) {
        File::putFileContents($localDir, $textConnection);
        $textPage = str_replace('#PATH#', str_replace($_SERVER['DOCUMENT_ROOT'], '', $localDir), $textPage);
    }
    else {
        $content = File::getFileContents($path);
        if (stripos($content, Settings::getInstance()->getModule()['connectionString']) === false) {
            File::putFileContents(str_replace('.php', '-bcp-'.date('Y-m-d').'.php', $path), $content);
            if (stripos($content, '<?php') !== false) {
                $content = str_replace_once('<?php', $textConnection, $content);
            }
            elseif (stripos($content, '<?') !== false) {
                $content = str_replace_once('<?', $textConnection, $content);
            }
        }
        elseif (stripos($content, '// '.Settings::getInstance()->getModule()['connectionString']) !== false) {
            $content = str_replace('// '.Settings::getInstance()->getModule()['connectionString'], Settings::getInstance()->getModule()['connectionString'], $content);
        }
        File::putFileContents($path, $content);
        $textPage = str_replace('#PATH#', str_replace($_SERVER['DOCUMENT_ROOT'], '', $path), $textPage);
    }
    function str_replace_once($search, $replace, $text) {
        $pos = strpos($text, $search);
        return $pos !== false ? substr_replace($text, $replace, $pos, strlen($search)) : $text;
    }

    $textPage .= getMessageModule('XwildRestInstallMessageFooter');

    echo CAdminMessage::ShowNote($textPage);