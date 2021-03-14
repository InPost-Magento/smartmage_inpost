<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Document;

use Magento\Framework\App\Response\Http;
use Smartmage\Inpost\Model\ApiShipx\AbstractService;

abstract class AbstractPrintout extends AbstractService
{
    protected $method = CURLOPT_HTTPGET;

    protected $successResponseCode = Http::STATUS_CODE_200;

    protected $isResponseJson = false;

}
