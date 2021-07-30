<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element\Address;

use Smartmage\Inpost\Ui\Component\Form\Element\AbstractInput;

class Email extends AbstractInput
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
        $data= $this->request->getParams();

        if (isset($config['dataScope']) && $config['dataScope'] == 'email') {
            if (strpos($data['shipping_method'], 'inpostlocker') !== false) {
                $config['validation']['required-entry'] = true;
            }
            if (isset($data['email'])) {
                $config['default'] = $data['email'];
            } else {
                $config['default'] = $this->order->getCustomerEmail();
            }
            $this->setData('config', (array)$config);
        }
    }
}
