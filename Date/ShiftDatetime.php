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

    public static function date($format, $timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = self::time();
        }
        return date($format, $timestamp);
    }

    public static function getdate($timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = self::time();
        }
        return getdate($format, $timestamp);
    }

    public static function gettimeofday($return_float = false)
    {
        $ds = gettimeofday();
        $ds['sec'] += self::$offset;
        return $ds; 
    }

    public static function gmdate($format, $timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = self::time();
        }
        return gmdate($format, $timestamp);
    }

    public static function gmstrftime($format, $timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = self::time();
        }
        return gmstrftime($format, $timestamp);
    }
}
