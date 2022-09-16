<?php
    namespace Xwild\Restapi\Controllers\Native;


    use Xwild\Restapi\Foundation\Config;
    use Xwild\Restapi\Foundation\Response;
    use Xwild\Restapi\Foundation\Settings;

    class Documentation {
        public function __construct() {
            if (!Config::getInstance()->getOption('useNativeRoute')) {
                Response::getInstance()->json('The use of native routes is disabled in the settings');
            }
        }
        public function get() {
            $routes = [];
            $dir = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'routes';
            $files[$dir] = array_diff(scandir($dir), ['..', '.']);

            if (Config::getInstance()->getOption('localRouteMap')) {
                $dir = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.Config::getInstance()->getOption('localRouteMap');
                $files[$dir] = array_diff(scandir($dir), ['..', '.']);
            }

            foreach ($files as $dir => $items) {
                foreach ($items as $file) {
                    if ($file === Settings::getInstance()->getFile()['example']){
                        continue;
                    }

                    $file = $dir.DIRECTORY_SEPARATOR.$file;
                    if (is_array($ar = require $file)) {
                        foreach ($ar as $type => $r) {
                            foreach ($r as $route => $config) {
                                if ($config['active'] === false || $config['documentation']['exclude']['admin'] || $config['documentation']['exclude']['public']){
                                    continue;
                                }
                                unset($config['controller'], $config['security']['login'], $config['security']['token'], $config['security']['group'], $config['example'], $config['documentation']);
                                $routes[$type][$route] = $config;
                            }
                        }
                    }
                }
            }
            Response::getInstance()->json($routes);
        }
    }
