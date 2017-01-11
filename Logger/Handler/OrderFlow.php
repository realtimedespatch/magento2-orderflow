<?php

namespace RealtimeDespatch\OrderFlow\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

/**
 * Class OrderFlow
 * @package RealtimeDespatch\OrderFlow\Logger\Handler
 */
class OrderFlow extends Base
{
    protected $fileName = '/var/log/orderflow.log';
    protected $loggerType = Logger::DEBUG;
}