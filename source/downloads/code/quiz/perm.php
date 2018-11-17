<?php
/**
 * Created by PhpStorm.
 * User: caiknife
 * Date: 2018/11/17
 * Time: 15:09
 */

namespace App\Cron;


class StringPlace
{
    public function main()
    {
        $this->stringPlace(3);
    }

    public function stringPlace($stringSize = 10)
    {
        $tmpString = [];

        for ($i = 0; $i < $stringSize; $i++) {
            $tmpString[] = chr(rand(97, 122));
        }

        echo "Origin string is " . implode('', $tmpString) . PHP_EOL;

        $this->perm($tmpString, 0, sizeof($tmpString) - 1);
    }

    public function perm(&$arr, $k, $m)
    {
        if ($k == $m) {
            echo implode('', $arr) . PHP_EOL;
        } else {
            for ($i = $k; $i <= $m; $i++) {
                $this->_swap($arr[$k], $arr[$i]);
                $this->perm($arr, $k + 1, $m);
                $this->_swap($arr[$k], $arr[$i]);
            }
        }
    }

    protected function _swap(&$a, &$b)
    {
        list($b, $a) = [$a, $b];
    }
}
