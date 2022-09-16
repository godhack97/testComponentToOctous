<?php

namespace App\Controllers;

use Bitrix\Main\Engine\ActionFilter;
use App\Helpers;

/**
 * Наследник класса контроллеров битрикса
 *
 * Исключения тоже обрабатываются в json
 * При включении debug в .settings.php показывается вывод вид ошибки
 *
 * @link https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=105&LESSON_ID=6436
 */
class BaseController extends \Bitrix\Main\Engine\Controller
{
    const CATALOG_ID = 2;
    const OFFERS_ID = 8;

    protected string $deviceId;
    protected string $lang;
    protected string $token;
    protected string $os;
    protected string $currency;
    protected string $version;

    protected array $translation = [];
    /**
     * Переопределяем префильтры
     * Удаляем csrf и обязательную авторизацию
     */
    protected function getDefaultPreFilters(): array
    {
        return [
            new ActionFilter\HttpMethod(
                [ActionFilter\HttpMethod::METHOD_GET, ActionFilter\HttpMethod::METHOD_POST]
            ),
            new ActionFilter\Csrf(false)
        ];
    }

    /**
     * Запуск экшена в контроллере
     *
     * Запускает стандартную обработку контроллера битрикс
     * Ответ отдает в json
     *
     * @param string $action
     */
    public function runAction(string $action): void
    {
        global $USER;

        $this->checkHeaders();

        if ($this->token) {
            $userId = Helpers\User::getUserIdByToken($this->token);
            $USER->Authorize($userId);
        }

        $app = \Bitrix\Main\Application::getInstance();
        $app->runController($this, $action);
    }

    protected function checkHeaders()
    {
        $headers = getallheaders();

        if (!$headers["id"]) {
            throw new \Exception("Не передан заголовок id");
        }

        if (!$headers["lang"]) {
            throw new \Exception("Не передан заголовок lang");
        }

        if (!$headers["os"]) {
            throw new \Exception("Не передан заголовок os");
        }

        if (!$headers["currency"]) {
            throw new \Exception("Не передан заголовок currency");
        }

        if (!$headers["version"]) {
            throw new \Exception("Не передан заголовок version");
        }

        $this->token = $headers["token"] ?? "";
        $this->deviceId = $headers["id"];
        $this->lang = $headers["lang"];
        $this->os = $headers["os"];
        $this->currency = $headers["currency"];
        $this->version = $headers["version"];

        $this->translation = Helpers\Language::getLanguage($this->lang);
        return true;
    }
}
