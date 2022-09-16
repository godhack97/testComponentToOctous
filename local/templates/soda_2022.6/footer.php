<?php
/**
 * @global \CMain $APPLICATION
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}
if(!IS_AJAX)
    include($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/template/footer.php');