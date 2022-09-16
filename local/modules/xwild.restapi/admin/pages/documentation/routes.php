<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

    use Xwild\Restapi\Foundation\Page;
    use Xwild\Restapi\Foundation\Settings;
    use Xwild\Restapi\Foundation\Helper;
    use Xwild\Restapi\Foundation\Config;

    Bitrix\Main\Loader::includeModule('xwild.restapi');

    Page::getInstance()->checkAccess('accessDocumentation');
    Page::getInstance()->addCss('/bitrix/css/'.getMessageModule('XwildRestModuleId').'/'.basename(__DIR__).'-'.basename(__FILE__, '.php').'.min.css');
    Page::getInstance()->setTitle(getMessageModule('XwildRestRoutesPageTitle'));

    $tabs = [
        ['DIV' => 'tab-1', 'TAB' => Helper::getInstance()->GET(), 'TITLE' => Helper::getInstance()->GET()],
        ['DIV' => 'tab-2', 'TAB' => Helper::getInstance()->POST(), 'TITLE' => Helper::getInstance()->POST()],
        ['DIV' => 'tab-3', 'TAB' => Helper::getInstance()->PUT(), 'TITLE' => Helper::getInstance()->PUT()],
        ['DIV' => 'tab-4', 'TAB' => Helper::getInstance()->PATCH(), 'TITLE' => Helper::getInstance()->PATCH()],
        ['DIV' => 'tab-5', 'TAB' => Helper::getInstance()->DELETE(), 'TITLE' => Helper::getInstance()->DELETE()],
        ['DIV' => 'tab-6', 'TAB' => Helper::getInstance()->HEAD(), 'TITLE' => Helper::getInstance()->HEAD()],
    ];
    $tabControl = new CAdminTabControl('tabControl', $tabs);
    $routes = [];

    $dir = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'routes';
    $files[$dir] = array_diff(scandir($dir), ['..', '.']);

    if (Config::getInstance()->getOption('localRouteMap')) {
        $dir = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.Config::getInstance()->getOption('localRouteMap');
        $files[$dir] = array_diff(scandir($dir), ['..', '.']);
    }
    foreach ($files as $dir => $items) {
        foreach ($items as $file) {
            if ((!Config::getInstance()->getOption('useNativeRoute') && $file === Settings::getInstance()->getFile()['native']) || (!Config::getInstance()->getOption('useExampleRoute') && $file === Settings::getInstance()->getFile()['example'])){
                continue;
            }
            if (Config::getInstance()->getOption('localRouteMap') && strpos($dir, Config::getInstance()->getOption('localRouteMap'))) {
                $file = DIRECTORY_SEPARATOR.Config::getInstance()->getOption('localRouteMap').DIRECTORY_SEPARATOR.$file;
            }
            else {
                $file = DIRECTORY_SEPARATOR.'local'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.getMessageModule('XwildRestModuleId').DIRECTORY_SEPARATOR.'routes'.DIRECTORY_SEPARATOR.$file;
            }
            if (is_array($ar = require $_SERVER['DOCUMENT_ROOT'].$file)) {
                foreach ($ar as $type => $r) {
                    foreach ($r as $route => $config) {
                        if ($config['documentation']['exclude']['admin'])
                            continue;
                        $routes[$type][$file][$route] = $config;
                    }
                }
            }
        }
    }

    $groups = [];
    $result = CGroup::GetList($by = 'NAME', $order = 'ASC', ['ACTIVE' => 'Y', 'ANONYMOUS' => 'N']);
    while ($group = $result->fetch()) {
        $groups[$group['ID']] = $group['NAME'];
    }
    $pathClass = [];

    function printRoutes($data, $type, $groups, &$pathClass) {
        if (!$data) {
            ?>
            <div class="routes-empty"><i class="far fa-folder-open"></i> ...</div>
            <?php
            return false;
        }

        ?>
        <div class="routes <?= strtolower($type) ?>">
            <?php foreach ($data as $file => $routes): ?>
                <?php foreach ($routes as $route => $config): ?>
                    <div class="route">
                        <ul class="meta">
                            <li class="path">
                                <a title="<?= getMessage('XwildRestEditHint') ?>" href="/bitrix/admin/fileman_file_edit.php?path=<?= $file; ?>&full_src=Y&lang=<?= LANG ?>">
                                    <?= Config::getInstance()->getOption('pathRestApi') !== Helper::getInstance()->ROOT() ? Config::getInstance()->getOption('pathRestApi').'/' : '' ?><?= trim($route, '/'); ?>
                                </a>
                            </li>
                            <?php if ($config['controller']): ?>
                                <li class="controller">
                                    <?php
                                        if (strpos($config['controller'], '@') !== false) {
                                            $namespace = explode('@', $config['controller'])[0];

                                            if (!$pathClass[$namespace]) {
                                                if (!class_exists($namespace)) {
                                                    spl_autoload_register(function ($file) {
                                                        $file = strtolower($file);
                                                        $file = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $file).'.php';

                                                        if (is_file($file)) {
                                                            require $file;
                                                        }
                                                    });
                                                }
                                                $reflector = new \ReflectionClass($namespace);
                                                $pathClass[$namespace] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $reflector->getFileName());
                                            }
                                        }
                                        else {
                                            $namespace = str_replace($_SERVER['DOCUMENT_ROOT'], '', $config['controller']);
                                            $pathClass[$namespace] = $namespace;
                                            $config['controller'] = $namespace;
                                        }
                                    ?>
                                    <a title="<?= getMessage('XwildRestEditHint') ?>"
                                       href="/bitrix/admin/fileman_file_edit.php?path=<?= $pathClass[$namespace] ?>&full_src=Y&lang=<?= LANG ?>">
                                        <?= trim($config['controller'], '\\, /') ?>
                                    </a>
                                </li>
                            <? endif ?>
                            <? if ($config['contentType']): ?>
                                <li class="content-type"
                                    title="<?= getMessage('XwildRestContentTypedHint') ?>"><?= $config['contentType'] ?></li>
                            <? endif ?>
                            <? if ((Config::getInstance()->getOption('useToken') || Config::getInstance()->getOption('useLoginPassword')) && $config['security']['auth']['required']): ?>
                                <li class="security-auth"
                                    title="<?= getMessage('XwildRestSecurityAuthHint') ?>"><?= getMessage('XwildRestSecurityAuth') ?></li>
                            <? endif ?>
                            <? if ($config['active'] === false): ?>
                                <li class="disabled"
                                    title="<?= getMessage('XwildRestDisabledHint') ?>"><i class="fas fa-power-off"></i>
                                </li>
                            <? endif ?>
                        </ul>
                        <?php if ($config['description']): ?>
                            <div class="description">
                                <section>
                                    <div class="header"><i
                                                class="fas fa-file-alt"></i><?= getMessage('XwildRestDescription') ?></div>
                                    <div class="body"><?= $config['description'] ?></div>
                                </section>
                            </div>
                        <?php endif ?>
                        <?php if ($config['parameters']): ?>
                            <section>
                                <div class="header"><i class="fas fa-upload"></i><?= getMessage('XwildRestParameters') ?>
                                </div>
                                <div class="body">
                                    <?php
                                        foreach ($config['parameters'] as $code => $ar) { // level one
                                            printParam(1, $code, $ar);
                                            if ($ar['parameters']) { // level two
                                                foreach ($ar['parameters'] as $paramCode => $ar) {
                                                    // If: param => value
                                                    if (is_string($paramCode)) {
                                                        printParam(2, $paramCode, $ar, $code);
                                                    } // If: params => array
                                                    elseif (is_integer($paramCode) && is_array($ar)) {
                                                        foreach ($ar as $paramCode => $ar) {
                                                            printParam(2, $paramCode, $ar, $code);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    ?>
                                </div>
                            </section>
                        <?php endif ?>
                        <?php if ($config['example']['request']['url'] && $config['example']['request']['response']['json']): ?>
                            <section>
                                <div class="header"><i
                                            class="fas fa-download"></i><?= getMessage('XwildRestResponseExample') ?>
                                </div>
                                <div class="body" style="margin-bottom: 15px">
                                    <?
                                        $config['example']['request']['url'] = str_replace('#DOMAIN#', $_SERVER['HTTP_HOST'], $config['example']['request']['url']);
                                        $config['example']['request']['url'] = str_replace('#API#', Config::getInstance()->getOption('pathRestApi'), $config['example']['request']['url']);
                                    ?>
                                    <a href="<?= $config['example']['request']['url'] ?>"
                                       target="_blank"><?= $config['example']['request']['url'] ?></a>
                                </div>
                                <div class="body">
                                    <pre><?= json_encode(json_decode($config['example']['request']['response']['json']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                                </div>
                            </section>
                        <?php endif ?>
                        <?php if ((Config::getInstance()->getOption('useToken') || Config::getInstance()->getOption('useLoginPassword')) && $config['security']['auth']['required'] && ($config['security']['login']['whitelist'] || $config['security']['token']['whitelist'] || $config['security']['group']['whitelist'])): ?>
                            <div class="whitelist" style="grid-template-columns: 1fr 1fr 1fr;">
                                <? if (Config::getInstance()->getOption('useLoginPassword') && $config['security']['login']['whitelist']): ?>
                                    <section>
                                        <div class="header"><i
                                                    class="fas fa-key"></i><?= getMessage('XwildRestSecurityUsersWhitelist') ?>
                                        </div>
                                        <div class="body">
                                            <? foreach ($config['security']['login']['whitelist'] as $item): ?>
                                                <div class="row">
                                                    <div class="cell"><?= $item ?></div>
                                                </div>
                                            <? endforeach ?>
                                        </div>
                                    </section>
                                <?php endif ?>
                                <?php if (Config::getInstance()->getOption('useToken') && $config['security']['token']['whitelist']): ?>
                                    <section>
                                        <div class="header"><i
                                                    class="fas fa-key"></i><?= getMessage('XwildRestSecurityTokensWhitelist') ?>
                                        </div>
                                        <div class="body">
                                            <? foreach ($config['security']['token']['whitelist'] as $item): ?>
                                                <div class="row">
                                                    <div class="cell"><?= $item ?></div>
                                                </div>
                                            <? endforeach ?>
                                        </div>
                                    </section>
                                <?php endif ?>
                                <?php if ($config['security']['group']['whitelist']): ?>
                                    <section>
                                        <div class="header"><i
                                                    class="fas fa-user-friends"></i><?= getMessage('XwildRestSecurityGroupsWhitelist') ?>
                                        </div>
                                        <div class="body">
                                            <?php foreach ($config['security']['group']['whitelist'] as $id): ?>
                                                <?php if ($groups[$id]): ?>
                                                    <div class="row">
                                                        <div class="cell"><?= $groups[$id] ?></div>
                                                    </div>
                                                <?php endif ?>
                                            <?php endforeach ?>
                                        </div>
                                    </section>
                                <? endif ?>
                            </div>

                        <? endif ?>
                    </div>
                    <br>
                <? endforeach ?>
            <? endforeach ?>
        </div>
        <?php
    }
    function printParam($level, $code, $ar, $parentCode = null) {
        if ($level === 1):
            ?>
            <div class="row">
                <div class="cell"><?= $code ?></div>
                <div class="cell"><?= $ar['type'] ?></div>
                <div class="cell"><?= $ar['required'] ? '<span class="required">required</span>' : 'optional'; ?></div>
                <div class="cell" style="max-width: 600px;"><?= $ar['possibleValue'] ? implode('<span style="color: #c6c6c6; margin: 0 2px">,</span>', $ar['possibleValue']) : '<span style="color: #c6c6c6">-</span>' ?></div>
                <div class="cell"><?= $ar['description'] ?></div>
            </div>
        <?
        else:
            ?>
            <div class="row">
                <div class="cell"><?= $parentCode.' -> '.$code ?></div>
                <div class="cell"><?= $ar['type'] ?></div>
                <div class="cell"<?= $ar['required'] ? '<span class="required">required</span>' : 'optional'; ?></div>
                <div class="cell" style="max-width: 600px;"><?= $ar['possibleValue'] ? implode('<span style="color: #c6c6c6; margin: 0 2px">,</span>', $ar['possibleValue']) : '<span style="color: #c6c6c6">-</span>' ?></div>
                <div class="cell"><?= $ar['description'] ?></div>
            </div>
        <?
        endif;
    }

    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';
    $tabControl->Begin();
    $tabControl->BeginNextTab();
    printRoutes($routes[Helper::getInstance()->GET()], Helper::getInstance()->GET(), $groups, $pathClass);
    $tabControl->BeginNextTab();
    printRoutes($routes[Helper::getInstance()->POST()], Helper::getInstance()->POST(), $groups, $pathClass);
    $tabControl->BeginNextTab();
    printRoutes($routes[Helper::getInstance()->PUT()], Helper::getInstance()->PUT(), $groups, $pathClass);
    $tabControl->BeginNextTab();
    printRoutes($routes[Helper::getInstance()->PATCH()], Helper::getInstance()->PATCH(), $groups, $pathClass);
    $tabControl->BeginNextTab();
    printRoutes($routes[Helper::getInstance()->DELETE()], Helper::getInstance()->DELETE(), $groups, $pathClass);
    $tabControl->BeginNextTab();
    printRoutes($routes[Helper::getInstance()->HEAD()], Helper::getInstance()->HEAD(), $groups, $pathClass);
    $tabControl->End();

    require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
