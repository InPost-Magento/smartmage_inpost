<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

class Service extends AbstractSelect
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'service') {
            $config['default'] = $data['shipping_method'];

            $this->setData('config', (array)$config);
        }
    }
}
