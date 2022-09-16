<?php
    namespace Sodamoda\Core;

    use Bitrix\Main\Config\Option;
    use Bitrix\Main\Data\Cache;

    class Instagram {
        private static $instance = null;
        private $apiBaseUrl = 'https://api.instagram.com/';
        private $graphBaseUrl = 'https://graph.instagram.com/';
        private $limit = 9;

        public function getMediaList($token, $block) {
            return $this->getMediaListData($token, $block);
        }

        public function getMediaListData($token, $block) {
            $cache = Cache::createInstance();

            $mediaUser = [];
            $cacheID = 'instaMediaList_'.$block;
            if ($cache->initCache(86400 * 7, $cacheID)) {
                $mediaUser = $cache->getVars();
            }
            elseif ($cache->startDataCache()) {
                $instagram_cnct = curl_init(); // инициализация cURL подключения
                $multi = curl_multi_init(); // инициализируем мультипоточный запрос cURL

                $instagram_cnct = curl_init(); // инициализация cURL подключения
                curl_setopt($instagram_cnct, CURLOPT_URL, $this->graphBaseUrl."me?fields=id,username,media&access_token=".$token); // подключаемся
                curl_setopt($instagram_cnct, CURLOPT_RETURNTRANSFER, 1); // просим вернуть результат

                // ограничиваем по времени
                curl_setopt($instagram_cnct, CURLOPT_TIMEOUT, 9);
                curl_setopt($instagram_cnct, CURLOPT_CONNECTTIMEOUT, 9);

                $media = json_decode(curl_exec($instagram_cnct)); // получаем и декодируем данные из JSON
				
                $data = $media->media->data;
                // функция array_slice() задает количество элементов, которые нам нужно получить из массива
                // если нам нужно вывести все фото, тогда: foreach($data as $key => $value) {
                foreach (array_slice($data, 0, $this->limit) as $key => $value) {
                    $data_ids[] = $data[$key]->id; // создаем массив с id медиафайлов
                }

                foreach ($data_ids as $data_id) {
                    // Записываем данные по каждому отдельномуэлементу в массив
                    $item = curl_init();
                    curl_setopt($item, CURLOPT_URL, $this->graphBaseUrl.$data_id."?fields=media_url,media_type,thumbnail_url,caption,permalink&access_token=".$token); // получаем данные файла
                    curl_setopt($item, CURLOPT_HEADER, false);
                    curl_setopt($item, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($instagram_cnct, CURLOPT_TIMEOUT, 9);
                    curl_setopt($instagram_cnct, CURLOPT_CONNECTTIMEOUT, 9);

                    curl_multi_add_handle($multi, $item);

                    $img[$data_id] = $item;
                }

                // читаем заклинания
                $active = null;
                do {
                    $mrc = json_decode(curl_multi_exec($multi, $active));
                }
                while ($mrc == CURLM_CALL_MULTI_PERFORM);

                while ($active && $mrc == CURLM_OK) {
                    if (curl_multi_select($multi) == -1) {
                        continue;
                    }

                    do {
                        $mrc = json_decode(curl_multi_exec($multi, $active));
                    }
                    while ($mrc == CURLM_CALL_MULTI_PERFORM);
                }

                // Выводим данные элементов
                foreach ($img as $data) {
                    $data = json_decode(curl_multi_getcontent($data));

                    $dataArray = [
                        'link'        => $data->permalink,
                        'thumb'       => $data->media_url,
                        'description' => $data->caption
                    ];
                    if ($data->media_type == "VIDEO") {
                        $dataArray['thumb'] = $data->thumbnail_url;
                    }
                    $mediaUser[] = $dataArray;

                    curl_multi_remove_handle($multi, $img); // удаляем исполненные дескрипторы
                }

                curl_multi_close($multi); // закрываем мультисоединение
                curl_close($instagram_cnct); // закрываем соединение

                $cache->endDataCache($mediaUser); // записываем в кеш
            }

            return $mediaUser;
        }

        public function getLongToken() {
            $instagram_app_id = '312480446762877';
            $instagram_app_secret = '8fa52f0bd72c9a0c7067ed5129887303';
            $instagram_app_url = 'https://sodamoda.ru/dev';
            $apiBaseUrl = 'https://api.instagram.com/';
            $graphBaseUrl = 'https://graph.instagram.com/';

            $get_code = isset($_GET['code']) ? $_GET['code'] : '';
            $accessToken = '';
            $user_id = '';
            $hasUserAccessToken = false;

            $authorizationVars = [
                'app_id'        => $instagram_app_id,
                'redirect_uri'  => $instagram_app_url,
                'scope'         => 'user_profile,user_media',
                'response_type' => 'code'
            ];

            $output = '';

            // формируем ссылку для авторизации
            $authorizationUrl = $apiBaseUrl.'oauth/authorize?'.http_build_query($authorizationVars);

            // проверяем получен ли код
            if (!$get_code) {
                header("Location:$authorizationUrl");
            }

            // формируем ссылку для получения краткосрочного маркера
            $shortAccessToken = [
                'endpoint_url' => $apiBaseUrl.'oauth/access_token',
                'type'         => 'POST',
                'url_params'   => [
                    'app_id'       => $instagram_app_id,
                    'app_secret'   => $instagram_app_secret,
                    'grant_type'   => 'authorization_code',
                    'redirect_uri' => $instagram_app_url,
                    'code'         => $get_code
                ]
            ];

            // формируем ссылку для получения долгосрочного маркера
            $longAccessToken = [
                'endpoint_url' => $graphBaseUrl.'access_token',
                'type'         => 'GET',
                'url_params'   => [
                    'client_secret' => $instagram_app_secret,
                    'grant_type'    => 'ig_exchange_token'
                ]
            ];

            // если полученны данные или получен код
            if ($accessToken) { // при наличии access token
                $userAccessToken = $accessToken;
                $hasUserAccessToken = true;
                $userId = $user_id;
            }
            elseif ($get_code) { // если только GET код то получаем access token

                $response = $this->makeApiCall($shortAccessToken);

                // записываем полученные данные в переменные
                $accessToken = $response[access_token];
                $user_id = $response[user_id];
                $hasUserAccessToken = true;

                // получаем долгосрочный маркер
                $longAccessToken['url_params']['access_token'] = $accessToken;
                $longLivedAccessTokenResponse = $this->makeApiCall($longAccessToken);
                $userAccessToken = $longLivedAccessTokenResponse['access_token'];
                $userAccessTokenExpires = $longLivedAccessTokenResponse['expires_in'];

                Option::set('sodamoda.core', 'insta_user_token', $userAccessToken);
            }

            // функция вызова api и получения данных

            foreach ($longLivedAccessTokenResponse as $key => $value) {
                $output .= $key.': '.$value.'<br>';
            }

            return $output;
        }
        public function updateToken($type){
          $token = false;
          $expires_in = false;
          $token = Option::get('sodamoda.core', 'insta_user_token');
          $expires_in = Option::get('sodamoda.core', 'insta_user_token_date_end');
          
          if($token !== false){
            if((date('U', $expires_in) - 864000) < date('U')){
              $url = "https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=" . $token;
  
              $instagramCnct = curl_init(); // инициализация cURL подключения
              curl_setopt($instagramCnct, CURLOPT_URL, $url); // адрес запроса
              curl_setopt($instagramCnct, CURLOPT_RETURNTRANSFER, 1); // просим вернуть результат
              $response = json_decode(curl_exec($instagramCnct)); // получаем и декодируем данные из JSON
              curl_close($instagramCnct); // закрываем соединение
  
              // обновляем токен и дату его создания в базе
  
              $accessToken = $response->access_token; // обновленный токен
              $expires_in = $response->expires_in; // обновленный токен
              if($expires_in){
                Option::set('sodamoda.core', 'insta_user_token', $accessToken);
                Option::set('sodamoda.core', 'insta_user_token_date_end', date('d.m.Y H:i', date('U') + $expires_in));
              }
            }
          }
        }
        function makeApiCall($params) {
            $ch = curl_init();

            $endpoint = $params['endpoint_url'];

            if ('POST' == $params['type']) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params['url_params']));
                curl_setopt($ch, CURLOPT_POST, 1);
            }
            elseif ('GET' == $params['type']) {
                // добавляем параметры в конец адреса
                $endpoint .= '?'.http_build_query($params['url_params']);
            }

            curl_setopt($ch, CURLOPT_URL, $endpoint);

            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            curl_close($ch);

            $responseArray = json_decode($response, true);

            if (isset($responseArray['error_type'])) {
                var_dump($responseArray);
                die();
            }
            else {
                return $responseArray;
            }
        }

        //base singleton
        public static function getInstance() {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __clone() {}
        private function __construct() {}
        private function __sleep() {}
        private function __wakeup() {}
    }