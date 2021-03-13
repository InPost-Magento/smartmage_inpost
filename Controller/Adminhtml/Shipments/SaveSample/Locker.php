<?php

namespace Smartmage\Inpost\Controller\Adminhtml\Shipments\SaveSample;

use Smartmage\Inpost\Controller\Adminhtml\Shipments\AbstractSave;
use Smartmage\Inpost\Model\ApiShipx\CallResult;

class Locker extends AbstractSave
{

    protected function processShippment()
    {
        $result = $this->locker->setSampleData()->createShipment();
        $this->messageManager->addSuccessMessage($result[CallResult::STRING_MESSAGE]);
    }
}
