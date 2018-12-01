<?php
/**
 * Created by PhpStorm.
 * User: caiknife
 * Date: 2018/12/1
 * Time: 18:54
 */

namespace App\Cron;


class SheepRecursive
{
    public function __construct()
    {
        ini_set("memory_limit", "2048M");
    }

    public function main()
    {
        echo $this->_countSheep(50) . PHP_EOL;
    }

    protected function _countSheep($year)
    {

        if ($year == 3) {
            return 3;
        } elseif ($year == 2) {
            return 2;
        } elseif ($year == 1) {
            return 1;
        }

        // $year >= 5时，表示有羊死去
        return ($year >= 5 ? 0 : 1) + $this->_countSheep($year - 3) + $this->_countSheep($year - 2);
    }
}
