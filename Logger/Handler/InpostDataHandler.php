<?php

namespace Smartmage\Inpost\Logger\Handler;

use Magento\Framework\Logger\Handler\Base as BaseHandler;
use Monolog\Logger as MonologLogger;

class InpostDataHandler extends BaseHandler
{
    /**
     * @var int
     */
    protected $loggerType = MonologLogger::DEBUG;


    /**
     * File name
     *
     * @var string
     */
    protected $fileName = '/var/log/inpost.log';
}
