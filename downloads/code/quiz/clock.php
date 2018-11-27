<?php
/**
 * Created by PhpStorm.
 * User: caiknife
 * Date: 2018/11/27
 * Time: 10:51
 */

namespace App\Cron;


class Time
{
    const TOTAL_TIME = 12 * 60 * 60;

    protected $_hourNeedle   = 0;
    protected $_minuteNeedle = 0;
    protected $_secondNeedle = 0;

    protected $_nextMinuteNeedle = 0;

    public function __construct()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    public function main()
    {
        $baseTime = strtotime('2018-01-01 00:00:00');

        for ($i = 0; $i <= static::TOTAL_TIME; $i++) {
            // 求时针的偏移量
            $this->_hourNeedle = (1 * $i) % static::TOTAL_TIME;
            // 求分针的偏移量
            $this->_minuteNeedle = (12 * $i) % static::TOTAL_TIME;
            // 求秒针的偏移量
            $this->_secondNeedle = (720 * $i) % static::TOTAL_TIME;

            if ($this->_hourNeedle == $this->_minuteNeedle
                && $this->_minuteNeedle == $this->_secondNeedle) {
                echo $i . PHP_EOL;
                echo date("H:i:s", $baseTime + $i) . PHP_EOL;
            }
        }
    }
}
