<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\Source\Boolean;

/**
 * Class AddProductBlockSendWithCourierAttribute which adds an attribute block_send_with_courier
 */
class AddProductBlockSendWithCourierAttribute extends AbstractProductAttributePatch
{
    protected const GROUP_NAME = 'General';
    protected const SORT_ORDER = 32766;

    protected static function attributeCode(): string
    {
        return 'block_send_with_courier';
    }

    protected function attributeDefinition(): array
    {
        return [
            'type'   => 'int',
            'label' => 'Block send with InPost Courier',
            'input'  => 'boolean',
            'source' => Boolean::class,
            'default'=> 0,
        ];
    }
}
