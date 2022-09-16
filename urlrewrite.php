<?php
$arUrlRewrite=array (
  1 => 
  array (
    'CONDITION' => '#^/online/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#',
    'RULE' => 'alias=$1',
    'ID' => NULL,
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  9 => 
  array (
    'CONDITION' => '#^/video/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#',
    'RULE' => 'alias=$1&videoconf',
    'ID' => 'bitrix:im.router',
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  13 => 
  array (
    'CONDITION' => '#^/personal/favorites/wl([0-9]+)/(.*)#',
    'RULE' => 'USER=$1',
    'ID' => '',
    'PATH' => '/personal/favorites/index.php',
    'SORT' => 100,
  ),
  14 => 
  array (
    'CONDITION' => '#^/partner/products/edit/([0-9]+)/#',
    'RULE' => 'product-id=$1',
    'ID' => NULL,
    'PATH' => '/partner/products/edit/index.php',
    'SORT' => 100,
  ),
  17 => 
  array (
    'CONDITION' => '#^/partner/products/edit/([0-9]+)/#',
    'RULE' => 'product-id=$1',
    'ID' => NULL,
    'PATH' => '/partner/products/edit/index.php',
    'SORT' => 100,
  ),
  16 => 
  array (
    'CONDITION' => '#^/manager/products/edit/([0-9]+)/#',
    'RULE' => 'product-id=$1',
    'ID' => NULL,
    'PATH' => '/manager/products/edit/index.php',
    'SORT' => 100,
  ),
  15 => 
  array (
    'CONDITION' => '#^/manager/products/edit/([0-9]+)/#',
    'RULE' => 'product-id=$1',
    'ID' => NULL,
    'PATH' => '/manager/products/edit/index.php',
    'SORT' => 100,
  ),
  11 => 
  array (
    'CONDITION' => '#^/en/personal/order/([0-9]+)/#',
    'RULE' => 'ORDER_ID=$1',
    'ID' => '',
    'PATH' => '/en/personal/order/detail.php',
    'SORT' => 100,
  ),
  4 => 
  array (
    'CONDITION' => '#^/bitrix/services/ymarket/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/ymarket/index.php',
    'SORT' => 100,
  ),
  7 => 
  array (
    'CONDITION' => '#^/personal/order/([0-9]+)/#',
    'RULE' => 'ORDER_ID=$1',
    'ID' => '',
    'PATH' => '/personal/order/detail.php',
    'SORT' => 100,
  ),
  2 => 
  array (
    'CONDITION' => '#^/online/(/?)([^/]*)#',
    'RULE' => '',
    'ID' => NULL,
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  0 => 
  array (
    'CONDITION' => '#^/stssync/calendar/#',
    'RULE' => '',
    'ID' => 'bitrix:stssync.server',
    'PATH' => '/bitrix/services/stssync/calendar/index.php',
    'SORT' => 100,
  ),
  5 => 
  array (
    'CONDITION' => '#^/personal/lists/#',
    'RULE' => '',
    'ID' => 'bitrix:lists',
    'PATH' => '/personal/lists/index.php',
    'SORT' => 100,
  ),
  10 => 
  array (
    'CONDITION' => '#^/en/catalog/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/en/catalog/index.php',
    'SORT' => 100,
  ),
  12 => 
  array (
    'CONDITION' => '#^/catalog/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/catalog/index.php',
    'SORT' => 100,
  ),
  3 => 
  array (
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => NULL,
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => 100,
  ),
  18 =>
    array (
      'CONDITION' => '#^/articles/(.*)/.*#',
      'RULE' => 'ELEMENT_CODE=$1',
      'ID' => 'bitrix:news',
      'PATH' => '/articles/detail.php',
      'SORT' => 100,
    ),
  19 =>
    array (
      'CONDITION' => '#^/articles/#',
      'RULE' => '',
      'ID' => 'bitrix:news',
      'PATH' => '/articles/index.php',
      'SORT' => 100,
    ),

);
