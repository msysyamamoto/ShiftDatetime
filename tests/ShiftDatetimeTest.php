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
    public function testTime($offset)
    {
        ShiftDatetime::offset($offset);

        $stime = ShiftDatetime::time();
        $otime = time();

        $diff = $stime - $otime;
        $test = abs($offset - $diff);
        $this->assertTrue($test <= 1);
    }

    /**
     * @dataProvider providerTestStatic
     */
    public function testLocaltime($offset)
    {
        ShiftDatetime::offset($offset);

        $stime = ShiftDatetime::localtime();
        $otime = localtime();

        $sdate = self::mktime(implode(',', array(
            $stime[2],
            $stime[1],
            $stime[0],
            $stime[4] + 1,
            $stime[3],
            $stime[5] + 1900,
        )));

        $odate = self::mktime(implode(',', array(
            $otime[2],
            $otime[1],
            $otime[0],
            $otime[4] + 1,
            $otime[3],
            $otime[5] + 1900,
        )));

        $diff = $sdate - $odate;
        $test = abs($offset - $diff);
        $this->assertTrue($test <= 1);
    }

    /**
     * @dataProvider providerTestStatic
     */
    public function testGetdate($offset)
    {
        ShiftDatetime::offset($offset);

        $stime = ShiftDatetime::getdate();
        $otime = getdate();

        $sdate = $stime[0];
        $odate = $otime[0];

        $diff = $sdate - $odate;
        $test = abs($offset - $diff);
        $this->assertTrue($test <= 1);
    }

    /**
     * @dataProvider providerTestStatic
     */
    public function testDate($offset)
    {
        ShiftDatetime::offset($offset);

        $fmt   = 'H,i,s,m,d,Y';
        $sdate = self::mktime(ShiftDatetime::date($fmt));
        $odate = self::mktime(date($fmt));

        $diff = $sdate - $odate;
        $test = abs($offset - $diff);
        $this->assertTrue($test <= 1);
    }

    /**
     * @dataProvider providerTestStatic
     */
    public function testGmdate($offset)
    {
        ShiftDatetime::offset($offset);

        $fmt   = 'H,i,s,m,d,Y';
        $sdate = self::mktime(ShiftDatetime::gmdate($fmt));
        $odate = self::mktime(gmdate($fmt));

        $diff = $sdate - $odate;
        $test = abs($offset - $diff);
        $this->assertTrue($test <= 1);
    }

    /**
     * @dataProvider providerTestStatic
     */
    public function testGmstrftime($offset)
    {
        ShiftDatetime::offset($offset);

        $fmt   = '%H,%M,%S,%m,%d,%Y';
        $sdate = self::mktime(ShiftDatetime::gmstrftime($fmt));
        $odate = self::mktime(gmstrftime($fmt));

        $diff = $sdate - $odate;
        $test = abs($offset - $diff);
        $this->assertTrue($test <= 1);
    }

    /**
     * @dataProvider providerTestStatic
     */
    public function testStrftime($offset)
    {
        ShiftDatetime::offset($offset);

        $fmt   = '%H,%M,%S,%m,%d,%Y';
        $sdate = self::mktime(ShiftDatetime::strftime($fmt));
        $odate = self::mktime(strftime($fmt));

        $diff = $sdate - $odate;
        $test = abs($offset - $diff);
        $this->assertTrue($test <= 1);
    }


    /**
     * @dataProvider providerTestStatic
     */
    public function testIdate($offset)
    {
        ShiftDatetime::offset($offset);

        $sdate = self::mktime(implode(',', array(
            ShiftDatetime::idate('H'),
            ShiftDatetime::idate('i'),
            ShiftDatetime::idate('s'),
            ShiftDatetime::idate('m'),
            ShiftDatetime::idate('d'),
            ShiftDatetime::idate('Y'),
        )));
        $odate = self::mktime(implode(',', array(
            idate('H'),
            idate('i'),
            idate('s'),
            idate('m'),
            idate('d'),
            idate('Y'),
        )));

        $diff = $sdate - $odate;
        $test = abs($offset - $diff);
        $this->assertTrue($test <= 1);
    }

    /**
     * @dataProvider providerTestStatic
     */
    public function testStrtotime($offset)
    {
        ShiftDatetime::offset($offset);

        $sdate = ShiftDatetime::strtotime('now');
        $odate = strtotime('now');

        $diff = $sdate - $odate;
        $test = abs($offset - $diff);
        $this->assertTrue($test <= 1);
    }

    /**
     * @dataProvider providerTestStatic
     */
    public function testMicrotime($offset)
    {
        ShiftDatetime::offset($offset);

        list($null, $sdate) = explode(' ', ShiftDatetime::microtime());
        list($null, $odate) = explode(' ', microtime());

        $diff = $sdate - $odate;
        $test = abs($offset - $diff);
        $this->assertTrue($test <= 1);
    }

    /**
     * @dataProvider providerTestStatic
     */
    public function testMicrotimeWithTrue($offset)
    {
        ShiftDatetime::offset($offset);

        $sdate = ShiftDatetime::microtime(true);
        $odate = microtime(true);

        $diff = $sdate - $odate;
        $test = abs($offset - $diff);
        $this->assertTrue($test <= 1.0);
    }

    /**
     * @dataProvider providerTestStatic
     */
    public function testGettimeofday($offset)
    {
        ShiftDatetime::offset($offset);

        $sdate = ShiftDatetime::gettimeofday();
        $odate = gettimeofday();

        $diff = $sdate['sec'] - $odate['sec'];
        $test = abs($offset - $diff);
        $this->assertTrue($test <= 1);
    }

    /**
     * @dataProvider providerTestStatic
     */
    public function testGettimeofdayWithTrue($offset)
    {
        ShiftDatetime::offset($offset);

        $sdate = ShiftDatetime::gettimeofday(true);
        $odate = gettimeofday(true);

        $diff = $sdate - $odate;
        $test = abs($offset - $diff);
        $this->assertTrue($test <= 1.0);
    }

    public static function mktime($csv)
    {
        return call_user_func_array('mktime', explode(',', $csv));
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
