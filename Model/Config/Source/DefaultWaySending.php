<?php

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 */
class DefaultWaySending implements OptionSourceInterface
{
    const INPOST_LOCKER_STNDARD = 'standard';
    const INPOST_LOCKER_STNDARD_COD = 'standard_cod';

    protected $code = '';

    public function __construct($code = null)
    {
        $this->code = $code;
    }
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->error('$this->code');
        \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->error($this->code);
        switch ($this->code){
            case (self::INPOST_LOCKER_STNDARD):
                return [
                    ['value' => 'parcel_locker', 'label' => __('Nadanie w Paczkomacie')],
                    ['value' => 'branch', 'label' => __('Nadanie w Oddziale')],
                    ['value' => 'dispatch_order', 'label' => __('Odbiór przez Kuriera')],
                    ['value' => 'pop', 'label' => __('Nadanie w POP')],
                ];
            case (self::INPOST_LOCKER_STNDARD_COD):
                return [
                    ['value' => 'UPS', 'label' => __('United Parcel Service')],
                    ['value' => 'UPS_XML', 'label' => __('United Parcel Service XML')]
                ];
            default:
                return [];
        }
    }
}
