<?php

namespace tystudy\swoole\web;

class Dispatcher extends \yii\log\Dispatcher
{
    public $yiiBeginAt;

    public function getElapsedTime()
    {
        return microtime(true) - $this->yiiBeginAt;
    }
}