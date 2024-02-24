<?php

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Dimensions extends AbstractSource
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('Dimension A'), 'value' => 'a'],
                ['label' => __('Dimension B'), 'value' => 'b'],
                ['label' => __('Dimension C'), 'value' => 'c']
            ];
        }
        return $this->_options;
    }
}
