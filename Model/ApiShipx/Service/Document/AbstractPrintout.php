<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Document;

use Magento\Framework\App\Response\Http;
use Smartmage\Inpost\Model\ApiShipx\AbstractService;
use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\Config\Source\ShippingMethods;
use Smartmage\Inpost\Model\ConfigProvider;

abstract class AbstractPrintout extends AbstractService
{
    protected $method = CURLOPT_HTTPGET;

    protected $successResponseCode = Http::STATUS_CODE_200;

    protected $successMessage;

    protected $callUri = 'v1/organizations/71/shipments/labels';

    protected $fileFormat = 'zpl';

    protected $labelSize = 'a4';

    /**
     * @var ConfigProvider
     */
    protected $configProvider;


    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
        $this->successMessage = __('The shipment created sccessfully');
    }

}
