<?php
/**
 * Created by PhpStorm.
 * User: zcai
 * Date: 14-3-4
 * Time: 上午10:33
 *
 * 比较三种方式下，字符串拼接最快的方法。
 * 1、字符串直接连接
 * 2、双引号内变量转义
 * 3、sprintf格式化
 */

class Benchmark {
    protected $_timeStart = null;
    protected $_timeEnd   = null;

    protected $_repeatTimes = null;

    protected $_formatters = array();

    public function __construct() {
        $this->_repeatTimes = 10000;
    }

    public function setRepeatTimes($repeatTimes) {
        $this->_repeatTimes = $repeatTimes;
        return $this;
    }

    public function addFormatter(StringFormatter $formatter) {
        $this->_formatters[] = $formatter;
        return $this;
    }

    public function bench() {
        foreach ($this->_formatters as $formatter) {
            $this->_invokeFormatter($formatter);
        }
    }

    protected function _invokeFormatter(StringFormatter $formatter) {
        $this->_timeStart = microtime(true);
        for ($i = 0; $i < $this->_repeatTimes; ++$i) {
            $formatter->format();
        }
        $this->_timeEnd = microtime(true);

        var_dump($this->_timeEnd - $this->_timeStart);
    }
}

abstract class StringFormatter {
    protected $_firstString = '';
    protected $_secondString = '';

    protected $_result = '';

    public function __construct($firstString, $secondString) {
        $this->_firstString = $firstString;
        $this->_secondString = $secondString;
    }

    public function getResult() {
        return $this->_result;
    }

    public function getFirstString() {
        return $this->_firstString;
    }

    public function getSecondString() {
        return $this->_secondString;
    }

    abstract public function format();
}

class StringContactFormatter extends StringFormatter {
    public function format() {
        $this->_result = $this->_firstString . $this->_secondString;
        return $this;
    }
}

class DoubleQuoteFormatter extends StringFormatter {
    public function format() {
        $this->_result = "{$this->_firstString}{$this->_secondString}";
        return $this;
    }
}

class SprintfFormatter extends StringFormatter {
    public function format() {
        $this->_result = sprintf('%s%s', $this->_firstString, $this->_secondString);
        return $this;
    }
}

$benchmark = new Benchmark();
$benchmark->addFormatter(new StringContactFormatter('cai', 'knife'))
          ->addFormatter(new DoubleQuoteFormatter('cai', 'knife'))
          ->addFormatter(new SprintfFormatter('cai', 'knife'));

$benchmark->bench();