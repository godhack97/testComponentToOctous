<?
$APPLICATION->SetAdditionalCSS('/bitrix/admin_styles.css');

/**
 * Модификация верхнего жёлтого блока на странице заказа:
 * 
 * /bitrix/admin_styles.css
 * 
 * Событие добавления данных пользователя onSaleAdminOrderInfoBlockShow
 * /bitrix/php_interface/include/events.php
 * 
 * Модификация файлов ядра
 * /bitrix/modules/sale/admin/order_view.php // :466 getViewExt($saleOrder, $orderBasket)
 * /bitrix/modules/sale/admin/order_edit.php // :644
 * /bitrix/modules/sale/lib/helpers/admin/blocks/orderinfo.php // :95 public static function getViewExt(Order $order, $orderBasket)
 * /bitrix/modules/sale/lang/ru/lib/helpers/admin/blocks/orderinfo.php
 * 
 * Огругление стоимости доставки
 * /bitrix/modules/sale/lib/delivery/calculationresult.php
 * 
 * Изменена длина кода СМС с 6 до 4 символов.
 * /bitrix/modules/main/lib/security/mfa/otpalgorithm.php // :6 protected $digits = 4;
 *
 * Исправление международных номеров
 * bitrix/modules/main/lib/phonenumber/parser.php //346
 */


/*
 * Изменения в платёжной системе
 * 
/bitrix/modules/sberbank.ecom2/template/payment.php - строка 33 # <span class="sberbank__price-string"><?=getMessage("SBERBANK_PAYMENT_PAYMENT_TITLE");?>: <b><?=CurrencyFormat($params['SBERBANK_ORDER_AMOUNT'], $params['currency'])?></b> <?=LANGUAGE_ID == 'en' ? '('.round(\CCurrencyRates::ConvertCurrency($params['SBERBANK_ORDER_AMOUNT'], "USD", "RUB"), 2).' RUB)' : '';?></span>
/bitrix/modules/sberbank.ecom2/lib/rbs/Gateway.php - строка 351 # добавлена под $this->data['amount'] = $this->data['amount'] * 100; строка $this->data['amount'] = LANGUAGE_ID == 'en' ? \CCurrencyRates::ConvertCurrency($this->data['amount'], "USD", "RUB") : $this->data['amount'];
/bitrix/modules/sberbank.ecom2/lib/rbs/Gateway.php - строка 335 # добавлен цикл
/bitrix/modules/sberbank.ecom2/handler/handler.php - строка 135 # 'itemAmount' => LANGUAGE_ID == 'en' ? \CCurrencyRates::ConvertCurrency($BasketItem->getFinalPrice(), "USD", "RUB") : $BasketItem->getFinalPrice(),
/bitrix/modules/sberbank.ecom2/handler/handler.php - строка 136 # 'itemPrice' => LANGUAGE_ID == 'en' ? \CCurrencyRates::ConvertCurrency($BasketItem->getPrice(), "USD", "RUB") : $BasketItem->getPrice(),
/bitrix/modules/sberbank.ecom2/handler/handler.php - строка 169 # 'itemAmount' => LANGUAGE_ID == 'en' ? \CCurrencyRates::ConvertCurrency($Order->getField('PRICE_DELIVERY'), "USD", "RUB") : $Order->getField('PRICE_DELIVERY'),
/bitrix/modules/sberbank.ecom2/handler/handler.php - строка 170 # 'itemPrice' => LANGUAGE_ID == 'en' ? \CCurrencyRates::ConvertCurrency($Order->getField('PRICE_DELIVERY'), "USD", "RUB") : $Order->getField('PRICE_DELIVERY'),
 */