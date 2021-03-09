<?php

namespace Smartmage\Inpost\Model\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartExtensionFactory;

class Processor
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var CartExtensionFactory
     */
    protected $cartExtensionFactory;

    /**
     * Processor constructor.
     * @param Session $checkoutSession
     * @param CartRepositoryInterface $cartRepository
     * @param CartExtensionFactory $cartExtensionFactory
     */
    public function __construct(
        Session $checkoutSession,
        CartRepositoryInterface $cartRepository,
        CartExtensionFactory $cartExtensionFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
        $this->cartExtensionFactory = $cartExtensionFactory;
    }

    /**
     * @param $inpostLockerId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setLockerId($inpostLockerId)
    {
        try {
            $quote = $this->checkoutSession->getQuote();
            $extensionAttributes = $this->cartExtensionFactory->create();
            $extensionAttributes->setInpostLockerId($inpostLockerId);
            $quote->setExtensionAttributes($extensionAttributes);
            $this->cartRepository->save($quote);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return true;
    }

    public function getLockerId()
    {
        try {
            $quote = $this->checkoutSession->getQuote();
            $extensionAttributes = $quote->getExtensionAttributes();
            return $extensionAttributes->getInpostLockerId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}
