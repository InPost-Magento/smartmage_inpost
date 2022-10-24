<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

class Insurance extends AbstractInput
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'insurance') {

            if (isset($data['insurance'])) {
                $config['default'] = $data['insurance'];
            } else {
                if ($this->configProvider->getAutomaticInsuranceForPackage()) {
                    $config['default'] = $this->priceCurrency->convertAndRound($this->order->getGrandTotal());
                } elseif($this->configProvider->getDefaultInsuranceValue()) {
                    $config['default'] = $this->configProvider->getDefaultInsuranceValue();
                }
            }

            $this->setData('config', (array)$config);

        }
    }
}
