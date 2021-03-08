<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Locker;

use Smartmage\Inpost\Model\Carrier\AbstractMethod;

class StandardEow extends AbstractMethod
{
    public $methodKey = 'standardeow';

    public $carrierCode = 'inpostlocker';

    protected $blockAttribute = 'block_send_with_locker';

    protected function isWeekendSendAvailable(): bool
    {
        $startDay = $this->configProvider->getConfigData(
            $this->carrierCode . '/' . $this->methodKey .'/start_day'
        );

        $endDay = $this->configProvider->getConfigData(
            $this->carrierCode . '/' . $this->methodKey .'/end_day'
        );

        $startHour = $this->configProvider->getConfigData(
            $this->carrierCode . '/' . $this->methodKey .'/start_hour'
        );

        $endHour = $this->configProvider->getConfigData(
            $this->carrierCode . '/' . $this->methodKey .'/end_hour'
        );

        $currentDayOfWeek = date('w');

        if ($currentDayOfWeek == 0) {
            $currentDayOfWeek = 7;
        }

        if ($currentDayOfWeek >= $startDay && $currentDayOfWeek <= $endDay) {

        }
        return false;
    }
}
