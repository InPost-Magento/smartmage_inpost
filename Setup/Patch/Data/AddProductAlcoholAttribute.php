<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\Source\Boolean;

class AddProductAlcoholAttribute extends AbstractProductAttributePatch
{
    protected const GROUP_NAME = 'General';
    protected const SORT_ORDER = 32769;

    protected static function attributeCode(): string
    {
        return 'inpost_alcohol';
    }

    protected function attributeDefinition(): array
    {
        return [
            'type'   => 'int',
            'label' => 'Send with InPost SmartCourier',
            'input'  => 'boolean',
            'source' => Boolean::class,
            'default'=> 0,
        ];
    }
}
