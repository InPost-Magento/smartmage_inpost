<?php

namespace Smartmage\Inpost\Setup\Patch\Data;

class AddProductDimensionAttribute extends AbstractProductAttributePatch
{
    protected const GROUP_NAME = 'General';
    protected const SORT_ORDER = 32768;

    protected static function attributeCode(): string
    {
        return 'inpost_dimension';
    }

    protected function attributeDefinition(): array
    {
        return [
            'type' => 'varchar',
            'label' => 'InPost package dimension',
            'input' => 'select',
            'source' => 'Smartmage\Inpost\Model\Config\Source\Dimensions',
            'default' => 0
        ];
    }
}
