<?php
/**
 * Created by PhpStorm.
 * User: caiknife
 * Date: 2018/12/1
 * Time: 22:11
 */

namespace App\Cron;


class SheepWindow
{
    public function __construct()
    {
        ini_set("memory_limit", "2048M");
    }

    public function main()
    {
        $startTime = microtime(true);
        $arr       = [1, 0, 0, 0, 0]; // 这个数组分别表示一到五年
        for ($i = 1; $i <= 50; $i++) {
            $tmp = $arr[1] + $arr[2]; // 只有2岁和3岁的羊会生小羊
            array_unshift($arr, $tmp); // 这表示新一年生的小羊数
            array_pop($arr); // 将5岁的小羊消灭

        }
        $endTime = microtime(true);
        echo "Time cost:" . ($endTime - $startTime) . PHP_EOL;
        echo array_sum($arr) . PHP_EOL;
    }

    public function another()
    {
        $startTime = microtime(true);
        $arr       = [1, 0, 0, 0, 0];
        for ($i = 1; $i <= 50; $i++) {
            list($one, $two, $three, $four, $five) = $arr;
            $arr[0] = $two + $three;
            $arr[1] = $one;
            $arr[2] = $two;
            $arr[3] = $three;
            $arr[4] = $four;
        }
        $endTime = microtime(true);
        echo "Time cost:" . ($endTime - $startTime) . PHP_EOL;
        echo array_sum($arr) . PHP_EOL;
    }
}
