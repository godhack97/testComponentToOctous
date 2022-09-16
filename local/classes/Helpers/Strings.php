<?php

namespace App\Helpers;

class Strings
{
    public static function declOfNum(string $num, string $titles): string
    {
        $cases = [2, 0, 1, 1, 1, 2];
        return $num . " " . $titles[($num % 100 > 4 && $num % 100 < 20) ? 2 : $cases[min($num % 10, 5)]];
    }
}
