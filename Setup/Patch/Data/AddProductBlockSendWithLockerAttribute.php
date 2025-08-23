<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\Source\Boolean;

/**
 * Class AddProductBlockSendWithLockerAttribute which adds an attribute block_send_with_locker
 */
class AddProductBlockSendWithLockerAttribute extends AbstractProductAttributePatch
{
    protected const GROUP_NAME = 'General';
    protected const SORT_ORDER = 32765;

    protected static function attributeCode(): string
    {
        return 'block_send_with_locker';
    }

    protected function attributeDefinition(): array
    {
        return [
            'type'   => 'int',
            'label' => 'Block send with Locker',
            'input'  => 'boolean',
            'source' => Boolean::class,
            'default'=> 0,
        ];
    }
}
