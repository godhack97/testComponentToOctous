<?php
    use Bitrix\Main\Loader;
    use Bitrix\Main\Localization\Loc;
    use Xwild\Restapi\Foundation\Settings;
    use Xwild\Restapi\Foundation\Page;

    Loc::loadLanguageFile(__FILE__);
    Loader::includeModule('xwild.restapi');

    if (Page::getInstance()->checkAccess('accessMenuItems') === false) {
        return [];
    }

    return [
        [
            'parent_menu' => 'global_menu_services', 'text' => getMessageModule('XwildRestModuleName'),
            'section'     => getMessageModule('XwildRestModuleId'), 'module_id' => getMessageModule('XwildRestModuleId'),
            'items_id'    => 'menu_'.getMessageModule('XwildRestModuleId'), 'icon' => 'clouds_menu_icon',
            'page_icon'   => 'clouds_menu_icon', 'sort' => 1, 'items' => [
                [
                    'items_id'  => 'menu_'.getMessageModule('XwildRestModuleId').'_documentation', 'icon' => 'learning_menu_icon',
                    'page_icon' => 'learning_menu_icon', 'text' => getMessageModule('XwildRestMenuItemDocumentation'), 'items' => [
                        //[
                            //'items_id' => 'menu_'.getMessageModule('XwildRestModuleId').'_documentation_general',
                            //'text'     => getMessageModule('XwildRestMenuItemDocumentationGeneral'),
                            //'url'      => 'xwild-restapi-documentation-general.php?lang='.LANG
                        //],
                        [
                            'items_id' => 'menu_'.getMessageModule('XwildRestModuleId').'_documentation_routes',
                            'text'     => getMessageModule('XwildRestMenuItemDocumentationRoutes'),
                            'url'      => 'xwild-restapi-documentation-routes.php?lang='.LANG
                        ]
                    ]
                ],
                [
                    'items_id'  => 'menu_'.getMessageModule('XwildRestModuleId').'_security', 'icon' => 'security_menu_ddos_icon',
                    'page_icon' => 'security_menu_ddos_icon', 'text' => getMessageModule('XwildRestMenuItemSecurity'),
                    'url'       => 'xwild-restapi-security.php?lang='.LANG
                ],
                [
                    'items_id'  => 'menu_'.getMessageModule('XwildRestModuleId').'_journal', 'icon' => 'update_marketplace',
                    'page_icon' => 'update_marketplace', 'text' => getMessageModule('XwildRestMenuItemJournal'), 'items' => [
                        [
                            'items_id' => 'menu_'.getMessage('XwildRestModuleId').'_journal_request_response',
                            'text'     => getMessage('XwildRestMenuItemJournalRequestResponse'),
                            'url'      => 'xwild-restapi-journal-request-response.php?lang='.LANG, 'more_url' => [
                            'xwild-restapi-journal-request-response-record.php?lang='.LANG
                        ]
                        ], [
                            'items_id' => 'menu_'.getMessage('XwildRestModuleId').'_journal_request_limit',
                            'text'     => getMessage('XwildRestMenuItemJournalRequestLimit'),
                            'url'      => 'xwild-restapi-journal-request-limit.php?lang='.LANG
                        ]
                    ]
                ],
                [
                    'items_id'  => 'menu_'.getMessage('XwildRestModuleId').'_support', 'icon' => 'support_menu_icon',
                    'page_icon' => 'support_menu_icon', 'text' => getMessage('XwildRestMenuItemSupport'),
                    'url'       => 'xwild-restapi-support.php?lang='.LANG
                ],
                [
                    'items_id'  => 'menu_'.getMessage('XwildRestModuleId').'_settings', 'icon' => 'sys_menu_icon',
                    'page_icon' => 'sys_menu_icon', 'text' => getMessage('XwildRestMenuItemSettings'),
                    'url'       => 'xwild-restapi-config.php?lang='.LANG
                ]
            ]
        ]
    ];
