<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element\Address;

use Smartmage\Inpost\Ui\Component\Form\Element\AbstractInput;

class Street extends AbstractInput
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'street') {
            if (isset($data['street'])) {
                $config['default'] = $data['street'];
            } else {
                $config['default'] = $this->orderProcessor->getStreet();
            }
            $this->setData('config', (array)$config);
        }
    }
}
