<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Locker;

use Smartmage\Inpost\Model\Carrier\Methods\AbstractMethod;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Smartmage\Inpost\Model\ConfigProvider;

class StandardEowCod extends AbstractMethod
{
    public string $methodKey = 'standardeowcod';

    public string $carrierCode = 'inpostlocker';

    protected string $blockAttribute = 'block_send_with_locker';

    /** @var TimezoneInterface  */
    private TimezoneInterface $timezone;

    public function __construct(
        PsrLoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        ConfigProvider $configProvider,
        StoreManagerInterface $storeManager,
        TimezoneInterface $timezone
    ) {
        $this->timezone = $timezone;
        parent::__construct($logger, $scopeConfig, $configProvider, $storeManager);
    }

    protected function isWeekendSendAvailable(): bool
    {
        $startDay = $this->configProvider->getConfigData(
            $this->carrierCode . '/' . $this->methodKey . '/start_day'
        );

        $endDay = $this->configProvider->getConfigData(
            $this->carrierCode . '/' . $this->methodKey . '/end_day'
        );

        $startHour = $this->configProvider->getConfigData(
            $this->carrierCode . '/' . $this->methodKey . '/start_hour'
        );

        $endHour = $this->configProvider->getConfigData(
            $this->carrierCode . '/' . $this->methodKey . '/end_hour'
        );

        $currentDate = $this->timezone->date();
        $currentDayOfWeek = $currentDate->format('w');

        if ($currentDayOfWeek == 0) {
            $currentDayOfWeek = 7;
        }

        if ($currentDayOfWeek > $startDay && $currentDayOfWeek < $endDay) {
            return true;
        }

        if ($currentDayOfWeek == $startDay) {
            if ((int) $currentDate->format('Hi') >= (int) str_replace(':', '', $startHour)) {
                return true;
            }
        }

        if ($currentDayOfWeek == $endDay) {
            if ((int) $currentDate->format('Hi') < (int) str_replace(':', '', $endHour)) {
                return true;
            }
        }

        return false;
    }
}
