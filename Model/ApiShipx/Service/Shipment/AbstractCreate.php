<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment;

use Magento\Framework\App\Response\Http;
use Smartmage\Inpost\Api\Data\ShipmentInterface;
use Smartmage\Inpost\Model\ApiShipx\AbstractService;
use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\ApiShipx\ErrorHandler;
use Smartmage\Inpost\Model\Config\Source\ShippingMethods;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\ShipmentManagement;

abstract class AbstractCreate extends AbstractService
{
    protected $method = CURLOPT_POST;

    protected $successResponseCode = Http::STATUS_CODE_201;

    protected $successMessage;

    protected $requestBody;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;
    protected $shipmentManagement;

    /**
     * @var ShippingMethods
     */
    protected $shippingMethods;

    public function __construct(
        ConfigProvider $configProvider,
        ShippingMethods $shippingMethods,
        ShipmentManagement $shipmentManagement,
        ErrorHandler $errorHandler
    ) {
        $this->configProvider = $configProvider;
        $this->shippingMethods = $shippingMethods;
        $this->shipmentManagement = $shipmentManagement;

        $organizationId = $configProvider->getOrganizationId();
        $this->callUri = 'v1/organizations/' . $organizationId . '/shipments';

        $this->successMessage = __('The shipment created sccessfully');
        parent::__construct($configProvider, $errorHandler);
    }

    public function createShipment()
    {
        $response = $this->call($this->requestBody);

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inpost.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($this->callResult);
        $logger->info($response);

        //throw if api fail
        if ($this->callResult[CallResult::STRING_STATUS] != CallResult::STATUS_SUCCESS) {
            throw new \Exception(
                $this->callResult[CallResult::STRING_MESSAGE],
                $this->callResult[CallResult::STRING_RESPONSE_CODE]
            );
        }

        //set success message for frontend
        if (
            !isset($this->callResult[CallResult::STRING_MESSAGE]) ||
            empty($this->callResult[CallResult::STRING_MESSAGE]) ||
            is_null($this->callResult[CallResult::STRING_MESSAGE])
        ) {
            $this->callResult[CallResult::STRING_MESSAGE] = $this->successMessage;
        }
        if (isset($response['id'])) {
            $this->callResult[CallResult::STRING_RESPONSE_SHIPMENT_ID] = $response['id'];
            try {
                $formatedData = [];

                $parcel             = $response['parcels'][0];
                $shipmentAttributes = '';
                if ($response['service'] == 'inpost_locker_standard') {
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

                $receiver     = $response['receiver'];
                $receiverData = '';
                $receiverData .= $receiver['first_name'] . ' ';
                $receiverData .= $receiver['last_name'];

                $formatedData[ShipmentInterface::SHIPMENT_ID]         = $response['id'];
                $formatedData[ShipmentInterface::STATUS]              = $response['status'];
                $formatedData[ShipmentInterface::SERVICE]             = $response['service'];
                $formatedData[ShipmentInterface::SHIPMENT_ATTRIBUTES] = $shipmentAttributes;
                $formatedData[ShipmentInterface::SENDING_METHOD]      = $response['sending_method'];
                $formatedData[ShipmentInterface::RECEIVER_DATA]       = $receiverData;
                $formatedData[ShipmentInterface::REFERENCE]           = $response['reference'];
                $formatedData[ShipmentInterface::TRACKING_NUMBER]     = $response['tracking_number'];

                if (isset($response['custom_attributes']) && isset($response['custom_attributes']['target_point'])) {
                    $formatedData[ShipmentInterface::TARGET_POINT] = $response['custom_attributes']['target_point'];
                }

                $this->shipmentManagement->addOrUpdate($formatedData);
            } catch (\Exception $exception) {
                $logger->info($exception->getMessage());
            }
        }

        return $this->callResult;
    }

    public function createBody($data, $order)
    {
        $this->requestBody['service'] = $this->shippingMethods::INPOST_MAPPER[$data['service']];
        $this->requestBody['reference'] = $data['reference'];
        $this->requestBody['only_choice_of_offer'] = false;

        if ($data['insurance']) {
            $this->requestBody['insurance'] = [
                "amount" => $data['insurance'],
                "currency" => "PLN"
            ];
        }

        if ($data['cod']) {
            $this->requestBody['cod'] = [
                "amount" => $data['cod'],
                "currency" => "PLN"
            ];
        }

        $this->requestBody['custom_attributes']['sending_method'] = $data['sending_method'];
        $this->requestBody['comments'] = '';

        if ($data['sending_method'] != 'dispatch_order') {
            $this->requestBody['custom_attributes']['dropoff_point'] = $this->configProvider->getConfigData(
                str_replace('_', '/', $data['service']) . '/default_sending_point'
            );
        }
    }

}
