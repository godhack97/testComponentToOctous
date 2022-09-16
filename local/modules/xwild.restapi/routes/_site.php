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

    'POST' => [],
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
