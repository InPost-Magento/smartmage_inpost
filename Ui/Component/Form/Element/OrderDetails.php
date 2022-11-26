<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

class OrderDetails extends AbstractInput
{

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();

        $config = $this->getData('config');
        $data = $this->request->getParams();

        if (isset($config['dataScope']) && $config['dataScope'] == 'order_details') {
            $config['default'] = $this->order->getIncrementId() . ' - '
                . $this->priceCurrency->convertAndRound($this->order->getGrandTotal())
                . ' ' . $this->order->getOrderCurrencyCode();

            $this->setData('config', (array)$config);
        }
    }
}
