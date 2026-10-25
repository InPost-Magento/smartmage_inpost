<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Smartmage\Inpost\Model\ConfigProvider;

class CheckoutAssets extends Template
{
    private ConfigProvider $configProvider;

    public function __construct(
        Context $context,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        $this->configProvider = $configProvider;
        parent::__construct($context, $data);
    }

    /**
     * @throws NoSuchEntityException
     */
    protected function _prepareLayout()
    {
        if ($this->configProvider->isCheckoutIntegrationEnabled()) {
            $this->pageConfig->addRemotePageAsset(
                'https://geowidget.inpost.pl/inpost-geowidget.css',
                'css_rel'
            );
            $this->pageConfig->addPageAsset('Smartmage_Inpost::js/inpost-event.js');
        }

        return parent::_prepareLayout();
    }

    protected function _toHtml(): string
    {
        return '';
    }
}
