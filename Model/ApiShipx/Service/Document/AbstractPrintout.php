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

    protected $isResponseJson = false;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

}
