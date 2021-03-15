<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

class TargetLocker extends AbstractInput
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'target_locker') {
            if (isset($data['target_locker'])) {
                $config['default'] = $data['target_locker'];
            } else {
                $extensionAttributes = $this->order->getExtensionAttributes();
                $config['default'] = $extensionAttributes->getInpostLockerId();
            }

            $this->setData('config', (array)$config);
        }
    }
}
