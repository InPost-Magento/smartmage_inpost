<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Search;

use \Smartmage\Inpost\{
    Api\Data\ShipmentInterface,
    Model\ApiShipx\Service\Shipment\AbstractSearch,
    Model\ShipmentRepository,
    Model\ShipmentManagement
};

class Multiple extends AbstractSearch
{

    protected $shipmentRepository;

    protected $shipmentManagement;

    public function __construct(
        ShipmentRepository $shipmentRepository,
        ShipmentManagement $shipmentManagement
    )
    {
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentManagement = $shipmentManagement;
        $this->successMessage = __('The shipment list has been successfully synchronized');
    }

    public function getAllShipments()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inpost.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $totalPages = 0;
        $totalPagesUpdated = false;

        for ($page = 1; ; $page++) {

            $result = $this->call(null, ['page' => $page]);

            if (!$totalPagesUpdated) {
                $totalPagesRaw = (float)$result['count'] / (float)$result['per_page'];
                $logger->info($totalPagesRaw);
                $totalPages = ceil($totalPagesRaw);
                $logger->info($totalPages);
                $totalPagesUpdated = true;
            }

            if (isset($result['items']) && !empty($result['items'])) {
                foreach ($result['items'] as $item) {
                    try {
                        $formatedData = array();

                        $parcel             = $item['parcels'][0];
                        $shipmentAttributes = '';
                        $shipmentAttributes .= $parcel['dimensions']['length'] . 'x';
                        $shipmentAttributes .= $parcel['dimensions']['width'] . 'x';
                        $shipmentAttributes .= $parcel['dimensions']['height'];
                        $shipmentAttributes .= $parcel['dimensions']['unit'];
                        $shipmentAttributes .= ' - ';
                        $shipmentAttributes .= $parcel['weight']['amount'];
                        $shipmentAttributes .= $parcel['weight']['unit'];

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

                        $this->shipmentManagement->addOrUpdate($formatedData);

                    } catch (\Exception $exception) {
                        $logger->info($exception->getMessage());
                    }
                }
            } else { // If no shipments from api end for loop
                break;
            }

            if ($page >= $totalPages) // If end of pages end for loop
                break;
        }

        return $this->callResult;

    }

}
