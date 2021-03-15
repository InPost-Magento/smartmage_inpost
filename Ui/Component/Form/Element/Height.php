<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

class Height extends AbstractInput
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'height') {
            if (isset($data['height'])) {
                $config['default'] = $data['height'];
                $this->setData('config', (array)$config);
            }
        }
    }
}
