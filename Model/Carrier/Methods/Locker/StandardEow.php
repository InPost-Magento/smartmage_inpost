<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Locker;

use Smartmage\Inpost\Model\Carrier\Methods\AbstractMethod;
use Smartmage\Inpost\Model\Config\Source\ShippingMethodsMode;

class StandardEow extends AbstractMethod
{
    public string $methodKey = 'standardeow';

    public string $carrierCode = 'inpostlocker';

    protected string $blockAttribute = 'block_send_with_locker';

    public int $shippingMethodsMode = ShippingMethodsMode::SHIPPING_METHODS_MODE_STANDARD;

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

        $currentDayOfWeek = date('w');

        if ($currentDayOfWeek == 0) {
            $currentDayOfWeek = 7;
        }

        if ($currentDayOfWeek > $startDay && $currentDayOfWeek < $endDay) {
            return true;
        }

        if ($currentDayOfWeek == $startDay) {
            if (date('Hi') >= str_replace(':', '', $startHour)) {
                return true;
            }
        }

        if ($currentDayOfWeek == $endDay) {
            if (date('Hi') < str_replace(':', '', $endHour)) {
                return true;
            }
        }

        return false;
    }
}
