<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\Source\Boolean;

/**
 * Class AddProductBlockSendWithPaletteAttribute which adds an attribute block_send_with_palette
 */
class AddProductBlockSendWithPaletteAttribute extends AbstractProductAttributePatch
{
    protected const GROUP_NAME = 'General';
    protected const SORT_ORDER = 32767;

    protected static function attributeCode(): string
    {
        return 'block_send_with_palette';
    }

    protected function attributeDefinition(): array
    {
        return [
            'type'   => 'int',
            'label' => 'Block send with InPost Palette',
            'input'  => 'boolean',
            'source' => Boolean::class,
            'default'=> 0,
        ];
    }
}
