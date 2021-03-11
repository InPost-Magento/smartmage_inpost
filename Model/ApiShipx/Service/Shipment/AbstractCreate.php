<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment;

use Smartmage\Inpost\{
    Model\ApiShipx\AbstractService
};

abstract class AbstractCreate extends AbstractService
{

    protected $method = CURLOPT_POST;

    protected $callUri = 'v1/organizations/71/shipments';

    protected $sampleBody;

}
