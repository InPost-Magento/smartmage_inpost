<?php

namespace Smartmage\Inpost\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Abstract Class Shipments
 * @package Smartmage\Inpost\Controller\Adminhtml
 */
abstract class Shipments extends Action
{
    const ADMIN_RESOURCE = 'Smartmage_Inpost::shipments';

    /**
     * @param Action\Context $context
     */
    public function __construct(
        Action\Context $context
    ) {
        parent::__construct($context);
    }
}
