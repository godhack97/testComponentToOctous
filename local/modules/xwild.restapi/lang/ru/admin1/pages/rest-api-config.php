<?php
    $MESS = [
        // Config page
        'XwildRestConfigPageTitle' => 'Настройки',
        'XwildRestTabMainTitle' => 'Основное',
        'XwildRestTabRoutesTitle' => 'Роуты',
        'XwildRestTabAccessTitle' => 'Доступы к разделам модуля',
        'XwildRestUseRestApi' => 'Активировать интерфейс',
        'XwildRestUseRestApiHint' => 'Активация/деактивация API-интерфейса.',
        'XwildRestPathRestApi' => 'Путь интерфейса',
        'XwildRestPathRestApiHint' => 'Путь по которому будет доступен интерфейс.<br><br>Пример: http://{domain}/{<b>api</b>}/example/check/<br><br>Также, можно указать константу <b>ROOT</b>.<br>Тогда интрефейс будет запускаться от корня.<br><br>Пример: http://{domain}/example/check/',

        'XwildRestUseLateStart' => 'Отложенный запуск',
        'XwildRestUseLateStartHint' => 'Интерфейс запускается на событии OnProlog (1.15), а не в init.php (1.5).<br><br><a href="https://dev.1c-bitrix.ru/api_help/main/general/pageplan.php" target="_blank">Страница и порядок её выполнения</a>',

        'XwildRestUseJournal' => 'Журналирование Входящих запросов',
        'XwildRestUseJournalHint' => 'Если параметр активирован, тогда будет выполняться журналирование запросов.<br><br>Внимание, для журналирования запроса в контроллере должна быть прописана функция journal()->add().<br>Подробнее смотрите в карте-примере.',
        'XwildRestShowExamples' => 'Отображать примеры и подсказки',
        'XwildRestShowExamplesHint' => 'Если параметр активирован, тогда будут отображаться различные подсказки и примеры на страницах модуля.',
        'XwildRestUseExampleRoute' => 'Активировать тестовый роут',
        'XwildRestUseExampleRouteHint' => 'Параметр активирует тестовый роут http|https://{domain}/{api}/example/check.<br><br>Внимание, после проверки работы интерфейса, тестовый роут желательно отключить, чтобы какой-нибудь злоумышленник не мог использовать его.',
        'XwildRestUseNativeRoute' => 'Активировать нативные роуты',
        'XwildRestUseNativeRouteHint' => 'Параметр активирует нативные роуты модуля, расположенные в карте _native.php.',
        'XwildRestLocalRouteMap' => 'Путь к собственным картам роутов',
        'XwildRestLocalRouteMapHint' => 'Путь по которому будут доступны собственные карты роутов.<br>Карты по-прежнему можно хранить в папке модуля.<br>Собственные карты роутов, при совпадении роутов, будут перекрывать нативные карты роутов.',
        'XwildRestAccessDocumentation' => 'Документация',
        'XwildRestAccessDocumentationHint' => 'Группы пользователей, которые имеют доступ к разделу Документация.',
        'XwildRestAccessSecurity' => 'Безопасность',
        'XwildRestAccessSecurityHint' => 'Группы пользователей, которые имеют доступ к разделу Безопасность.',
        'XwildRestAccessJournal' => 'Журналы',
        'XwildRestAccessJournalHint' => 'Группы пользователей, которые имеют доступ к разделу Журналы.',
        'XwildRestAccessSupport' => 'Поддержка',
        'XwildRestAccessSupportHint' => 'Группы пользователей, которые имеют доступ к разделу Поддержка.',
        'XwildRestaccessMenuItems' => 'Пункт в меню',
        'XwildRestaccessMenuItemsHint' => 'Группы пользователей, которым виден пункт "#MODULE_NAME#" в меню.',
        'XwildRestButtonSave' => 'Сохранить',
        'XwildRestButtonRestore' => 'Сбросить',
        'XwildRestSaved' => 'Настройки сохранены',
        'XwildRestRestored' => 'Настройки сброшены',
    ];
