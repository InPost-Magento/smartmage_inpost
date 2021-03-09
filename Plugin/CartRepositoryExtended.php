<?php

namespace Smartmage\Inpost\Plugin;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Api\Data\CartInterface;

class CartRepositoryExtended
{

    protected $cartExtensionFactory;

    /**
     * @param CartExtensionFactory $cartExtensionFactory
     */
    public function __construct(
        CartExtensionFactory $cartExtensionFactory
    ) {
        $this->cartExtensionFactory = $cartExtensionFactory;
    }

    /**
     * Enables an administrative user to return information for a specified cart.
     *
     * @param int $cartId
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGet(
        CartRepositoryInterface $cartRepository,
        CartInterface $cart
    ): \Magento\Quote\Api\Data\CartInterface {
        $this->loadExtensionAttributes($cart);
        return $cart;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param CartRepositoryInterface $cartRepository
     * @param CartInterface $cart
     * @return CartInterface
     */
    public function afterGetForCustomer(CartRepositoryInterface $cartRepository, CartInterface $cart)
    {
        $this->loadExtensionAttributes($cart);
        return $cart;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param CartRepositoryInterface $cartRepository
     * @param CartInterface $cart
     * @return CartInterface
     */
    public function afterGetActive(CartRepositoryInterface $cartRepository, CartInterface $cart)
    {
        $this->loadExtensionAttributes($cart);
        return $cart;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param CartRepositoryInterface $cartRepository
     * @param CartInterface $cart
     * @return CartInterface
     */
    public function afterGetActiveForCustomer(CartRepositoryInterface $cartRepository, CartInterface $cart)
    {
        $this->loadExtensionAttributes($cart);
        return $cart;
    }

    protected function loadExtensionAttributes(CartInterface &$cart)
    {
        $cartExtension = $cart->getExtensionAttributes();
        if ($cartExtension === null) {

            $cartExtension = $this->cartExtensionFactory->create();
        }

        $inpostLockerId = $cart->getData('inpost_locker_id');
        $cartExtension->setInpostLockerId($inpostLockerId);

        $cart->setExtensionAttributes($cartExtension);
    }

    /**
     * @param CartRepositoryInterface $subject
     * @param CartInterface $result
     * @return array
     */
    public function beforeSave(
        CartRepositoryInterface $subject,
        CartInterface $quote
    ) {
        $extensionAttributes = $quote->getExtensionAttributes() ?: $this->cartExtensionFactory->create();

        if ($extensionAttributes !== null && $extensionAttributes->getInpostLockerId() !== null) {
            $quote->setInpostLockerId($extensionAttributes->getInpostLockerId());
        }

        return [$quote];
    }

}
