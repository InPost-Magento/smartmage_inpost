<?php

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Dimensions extends AbstractSource
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __(' '), 'value' => ''],
                ['label' => __('Dimension A'), 'value' => 'small'],
                ['label' => __('Dimension B'), 'value' => 'medium'],
                ['label' => __('Dimension C'), 'value' => 'large']
            ];
        }
        return $this->_options;
    }
}
