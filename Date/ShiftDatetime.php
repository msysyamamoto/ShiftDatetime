<?php
class ShiftDatetime
{
    protected static $offset = 0;

    public static function offset($offset)
    {
        self::$offset = intval($offset);
    }

    public static function create($time = null, DateTimeZone $timezone = null)
    {
        if ($time === null) {
            $interval = new DateInterval('PT' . abs(self::$offset) . 'S');
            $datetime = new Datetime();
            if (self::$offset >= 0) {
                $datetime->add($interval);
            } else {
                $datetime->sub($interval);
            }
            return $datetime;
        }

        if ($timezone === null) {
            return new Datetime($time);
        }

        return new Datetime($time, $timezone);
    }

    public static function time()
    {
        return time() + self::$offset;
    }

    public static function gettimeofday($return_float = false)
    {
        $ds = gettimeofday();
        $ds['sec'] += self::$offset;
        return $ds;
    }

    public static function microtime($return_float = false)
    {
        if ($return_float === true) {
            return microtime(true) + floatval(self::$offset);
        }

        list($usec, $sec) = explode(' ', microtime());
        return $usec . ' ' . ($sec + self::$offset);
    }

    public static function __callStatic($method, $args)
    {
        switch($method) {
        case 'getdate':
        case 'localtime':
            if (!isset($args[0])) {
                $args[0] = self::time();
            }
            return call_user_func_array($method, $args);

        case 'date':
        case 'gmdate':
        case 'gmstrftime':
        case 'idate':
        case 'strftime':
        case 'strtotime':
            if (isset($args[0]) && !isset($args[1])) {
                $args[1] = self::time();
            }
            return call_user_func_array($method, $args);
        }

        trigger_error("Call to undefined method {$method}()", E_ERROR);
    }
}
