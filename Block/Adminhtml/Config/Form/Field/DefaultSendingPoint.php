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
    protected $points = '';

    public function __construct(
        Context $context,
        array $data = [],
        $code = null,
        $points = null
    ) {
        parent::__construct($context, $data);
        $this->code = $code;
        $this->points = $points;
    }

    /**
     * @inheritDoc
     */
    public function render(AbstractElement $element) : string
    {
        //inpostlocker_standardeow
        $html = parent::render($element);
        $points = '';

        switch ($this->points) {
            case 'standard':
                $points = json_encode(['parcel_locker', 'pop']);
                break;
            case 'parcel_locker':
                $points = json_encode(['parcel_locker_only']);
                break;
            default:
                $points = json_encode(['pop']);
                break;
        }
        return $html . '
        <script type="text/x-magento-init">
            {
                "*": {
                    "Smartmage_Inpost/js/easyPackWidget": {
                        "wrapper":"' . $this->code . '",
                        "points": ' . $points . '
                    }
                }
            }
        </script>';
    }
}
