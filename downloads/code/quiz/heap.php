<?php
/**
 * Created by PhpStorm.
 * User: caiknife
 * Date: 2018/11/17
 * Time: 14:54
 */

namespace App\Cron;


class Number
{
    public function main()
    {
        $this->printKMaxFromN(10, 3);
    }

    public function printKMaxFromN($n = 1, $k = 1)
    {
        if ($n <= 0 || $k <= 0) {
            echo "BOTH n k must be positive." . PHP_EOL;
            exit();
        }

        if ($n < $k) {
            echo "n MUST geq k." . PHP_EOL;
            exit();
        }

        $tmpHeap = new \SplMaxHeap();

        for ($i = 0; $i < $n; $i++) {
            $tmpHeap->insert(rand(0, 50));
        }

        for ($i = 0; $i < $k; $i++) {
            echo $tmpHeap->current() . PHP_EOL;
            $tmpHeap->next();
        }
    }
}
