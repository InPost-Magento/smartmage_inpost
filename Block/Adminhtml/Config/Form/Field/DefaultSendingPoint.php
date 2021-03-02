<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Block\Adminhtml\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class DefaultSendingPoint
 * @package Smartmage\Inpost\Block\Adminhtml\Config\Form\Field
 */
class DefaultSendingPoint extends Field
{
    protected $code = '';

    public function __construct(
        Context $context,
        array $data = [],
        $code = null
    ) {
        parent::__construct($context, $data);
        $this->code = $code;
    }

    /**
     * @inheritDoc
     */
    public function render(AbstractElement $element) : string
    {
        $html = parent::render($element);
        return $html . '
        <script type="text/x-magento-init">
            {
                "*": {
                    "Smartmage_Inpost/js/easyPackWidget": {
                        "wrapper":"'.$this->code.'"
                    }
                }
            }        
        </script>';
    }
}
