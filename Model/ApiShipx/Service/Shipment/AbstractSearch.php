<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment;

use Magento\Framework\App\Response\Http;
use Smartmage\Inpost\{
    Model\ApiShipx\AbstractService
};

abstract class AbstractSearch extends AbstractService
{
    protected $method = CURLOPT_HTTPGET;

    protected $successResponseCode = Http::STATUS_CODE_200;

}
