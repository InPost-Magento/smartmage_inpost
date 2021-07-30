<?php

namespace Smartmage\Inpost\Model\Config\Provider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class SendingMethod implements ConfigProviderInterface
{
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfig()
    {
        $config =[
            [
                'label'=>'Large',
                'value'=> '36'
            ],
            [
                'label'=>'Medium',
                'value'=> '32'
            ],
            [
                'label'=>'Small',
                'value'=> '32'
            ]
        ];
        return $config;
    }
}
