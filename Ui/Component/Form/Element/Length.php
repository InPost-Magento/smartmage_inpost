<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

class Length extends AbstractInput
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'length') {
            if (isset($data['length'])) {
                $config['default'] = $data['length'];
                $this->setData('config', (array)$config);
            }
        }
    }
}
