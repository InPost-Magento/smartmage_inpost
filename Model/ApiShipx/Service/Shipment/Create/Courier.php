<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Create;

use Magento\Framework\App\Request\Http as RequestHttp;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Smartmage\Inpost\Model\ApiShipx\ErrorHandler;
use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\AbstractCreate;
use Smartmage\Inpost\Model\Config\Source\ShippingMethods;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\Order\Processor as OrderProcessor;
use Smartmage\Inpost\Model\ShipmentManagement;

class Courier extends AbstractCreate
{
    /**
     * @var OrderProcessor
     */
    protected $orderProcessor;

    /**
     * @param PsrLoggerInterface $logger
     * @param ConfigProvider $configProvider
     * @param ShippingMethods $shippingMethods
     * @param OrderProcessor $orderProcessor
     * @param ErrorHandler $errorHandler
     * @param ShipmentManagement $shipmentManagement
     * @param RequestHttp $request
     */
    public function __construct(
        PsrLoggerInterface $logger,
        ConfigProvider $configProvider,
        ShippingMethods $shippingMethods,
        OrderProcessor $orderProcessor,
        ErrorHandler $errorHandler,
        ShipmentManagement $shipmentManagement,
        RequestHttp $request
    ) {
        $this->orderProcessor = $orderProcessor;
        parent::__construct($logger, $configProvider, $shippingMethods, $shipmentManagement, $errorHandler, $request);
    }

    public function createBody($data, $order)
    {
        $this->requestBody = [
            "receiver" => [
                "company_name" => $data['company_name'],
                "first_name" => $data['first_name'],
                "last_name" => $data['last_name'],
                'phone' => $data['phone'],
                "address" => [
                    "street" => $data['street'],
                    "building_number" => $data['building_number'],
                    "city" => $data['city'],
                    "post_code" => $data['post_code'],
                    "country_code" => "PL",
                ]
            ],
            "parcels" => [
                "dimensions" => [
                    "length" => $data['length'],
                    "width" => $data['width'],
                    "height" => $data['height'],
                    "unit" => "mm",
                ],
                "weight" => [
                    "amount" => str_replace(',', '.', $data['weight']),
                    "unit" => 'kg',
                ]
            ],
        ];

        if ($data['email']) {
            $this->requestBody['receiver']['email'] = $data['email'];
        }

        parent::createBody($data, $order);
    }

    public function setSampleData()
    {
        $this->requestBody = [
            "receiver" => [
                "company_name" => "Company name",
                "first_name" => "Jan",
                "last_name" => "Kowalski",
                "email" => "receiver@example.com",
                "phone" => "888000000",
                "address" => [
                    "street" => "Cybernetyki",
                    "building_number" => "10",
                    "city" => "Warszawa",
                    "post_code" => "02-677",
                    "country_code" => "PL"
                ]
            ],
            "parcels" => [
                "dimensions" => [
                    "length" => "80",
                    "width" => "160",
                    "height" => "340",
                    "unit" => "mm"
                ],
                "weight" => [
                    "amount" => "1",
                    "unit" => "kg"
                ],
            ],
            "insurance" => [
                "amount" => 50,
                "currency" => "PLN"
            ],
            "cod" => [
                "amount" => 50.00,
                "currency" => "PLN"
            ],
            "custom_attributes" => [
                "sending_method" => "dispatch_order",
            ],
            "service" => "inpost_courier_standard",
            "reference" => "Test",
            "comments" => "dowolny komentarz"
        ];

        return $this;
    }
}
