<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Sales\Block\Adminhtml\Order\AbstractOrder;
use Magento\Sales\Helper\Admin;
use Magento\Shipping\Helper\Data as ShippingHelper;
use Magento\Tax\Helper\Data as TaxHelper;
use Smartmage\Inpost\Model\Config\Source\ShippingMethods;
use Smartmage\Inpost\Model\Config\Source\Size as SizeConfig;
use Smartmage\Inpost\Model\Config\Source\Service as ServiceConfig;
use Smartmage\Inpost\Model\Config\Source\Status as StatusConfig;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\ShipmentRepository;
use Smartmage\Inpost\Api\Data\ShipmentInterface;

class Inpost extends AbstractOrder
{
    /**
     * @var ShippingMethods
     */
    protected $shippingMethods;
    /**
     * @var
     */
    protected $inpostShipment;
    /**
     * @var \Smartmage\Inpost\Model\ShipmentRepository
     */
    protected $shipmentRepository;
    /**
     * @var \Smartmage\Inpost\Model\Config\Source\Size
     */
    protected $sizeConfig;
    /**
     * @var \Smartmage\Inpost\Model\Config\Source\Service
     */
    protected $serviceConfig;
    /**
     * @var \Smartmage\Inpost\Model\Config\Source\Status
     */
    protected $statusConfig;
    protected $configProvider;

    /**
     * Inpost constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param ShippingMethods $shippingMethods
     * @param \Smartmage\Inpost\Model\ShipmentRepository $shipmentRepository
     * @param \Smartmage\Inpost\Model\Config\Source\Size $sizeConfig
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
        SizeConfig $sizeConfig,
        ServiceConfig $serviceConfig,
        StatusConfig $statusConfig,
        ConfigProvider $configProvider,
        array $data = [],
        ?ShippingHelper $shippingHelper = null,
        ?TaxHelper $taxHelper = null
    ) {
        $this->shippingMethods = $shippingMethods;
        $this->shipmentRepository = $shipmentRepository;
        $this->sizeConfig = $sizeConfig;
        $this->serviceConfig = $serviceConfig;
        $this->statusConfig = $statusConfig;
        $this->configProvider = $configProvider;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * @return array
     */
    public function getInpostShippingMethods() : array
    {
        return $this->shippingMethods->toOptionArray(true);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSelectedMethod() : string
    {
        return $this->getOrder()->getShippingMethod();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getInpostShipments()
    {
        $shipments = [];
        if ($inpostShipmentLinks = $this->getOrder()->getExtensionAttributes()->getInpostShipmentLinks()) {
            foreach ($inpostShipmentLinks as $inpostShipmentLink) {
                if ($shipment = $this->getInpostShippment($inpostShipmentLink->getShipmentId())) {
                    $shipments[] = $shipment;
                }
            }
        }
        return $shipments;
    }

    /**
     * @param $shipment
     * @return string
     */
    public function getShippingTrackingUrl($shipment)
    {
        $tracking = $shipment->getTrackingNumber();
        return 'https://inpost.pl/sledzenie-przesylek?number=' . $tracking;
    }

    /**
     * @param $shipment
     * @return \Magento\Framework\Phrase
     */
    public function getShippingService($shipment)
    {
        return $this->serviceConfig->getServiceLabel($shipment->getShippingMethod());
    }

    /**
     * @param $shipment
     * @return array
     */
    public function getShippingDetails($shipment)
    {
        $details = [];
        $details[ShipmentInterface::STATUS] = $this->statusConfig->getStatusLabel($shipment->getStatus());
        if (strpos($shipment->getService(), 'inpost_locker') !== false) {
            $details[ShipmentInterface::SHIPMENT_ATTRIBUTES] =
                $this->sizeConfig->getSizeLabel($shipment->getShipmentsAttributes());
            $details[ShipmentInterface::TARGET_POINT] =  __("Point: ") . $shipment->getTargetPoint();
        }
        return $details;
    }

    /**
     * @param $shipment
     * @return string
     */
    public function getLabelUrl($shipment)
    {
        return $this->getUrl(
            'smartmageinpost/shipments/printLabel',
            ['id' => $shipment->getShipmentId(), 'order_id' => $this->getOrder()->getId()]
        );
    }

    /**
     * @param $shipment
     * @return boolean
     */
    public function isReturnPossible($shipment)
    {
        if (in_array($shipment->getService(), ['inpost_courier_c2c', 'inpost_courier_c2ccod'])) {
            return false;
        }
        return true;
    }

    /**
     * @param $shipment
     * @return string
     */
    public function getReturnUrl($shipment)
    {
        if ($shipment->getService() != 'inpost_locker_standard') {
            return $this->getUrl(
                'smartmageinpost/shipments/printReturnLabel',
                ['id' => $shipment->getShipmentId(), 'order_id' => $this->getOrder()->getId()]
            );
        } else {
            return $this->configProvider->getSzybkiezwrotyUrl();
        }
    }

    public function isUrlBlank($shipment)
    {
        if ($shipment->getService() != 'inpost_locker_standard') {
            return false;
        }

        return true;
    }

    /**
     * @param $inpostShipmentId
     * @return \Magento\Framework\Model\AbstractModel|\Smartmage\Inpost\Api\Data\ShipmentInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getInpostShippment($inpostShipmentId)
    {
        $shipment = null;
        try {
            $shipment =  $this->shipmentRepository->getByShipmentId($inpostShipmentId);
        } catch (\Exception $e) {
        }
        return $shipment;
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getInPostToken()
    {
        return $this->configProvider->getShippingConfigData('geowidget_token');
    }
}
