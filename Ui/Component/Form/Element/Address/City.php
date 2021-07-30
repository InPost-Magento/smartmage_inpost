<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element\Address;

use Smartmage\Inpost\Ui\Component\Form\Element\AbstractInput;

class City extends AbstractInput
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'city') {
            if (isset($data['city'])) {
                $config['default'] = $data['city'];
            } else {
                $config['default'] = $this->order->getShippingAddress()->getCity();
            }
            $this->setData('config', (array)$config);
        }
    }
}
