<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment;

use Magento\Framework\App\Response\Http;
use Smartmage\Inpost\{
    Model\ApiShipx\AbstractService
};

abstract class AbstractCreate extends AbstractService
{
    protected $method = CURLOPT_POST;

    protected $successResponseCode = Http::STATUS_CODE_201;

    protected $successMessage;

    protected $callUri = 'v1/organizations/71/shipments';

    protected $sampleBody;

}
