<?php

namespace Smartmage\Inpost\Logger\Handler;

use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base as BaseHandler;
use Magento\Framework\Stdlib\DateTime\Timezone as Timezone;
use Monolog\Logger as MonologLogger;
use Smartmage\Inpost\Model\ConfigProvider;

/**
 * Class InpostGeneralHandler
 */
class InpostGeneralHandler extends BaseHandler
{
    /**
     * @var int
     */
    protected $loggerType = MonologLogger::DEBUG;

    /**
     * @var \Smartmage\Inpost\Model\ConfigProvider
     */
    protected $configProvider;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $timezone;

    /**
     * InpostGeneralHandler constructor.
     * @param \Magento\Framework\Filesystem\DriverInterface $filesystem
     * @param \Smartmage\Inpost\Model\ConfigProvider $configProvider
     * @param \Magento\Framework\Stdlib\DateTime\Timezone $timezone
     * @param null $filePath
     * @param null $fileName
     * @throws \Exception
     */
    public function __construct(
        DriverInterface $filesystem,
        ConfigProvider $configProvider,
        Timezone $timezone,
        $filePath = null,
        $fileName = null
    ) {
        $this->configProvider = $configProvider;
        $this->timezone = $timezone;
        $this->fileName = '/var/log/inpost/general-' . $timezone->date()->format('y-m-d_H') . '.log';
        parent::__construct($filesystem, $filePath, $fileName);
    }

    /**
     * @param array $record
     */
    public function write(array $record)
    {
        if ($this->configProvider->getDebugEnabled()) {
            parent::write($record);
        }
    }
}
