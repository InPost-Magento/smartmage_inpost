<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

class Reference extends AbstractInput
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'reference') {
            if (isset($data['reference'])) {
                $config['default'] = $data['reference'];
            } else {
                $config['default'] = $this->order->getIncrementId();
            }
            $this->setData('config', (array)$config);
        }
    }
}
