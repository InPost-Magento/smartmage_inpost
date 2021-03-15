<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

class Width extends AbstractInput
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'width') {
            if (isset($data['width'])) {
                $config['default'] = $data['width'];
                $this->setData('config', (array)$config);
            }
        }
    }
}
