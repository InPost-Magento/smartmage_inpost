<?php

namespace Smartmage\Inpost\Controller\Adminhtml\Shipments\SaveSample;

use Smartmage\Inpost\Controller\Adminhtml\Shipments\AbstractSave;

class Courier extends AbstractSave
{

    protected function processShippment()
    {
        return $this->courier->setSampleData()->createShipment();
    }
}
