<?php
/**
 * Created by PhpStorm.
 * User: caiknife
 * Date: 2018/12/1
 * Time: 18:29
 */

namespace App\Cron;


class Sheep
{
    public function __construct()
    {
        ini_set("memory_limit", "1024M");
    }

    public function main()
    {
        $sheeps    = [0];
        $startTime = microtime(true);
        for ($i = 1; $i <= 50; $i++) {
            foreach ($sheeps as $key => &$age) {
                $age++;

                switch ($age) {
                    case 1:
                    case 4:
                        // do nothing
                        break;
                    case 2:
                    case 3:
                        $sheeps[] = 0;
                        break;
                    case 5:
                        unset($sheeps[$key]);
                    default:
                        break;
                }
            }
        }
        $endTime = microtime(true);
        echo "Time cost:" . ($endTime - $startTime) . PHP_EOL;
        echo count($sheeps) . PHP_EOL;
    }
}