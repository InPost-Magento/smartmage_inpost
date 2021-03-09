<?php
namespace Smartmage\Inpost\Observer;

use Magento\Framework\DataObject\Copy\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;

class SaveOrderBeforeSalesModelQuoteObserver implements ObserverInterface
{
    protected $fieldsetConfig;

    protected $orderInterface;

    public function __construct(
        Config $fieldsetConfig,
        OrderInterface $orderInterface
    ) {
        $this->fieldsetConfig = $fieldsetConfig;
        $this->orderInterface = $orderInterface;
    }

    public function execute(Observer $observer)
    {
        $source = $observer->getEvent()->getQuote();
        $target = $observer->getEvent()->getOrder();
        $this->copyFieldsetToTarget('sales_convert_quote', 'to_order', 'global', $source, $target);

        return $this;
    }

    public function copyFieldsetToTarget($fieldset, $aspect, $root, $source, $target)
    {
        $fields = $this->fieldsetConfig->getFieldset($fieldset, $root);

        $methods = get_class_methods($this->orderInterface);

        foreach ($fields as $code => $node) {
            $targetCode = (string)$node[$aspect];
            $targetCode = $targetCode == '*' ? $code : $targetCode;

            if (!in_array($this->getMethodName($targetCode), $methods)) {
                $target->setData($targetCode, $source->getData($code));
            }
        }
    }

    public function getMethodName($key)
    {
        return 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
    }
}
