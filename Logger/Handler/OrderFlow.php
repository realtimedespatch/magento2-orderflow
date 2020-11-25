<?php

namespace RealtimeDespatch\OrderFlow\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

/**
 * OrderFlow Log Handler.
 */
class OrderFlow extends Base
{
    /**
     * File Name.
     *
     * @var string
     */
    protected $fileName = '/var/log/orderflow.log';

    /**
     * Logger Type.
     *
     * @var int
     */
    protected $loggerType = Logger::DEBUG;
}
