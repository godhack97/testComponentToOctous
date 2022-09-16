<?php

namespace App\Helpers;

class Arrays
{
    public static function arrayChangeKeyCaseRecursive(array $array, int $case = null): array
    {
        $mutated = [];
        $mutator = $case === CASE_LOWER ? 'strtolower' : 'strtoupper';

        foreach ($array as $key => $value) {
            if (is_string($key)) {
                $key = $mutator($key);
            }

            if (is_array($value)) {
                $value = self::arrayChangeKeyCaseRecursive($value, $case);
            }

            $mutated[$key] = $value;
        }

        return $mutated;
    }
}
