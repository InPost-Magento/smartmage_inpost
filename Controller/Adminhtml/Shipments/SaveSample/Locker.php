<?php

namespace Smartmage\Inpost\Controller\Adminhtml\Shipments\SaveSample;

use Smartmage\Inpost\Controller\Adminhtml\Shipments\AbstractSave;

class Locker extends AbstractSave
{

    protected function processShippment()
    {
        return $this->locker->setSampleData()->createShipment();
    }
}
