<?php
$MESS["NETWORK_PAYMENT_MODULE_TITLE"] = 'Интернет-эквайринг Network';
$MESS["NETWORK_PAYMENT_GROUP_GATE"] = 'Параметры подключения платежного шлюза';
$MESS["NETWORK_PAYMENT_GROUP_HANDLER"] = 'Параметры платежного обработчика';
$MESS["NETWORK_PAYMENT_GROUP_ORDER"] = 'Параметры заказа';
//$MESS["NETWORK_PAYMENT_GROUP_FFD"] = 'Настройки ФФД';
$MESS["NETWORK_PAYMENT_GROUP_OFD"] = "Отправка чеков";

$MESS["NETWORK_PAYMENT_HANDLER_AUTO_REDIRECT_NAME"] = 'Автоматический редирект на форму оплаты';
$MESS["NETWORK_PAYMENT_HANDLER_AUTO_REDIRECT_DESCR"] = 'Если отмечено, после оформления заказа, покупатель будет автоматически перенаправлен на страницу платежной формы.';
$MESS["NETWORK_PAYMENT_HANDLER_LOGGING_NAME"] = 'Логирование';
$MESS["NETWORK_PAYMENT_HANDLER_LOGGING_DESCR"] = 'Если отмечено, плагин будет логировать запросы в файл.';
$MESS["NETWORK_PAYMENT_HANDLER_SHIPMENT_NAME"] = 'Разрешить отгрузку';
$MESS["NETWORK_PAYMENT_HANDLER_SHIPMENT_DESCR"] = 'Если отмечено, то после успешной оплаты будет автоматически разрешена отгрузка заказа.';

$MESS["NETWORK_PAYMENT_ORDER_NUMBER_NAME"] = 'Уникальный идентификатор заказа в магазине';
$MESS["NETWORK_PAYMENT_ORDER_NUMBER_DESCR"] = '';
$MESS["NETWORK_PAYMENT_ORDER_AMOUNT_NAME"] = 'Сумма заказа';
$MESS["NETWORK_PAYMENT_ORDER_AMOUNT_DESCR"] = '';
$MESS["NETWORK_PAYMENT_ORDER_DESCRIPTION_NAME"] = 'Описание заказа';
$MESS["NETWORK_PAYMENT_ORDER_DESCRIPTION_DESCR"] = "Поле может содержать метки: #PAYMENT_ID# - ID оплаты, #ORDER_ID# - ID заказа, #PAYMENT_NUMBER# - номер оплаты, \n#ORDER_NUMBER# - номер заказа, #USER_EMAIL# - Email покупателя. (не более 24 символов, запрещены к использованию %, +, конец строки '\\r' и перенос строки '\\n').";

$MESS["NETWORK_PAYMENT_RETURN_URL_NAME"] = "Страница, на которую необходимо перенаправить пользователя в случае успешной оплаты";
$MESS["NETWORK_PAYMENT_RETURN_URL_DESCR"] = "Оставьте это поле пустым, если хотите использовать настройки по умолчанию.";
$MESS["NETWORK_PAYMENT_FAIL_URL_NAME"] = "Страница, на которую требуется перенаправить пользователя в случае неуспешной оплатыa";
$MESS["NETWORK_PAYMENT_FAIL_URL_DESCR"] = "Оставьте это поле пустым, если хотите использовать настройки по умолчанию.";