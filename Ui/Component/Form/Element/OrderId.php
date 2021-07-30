<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

class OrderId extends AbstractInput
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'order_id') {
            $config['default'] = $data['order_id'];
            $this->setData('config', (array)$config);
        }
    }
}
