<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Validator\ValidateException;

/**
 * Class AddProductBlockSendWithLockerAttribute which adds an attribute block_send_with_locker
 */
class AddProductBlockSendWithLockerAttribute implements DataPatchInterface, PatchRevertableInterface
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
    protected ModuleDataSetupInterface $moduleDataSetup;
    protected EavSetupFactory $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    final public function apply(): self
    {
        $this->db()->startSetup();
        try {
            $this->ensureAttributeExistsAndAssigned();
        } finally {
            $this->db()->endSetup();
        }
        return $this;
    }

    final public function revert(): void
    {
        $this->db()->startSetup();
        try {
            $this->safeRemoveAttribute(static::attributeCode());
        } finally {
            $this->db()->endSetup();
        }
    }

    public static function getDependencies(): array { return []; }
    public function getAliases(): array { return []; }

    /**
     * @throws ValidateException
     * @throws LocalizedException
     */
    protected function ensureAttributeExistsAndAssigned(): void
    {
        $eav = $this->eav();
        $code = static::attributeCode();

        $existing = $eav->getAttribute(Product::ENTITY, $code);
        if (!$existing || empty($existing['attribute_id'])) {
            $definition = $this->withSafeDefaults($this->attributeDefinition());
            $eav->addAttribute(Product::ENTITY, $code, $definition);
        }

        $this->assignToAllSets($code, static::GROUP_NAME, static::SORT_ORDER);
    }

    /**
     * @throws LocalizedException
     */
    protected function assignToAllSets(string $code, string $groupName, int $sortOrder): void
    {
        $eav = $this->eav();
        $entityTypeId = (int) $eav->getEntityTypeId(Product::ENTITY);

        $sets = $this->db()->fetchCol(
            $this->db()->select()
                ->from($this->table('eav_attribute_set'), ['attribute_set_id'])
                ->where('entity_type_id = ?', $entityTypeId)
        );

        foreach ($sets as $setId) {
            $groupId = (int) $eav->getAttributeGroupId($entityTypeId, (int)$setId, $groupName);
            if (!$groupId) {
                $eav->addAttributeGroup($entityTypeId, (int)$setId, $groupName);
                $groupId = (int) $eav->getAttributeGroupId($entityTypeId, (int)$setId, $groupName);
            }
            try {
                $eav->addAttributeToGroup(
                    $entityTypeId,
                    (int)$setId,
                    $groupId,
                    $code,
                    $sortOrder
                );
            } catch (\Throwable $e) {
                // Attribute already in a group
            }
        }
    }

    protected function safeRemoveAttribute(string $code): void
    {
        $eav = $this->eav();
        if ($eav->getAttribute(Product::ENTITY, $code)) {
            $eav->removeAttribute(Product::ENTITY, $code);
        }
    }

    protected function withSafeDefaults(array $def): array
    {
        return $def + [
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible_on_front' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'used_in_product_listing' => false,
                'sort_order' => static::SORT_ORDER,
                'required' => false,
            ];
    }

    protected function eav(): EavSetup
    {
        return $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
    }

    protected function db(): AdapterInterface
    {
        return $this->moduleDataSetup->getConnection();
    }

    protected function table(string $name): string
    {
        return $this->moduleDataSetup->getTable($name);
    }
}
