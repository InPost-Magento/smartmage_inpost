<?php

namespace Smartmage\Inpost\Model\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;

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
     * Save constructor.
     * @param Session $checkoutSession
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        Session $checkoutSession,
        CartRepositoryInterface $cartRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
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
            $extensionAttributes = $quote->getExtensionAttributes();
            $extensionAttributes->setInpostLockerId($inpostLockerId);
            $this->cartRepository->save($quote);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return true;
    }
}
