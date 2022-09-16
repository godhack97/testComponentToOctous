<?php

use Bitrix\Main\Routing\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    // api v1
    $routes->prefix("api")->group(function (RoutingConfigurator $routes) {
        $routes->prefix("v1")->group(function (RoutingConfigurator $routes) {

            // версия API
            $routes->get("version", function () {
                return (new App\Controllers\Version())->runAction("getVersion");
            });

            // главная страница
            $routes->get("main", function () {
                return (new App\Controllers\Main())->runAction("getMain");
            });

            // список разделов
            $routes->get("sections", function () {
                return (new App\Controllers\Sections())->runAction("getSections");
            });

            // контакты
            $routes->get("contacts", function () {
                return (new App\Controllers\Contacts())->runAction("getContacts");
            });

            // новости
            $routes->get("news", function () {
                return (new App\Controllers\News())->runAction("getNews");
            });

            // список товаров
            $routes->get("products/{section}", function ($section) {
                return (new App\Controllers\Products())->runAction("getProducts");
            });

            // получить товар
            $routes->get("product/{productID}", function ($productID) {
                return (new App\Controllers\Product())->runAction("getProduct");
            });

            // поиск
            $routes->get("search", function () {
                return (new App\Controllers\Search())->runAction("getResult");
            });

            // логин
            $routes->post("login", function () {
                return (new App\Controllers\Login())->runAction("getResult");
            });

            // logout
            $routes->post("logout", function () {
                return (new App\Controllers\Logout())->runAction("logout");
            });

            // регистрация
            $routes->post("registration", function () {
                return (new App\Controllers\Register())->runAction("registration");
            });

            // получить информацию о корзине
            $routes->get("get_basket", function () {
                return (new App\Controllers\Basket())->runAction("getBasket");
            });

            // добавить в корзину
            $routes->post("add_to_cart", function () {
                return (new App\Controllers\Basket())->runAction("addToBasket");
            });

            // уменьшить кол-ва товара в корзине
            $routes->post("decrease_in_cart", function () {
                return (new App\Controllers\Basket())->runAction("decreaseBasket");
            });

            // удалить товар из корзины
            $routes->post("delete_in_cart", function () {
                return (new App\Controllers\Basket())->runAction("deleteInBasket");
            });

            // применить купон
            $routes->post("apply_coupon", function () {
                return (new App\Controllers\Basket())->runAction("applyCoupon");
            });

            // применить купон
            $routes->post("delete_coupon", function () {
                return (new App\Controllers\Basket())->runAction("deleteCoupon");
            });

            // добавить товар в избранное
            $routes->post("add_favorites", function () {
                return (new App\Controllers\Favorites())->runAction("add");
            });

            // получить список избранных товаров
            $routes->get("get_favorites", function () {
                return (new App\Controllers\Favorites())->runAction("get");
            });

            // удалить товар из избранного
            $routes->post("delete_favorites", function () {
                return (new App\Controllers\Favorites())->runAction("delete");
            });

            // получить профиль пользователя
            $routes->get("profile", function () {
                return (new App\Controllers\Profile())->runAction("get");
            });

            // изминить данные пользователя
            $routes->post("profile", function () {
                return (new App\Controllers\Profile())->runAction("update");
            });

            // получить список информации
            $routes->get("content_list", function () {
                return (new App\Controllers\Content())->runAction("getList");
            });

            // получить информация
            $routes->get("content/{id}", function ($id) {
                return (new App\Controllers\Content())->runAction("getById");
            });

            // список моих "заказов"
            $routes->get("my_order_list", function () {
                return (new App\Controllers\MyOrder())->runAction("getList");
            });

            // детально "мой заказ"
            $routes->get("my_order", function () {
                return (new App\Controllers\MyOrder())->runAction("getById");
            });

            // проверка смс кода
            $routes->post("verify_phone", function () {
                return (new App\Controllers\Register())->runAction("verifyPhoneCode");
            });

            // получить адреса
            $routes->get("addresses", function () {
                return (new App\Controllers\Addresses())->runAction("getList");
            });

            // дабавить адрес
            $routes->post("add_address", function () {
                return (new App\Controllers\Addresses())->runAction("add");
            });

            // удалить адрес
            $routes->post("del_address", function () {
                return (new App\Controllers\Addresses())->runAction("delete");
            });

            // получить один адрес
            $routes->get("address", function () {
                return (new App\Controllers\Addresses())->runAction("get");
            });

            // редактировать адрес
            $routes->post("address", function () {
                return (new App\Controllers\Addresses())->runAction("edit");
            });

            // получить сумму заказа и способы доставки
            $routes->get("order", function () {
                return (new App\Controllers\Orders())->runAction("get");
            });

            // создать заказ
            $routes->post("order", function () {
                return (new App\Controllers\Orders())->runAction("create");
            });

            // подучить местоположения
            $routes->get("location", function () {
                return (new App\Controllers\Location())->runAction("get");
            });

            // test
            $routes->get("test", function () {
                return (new App\Controllers\Test())->runAction("test");
            });
        });
    });
};
