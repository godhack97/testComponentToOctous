<?php

namespace App;

class IpWhoIs
{
    const API_KEY = "PpWCki44I0ZweWRR";
    /**
     * RU - Росссия
     * BY - Булорусь
     * AE - ОАЭ
     *
     * @param string $code
     * @return string
     */
    public static function getCountryCode(string $code = "country_code")
    {
        $ip = $_SERVER["REMOTE_ADDR"];

        $ch = curl_init('http://ipwhois.pro/' . $ip . "?key=" . SELF::API_KEY);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $ipwhois = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $ipwhois[$code];
    }

    public static function getAll(): array
    {
        $ip = $_SERVER["REMOTE_ADDR"];

        $ch = curl_init('http://ipwho.is/' . $ip);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $ipwhois = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $ipwhois;
    }
}
