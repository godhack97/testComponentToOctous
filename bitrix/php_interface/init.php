<?

include_once 'include/mobile_detect.php';
include_once 'include/functions.php';
include_once 'include/phones.php';
//include_once 'include/events.php';
//include_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/sertificate.php');



global $APPLICATION;

define('CATALOG_ID', 2);
define('CATALOG_OFFERS_ID', 8);
define('SLIDER_ID', 3);
define('CATALOG_EN_ID', 15);
define('SLIDER_EN_ID', 17);
define('COLORS_ID', 11);
define('COMPOSITION_ID', 22);
define('MATERIALS_ID', 21);
define('VACANCY_ID', 23);

define('HISTORY_ID', 4);
define('HISTORY_EN_ID', 20);

define('HL_COLOR_ID', 2);
define('HL_COLOR_ID_EN', 6);
define('HL_SUBSCRIBE', 7);

define('PAY_SBER_ID', 14);

$arUrlPath = explode('/', trim($APPLICATION->GetCurDir(), '/'));
define('IS_CATALOG', ($arUrlPath[0] == 'catalog' || $arUrlPath[1] == 'catalog') ? true : false); // Раздел каталога
define('IS_PRODUCT', IS_CATALOG && ((count($arUrlPath) == 3 && !isLangDir()) || (count($arUrlPath) == 4 && isLangDir()))? true : false); // Карточка товара
define('IS_MAIN', str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']) == '/' || str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']) == '/en/' ? true : false); // Главная страница

$isAsideHover = false;
if(IS_PRODUCT || IS_CATALOG || in_array($arUrlPath[0], Array('basket', 'order')) || in_array($arUrlPath[1], Array('basket', 'order')))
  $isAsideHover = true;

if((in_array($arUrlPath[0], Array('personal')) && in_array($arUrlPath[1], Array('create'))) || (in_array($arUrlPath[1], Array('personal')) && in_array($arUrlPath[1], Array('create'))))
  $isAsideHover = true;

if(IS_MAIN)
  $isAsideHover = true;

define('IS_ASIDE_HOVER', $isAsideHover);

define("SITE_SERVER_PROTOCOL", (CMain::IsHTTPS()) ? 'https://' : 'http://');


$detect = new Mobile_Detect;
// $detect = new MobileDetect; // Битриксовый метод, работает криво, его вырезают
define('IS_MOBILE', $detect->isMobile());
define('IS_TABLET', $detect->isTablet());


define('PERCENT_PAY_BONUS', 70); // % - в процентах оплата бонусами
