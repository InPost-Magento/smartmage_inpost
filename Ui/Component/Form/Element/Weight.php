<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

class Weight extends AbstractInput
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'weight') {
            $config['label'] = __('Weight') . ' (' . __('kg') . ')';
            if (isset($data['weight'])) {
                $config['default'] = $data['weight'];
            } else {
                $config['default'] = $this->orderProcessor->getOrderWeight();
            }

            $this->setData('config', (array)$config);
        }
    }
}
