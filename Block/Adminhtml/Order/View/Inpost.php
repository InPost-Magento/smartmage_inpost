<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Sales\Block\Adminhtml\Order\AbstractOrder;
use Magento\Sales\Helper\Admin;
use Magento\Shipping\Helper\Data as ShippingHelper;
use Magento\Tax\Helper\Data as TaxHelper;
use Smartmage\Inpost\Model\Config\Source\ShippingMethods;

/**
 * Class Inpost
 * @package Smartmage\Inpost\Block\Adminhtml\Order\View
 */
class Inpost extends AbstractOrder
{
    protected $shippingMethods;

    /**
     * Inpost constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Smartmage\Inpost\Model\Config\Source\ShippingMethods $shippingMethods
     * @param array $data
     * @param \Magento\Shipping\Helper\Data|null $shippingHelper
     * @param \Magento\Tax\Helper\Data|null $taxHelper
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Admin $adminHelper,
        ShippingMethods $shippingMethods,
        array $data = [],
        ?ShippingHelper $shippingHelper = null,
        ?TaxHelper $taxHelper = null
    ) {
        $this->shippingMethods = $shippingMethods;
        parent::__construct($context, $registry, $adminHelper, $data, $shippingHelper, $taxHelper);
    }

    /**
     * @return array
     */
    public function getInpostShippingMethods() : array
    {
        return $this->shippingMethods->toOptionArray();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSelectedMethod() : string
    {
        return $this->getOrder()->getShippingMethod();
    }
}
