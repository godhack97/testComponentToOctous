<?php

namespace App;

use Bitrix\Main\Context;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

class Locale
{
    public static function setLocaleAndCurency()
    {
        global $APPLICATION;

        $request = Context::getCurrent()->getRequest();
        $language = $request->getQuery("language");
        $currency = $request->getQuery("currency");

        if ($language) {
            switch ($language) {
                case "RU":
                    self::setCookie("CATALOG_CURRENCY", "RUB");
  
                    LocalRedirect($APPLICATION->GetCurPageParam('', ['language']));
                    break;
                case "EN":
                    self::setCookie("CATALOG_CURRENCY", "USD");
  
                    LocalRedirect($APPLICATION->GetCurPageParam('', ['language']));
                    break;
            }
        }

        if ($currency) {
            self::setCookie("CATALOG_CURRENCY", strtoupper($currency));
            LocalRedirect($APPLICATION->GetCurPageParam('', ['currency']));
        }
    }

    public static function getLangAndCurrency(): array
    {
        list($currency, $language) = (SITE_ID === "s1") ? ["rub", "RU"] : ["usd", "EN"];

        $GLOBALS["LANG"] = $language;

        if ($_COOKIE["CATALOG_CURRENCY"]) {
            switch ($_COOKIE["CATALOG_CURRENCY"]) {
                case "RUB":
                    $currency = "rub";
                    break;
                case "USD":
                    $currency = "usd";
                    break;
                case "AED":
                    $currency = "aed";
                    break;
            }
        }

        return [
            "lang"     => $language,
            "currency" => $currency,
        ];
    }

    public static function setCookie(string $name, string $value = "")
    {
        if (!$name) {
            return;
        }
        if (!$value) {
            $value = "";
        }
        setcookie($name, $value, strtotime('+180 days'), "/");
        // $cookie = new Cookie($name, $value, time() + 86400 * 180);

        // $cookie->setSpread(\Bitrix\Main\Web\Cookie::SPREAD_DOMAIN);
        // $cookie->setDomain(SITE_SERVER_NAME);
        // $cookie->setPath("/");
        // $cookie->setSecure(false);
        // $cookie->setHttpOnly(false);
        // Application::getInstance()->getContext()->getResponse()->addCookie($cookie);
    }
}
