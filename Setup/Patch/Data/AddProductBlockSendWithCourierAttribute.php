<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class AddProductBlockSendWithCourierAttribute implements DataPatchInterface, PatchRevertableInterface
{

    const BLOCK_SEND_COURIER_ATTRIBUTE_NAME = 'block_send_with_courier';

    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * AddProductSendLockerAttribute constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(): AddProductBlockSendWithCourierAttribute
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            Product::ENTITY,
            self::BLOCK_SEND_COURIER_ATTRIBUTE_NAME,
            [
                'type' => 'int',
                'label' => 'Block send with InPost Courier',
                'input' => 'boolean',
                'user_defined' => false,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General',
                'source' => Boolean::class,
                'default' => 0,
                'sort_order' => 99998,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
            ]
        );

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(
            Product::ENTITY,
            self::BLOCK_SEND_COURIER_ATTRIBUTE_NAME
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }

}
