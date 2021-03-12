<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Search;

use \Smartmage\Inpost\{
    Api\Data\ShipmentInterface,
    Model\ApiShipx\Service\Shipment\AbstractSearch,
    Model\ShipmentRepository,
    Model\ShipmentManagement,
    Model\ConfigProvider
};

class Multiple extends AbstractSearch
{

    protected $shipmentRepository;

    protected $shipmentManagement;

    public function __construct(
        ConfigProvider $configProvider,
        ShipmentRepository $shipmentRepository,
        ShipmentManagement $shipmentManagement
    )
    {
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentManagement = $shipmentManagement;
        parent::__construct($configProvider);
    }

    public function getAllShipments()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inpost.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $totalPages = 0;
        $totalPagesUpdated = false;

        for ($page = 1; ; $page++) {
            $logger->info('CURPAGE');
            $logger->info($page);

            $callResult = $this->call(null, ['page' => $page]);

            if (!$totalPagesUpdated) {
                $totalPagesRaw = (float)$callResult['count'] / (float)$callResult['per_page'];
                $logger->info($totalPagesRaw);
                $totalPages = ceil($totalPagesRaw);
                $logger->info($totalPages);
                $totalPagesUpdated = true;
            }

            if (isset($callResult['items']) && !empty($callResult['items'])) {
                foreach ($callResult['items'] as $item) {
                    try {
                        $formatedData = array();

                        $parcel             = $item['parcels'][0];
                        $shipmentAttributes = '';
                        if ($item['service'] == 'inpost_locker_standard') {
                            $shipmentAttributes .= $parcel['template'];
                        } else {
                            $shipmentAttributes .= $parcel['dimensions']['length'] . 'x';
                            $shipmentAttributes .= $parcel['dimensions']['width'] . 'x';
                            $shipmentAttributes .= $parcel['dimensions']['height'];
                            $shipmentAttributes .= $parcel['dimensions']['unit'];
                            $shipmentAttributes .= ' - ';
                            $shipmentAttributes .= $parcel['weight']['amount'];
                            $shipmentAttributes .= $parcel['weight']['unit'];
                        }

                        $receiver     = $item['receiver'];
                        $receiverData = '';
                        $receiverData .= $receiver['first_name'] . ' ';
                        $receiverData .= $receiver['last_name'];

                        $formatedData[ShipmentInterface::SHIPMENT_ID]         = $item['id'];
                        $formatedData[ShipmentInterface::STATUS]              = $item['status'];
                        $formatedData[ShipmentInterface::SERVICE]             = $item['service'];
                        $formatedData[ShipmentInterface::SHIPMENT_ATTRIBUTES] = $shipmentAttributes;
                        $formatedData[ShipmentInterface::SENDING_METHOD]      = $item['sending_method'];
                        $formatedData[ShipmentInterface::RECEIVER_DATA]       = $receiverData;
                        $formatedData[ShipmentInterface::REFERENCE]           = $item['reference'];
                        $formatedData[ShipmentInterface::TRACKING_NUMBER]     = $item['tracking_number'];
                        $formatedData[ShipmentInterface::TARGET_POINT]        = $item['custom_attributes']['target_point'];

                        $this->shipmentManagement->addOrUpdate($formatedData);

                    } catch (\Exception $exception) {
                        $logger->info($exception->getMessage());
                    }
                }
            } else {
                break;
            }

            if ($page >= $totalPages)
                break;
        }
    }

}
