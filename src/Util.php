<?php


namespace Crmdesenvolvimentos\ApiBraspress;


use ArrayAccess;
use Closure;

final class Util
{

    /**
     * @param string $name
     * @return string
     */
    public static function camel_case($name)
    {
        $string = str_replace('-', ' ', $name);
        return str_replace(' ', '', lcfirst(ucwords($string)));
    }


    /**
     * @param string $value
     * @return bool
     */
    public static function validateCnpjOrCpf($value)
    {
        $length = strlen(preg_replace('/\D/', '', $value));
        if ($length == 14) {
            return self::validateCnpj($value);
        }
        return self::validateCpf($value);
    }


    /**
     * @param string $value
     * @return bool
     */
    public static function validateCpf($value)
    {
        $c = preg_replace('/\D/', '', $value);

        if (strlen($c) != 11 || preg_match("/^{$c[0]}{11}$/", $c)) {
            return false;
        }

        for ($s = 10, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--) ;

        if ($c[9] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        for ($s = 11, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--) ;

        if ($c[10] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        return true;
    }


    /**
     * @param string $value
     * @return bool
     */
    public static function validateCnpj($value)
    {
        $c = preg_replace('/\D/', '', $value);

        $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        if (strlen($c) != 14) {
            return false;
        } elseif (preg_match("/^{$c[0]}{14}$/", $c) > 0) {
            return false;
        }

        for ($i = 0, $n = 0; $i < 12; $n += $c[$i] * $b[++$i]) ;

        if ($c[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        for ($i = 0, $n = 0; $i <= 12; $n += $c[$i] * $b[$i++]) ;

        if ($c[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        return true;
    }


    /**
     * @param $value
     * @return bool
     */
    public static function validateCep($value)
    {
        return strlen(preg_replace('/\D/', '', $value)) == 8;
    }


    /**
     * @param string $value
     * @return string|string[]|null
     */
    public static function onlyNumbers($value)
    {
        return preg_replace('/\D/', '', $value);
    }


    /**
     * @param $dateTime
     * @return string
     */
    public static function toDateTime($dateTime)
    {
        if (!is_null($dateTime))
            $date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $dateTime)));
        else
            $date = null;

        return $date === false ? null : $date;
    }


    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param mixed $target
     * @param string|array|int|null $key
     * @param mixed $default
     * @return mixed
     */
    public static function data_get($target, $key, $default = null)
    {
        function value($value, ...$args)
        {
            return $value instanceof Closure ? $value(...$args) : $value;
        }

        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        foreach ($key as $i => $segment) {
            unset($key[$i]);

            if (is_null($segment)) {
                return $target;
            }

            if ($segment === '*') {

                if (!is_iterable($target)) {
                    return value($default);
                }

                $result = [];

                foreach ($target as $item) {
                    $result[] = self::data_get($item, $key);
                }

                return in_array('*', $key) ? self::collapse($result) : $result;
            }

            if ((is_array($target) || $target instanceof ArrayAccess) && self::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param \ArrayAccess|array $array
     * @param string|int $key
     * @return bool
     */
    public static function exists($array, $key)
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        if (is_float($key)) {
            $key = (string)$key;
        }

        return array_key_exists($key, $array);
    }

    /**
     * Collapse an array of arrays into a single array.
     *
     * @param array $array
     * @return array
     */
    public static function collapse($array)
    {
        $results = [];

        foreach ($array as $values) {
            if (!is_array($values)) {
                continue;
            }

            $results[] = $values;
        }

        return array_merge([], ...$results);
    }

}
