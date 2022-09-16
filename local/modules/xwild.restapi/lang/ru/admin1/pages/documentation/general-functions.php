<?php
    $MESS = [
        'XwildRestContent' => '<div class="paragraphs functions">
    <div class="paragraph">
        <div class="meta">
            <ul>
                <li>request()</li>
            </ul>
        </div>
        <div class="description">
            <section>
                <div class="header"><i class="fas fa-file-alt"></i>Описание</div>
                <div class="body">Функция возвращает данные по текущему запросу.</div>
            </section>
        </div>
        <section>
            <div class="header"><i class="fas fa-cogs"></i>Методы</div>
            <div class="body">
                <div class="row">
                    <div class="cell">Request::getInstance()->getData($code = "")</div>
                    <div class="cell">$code - код параметра</div>
                    <div class="cell">Параметры запроса</div>
                </div>
                <div class="row">
                    <div class="cell">Request::getInstance()->header($code = "")</div>
                    <div class="cell">$code - код заголовка</div>
                    <div class="cell">Заголовки запроса</div>
                </div>
                <div class="row">
                    <div class="cell">Request::getInstance()->method()</div>
                    <div class="cell"></div>
                    <div class="cell">Тип запроса</div>
                </div>
                <div class="row">
                    <div class="cell">Request::getInstance()->ip()</div>
                    <div class="cell"></div>
                    <div class="cell">Ip-адрес клиента</div>
                </div>
                <div class="row">
                    <div class="cell">Request::getInstance()->path()</div>
                    <div class="cell"></div>
                    <div class="cell">Путь запроса</div>
                </div>
                <div class="row">
                    <div class="cell">Request::getInstance()->map()</div>
                    <div class="cell"></div>
                    <div class="cell">Карта роута для обработки запроса</div>
                </div>
            </div>
        </section>
    </div>
    <div class="paragraph">
        <div class="meta">
            <ul>
                <li>response()</li>
            </ul>
        </div>
        <div class="description">
            <section>
                <div class="header"><i class="fas fa-file-alt"></i>Описание</div>
                <div class="body">Функция возвращает ответ клиенту. Имеется набор предустановленных ответов, которые
                    реализованы согласно
                    <a href="https://ru.wikipedia.org/wiki/Список_кодов_состояния_HTTP" rel="nofollow">стандартам
                        HTTP</a>.
                </div>
            </section>
        </div>
        <section>
            <div class="header"><i class="fas fa-cogs"></i>Методы</div>
            <div class="body">
                <div class="row">
                    <div class="cell">response()->json($data = [], $statusCode = 200, $options = [], $headers = [])
                    </div>
                </div>
                #METHODS#
            </div>
        </section>
    </div>
    <div class="paragraph">
        <div class="meta">
            <ul>
                <li>cache()</li>
            </ul>
        </div>
        <div class="description">
            <section>
                <div class="header"><i class="fas fa-file-alt"></i>Описание</div>
                <div class="body">
                    Функция для работы с системным кэшем. Является обёрткой над системным классом Cache. Используется
                    кэш <a href="/bitrix/admin/perfmon_panel.php?lang=ru">установленный в настройках системы</a>.
                </div>
            </section>
        </div>
        <section>
            <div class="header"><i class="fas fa-cogs"></i>Методы</div>
            <div class="body">
                <div class="row">
                    <div class="cell">cache()->get($id, $ttl = 86400, $dir = false)</div>
                    <div class="cell">
                        $id - идентификатор
                        <br>$ttl - время жизни
                        <br>$dir - директория
                    </div>
                    <div class="cell">Получение кэша по ID, если кэша нет, тогда инициализирует его</div>
                </div>
                <div class="row">
                    <div class="cell">cache()->set($data)</div>
                    <div class="cell">
                        $data - данные для кэша
                    </div>
                    <div class="cell">Сохранение кэша в ранее инициализированный кэш</div>
                </div>
                <div class="row">
                    <div class="cell">cache()->clear($dir)</div>
                    <div class="cell">
                        $dir - директория кэша
                    </div>
                    <div class="cell">Очистка кэша</div>
                </div>
            </div>
        </section>
    </div>
    <div class="paragraph">
        <div class="meta">
            <ul>
                <li>journal()</li>
            </ul>
        </div>
        <div class="description">
            <section>
                <div class="header"><i class="fas fa-file-alt"></i>Описание</div>
                <div class="body">
                    Функция для работы c журналами. В настоящее время, для работы доступен только тип журнала
                    request-response.
                </div>
            </section>
        </div>
        <section>
            <div class="header"><i class="fas fa-cogs"></i>Методы</div>
            <div class="body">
                <div class="row">
                    <div class="cell">journal()->add($type, $data = [])</div>
                    <div class="cell">
                        $type - тип журнала
                        <br>$data - данные
                    </div>
                    <div class="cell">Запись данных в журнал, поддерживаются ключи request, response</div>
                </div>
            </div>
        </section>
    </div>
    <div class="paragraph">
        <div class="meta">
            <ul>
                <li>db()</li>
            </ul>
        </div>
        <div class="description">
            <section>
                <div class="header"><i class="fas fa-file-alt"></i>Описание</div>
                <div class="body">
                    Функция возвращает текущее подключение к базе данных.
                    <br>Является обёрткой над системным методом getConnection() класса Application.
                </div>
            </section>
        </div>
        <section>
            <div class="header"><i class="fas fa-cogs"></i>Методы</div>
            <div class="body">
                <div class="row">
                    <div class="cell">db()->query($sql)</div>
                    <div class="cell">$sql - строка SQL-запроса</div>
                    <div class="cell">Выполнение запроса</div>
                </div>
            </div>
        </section>
    </div>
</div>'
    ];
