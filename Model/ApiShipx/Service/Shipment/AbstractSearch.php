<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment;

use Smartmage\Inpost\{
    Model\ApiShipx\AbstractService
};

abstract class AbstractSearch extends AbstractService
{
    protected $method = CURLOPT_HTTPGET;

    protected $callUri = 'v1/organizations/71/shipments';

    protected $sampleBody;

}
