<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

class Cod extends AbstractInput
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'cod') {
            if (isset($data['cod'])) {
                $config['default'] = $data['cod'];
            } else {
                if (strpos($data['shipping_method'], 'cod') !== false) {
                    $config['default'] = $this->priceCurrency->convertAndRound($this->order->getGrandTotal());
                }
            }
            $this->setData('config', (array)$config);
        }
    }
}
