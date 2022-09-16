<?php
  use Bitrix\Main\Localization\Loc;
  
  Loc::loadLanguageFile(__FILE__);
  
  /*
  |--------------------------------------------------------------------------
  | Встроенные роуты
  |--------------------------------------------------------------------------
  |
  | Внимание!
  |
  | - Файл может быть перезаписан при обновлении модуля.
  | - Роуты модулем нигде не используются.
  | - Роуты нужны лишь для разработчиков, чтобы сократить время разработки.
  | - Роуты могут быть перекрыты собственной картой.
  | - Активностью карты можно управлять из интерфейса модуля.
  |
  |--------------------------------------------------------------------------
  */
  
  return [
    'GET'    => [
      'docs'          => [
        'description'   => getMessageModule('GET:docs'),
        'controller'    => '\Xwild\Restapi\Controllers\Native\Documentation@get',
        'documentation' => [
          'exclude' => [
            'public' => true
          ]
        ]
      ],
      'user'       => [
        'description'   => getMessageModule('GET:user'),
        'controller' => '\Xwild\Restapi\Controllers\Native\User@get',
        'security'      => [
          'auth'     => [
            'required' => true, 'type' => 'token'
          ],
          'token' => [
            'checkExpire' => true
          ]
        ],
        'parameters' => [
          'id'       => [
            'type' => 'integer', 'description' => getMessageModule('parameter:id')
          ],
          'login' => [
            'type' => 'string', 'description' => getMessageModule('parameter:login')
          ],
          'token' => [
            'type' => 'string', 'description' => getMessageModule('parameter:token')
          ],
        ],
      ],
      'user/token' => [
        'description' => getMessageModule('GET:user/token'), 'controller' => '\Xwild\Restapi\Controllers\Native\Token@get',
        'parameters'  => [
          'login'       => [
            'required' => true, 'type' => 'string', 'description' => getMessageModule('parameter:login')
          ],
          'password' => [
            'required' => true, 'type' => 'string', 'description' => getMessageModule('parameter:password')
          ]
        ]
      ],
      'provider'   => [
        'description'   => getMessageModule('GET:provider'),
        'controller'    => '\Xwild\Restapi\Controllers\Native\Provider@get', 'security' => [
          'auth'     => [
            'required' => true, 'type' => 'token'
          ],
          'token' => [
            'checkExpire' => true
          ]
        ],
        'parameters' => [
          'class'     => [
            'required' => true, 'type' => 'string', 'description' => getMessageModule('parameter:class')
          ],
          'method' => [
            'required' => false, 'type' => 'string', 'description' => getMessageModule('parameter:method')
          ],
        ]
      ],
    ],
    'POST'   => [
      'user'          => [
        'description'   => getMessageModule('POST:user'), 'controller' => '\Xwild\Restapi\Controllers\Native\User@create',
        'contentType'   => 'application/json',
        'security' => [
          'auth'     => [
            'required' => true, 'type' => 'token'
          ], 'token' => [
            'checkExpire' => true
          ]
        ],
        'parameters' => [
          'phone'       => [
            'required' => false, 'type' => 'string', 'description' => getMessageModule('parameter:phone')
          ],
          'login'       => [
            'required' => false, 'type' => 'string', 'description' => getMessageModule('parameter:login')
          ], 'email'    => [
            'required' => false, 'type' => 'string', 'description' => getMessageModule('parameter:email')
          ], 'password' => [
            'required' => false, 'type' => 'string', 'description' => getMessageModule('parameter:password')
          ]
        ],
      ],
      'user/token' => [
        'description' => getMessageModule('POST:user/token'),
        'controller'  => '\Xwild\Restapi\Controllers\Native\Token@create', 'contentType' => 'application/json',
        'parameters'  => [
          'login'       => [
            'required' => true, 'type' => 'string', 'description' => getMessageModule('parameter:login')
          ],
          'password' => [
            'required' => false, 'type' => 'string', 'description' => getMessageModule('parameter:password')
          ]
        ]
      ],
      'provider'   => [
        'description'   => getMessageModule('POST:provider'),
        'controller'    => '\Xwild\Restapi\Controllers\Native\Provider@exec',
        'contentType'   => 'application/json', 'security' => [
          'auth'     => [
            'required' => true, 'type' => 'token'
          ], 'token' => [
            'checkExpire' => true
          ]
        ], 'parameters' => [
          'class'         => [
            'required' => true, 'type' => 'string', 'description' => getMessageModule('parameter:class')
          ], 'method'     => [
            'required' => true, 'type' => 'string', 'description' => getMessageModule('parameter:method')
          ], 'parameters' => [
            'required' => false, 'type' => 'array', 'description' => getMessageModule('parameter:methodParameters')
          ], 'callback'   => [
            'required'    => false, 'type' => 'string',
            'description' => getMessageModule('parameter:methodCallbackFunction')
          ]
        ]
      ],
    ],
    'PUT'    => [
      'user' => [
        'description'   => getMessageModule('PUT:user'), 'controller' => '\Xwild\Restapi\Controllers\Native\User@update',
        'contentType'   => 'application/json', 'security' => [
          'auth'     => [
            'required' => true, 'type' => 'token'
          ], 'token' => [
            'checkExpire' => true
          ]
        ], 'parameters' => [
          'id'       => [
            'required' => false, 'type' => 'integer', 'description' => getMessageModule('parameter:id')
          ], 'login' => [
            'required' => false, 'type' => 'string', 'description' => getMessageModule('parameter:login')
          ], 'token' => [
            'required' => false, 'type' => 'string', 'description' => getMessageModule('parameter:token')
          ],
        ],
      ]
    ],
    'DELETE' => [
      'user'          => [
        'description'   => getMessageModule('DELETE:user'), 'controller' => '\Xwild\Restapi\Controllers\Native\User@delete',
        'contentType'   => 'application/json', 'security' => [
          'auth'     => [
            'required' => true, 'type' => 'token'
          ], 'token' => [
            'checkExpire' => true
          ]
        ],
        'parameters' => [
          'id'       => [
            'required' => false, 'type' => 'integer', 'description' => getMessageModule('parameter:id')
          ], 'login' => [
            'required' => false, 'type' => 'string', 'description' => getMessageModule('parameter:login')
          ], 'token' => [
            'required' => false, 'type' => 'string', 'description' => getMessageModule('parameter:token')
          ],
        ],
      ],
      'user/token' => [
        'description' => getMessageModule('DELETE:user/token'),
        'controller'  => '\Xwild\Restapi\Controllers\Native\Token@delete', 'contentType' => 'application/json',
        'parameters'  => [
          'login'       => [
            'required' => true, 'type' => 'string', 'description' => getMessageModule('parameter:login')
          ], 'password' => [
            'required' => true, 'type' => 'string', 'description' => getMessageModule('parameter:password')
          ]
        ]
      ]
    ]
  ];
