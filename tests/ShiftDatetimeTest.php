<?php
require_once realpath(dirname(__FILE__) . '/../Date/ShiftDatetime.php');
require_once 'PHPUnit/Autoload.php';

class ShiftDatetimeTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        ShiftDatetime::offset(0);
    }

    /**
     * @dataProvider providerTestOffset
     */
    public function testOffset($offset)
    {
        ShiftDatetime::offset($offset);
        $shiftdate = ShiftDatetime::create();
        $date = new Datetime();

        $diff     = $shiftdate->getTimestamp() - $date->getTimestamp();
        $abs_diff = abs($diff - $offset);
        $this->assertTrue($abs_diff <= 1);
    }

    public function providerTestOffset()
    {
        return array(
            array(0),
            array(59),
            array(3600),
            array(3600 * 24),
            array(3600 * 24 * 365),
            array(3600 * 24 * 365 * 10),
            array(-59),
            array(-3600),
            array(-3600 * 24),
            array(-3600 * 24 * 365),
            array(-3600 * 24 * 365 * 10),
        );
    }

    /**
     * @dataProvider providerTestFormat
     */
    public function testFormat($format)
    {
        ShiftDatetime::offset(123456789);
        $shiftdate = ShiftDatetime::create($format);
        $date = new Datetime($format);

        $diff = $shiftdate->getTimestamp() - $date->getTimestamp();

        $this->assertEquals(0, $diff);
    }

    public function providerTestFormat()
    {
        return array(
            array('2000-01-01'),
            array('2001-01-01 01:01:01'),
            array('Apr-17-1990'),
        );
    }

    /**
     * @dataProvider providerTestTimezone
     */
    public function testTimezone($format, $timezone)
    {
        ShiftDatetime::offset(123456789);
        $shiftdate = ShiftDatetime::create($format, $timezone);
        $date = new Datetime($format, $timezone);

        $diff = $shiftdate->getTimestamp() - $date->getTimestamp();

        $this->assertEquals(0, $diff);
    }

    public function providerTestTimezone()
    {
        return array(
            array('2000-01-01', new DateTimeZone('Pacific/Nauru')),
            array('2001-01-01 01:01:01', new DateTimeZone('Pacific/Nauru')),
            array('Apr-17-1990', new DateTimeZone('Pacific/Nauru')),
        );
    }

    /**
     * @dataProvider providerTestStatic
     */
    public function testDate($offset)
    {
        ShiftDatetime::offset($offset);

        $fmt   = 'H,i,s,m,d,Y';
        $sdate = call_user_func_array(
            'mktime',
            explode(',', ShiftDatetime::date($fmt))
        );
        $odate = call_user_func_array(
            'mktime',
            explode(',', date($fmt))
        );

        $diff = $sdate - $odate;
        $test = abs($offset - $diff);
        $this->assertTrue($test <= 1);
    }

    public function providerTestStatic()
    {
        return array(
            array(0),
            array(123456789),
            array(-987654321),
        );
    }
}
