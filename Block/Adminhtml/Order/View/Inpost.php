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
use Smartmage\Inpost\Model\ShipmentRepository;
use Smartmage\Inpost\Api\Data\ShipmentInterface;

/**
 * Class Inpost
 * @package Smartmage\Inpost\Block\Adminhtml\Order\View
 */
class Inpost extends AbstractOrder
{
    protected $shippingMethods;

    protected $inpostShipment;
    protected $shipmentRepository;

    /**
     * Inpost constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Smartmage\Inpost\Model\Config\Source\ShippingMethods $shippingMethods
     * @param \Smartmage\Inpost\Model\ShipmentRepository $shipmentRepository
     * @param array $data
     * @param \Magento\Shipping\Helper\Data|null $shippingHelper
     * @param \Magento\Tax\Helper\Data|null $taxHelper
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Admin $adminHelper,
        ShippingMethods $shippingMethods,
        ShipmentRepository $shipmentRepository,
        array $data = [],
        ?ShippingHelper $shippingHelper = null,
        ?TaxHelper $taxHelper = null
    ) {
        $this->shippingMethods = $shippingMethods;
        $this->shipmentRepository = $shipmentRepository;
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

    public function getInpostShipmentId()
    {
        return $this->getOrder()->getExtensionAttributes()->getInpostShipmentId();
    }

    public function getShippingTrackingUrl()
    {
        $tracking = $this->getShippingTrackingNumber();
        return 'https://inpost.pl/sledzenie-przesylek?number=' . $tracking;
    }

    public function getShippingTrackingNumber()
    {
        return $this->getInpostShippment()->getTrackingNumber();
    }

    public function getShippingService()
    {
        return $this->getInpostShippment()->getService();
    }

    public function getShippingDetails()
    {
        $details = [];
        $details[ShipmentInterface::STATUS] = $this->getInpostShippment()->getStatus();
        if ($this->getShippingService() == 'inpost_locker_standard') {
            $details[ShipmentInterface::SHIPMENT_ATTRIBUTES] =
                __("Dimension: ") . $this->getInpostShippment()->getShipmentsAttributes();
            $details[ShipmentInterface::TARGET_POINT] =  __("Point: ") . $this->getInpostShippment()->getTargetPoint();
        }
        return $details;
    }

    public function getLabelUrl()
    {
        return $this->getUrl('smartmageinpost/shipments/printLabel', ['id' => $this->getInpostShipmentId()]);
    }

    public function getReturnUrl()
    {
        return $this->getUrl('smartmageinpost/shipments/printReturnLabel', ['id' => $this->getInpostShipmentId()]);
    }

    public function getInpostShippment()
    {
        if (!$this->inpostShipment) {
            $this->inpostShipment = $this->shipmentRepository->getByShipmentId($this->getInpostShipmentId());
        }
        return $this->inpostShipment;
    }
}
