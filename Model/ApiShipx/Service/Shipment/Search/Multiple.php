<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Search;

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Smartmage\Inpost\Api\Data\ShipmentInterface;
use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\ApiShipx\ErrorHandler;
use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\AbstractSearch;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\ShipmentManagement;
use Smartmage\Inpost\Model\ShipmentRepository;

class Multiple extends AbstractSearch
{
    protected $shipmentRepository;

    protected $shipmentManagement;

    public function __construct(
        PsrLoggerInterface $logger,
        ConfigProvider $configProvider,
        ShipmentRepository $shipmentRepository,
        ShipmentManagement $shipmentManagement,
        ErrorHandler $errorHandler
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentManagement = $shipmentManagement;
        $organizationId = $configProvider->getOrganizationId();
        $this->callUri = 'v1/organizations/' . $organizationId . '/shipments';
        $this->successMessage = __('The shipment list has been successfully synchronized');
        parent::__construct($logger, $configProvider, $errorHandler);
    }

    public function getAllShipments()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inpost.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $totalPages = 0;
        $totalPagesUpdated = false;
        $daysAgo = $this->configProvider->getGetShipmentsDays();
        $daysAgo = date('Y-m-d', strtotime("-" . $daysAgo . " days"));

        for ($page = 1;; $page++) {
            $result = $this->call(null, ['page' => $page, 'created_at_gteq' => $daysAgo]);

            $logger->info($this->callResult);

            if ($this->callResult[CallResult::STRING_STATUS] != CallResult::STATUS_SUCCESS) {
                throw new \Exception($this->callResult[CallResult::STRING_MESSAGE], $this->callResult[CallResult::STRING_RESPONSE_CODE]);
            }

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
                        $formatedData = [];

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
                        if (strpos($item['service'], 'inpost_locker') !== false) {
                            $receiverData .= $receiver['email'] . '<br>'
                                . $receiver['phone'] . '<br>'
                                . $item['target_point'];
                        } else {
                            if (isset($receiver['company_name'])) {
                                $receiverData .= $receiver['company_name'] . '<br>';
                            }

                            if (isset($receiver['email'])) {
                                $receiverData .= $receiver['email'] . '<br>';
                            }

                            $receiverData .= $receiver['first_name'] . '<br>'
                                . $receiver['last_name'] . '<br>'
                                . $receiver['phone'] . '<br>'
                                . 'ul. ' . $receiver['address']['street'] . ' '
                                . $receiver['address']['building_number'] . '<br>'
                                . $receiver['address']['post_code'] . ' ' . $receiver['address']['city'];
                        }

                        $formatedData[ShipmentInterface::SHIPMENT_ID]         = $item['id'];
                        $formatedData[ShipmentInterface::STATUS]              = $item['status'];
                        $formatedData[ShipmentInterface::SERVICE]             = $item['service'];
                        $formatedData[ShipmentInterface::SHIPMENT_ATTRIBUTES] = $shipmentAttributes;
                        $formatedData[ShipmentInterface::SENDING_METHOD]      = $item['sending_method'];
                        $formatedData[ShipmentInterface::RECEIVER_DATA]       = $receiverData;
                        $formatedData[ShipmentInterface::REFERENCE]           = $item['reference'];
                        $formatedData[ShipmentInterface::TRACKING_NUMBER]     = $item['tracking_number'];

                        if (isset($item['custom_attributes']['dispatch_order_id'])) {
                            $formatedData[ShipmentInterface::DISPATCH_ORDER_ID]
                                = $item['custom_attributes']['dispatch_order_id'];
                        }

                        if (isset($item['custom_attributes']) && isset($item['custom_attributes']['target_point'])) {
                            $formatedData[ShipmentInterface::TARGET_POINT] = $item['custom_attributes']['target_point'];
                        }

                        $this->shipmentManagement->addOrUpdate($formatedData);
                    } catch (\Exception $exception) {
                        $logger->info($exception->getMessage());
                    }
                }
            } else { // If no shipments from api end for loop
                break;
            }

            if ($page >= $totalPages) { // If end of pages end for loop
                break;
            }
        }
        return $this->callResult;
    }
}
