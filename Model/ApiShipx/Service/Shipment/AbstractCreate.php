<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment;

use Magento\Framework\App\Response\Http;
use Smartmage\Inpost\Model\ApiShipx\AbstractService;
use Smartmage\Inpost\Model\ApiShipx\CallResult;
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
        $this->successMessage = __('The shipment created sccessfully');
    }

    public function createShipment()
    {
        $this->call($this->requestBody);

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inpost.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($this->callResult);

        //throw if api fail
        if ($this->callResult[CallResult::STRING_STATUS] != CallResult::STATUS_SUCCESS)
            throw new \Exception($this->callResult[CallResult::STRING_MESSAGE], $this->callResult[CallResult::STRING_RESPONSE_CODE]);

        //set success message for frontend
        if (
            !isset($this->callResult[CallResult::STRING_MESSAGE]) ||
            empty($this->callResult[CallResult::STRING_MESSAGE]) ||
            is_null($this->callResult[CallResult::STRING_MESSAGE])
        ) {
            $this->callResult[CallResult::STRING_MESSAGE] = $this->successMessage;
        }

        return $this->callResult;
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
    }

}
