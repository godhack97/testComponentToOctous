<?
$siteId = "";
if (isset($_REQUEST["site_id"]) && is_string($_REQUEST["site_id"]))
  $siteId = substr(preg_replace("/[^a-z0-9_]/i", "", $_REQUEST["site_id"]), 0, 2);

if ($siteId){
  define("SITE_ID", $siteId);
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;

Bitrix\Main\Loader::includeModule("catalog");
$request = Application::getInstance()->getContext()->getRequest();

if($request->isPost())
{
	global $USER;
	
  $action = $request->getPost("action");
  $id     = (int)$request->getPost("id");
  $prop   = $request->getPost("prop");

  $siteDir = SITE_DIR;
  if(!empty($siteId)) {
    $siteDir = CSite::GetArrayByID($siteId)["DIR"];
  }

  if($id > 0 && $action == "ADD2BASKET")
  {
    $fields = [
      "PRODUCT_ID" => $id,
      "QUANTITY" => 1,
      "PROPS" => $prop,
    ];

    $r = Bitrix\Catalog\Product\Basket::addProduct($fields, ["LID" => $siteId]);

		if(!$r->isSuccess()){
			echo json_encode($r->getErrors());
		}
  }

}

$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "basket_line", Array(
    "HIDE_ON_BASKET_PAGES" => "Y",	// Не показывать на страницах корзины и оформления заказа
    "PATH_TO_AUTHORIZE" => "",	// Страница авторизации
    "PATH_TO_BASKET" => $siteDir."basket/",	// Страница корзины
    "PATH_TO_ORDER" => $siteDir."order/",	// Страница оформления заказа
    "PATH_TO_PERSONAL" => $siteDir."personal/",	// Страница персонального раздела
    "PATH_TO_PROFILE" => $siteDir."personal/",	// Страница профиля
    "PATH_TO_REGISTER" => $siteDir."login/",	// Страница регистрации
    "POSITION_FIXED" => "N",	// Отображать корзину поверх шаблона
    "SHOW_AUTHOR" => "N",	// Добавить возможность авторизации
    "SHOW_EMPTY_VALUES" => "Y",	// Выводить нулевые значения в пустой корзине
    "SHOW_NUM_PRODUCTS" => "Y",	// Показывать количество товаров
    "SHOW_PERSONAL_LINK" => "Y",	// Отображать персональный раздел
    "SHOW_PRODUCTS" => "Y",	// Показывать список товаров
    "SHOW_REGISTRATION" => "N",	// Добавить возможность регистрации
    "SHOW_TOTAL_PRICE" => "Y",	// Показывать общую сумму по товарам
    "COMPONENT_TEMPLATE" => ".default",
    "SHOW_DELAY" => "N",	// Показывать отложенные товары
    "SHOW_NOTAVAIL" => "N",	// Показывать товары, недоступные для покупки
    "SHOW_IMAGE" => "Y",	// Выводить картинку товара
    "SHOW_PRICE" => "Y",	// Выводить цену товара
    "SHOW_SUMMARY" => "Y",	// Выводить подытог по строке
  ),
  false
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>