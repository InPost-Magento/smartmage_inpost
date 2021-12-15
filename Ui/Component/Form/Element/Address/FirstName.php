<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element\Address;

use Smartmage\Inpost\Ui\Component\Form\Element\AbstractInput;

class FirstName extends AbstractInput
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'first_name') {
            if (isset($data['first_name'])) {
                $config['default'] = $data['first_name'];
            } elseif($this->order->getCustomerFirstname()) {
                $config['default'] = $this->order->getCustomerFirstname();
            } else {
                $config['default'] = $this->order->getShippingAddress()->getFirstname();
            }
            $this->setData('config', (array)$config);
        }
    }
}
