<?php

namespace Smartmage\Inpost\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Class Shipment
 * @package Smartmage\Inpost\Controller\Adminhtml
 */
abstract class Shipments extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Smartmage_Inpost::shipments';

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * Report constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
    }
}
