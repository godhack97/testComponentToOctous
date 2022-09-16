<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

    use Bitrix\Main\Loader;
    use Bitrix\Main\Localization\Loc;
    use Xwild\Restapi\Foundation\Page;
    use Xwild\Restapi\Foundation\Helper;
    use Xwild\Restapi\Foundation\Config;
    use Xwild\Restapi\Foundation\Settings;
    use Xwild\Restapi\Foundation\Core;

    Loader::includeModule('xwild.restapi');

    Page::getInstance()->checkAccess('accessSupport');
    Page::getInstance()->addCss('/bitrix/css/'.getMessageModule('XwildRestModuleId').'/'.basename(__FILE__, '.php').'.min.css');
    Page::getInstance()->setTitle(getMessage('XwildRestSupportPageTitle'));
    $tabs = [
        [
            'DIV'   => 'tab-1', 'TAB' => getMessage('XwildRestTabMainTitle'),
            'TITLE' => getMessage('XwildRestTabMainDescription')
        ],
        [
            'DIV'   => 'tab-2', 'TAB' => getMessage('XwildRestTabMonitorTitle'),
            'TITLE' => getMessageModule('XwildRestTabMonitorDescription')
        ]
    ];
    $tabControl = new CAdminTabControl('tabControl', $tabs);
    $routes = [];
    $dir = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'routes';
    $files[$dir] = array_diff(scandir($dir), ['..', '.']);
    if (Config::getInstance()->getOption('localRouteMap')) {
        $dir = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.Config::getInstance()->getOption('localRouteMap');
        $files[$dir] = array_diff(scandir($dir), ['..', '.']);
    }
    foreach ($files as $dir => $items) {
        foreach ($items as $file) {
            if ((!Config::getInstance()->getOption('useNativeRoute') && $file === Settings::getInstance()->getFile()['native']) || (!Config::getInstance()->getOption('useExampleRoute') && $file === Settings::getInstance()->getFile()['example']))
                continue;
            $file = $dir.DIRECTORY_SEPARATOR.$file;
            if (is_array($ar = require $file)) {
                foreach ($ar as $type => $r) {
                    foreach ($r as $route => $config) {
                        $routes[$type][] = $route;
                        if ($config['active'] === false)
                            $routes['disabled'][] = $route;
                        if ($config['security']['auth']['required'])
                            $routes['auth'][] = $route;
                        if ($config['security']['login']['whitelist'])
                            $routes['login-whitelist'][] = $route;
                        if ($config['security']['token']['whitelist'])
                            $routes['token-whitelist'][] = $route;
                        if ($config['security']['group']['whitelist'])
                            $routes['group-whitelist'][] = $route;
                    }
                }
            }
        }
    }
    $totalTokens = (Config::getInstance()->getOption('useToken')) ? Core::getInstance()->getDB()->query('SELECT COUNT(VALUE_ID) as COUNT FROM b_uts_user WHERE '.Settings::getInstance()->getTokenField()['code'].' IS NOT NULL')->fetchRaw()['COUNT'] : 0;
    $sql = '
        SELECT
            COUNT(ID) as total,
            (SELECT COUNT(ID) as COUNT FROM '.Settings::getInstance()->getTable()['request-response'].' WHERE METHOD="GET") as totalGet, 
            (SELECT COUNT(ID) as COUNT FROM '.Settings::getInstance()->getTable()['request-response'].' WHERE METHOD="POST") as totalPost, 
            (SELECT COUNT(ID) as COUNT FROM '.Settings::getInstance()->getTable()['request-response'].' WHERE METHOD="PUT") as totalPut, 
            (SELECT COUNT(ID) as COUNT FROM '.Settings::getInstance()->getTable()['request-response'].' WHERE METHOD="PATCH") as totalPatch, 
            (SELECT COUNT(ID) as COUNT FROM '.Settings::getInstance()->getTable()['request-response'].' WHERE METHOD="DELETE") as totalDelete,
            (SELECT COUNT(ID) as COUNT FROM '.Settings::getInstance()->getTable()['request-response'].' WHERE METHOD="HEAD") as totalHead
        FROM '.Settings::getInstance()->getTable()['request-response'];

    $requests = Core::getInstance()->getDB()->query($sql)->fetchRaw();
    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';
    $tabControl->Begin();
?>
<? $tabControl->BeginNextTab() ?>
    <div class="paragraphs main">
        <div class="paragraph">
            <div class="meta">
                <ul>
                    <li><?= Settings::getInstance()->getModule()['name'] ?></li>
                    <li class="version"
                        title="<?= getMessageModule('XwildRestVersion') ?>"><?= Settings::getInstance()->getModule()['version'] ?></li>
                    <li class="version-date"
                        title="<?= getMessageModule('XwildRestVersionDate') ?>"><?= date_format(date_create(Settings::getInstance()->getModule()['versionDate']), 'd.m.Y') ?></li>
                </ul>
            </div>
            <div class="description">
                <section>
                    <div class="header"><i class="fas fa-file-alt"></i><?= getMessageModule('XwildRestDescription') ?>
                    </div>
                    <div class="body"><?= Settings::getInstance()->getModule()['description'] ?></div>
                </section>
            </div>
        </div>
        <div class="paragraph">
            <div class="meta">
                <ul>
                    <li title="<?= getMessageModule('XwildRestVendor') ?>">
                        <?= getMessageModule('XwildRestVendor') ?>:
                        <?= print_url(Settings::getInstance()->getAuthor()['website'], Settings::getInstance()->getAuthor()['name'], 'target="_blank"') ?>
                    </li>
                    <li class="marketplace"
                        title="<?= getMessageModule('XwildRestMarketplace') ?>"><?= print_url(Settings::getInstance()->getPath()['marketplace'], getMessageModule('XwildRestMarketplace'), 'target="_blank"') ?></li>
                </ul>
            </div>
        </div>
    </div>

<? $tabControl->BeginNextTab() ?>
    <div class="paragraphs monitor">
        <div class="paragraph">
            <div class="meta">
                <ul>
                    <li><?= getMessageModule('XwildRestModule') ?></li>
                </ul>
            </div>
            <div class="table-two-column">
                <section>
                    <div class="header"><i class="fas fa-cog"></i><?= getMessageModule('XwildRestConfig') ?></div>
                    <div class="body">
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestApi') ?></div>
                            <div class="cell"
                                 style="color: <?= Config::getInstance()->getOption('useRestApi') ? 'rgb(34, 162, 59)' : 'rgb(206, 0, 0)' ?>"><?= Config::getInstance()->getOption('useRestApi') ? getMessageModule('XwildRestEnabled') : getMessageModule('XwildRestDisabled') ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestPathRestApi') ?></div>
                            <div class="cell"><?= Config::getInstance()->getOption('pathRestApi') ? Config::getInstance()->getOption('pathRestApi') : '-' ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestNativeRoute') ?></div>
                            <div class="cell"
                                 style="color: <?= Config::getInstance()->getOption('useExampleRoute') ? 'rgb(34, 162, 59)' : 'rgb(206, 0, 0)' ?>"><?= Config::getInstance()->getOption('useNativeRoute') ? getMessageModule('XwildRestEnabled') : getMessageModule('XwildRestDisabled') ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestExampleRoute') ?></div>
                            <div class="cell"
                                 style="color: <?= Config::getInstance()->getOption('useExampleRoute') ? 'rgb(34, 162, 59)' : 'rgb(206, 0, 0)' ?>"><?= Config::getInstance()->getOption('useExampleRoute') ? getMessageModule('XwildRestEnabled') : getMessageModule('XwildRestDisabled') ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestUseJournal') ?></div>
                            <div class="cell"
                                 style="color: <?= Config::getInstance()->getOption('useJournal') ? 'rgb(34, 162, 59)' : 'rgb(206, 0, 0)' ?>"><?= Config::getInstance()->getOption('useJournal') ? getMessageModule('XwildRestEnabled') : getMessageModule('XwildRestDisabled') ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestShowExamples') ?></div>
                            <div class="cell"
                                 style="color: <?= Config::getInstance()->getOption('showExamples') ? 'rgb(34, 162, 59)' : 'rgb(206, 0, 0)' ?>"><?= Config::getInstance()->getOption('showExamples') ? getMessageModule('XwildRestEnabled') : getMessageModule('XwildRestDisabled') ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestPhp') ?></div>
                            <div class="cell"><?= PHP_VERSION ?></div>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="header"><i class="fas fa-key"></i><?= getMessageModule('XwildRestAuth') ?></div>
                    <div class="body">
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestUseLoginPassword') ?></div>
                            <div class="cell"
                                 style="color: <?= Config::getInstance()->getOption('useLoginPassword') ? 'rgb(34, 162, 59)' : 'rgb(206, 0, 0)' ?>"><?= Config::getInstance()->getOption('useLoginPassword') ? getMessageModule('XwildRestEnabled') : getMessageModule('XwildRestDisabled') ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestUseToken') ?></div>
                            <div class="cell"
                                 style="color: <?= Config::getInstance()->getOption('useToken') ? 'rgb(34, 162, 59)' : 'rgb(206, 0, 0)' ?>"><?= Config::getInstance()->getOption('useToken') ? getMessageModule('XwildRestEnabled') : getMessageModule('XwildRestDisabled') ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestTokenKey') ?></div>
                            <div class="cell"><?= Config::getInstance()->getOption('tokenKey') ? Config::getInstance()->getOption('tokenKey') : '-' ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestTokenLifetime') ?></div>
                            <div class="cell"><?= Config::getInstance()->getOption('tokenLifetime') ? Config::getInstance()->getOption('tokenLifetime') : '-' ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestTokenFiledCode') ?></div>
                            <div class="cell"><?= Settings::getInstance()->getTokenField()['code'] ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestUseRequestLimit') ?></div>
                            <div class="cell"
                                 style="color: <?= Config::getInstance()->getOption('useRequestLimit') ? 'rgb(34, 162, 59)' : 'rgb(206, 0, 0)' ?>"><?= Config::getInstance()->getOption('useRequestLimit') ? getMessageModule('XwildRestEnabled') : getMessageModule('XwildRestDisabled') ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestTotalTokens') ?></div>
                            <div class="cell"><?= $totalTokens ?></div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <div class="paragraph">
            <div class="meta">
                <ul>
                    <li><?= getMessageModule('XwildRestStatistics') ?></li>
                </ul>
            </div>
            <div class="table-two-column">
                <section>
                    <div class="header"><i class="fas fa-sitemap"></i><?= getMessageModule('XwildRestRoutes') ?></div>
                    <div class="body">
                        <div class="row">
                            <div class="cell"><?= Helper::getInstance()->GET() ?></div>
                            <div class="cell"><?= $routes[Helper::getInstance()->GET()] ? count($routes[Helper::getInstance()->GET()]) : 0 ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= Helper::getInstance()->POST() ?></div>
                            <div class="cell"><?= $routes[Helper::getInstance()->POST()] ? count($routes[Helper::getInstance()->POST()]) : 0 ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= Helper::getInstance()->PUT() ?></div>
                            <div class="cell"><?= $routes[Helper::getInstance()->PUT()] ? count($routes[Helper::getInstance()->PUT()]) : 0 ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= Helper::getInstance()->PATCH() ?></div>
                            <div class="cell"><?= $routes[Helper::getInstance()->PATCH()] ? count($routes[Helper::getInstance()->PATCH()]) : 0 ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= Helper::getInstance()->DELETE() ?></div>
                            <div class="cell"><?= $routes[Helper::getInstance()->DELETE()] ? count($routes[Helper::getInstance()->DELETE()]) : 0 ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= Helper::getInstance()->HEAD() ?></div>
                            <div class="cell"><?= $routes[Helper::getInstance()->HEAD()] ? count($routes[Helper::getInstance()->HEAD()]) : 0 ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestRoutesDisabled') ?></div>
                            <div class="cell"><?= ($routes['disabled']) ? count($routes['disabled']) : 0 ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestRoutesAuth') ?></div>
                            <div class="cell"><?= ($routes['auth']) ? count($routes['auth']) : 0 ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestRoutesLoginWhitelist') ?></div>
                            <div class="cell"><?= ($routes['login-whitelist']) ? count($routes['login-whitelist']) : 0 ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestRoutesTokensWhitelist') ?></div>
                            <div class="cell"><?= ($routes['token-whitelist']) ? count($routes['token-whitelist']) : 0 ?></div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestRoutesGroupsWhitelist') ?></div>
                            <div class="cell"><?= ($routes['group-whitelist']) ? count($routes['group-whitelist']) : 0 ?></div>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="header"><i class="fas fa-sign-in-alt"></i><?= getMessageModule('XwildRestRequests') ?>
                    </div>
                    <div class="body">
                        <div class="row">
                            <div class="cell"><?= getMessageModule('XwildRestTotalRequest') ?></div>
                            <div class="cell">
                                <a href="/bitrix/admin/xwild-restapi-journal-request-response.php?lang=<?= LANG ?>"><?= $requests['total'] ?></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= Helper::getInstance()->GET() ?></div>
                            <div class="cell">
                                <a href="/bitrix/admin/xwild-restapi-journal-request-response.php?PAGEN_1=1&SIZEN_1=20&lang=<?= LANG ?>&set_filter=Y&adm_filter_applied=0&find_method=GET"><?= $requests['totalGet'] ?></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= Helper::getInstance()->POST() ?></div>
                            <div class="cell">
                                <a href="/bitrix/admin/xwild-restapi-journal-request-response.php?PAGEN_1=1&SIZEN_1=20&lang=<?= LANG ?>&set_filter=Y&adm_filter_applied=0&find_method=POST"><?= $requests['totalPost'] ?></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= Helper::getInstance()->PUT() ?></div>
                            <div class="cell">
                                <a href="/bitrix/admin/xwild-restapi-journal-request-response.php?PAGEN_1=1&SIZEN_1=20&lang=<?= LANG ?>&set_filter=Y&adm_filter_applied=0&find_method=PUT"><?= $requests['totalPut'] ?></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= Helper::getInstance()->PATCH() ?></div>
                            <div class="cell">
                                <a href="/bitrix/admin/xwild-restapi-journal-request-response.php?PAGEN_1=1&SIZEN_1=20&lang=<?= LANG ?>&set_filter=Y&adm_filter_applied=0&find_method=PATCH"><?= $requests['totalPatch'] ?></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= Helper::getInstance()->DELETE() ?></div>
                            <div class="cell">
                                <a href="/bitrix/admin/xwild-restapi-journal-request-response.php?PAGEN_1=1&SIZEN_1=20&lang=<?= LANG ?>&set_filter=Y&adm_filter_applied=0&find_method=DELETE"><?= $requests['totalDelete'] ?></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="cell"><?= Helper::getInstance()->HEAD() ?></div>
                            <div class="cell">
                                <a href="/bitrix/admin/xwild-restapi-journal-request-response.php?PAGEN_1=1&SIZEN_1=20&lang=<?= LANG ?>&set_filter=Y&adm_filter_applied=0&find_method=HEAD"><?= $requests['totalHead'] ?></a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
<?php
    $tabControl->End();
    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
