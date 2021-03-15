<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element\Address;

use Smartmage\Inpost\Ui\Component\Form\Element\AbstractInput;

class CompanyName extends AbstractInput
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'company_name') {
            if (isset($data['company_name'])) {
                $config['default'] = $data['company_name'];
            } else {
                $config['default'] = $this->order->getShippingAddress()->getCompany();
            }
            $this->setData('config', (array)$config);
        }
    }
}
