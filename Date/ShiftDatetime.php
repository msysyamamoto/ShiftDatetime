<?php
class ShiftDatetime
{
    /**
     * @var Int
     */
    protected static $offset = 0;

    /**
     * @param $offset Int
     */
    public static function offset($offset)
    {
        self::$offset = intval($offset);
    }

    /**
     * @param $time String
     * @param $timezone DateTimeZone
     * @return Datetime
     */
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

    /**
     * @return Int
     */
    public static function time()
    {
        return time() + self::$offset;
    }

    /**
     * @param $return_float Bool
     * @return Array
     */
    public static function gettimeofday($return_float = false)
    {
        if ($return_float === true) {
            return gettimeofday(true) + floatval(self::$offset);
        }

        $ds = gettimeofday();
        $ds['sec'] += self::$offset;
        return $ds;
    }

    /**
     * @param $return_float Bool
     * @return mixed 
     */
    public static function microtime($return_float = false)
    {
        if ($return_float === true) {
            return microtime(true) + floatval(self::$offset);
        }

        list($usec, $sec) = explode(' ', microtime());
        return $usec . ' ' . ($sec + self::$offset);
    }

    /**
     * @param $hour Int
     * @param $min Int
     * @param $sec Int
     * @param $mon Int
     * @param $day Int
     * @param $year Int
     * @return Int 
     */
    public static function mktime(
        $hour, $min = null, $sec = null,
        $mon = null, $day = null, $year = null
    )
    {
        list($y, $m, $d, $h, $i, $s) = array_map(
            'intval', explode(',', self::date('Y,m,d,H,i,s')
        ));
        if ($min === null) {
            $min  = $i;
            $sec  = $s;
            $mon  = $m;
            $day  = $d;
            $year = $y;
        } elseif ($sec === null) {
            $sec  = $s;
            $mon  = $m;
            $day  = $d;
            $year = $y;
        } elseif ($mon === null) {
            $mon  = $m;
            $day  = $d;
            $year = $y;
        } elseif ($day === null) {
            $day  = $d;
            $year = $y;
        } elseif ($year === null) {
            $year = $y;
        }
        return mktime($hour, $min, $sec, $mon, $day, $year);
    }

    /**
     * @param $method String
     * @param $args Array
     * @return mixed 
     */
    public static function __callStatic($method, $args)
    {
        switch ($method) {
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
