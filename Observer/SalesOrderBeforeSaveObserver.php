<?php

namespace Smartmage\Inpost\Observer;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Verify order payment observer
 */
class SalesOrderBeforeSaveObserver implements ObserverInterface
{
    protected $request;

    public function __construct(Http $request)
    {
        $this->request = $request;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $orderPostData = $this->request->getPostValue('order');

        if (is_array($orderPostData) && array_key_exists('inpost_locker_id', $orderPostData)) {
            $inpostLockerId = $orderPostData['inpost_locker_id'];
            if (!empty($inpostLockerId)) {
                $order->setData('inpost_locker_id', $inpostLockerId);
            }
        }

        return $this;
    }
}
