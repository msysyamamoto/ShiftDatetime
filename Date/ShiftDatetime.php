<?php
/**
 * ShiftDatetime.php
 *
 * PHP version 5.3 or later
 *
 * @category  Date
 * @package   Date_ShiftDate
 * @author    ymmtmsys
 * @copyright Copyright (c) 2012 ymmtmsys
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/ymmtmsys/ShiftDatetime
 */

/**
 * Date_ShiftDatetime
 *
 * @category Date
 * @package  Date_ShiftDate
 * @author   ymmtmsys
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/ymmtmsys/ShiftDatetime
 */
class Date_ShiftDatetime
{
    /**
     * @var Int
     */
    protected static $offset = 0;

    /**
     * Set an offset from the time now by seconds.
     *
     * @param Int $offset offset form the time now
     *
     * @return null
     */
    public static function offset($offset)
    {
        self::$offset = intval($offset);
    }

    /**
     * Create the Datetime instance that shifted the time.
     *
     * @param String       $time     A date/time string
     * @param DateTimeZone $timezone A DateTimeZone object
     *
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
     * rapper of time()
     *
     * @return Int
     */
    public static function time()
    {
        return time() + self::$offset;
    }

    /**
     * rapper of gettimeofday()
     *
     * @param Bool $return_float When set to TRUE,
     *                           a float instead of an array is returned.
     *
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
     * rapper of microtime()
     *
     * @param Bool $return_float When set to TRUE,
     *                           a float instead of an array is returned.
     *
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
     * rapper of mktime()
     *
     * @param Int $hour hour
     * @param Int $min  minute
     * @param Int $sec  second
     * @param Int $mon  month
     * @param Int $day  day
     * @param Int $year year
     *
     * @return Int
     */
    public static function mktime(
        $hour, $min = null, $sec = null,
        $mon = null, $day = null, $year = null
    ) {

        $times = array_map(
            'intval', explode(',', self::date('i,s,m,d,Y'))
        );

        if ($min === null) {
            list($min, $sec, $mon, $day, $year) = $times;
        } elseif ($sec === null) {
            self::arrayShifts(1, $times);
            list($sec, $mon, $day, $year) = $times;
        } elseif ($mon === null) {
            self::arrayShifts(2, $times);
            list($mon, $day, $year) = $times;
        } elseif ($day === null) {
            self::arrayShifts(3, $times);
            list($day, $year) = $times;
        } elseif ($year === null) {
            self::arrayShifts(4, $times);
            list($year) = $times;
        }

        return mktime($hour, $min, $sec, $mon, $day, $year);
    }

    /**
     * rapper of gmmktime()
     *
     * @param Int $hour hour
     * @param Int $min  minute
     * @param Int $sec  second
     * @param Int $mon  month
     * @param Int $day  day
     * @param Int $year year
     *
     * @return Int
     */
    public static function gmmktime(
        $hour = null, $min = null, $sec = null,
        $mon = null, $day = null, $year = null
    ) {

        $times = array_map(
            'intval', explode(',', self::gmdate('H,i,s,m,d,Y'))
        );

        if ($hour === null) {
            list($hour, $min, $sec, $mon, $day, $year) = $times;
        } elseif ($min === null) {
            self::arrayShifts(1, $times);
            list($min, $sec, $mon, $day, $year) = $times;
        } elseif ($sec === null) {
            self::arrayShifts(2, $times);
            list($sec, $mon, $day, $year) = $times;
        } elseif ($mon === null) {
            self::arrayShifts(3, $times);
            list($mon, $day, $year) = $times;
        } elseif ($day === null) {
            self::arrayShifts(4, $times);
            list($day, $year) = $times;
        } elseif ($year === null) {
            self::arrayShifts(5, $times);
            list($year) = $times;
        }

        return gmmktime($hour, $min, $sec, $mon, $day, $year);
    }

    /**
     * Shift elements off the beginning of array
     *
     * @param Int   $n      number of shifts
     * @param Array &$array input array
     *
     * @return Array
     */
    protected static function arrayShifts($n, &$array)
    {
        $res = array();

        for ($i = 0; $i < $n; $i++) {
            $res[] = array_shift($array);
        }

        return $res;
    }

    /**
     * Triggered when invoking inaccessible methods in a static context.
     *
     * @param String $method Name of the method being called
     * @param Array  $args   An enumerated array containing the parameters
     *                       passed to the $name'ed method.
     *
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
