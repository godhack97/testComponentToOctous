<?php
/*
|--------------------------------------------------------------------------
| Пример карты роутов
|--------------------------------------------------------------------------
|
| Внимание!
|
| - Данный файл может быть перезаписан при обновлении модуля.
| - Данный файл исключен из публичной документации.
|
| Для добавления своих роутов, создайте собственную карту.
| Например:
| /routes/sale.php
| /routes/iblock.php
| /local/api/routes/iblock.php
| И так далее, количество карт неограниченно.
|
| Карты могут располагаться вне папки модуля. Для этого необходимо указать путь к собственной папке в настройках.
| Карты будут подгружены автоматически.
|
| Совет: не желательно разбивать карту на множество файлов,
|        так как, чем больше файлов, тем больше будет происходить их подключений,
|        соответственно, затрачивается дополнительное время при запуске интерфейса.
|
| А также, не забудьте указать контроллеры для обработки роутов.
| Контроллеры могут располагаться где угодно, главное чтобы они были доступны через пространство имён.
| Вместо класса можно указать любой php-файл, который будет подключен и отработан при запросе.
|
| Для получения карты из контроллера можно воспользоваться методом Request::getInstance()->map()
|
| Поддерживаемые типы параметров: string, integer, float, email, ip, domain, url
|
*/

return [
  // Тип запроса
  'GET' => [
    // Роут
    // Для получения всех параметров Request::getInstance()->getData()
    // Для получения конкретного параметра Request::getInstance()->getData({parameter_name})
    'mobile_app/news' => [
            // Описание роута
            // 'description' => 'GET',
            // Активность роутра
            // Необязательный параметр (по умолчанию роут активен)
            //'active' => false,
            // Контроллер обрабатывающий роут
            // Данный ключ исключен из публичной документации
            // Контроллер может располагаться за пределами модуля, главное чтобы он был доступен через пространство имён
            // Где Example - класс, _get - метод класса
            'controller' => '\Xwild\Restapi\Controllers\Site\Mobileapp@getNewsList',
            // Вместо класса можно указать любой php-файл, который будет подключен и отработан при запросе
            // 'controller' => $_SERVER['DOCUMENT_ROOT'] .  '/local/rest/controllers/file.php',
            // Безопасность участвует при запуске интерфейса
            'security' => [
                // Настройки авторизации при запросе
                'auth' => [
                    // Обязательная авторизация для роута
                    // Происходит проверка на наличие заголовков
                    'required' => false, // true || false

                    // Тип авторизации используемый для роута
                    // Если login, тогда должны быть переданы заголовки Authorization-Login и Authorization-Password
                    // Если token, тогда должен быть передан заголовок Authorization-Token
                    // 'type' => 'login', // login || token
                    //'type' => 'token', // login || token
                ],
                // Настройки безопасности для логинов
                // Данный ключ исключен из публичной документации
                'login' => [
                    // Доступ к методу будет доступен только для логинов из списка
                    'whitelist' => [
                        // 'admin',
                    ]
                ],
                // Настройки безопасности для токенов
                // Данный ключ исключен из публичной документации
                'token' => [
                    // Проверять срок годности токена
                    // Если токен находится в белом списке, тогда параметр игнорируется
                    'checkExpire' => false, // true || false
                    // Доступ к методу будет доступен только для токенов из списка
                    'whitelist' => [
                        // 'b3bfb6b8-82dca90f-6049641b-76d957d6',
                        // 'bc95d11b-f2fdf7f4-15e869d3-882e72b5',
                        // '408f4f2e-d5a6e4a7-06930a16-8301b343',
                    ]
                ],
                // Настройки безопасности для групп
                // Данный ключ исключен из публичной документации
                'group' => [
                    // Доступ к методу будет доступен только для групп из списка
                    // Указывается ID группы
                    // Если имеется белый список логинов или токенов, тогда параметр игнорируется
                    'whitelist' => [
                        // 1,
                        // 6,
                        // 7,
                    ]
                ]
            ],
            // Параметры запроса
            // Напоминание: для получения текущих параметров в контроллере, можно воспользоваться методом Request::getInstance()->map()
            'parameters' => [],
            // Пример ответа
            // Необходим лишь для документации
            // #DOMAIN# и #API# будут автоматически заменены на реальные данные при выводе на экран
            // Данный ключ исключен из публичной документации
            // 'example' => [
            //     'request' => [
            //         'url' => 'http://#DOMAIN#/#API#/example/check/?iblock_id=1&sort=id:asc',
            //         'response' => [
            //             'json' => '{"page":1,"total":3,"items":[{"ID":1,"NAME":"item1"},{"ID":2,"NAME":"item2"},{"ID":3,"NAME":"item3"}]}'
            //         ]
            //     ]
            // ],
            // Настройки для поведения роута в документации
            // Данный ключ исключен из публичной документации
            'documentation' => [
                // Исключить роут
                'exclude' => [
                    // Из документации в административной части сайта
                    'admin' => false, // true || false
                    // Из документации в публичной части сайта
                    'public' => false, // true || false
                ]
            ]
        ],
    'mobile_app/sections' => [
      // Описание роута
      'description' => 'GET',
      // Активность роутра
      // Необязательный параметр (по умолчанию роут активен)
      //'active' => false,
      // Контроллер обрабатывающий роут
      // Данный ключ исключен из публичной документации
      // Контроллер может располагаться за пределами модуля, главное чтобы он был доступен через пространство имён
      // Где Example - класс, _get - метод класса
      'controller' => '\Xwild\Restapi\Controllers\Site\Mobileapp@getSectionsList',
      // Вместо класса можно указать любой php-файл, который будет подключен и отработан при запросе
      // 'controller' => $_SERVER['DOCUMENT_ROOT'] .  '/local/rest/controllers/file.php',
      // Безопасность участвует при запуске интерфейса
      'security' => [
        // Настройки авторизации при запросе
        'auth' => [
          // Обязательная авторизация для роута
          // Происходит проверка на наличие заголовков
          'required' => false, // true || false
        
          // Тип авторизации используемый для роута
          // Если login, тогда должны быть переданы заголовки Authorization-Login и Authorization-Password
          // Если token, тогда должен быть передан заголовок Authorization-Token
          // 'type' => 'login', // login || token
          //'type' => 'token', // login || token
        ],
        // Настройки безопасности для логинов
        // Данный ключ исключен из публичной документации
        'login' => [
          // Доступ к методу будет доступен только для логинов из списка
          'whitelist' => [
            // 'admin',
          ]
        ],
        // Настройки безопасности для токенов
        // Данный ключ исключен из публичной документации
        'token' => [
          // Проверять срок годности токена
          // Если токен находится в белом списке, тогда параметр игнорируется
          'checkExpire' => false, // true || false
          // Доступ к методу будет доступен только для токенов из списка
          'whitelist' => [
            // 'b3bfb6b8-82dca90f-6049641b-76d957d6',
            // 'bc95d11b-f2fdf7f4-15e869d3-882e72b5',
            // '408f4f2e-d5a6e4a7-06930a16-8301b343',
          ]
        ],
        // Настройки безопасности для групп
        // Данный ключ исключен из публичной документации
        'group' => [
          // Доступ к методу будет доступен только для групп из списка
          // Указывается ID группы
          // Если имеется белый список логинов или токенов, тогда параметр игнорируется
          'whitelist' => [
            // 1,
            // 6,
            // 7,
          ]
        ]
      ],
      // Параметры запроса
      // Напоминание: для получения текущих параметров в контроллере, можно воспользоваться методом Request::getInstance()->map()
      'parameters' => [],
      // Пример ответа
      // Необходим лишь для документации
      // #DOMAIN# и #API# будут автоматически заменены на реальные данные при выводе на экран
      // Данный ключ исключен из публичной документации
      'example' => [
        'request' => [
          'url' => 'http://#DOMAIN#/#API#/mobile_app/sections/',
          'response' => [
            'json' => '{"items":[{"id":1,"name":"item1","URL":"item1"},{"id":2,"name":"item2","URL":"item1"},{"id":3,"name":"item3","URL":"item1"}],"page":1,"total":3}'
          ]
        ]
      ],
      // Настройки для поведения роута в документации
      // Данный ключ исключен из публичной документации
      'documentation' => [
        // Исключить роут
        'exclude' => [
          // Из документации в административной части сайта
          'admin' => false, // true || false
          // Из документации в публичной части сайта
          'public' => false, // true || false
        ]
      ]
    ],
    'mobile_app/section/{{section_id}}/{{page}}' => [
      // Описание роута
      'description' => 'GET',
      // Активность роутра
      // Необязательный параметр (по умолчанию роут активен)
      //'active' => false,
      // Контроллер обрабатывающий роут
      // Данный ключ исключен из публичной документации
      // Контроллер может располагаться за пределами модуля, главное чтобы он был доступен через пространство имён
      // Где Example - класс, _get - метод класса
      'controller' => '\Xwild\Restapi\Controllers\Site\Mobileapp@getSectionProductList',
      // Вместо класса можно указать любой php-файл, который будет подключен и отработан при запросе
      // 'controller' => $_SERVER['DOCUMENT_ROOT'] .  '/local/rest/controllers/file.php',
      // Безопасность участвует при запуске интерфейса
      'security' => [
        // Настройки авторизации при запросе
        'auth' => [
          // Обязательная авторизация для роута
          // Происходит проверка на наличие заголовков
          'required' => false, // true || false
        
          // Тип авторизации используемый для роута
          // Если login, тогда должны быть переданы заголовки Authorization-Login и Authorization-Password
          // Если token, тогда должен быть передан заголовок Authorization-Token
          // 'type' => 'login', // login || token
          //'type' => 'token', // login || token
        ],
        // Настройки безопасности для логинов
        // Данный ключ исключен из публичной документации
        'login' => [
          // Доступ к методу будет доступен только для логинов из списка
          'whitelist' => [
            // 'admin',
          ]
        ],
        // Настройки безопасности для токенов
        // Данный ключ исключен из публичной документации
        'token' => [
          // Проверять срок годности токена
          // Если токен находится в белом списке, тогда параметр игнорируется
          'checkExpire' => false, // true || false
          // Доступ к методу будет доступен только для токенов из списка
          'whitelist' => [
            // 'b3bfb6b8-82dca90f-6049641b-76d957d6',
            // 'bc95d11b-f2fdf7f4-15e869d3-882e72b5',
            // '408f4f2e-d5a6e4a7-06930a16-8301b343',
          ]
        ],
        // Настройки безопасности для групп
        // Данный ключ исключен из публичной документации
        'group' => [
          // Доступ к методу будет доступен только для групп из списка
          // Указывается ID группы
          // Если имеется белый список логинов или токенов, тогда параметр игнорируется
          'whitelist' => [
            // 1,
            // 6,
            // 7,
          ]
        ]
      ],
      // Параметры запроса
      // Напоминание: для получения текущих параметров в контроллере, можно воспользоваться методом Request::getInstance()->map()
      'parameters' => [
        'section_id' => [
          'required' => true,
          'type' => 'integer',
          'description' => 'ID раздела'
        ],
        'page' => [
          'required' => false,
          'type' => 'integer',
          'description' => 'страница пагинации'
        ],
      ],
      // Пример ответа
      // Необходим лишь для документации
      // #DOMAIN# и #API# будут автоматически заменены на реальные данные при выводе на экран
      // Данный ключ исключен из публичной документации
      'example' => [
        'request' => [
          'url' => 'http://#DOMAIN#/#API#/mobile_app/section/86/1',
          'response' => [
            'json' => '{"items":[{"id":"integer","name":"string","url":"string","picture":"string","article":"string","price":"integer","price_old":"integer","discount":"boolean","sort":"integer"},{"id":"integer","name":"string","url":"string","picture":"string","article":"string","price":"integer","price_old":"integer","discount":"boolean","sort":"integer"},{"id":"integer","name":"string","url":"string","picture":"string","article":"string","price":"integer","price_old":"integer","discount":"boolean","sort":"integer"}],"page":1,"total_page":3,"total_items":9}'
          ]
        ]
      ],
      // Настройки для поведения роута в документации
      // Данный ключ исключен из публичной документации
      'documentation' => [
        // Исключить роут
        'exclude' => [
          // Из документации в административной части сайта
          'admin' => false, // true || false
          // Из документации в публичной части сайта
          'public' => false, // true || false
        ]
      ]
    ],

    // Пример роута с использованием переменных в строке
    /*

        '/example/check/iblock/{{iblockId}}/' => [
            'controller' => '\Xwild\Restapi\Controllers\Native\Example@_get',
            'parameters' => [
                'iblockId' => [
                    'required' => true,
                    'type' => 'integer',
                    'description' => 'ID инфоблока',
                ],
            ]
        ],
        'example/check/section/{{sectionId}}' => [
            'controller' => '\Xwild\Restapi\Controllers\Native\Example@_get',
            'parameters' => [
                'sectionId' => [
                    'required' => true,
                    'type' => 'integer',
                    'description' => 'ID раздела',
                ]
            ]
        ],
        'example/check/section/{{sectionId}}/{{productId}}/view' => [
            'controller' => '\Xwild\Restapi\Controllers\Native\Example@_get',
            'parameters' => [
                'sectionId' => [
                    'required' => true,
                    'type' => 'integer',
                    'description' => 'ID раздела',
                ],
                'productId' => [
                    'required' => true,
                    'type' => 'integer',
                    'description' => 'ID товара',
                ],
            ]
        ],
        */
  ],
  // Запросы поддерживают все возможные параметры: из строки запроса (GET) и из тела запроса
  'POST' => [
    'mobile_app/news' => [
      // Описание роута
      // 'description' => 'GET',
      // Активность роутра
      // Необязательный параметр (по умолчанию роут активен)
      //'active' => false,
      // Контроллер обрабатывающий роут
      // Данный ключ исключен из публичной документации
      // Контроллер может располагаться за пределами модуля, главное чтобы он был доступен через пространство имён
      // Где Example - класс, _get - метод класса
      'controller' => '\Xwild\Restapi\Controllers\Site\Mobileapp@getNewsList',
      // Вместо класса можно указать любой php-файл, который будет подключен и отработан при запросе
      // 'controller' => $_SERVER['DOCUMENT_ROOT'] .  '/local/rest/controllers/file.php',
      // Безопасность участвует при запуске интерфейса
      'security' => [
        // Настройки авторизации при запросе
        'auth' => [
          // Обязательная авторизация для роута
          // Происходит проверка на наличие заголовков
          'required' => false, // true || false

          // Тип авторизации используемый для роута
          // Если login, тогда должны быть переданы заголовки Authorization-Login и Authorization-Password
          // Если token, тогда должен быть передан заголовок Authorization-Token
          // 'type' => 'login', // login || token
          //'type' => 'token', // login || token
        ],
        // Настройки безопасности для логинов
        // Данный ключ исключен из публичной документации
        'login' => [
          // Доступ к методу будет доступен только для логинов из списка
          'whitelist' => [
            // 'admin',
          ]
        ],
        // Настройки безопасности для токенов
        // Данный ключ исключен из публичной документации
        'token' => [
          // Проверять срок годности токена
          // Если токен находится в белом списке, тогда параметр игнорируется
          'checkExpire' => false, // true || false
          // Доступ к методу будет доступен только для токенов из списка
          'whitelist' => [
            // 'b3bfb6b8-82dca90f-6049641b-76d957d6',
            // 'bc95d11b-f2fdf7f4-15e869d3-882e72b5',
            // '408f4f2e-d5a6e4a7-06930a16-8301b343',
          ]
        ],
        // Настройки безопасности для групп
        // Данный ключ исключен из публичной документации
        'group' => [
          // Доступ к методу будет доступен только для групп из списка
          // Указывается ID группы
          // Если имеется белый список логинов или токенов, тогда параметр игнорируется
          'whitelist' => [
            // 1,
            // 6,
            // 7,
          ]
        ]
      ],
      // Параметры запроса
      // Напоминание: для получения текущих параметров в контроллере, можно воспользоваться методом Request::getInstance()->map()
      'parameters' => [],
      // Пример ответа
      // Необходим лишь для документации
      // #DOMAIN# и #API# будут автоматически заменены на реальные данные при выводе на экран
      // Данный ключ исключен из публичной документации
      // 'example' => [
      //     'request' => [
      //         'url' => 'http://#DOMAIN#/#API#/example/check/?iblock_id=1&sort=id:asc',
      //         'response' => [
      //             'json' => '{"page":1,"total":3,"items":[{"ID":1,"NAME":"item1"},{"ID":2,"NAME":"item2"},{"ID":3,"NAME":"item3"}]}'
      //         ]
      //     ]
      // ],
      // Настройки для поведения роута в документации
      // Данный ключ исключен из публичной документации
      'documentation' => [
        // Исключить роут
        'exclude' => [
          // Из документации в административной части сайта
          'admin' => false, // true || false
          // Из документации в публичной части сайта
          'public' => false, // true || false
        ]
      ]
    ],
  ],
  /*
    'POST' => [
        'example/check' => [
            // 'description' => 'POST',
            // Роут отключен
            // Клиент получит ответ со статусом: 434 Requested host unavailable
            //'active' => false,
            'controller' => '\Xwild\Restapi\Controllers\Native\Example@_post',
            // Сервер ожидает запрос с типом контента application/json
            // Если тип будет отличаться, запрос будет отклонен
            'contentType' => 'application/json',
            'security' => [
                'auth' => [
                    // 'required' => true,
                    // 'type' => 'login',
                ],
                'token' => [
                    'whitelist' => [
                        // '408f4f2e-d5a6e4a7-06930a16-8301b343'
                    ]
                ]
            ],
            'parameters' => [
                // Параметры: уровень 1
                'iblock_id' => [
                    'required' => true,
                    'type' => 'integer',
                    'description' => 'ID инфоблока'
                ],

                'user' => [
                    'required' => true,
                    'type' => 'array',
                    'description' => 'Автор',
                    // Параметры: уровень 2
                    'parameters' => [
                        'id' => [
                            'required' => true,
                            'type' => 'integer',
                            'description' => 'ID'
                        ],
                        'name' => [
                            'required' => true,
                            'type' => 'string',
                            'description' => 'Имя'
                        ]
                    ]
                ],
                'items' => [
                    'required' => true,
                    'type' => 'array',
                    'description' => 'Массив объектов (элементов)',
                    'parameters' => [
                        // Параметры для элемента типа массив
                        // Правила прописыаются только для одного item, но проверка происходит для каждого объекта/массива
                        //
                        // Пример items при запросе:
                        // "items": [
                        //		{
                        //			"name":"test",
                        //			"color":"blue",
                        //			"preview_text":"Описание анонса"
                        //		},
                        //		{
                        //			"name":"test2",
                        //			"color":"blue",
                        //			"detail_text":"Детальное описание"
                        //		},
                        //		{
                        //			"name":"test3",
                        //			"color":"blue"
                        //		}
                        //	],
                        [
                            'name' => [
                                'required' => true,
                                'type' => 'string',
                                'description' => 'Имя элемента'
                            ],
                            'preview_text' => [
                                'type' => 'string',
                                'description' => 'Описание анонса'
                            ],
                            'detail_text' => [
                                'type' => 'string',
                                'description' => 'Детальное описание'
                            ],
                            'preview_picture_url' => [
                                'type' => 'url',
                                'description' => 'Url изображения анонса'
                            ],
                            'detail_picture_url' => [
                                'type' => 'url',
                                'description' => 'Url детального изображения'
                            ],
                            'color' => [
                                'required' => true,
                                'type' => 'string',
                                'description' => 'Свойство: Цвет'
                            ],
                            'form' => [
                                'type' => 'string',
                                'description' => 'Свойство: Форма'
                            ]
                        ]

                    ]
                ]
            ]
        ]
    ],
    'PUT' => [
        'example/check' => [
            // 'description' => 'PUT',
            'controller' => '\Xwild\Restapi\Controllers\Native\Example@_put',
            'contentType' => 'application/json',
            'security' => [
                'auth' => [
                    // 'required' => true,
                    // 'type' => 'login',
                ],
            ],
            'parameters' => [
                'element_id' => [
                    'required' => true,
                    'type' => 'integer',
                    'description' => 'ID элемента'
                ],
                'name' => [
                    'type' => 'string',
                    'description' => 'Имя элемента'
                ],
                'preview_text' => [
                    'type' => 'string',
                    'description' => 'Описание анонса'
                ],
                'detail_text' => [
                    'type' => 'string',
                    'description' => 'Детальное описание'
                ],
            ]
        ]
    ],
    'PATCH' => [
        'example/check' => [
            // 'description' => 'PATCH',
            'controller' => '\Xwild\Restapi\Controllers\Native\Example@_put',
            'contentType' => 'application/json',
            'security' => [
                'auth' => [
                    // 'required' => true,
                    // 'type' => 'login',
                ],
            ],
            'parameters' => [
                'element_id' => [
                    'required' => true,
                    'type' => 'integer',
                    'description' => 'ID элемента'
                ],
                'name' => [
                    'type' => 'string',
                    'description' => 'Имя элемента'
                ],
                'preview_text' => [
                    'type' => 'string',
                    'description' => 'Описание анонса'
                ],
                'detail_text' => [
                    'type' => 'string',
                    'description' => 'Детальное описание'
                ],
            ]
        ]
    ],
    'DELETE' => [
        'example/check' => [
            // 'description' => 'DELETE',
            'controller' => '\Xwild\Restapi\Controllers\Native\Example@_delete',
            'security' => [
                'auth' => [
                    // 'required' => true,
                    // 'type' => 'login',
                ]
            ],
            'parameters' => [
                'element_ids' => [
                    'required' => true,
                    'type' => 'array',
                    'description' => 'ID элементов'
                ]
            ]
        ]
    ],
    'HEAD' => [
        'example/check' => [
            // 'description' => 'HEAD',
            'controller' => '\Xwild\Restapi\Controllers\Native\Example@_head'
        ]
    ]
    */
];
