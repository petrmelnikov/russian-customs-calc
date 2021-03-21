<?php

namespace App;

class Utils {

    public static function filterMoneyValue(string $value): float {
        if (
            (
                strstr($value, ',') &&
                strstr($value, '.')
            ) ||
            strstr($value, '$')
        ) {
            $value = str_replace(',', '', $value);
        } else {
            $value = str_replace(',', '.', $value);
        }
        $value = preg_replace('/[^\\d.]+/', '', $value);
        return (float) $value;
    }
}
