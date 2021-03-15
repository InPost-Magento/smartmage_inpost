<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element\Address;

use Smartmage\Inpost\Ui\Component\Form\Element\AbstractInput;

class Phone extends AbstractInput
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'phone') {
            if (isset($data['phone'])) {
                $config['default'] = $data['phone'];
            } else {
                $config['default'] = $this->order->getShippingAddress()->getTelephone();
            }
            $this->setData('config', (array)$config);
        }
    }
}
