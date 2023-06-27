<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment;

use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Smartmage\Inpost\Api\Data\ShipmentInterface;
use Smartmage\Inpost\Model\ApiShipx\AbstractService;
use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\ApiShipx\ErrorHandler;
use Smartmage\Inpost\Model\Config\Source\ShippingMethods;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\ShipmentManagement;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

abstract class AbstractCreate extends AbstractService
{
    protected $method = CURLOPT_POST;

    protected $successResponseCode = Http::STATUS_CODE_201;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;
    protected $shipmentManagement;

    /**
     * @var ShippingMethods
     */
    protected $shippingMethods;

    /**
     * @var PsrLoggerInterface
     */
    protected $logger;

    /**
     * @var RequestHttp
     */
    protected $request;

    /**
     * @param PsrLoggerInterface $logger
     * @param ConfigProvider $configProvider
     * @param ShippingMethods $shippingMethods
     * @param ShipmentManagement $shipmentManagement
     * @param ErrorHandler $errorHandler
     * @param RequestHttp $request
     * @throws NoSuchEntityException
     */
    public function __construct(
        PsrLoggerInterface $logger,
        ConfigProvider $configProvider,
        ShippingMethods $shippingMethods,
        ShipmentManagement $shipmentManagement,
        ErrorHandler $errorHandler,
        RequestHttp $request
    ) {
        $this->logger = $logger;
        $this->configProvider = $configProvider;
        $this->shippingMethods = $shippingMethods;
        $this->shipmentManagement = $shipmentManagement;

        $organizationId = $configProvider->getOrganizationId();
        $this->callUri = 'v1/organizations/' . $organizationId . '/shipments';
        $this->request = $request;

        $this->successMessage = __('The shipment created successfully');
        parent::__construct($logger, $configProvider, $errorHandler);
    }

    public function createShipment($order = null)
    {
        $response = $this->call($this->requestBody);
        $this->logger->info(print_r($this->callResult, true));
        $this->logger->info(print_r($response, true));

        //throw if api fail
        if ($this->callResult[CallResult::STRING_STATUS] != CallResult::STATUS_SUCCESS) {
            throw new \Exception(
                $this->callResult[CallResult::STRING_MESSAGE],
                $this->callResult[CallResult::STRING_RESPONSE_CODE]
            );
        }

        //set success message for frontend
        if (!isset($this->callResult[CallResult::STRING_MESSAGE]) ||
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
                if (strpos($response['service'], 'inpost_locker') !== false) {
                    $receiverData .= $receiver['email'] . '<br>'
                        . $receiver['phone'] . '<br>'
                        . $response['custom_attributes']['target_point'];
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

                $formatedData[ShipmentInterface::SHIPMENT_ID]         = $response['id'];
                $formatedData[ShipmentInterface::STATUS]              = $response['status'];
                $formatedData[ShipmentInterface::SERVICE]             = $response['service'];
                $formatedData[ShipmentInterface::SHIPMENT_ATTRIBUTES] = $shipmentAttributes;
                $formatedData[ShipmentInterface::SENDING_METHOD]      = $response['sending_method'];
                $formatedData[ShipmentInterface::RECEIVER_DATA]       = $receiverData;
                $formatedData[ShipmentInterface::REFERENCE]           = $response['reference'];
                $formatedData[ShipmentInterface::TRACKING_NUMBER]     = $response['tracking_number'];
                if ($this->request->getPost('shipment_fieldset')
                    && isset($this->request->getPost('shipment_fieldset')['shipping_method'])
                ) {
                    $formatedData[ShipmentInterface::SHIPPING_METHOD]     = $this->request->getPost('shipment_fieldset')['shipping_method'];
                } elseif ($order !== null) {
                    $formatedData[ShipmentInterface::SHIPPING_METHOD] = $order->getShippingMethod();
                }

                if (isset($response['custom_attributes']) && isset($response['custom_attributes']['target_point'])) {
                    $formatedData[ShipmentInterface::TARGET_POINT] = $response['custom_attributes']['target_point'];
                }

                $this->shipmentManagement->addOrUpdate($formatedData);
            } catch (\Exception $exception) {
                $this->logger->info($exception->getMessage());
            }
        }

        return $this->callResult;
    }

    public function createBody($data, $order)
    {
        if(!empty($data['commercial_product_identifier'])) {
            $this->requestBody['commercial_product_identifier'] = $data['commercial_product_identifier'];
        }

        $this->requestBody['service'] = $this->shippingMethods::INPOST_MAPPER[$data['service']];
        $this->requestBody['reference'] = $data['reference'];
        $this->requestBody['only_choice_of_offer'] = false;
        $this->requestBody['external_customer_id'] = 'Magento';

        if ($this->configProvider->getChangeAddress()) {
            $this->requestBody['sender'] = [
                'company_name' => $this->configProvider->getSenderCompany(),
                'email' => $this->configProvider->getSenderEmail(),
                'phone' => $this->configProvider->getSenderPhone(),
                'first_name' => $this->configProvider->getSenderName(),
                'last_name' => $this->configProvider->getSenderSurname(),
                'address' => [
                    "street" => $this->configProvider->getSenderStreet(),
                    "building_number" => $this->configProvider->getSenderBuildingNumber(),
                    "city" => $this->configProvider->getSenderCity(),
                    "post_code" => $this->configProvider->getSenderPostcode(),
                    "country_code" => $this->configProvider->getSenderCountryCode(),
                ]
            ];
        }

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
