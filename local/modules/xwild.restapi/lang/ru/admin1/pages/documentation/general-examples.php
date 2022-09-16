<?php
$MESS = [
    'XwildRestContent' => '<div class="paragraphs examples">
    <div class="paragraph">
        <div class="meta">
            <ul>
                <li>Добавление карты роутов</li>
            </ul>
        </div>
        <div class="description">
            <section>
                <div class="header"><i class="fas fa-file-alt"></i>Описание</div>
                <div class="body">
                    Карты роутов хранятся в директории, расположенной по пути <a href="/bitrix/admin/fileman_admin.php?lang=#LANG&path=/bitrix/modules/#MODULE_ID#/routes/&show_perms_for=0">/bitrix/modules/#MODULE_ID#/routes/</a>.
                    <br>Но, начиная с версии 2.7.0, вы можете указать собственный путь к картам роутов. Например, /local/api/routes/.
                    <br>Для добавления карты нужно создать php-файл, возвращающий массив, описывающий необходимые роуты.
                    <br>За основу можно взять карту-пример расположенную по пути <a href="/bitrix/admin/fileman_file_edit.php?path=/bitrix/modules/#MODULE_ID#/routes/' . settings()->get('file')['example'] . '&full_src=Y&lang=#LANG#">/bitrix/modules/#MODULE_ID#/routes/' . settings()->get('file')['example'] . '</a>.
                    <br>Также, можно ознакмоиться с картой нативных роутов, расположенных по пути <a href="/bitrix/admin/fileman_file_edit.php?path=/bitrix/modules/#MODULE_ID#/routes/' . settings()->get('file')['native'] . '&full_src=Y&lang=#LANG#">/bitrix/modules/#MODULE_ID#/routes/' . settings()->get('file')['native'] . '</a>.
                    <br>Количество карт роутов не ограничено.
                </div>
            </section>
        </div>
    </div>

    <div class="paragraph">
        <div class="meta">
            <ul>
                <li>Добавление роута в карту</li>
            </ul>
        </div>
        <div class="description">
            <section>
                <div class="header"><i class="fas fa-file-alt"></i>Описание</div>
                <div class="body">
                    Главное при добавление роута - сохранять структуру массива.
                    <br>Минимально необходимый набор ключей для работы роута: Тип запроса, Роут, Контроллер.
                    <br>Все возможные ключи описаны в карте-примере, расположенной по пути <a href="/bitrix/admin/fileman_file_edit.php?path=/bitrix/modules/#MODULE_ID#/routes/' . settings()->get('file')['example'] . '&full_src=Y&lang=#LANG#">/bitrix/modules/#MODULE_ID#/routes/' . settings()->get('file')['example'] . '</a>.
                </div>
            </section>
        </div>
    </div>

    <div class="paragraph">
        <div class="meta">
            <ul>
                <li>Добавление контроллера</li>
            </ul>
        </div>
        <div class="description">
            <section>
                <div class="header"><i class="fas fa-file-alt"></i>Описание</div>
                <div class="body">
                    По умолчанию контроллеры хранятся в директории расположенной по пути <a href="/bitrix/admin/fileman_admin.php?lang=#LANG#&show_perms_for=0&path=/bitrix/modules/#MODULE_ID#/lib/controllers/">/bitrix/modules/#MODULE_ID#/lib/controllers/</a>.
                    <br>Контроллером является обычный класс с методами.
                    <br>За основу можно взять классы из нативных контроллеров: <a href="/bitrix/admin/fileman_admin.php?lang=#LANG#&show_perms_for=0&path=/bitrix/modules/#MODULE_ID#/lib/controllers/native">/bitrix/modules/#MODULE_ID#/lib/controllers/native</a>.
                    <br>Контроллер может располагаться за пределами модуля, главное чтобы он был доступен через пространство имён.
                    <br>Также, начиная с версии 2.3.0, вернули поддержку файлового режима работы. То есть, в качестве контроллера можно указать путь к php-скрипту, вместо указания неймспейса контроллера.
                </div>
            </section>
        </div>
    </div>
    <div class="paragraph">
        <div class="meta">
            <ul>
                <li>Работа с кэшем</li>
            </ul>
        </div>
        <div class="description">
            <section>
                <div class="header"><i class="fas fa-file-alt"></i>Описание</div>
                <div class="body">
                    Взаимодействие с системным кэшем происходит за счет функции cache().
                    <br>Общий смысл таков: сначала проверяем наличие необходимого кэша, если его нет, тогда создаем
                    его.
                    <br>В следующий раз, данные уже будут подгружаться из кэша.
                    <br>Весь кэш сохраняется в директорию <a href="/bitrix/admin/fileman_admin.php?lang=#LANG#&show_perms_for=0&path=/bitrix/cache/">/bitrix/cache/</a>.
                    <br>Пример реализации работы с кэшем можно посмотреть здесь: <a href="/bitrix/admin/fileman_file_edit.php?path=/bitrix/modules/#MODULE_ID#/lib/controllers/native/example.php&full_src=Y&lang=#LANG#">/bitrix/modules/#MODULE_ID#/lib/controllers/native/example.php</a>.
                </div>
            </section>
        </div>
        <div class="description">
            <section>
                <div class="header"><i class="fas fa-code"></i>Пример работы с кэшем</div>
                <div class="body">
                        <pre>
namespace Xwild\Restapi\Controllers\Example;

class Check
{
    public function read()
    {
        // Отдадим данные из кэша если они в нем имеются
        // Иначе, получим данные из базы и запишем в кэш
        // $cacheId - уникальный идентификатор кэша
        // Срок кэша - 7 дней
        // Место хранения /bitrix/cache/example/check
        if (!$response = cache()->get($cacheId, 604800, \'example/check\')) {
            // Массив ответа для клиента
            // Какие-то данные из базы
            $response = [1, 2, 3];
            // Сохраняем данные в кэш чтобы при следующем запросе уже не делать запросы в базу
            if ($response) {
                cache()->set($response);
            }
        }
        // Возвращаем ответ клиенту
        response()->json($response);
        // Отдадим данные из кэша если они в нем имеются
        // Иначе, получим данные из базы и запишем в кэш
        // $cacheId - уникальный идентификатор кэша
        // Срок кэша - 7 дней
        // Место хранения /bitrix/cache/example/check
        if (!$response = cache()->get($cacheId, 604800, \'example/check\')) {
            // Массив ответа для клиента
            // Какие-то данные из базы
            $response = [1, 2, 3];
            // Сохраняем данные в кэш чтобы при следующем запросе уже не делать запросы в базу
            if ($response) {
                cache()->set($response);
            }
        }
        // Возвращаем ответ клиенту
        response()->json($response);
    }
}
                </pre>
                </div>
            </section>
        </div>
    </div>

    <div class="paragraph">
        <div class="meta">
            <ul>
                <li>Добавление записи в журнал</li>
            </ul>
        </div>
        <div class="description">
            <section>
                <div class="header"><i class="fas fa-file-alt"></i>Описание</div>
                <div class="body">Запись в журнал происходит с помощью функции journal().
                </div>
            </section>
        </div>

        <div class="description">
            <section>
                <div class="header"><i class="fas fa-code"></i>Пример добавления записи в журнал</div>
                <div class="body">
                        <pre>
namespace Xwild\Restapi\Controllers\Example;

class Check
{
    public function read()
    {
        // Запишем информацию в Журнал: Входящие запросы
        journal()->add(\'request-response\', [\'request\' => Request::getInstance()->getData(), \'response\' => $response]);
    }
}
                </pre>
                </div>
            </section>
        </div>
    </div>
</div>'
];
