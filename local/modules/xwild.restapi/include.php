<?php
    use Bitrix\Main\Localization\Loc;

    Loc::loadLanguageFile(__FILE__);

    function getMessageModule($message, $args = null){
       return Loc::getMessage($message, $args);
    }