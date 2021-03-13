<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment;

use Magento\Framework\App\Response\Http;
use Smartmage\Inpost\Model\ApiShipx\AbstractService;
use Smartmage\Inpost\Model\Config\Source\ShippingMethods;
use Smartmage\Inpost\Model\ConfigProvider;

abstract class AbstractCreate extends AbstractService
{
    protected $method = CURLOPT_POST;

    protected $successResponseCode = Http::STATUS_CODE_201;

    protected $successMessage;

    protected $callUri = 'v1/organizations/71/shipments';

    protected $requestBody;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var ShippingMethods
     */
    protected $shippingMethods;

    public function __construct(
        ConfigProvider $configProvider,
        ShippingMethods $shippingMethods
    ) {
        $this->configProvider = $configProvider;
        $this->shippingMethods = $shippingMethods;
    }

    public function createShipment()
    {
        return $this->call($this->requestBody);
    }

    public function createBody($data, $order)
    {
        $this->requestBody['service'] = $this->shippingMethods::INPOST_MAPPER[$data['service']];
        $this->requestBody['reference'] = $data['reference'];

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
