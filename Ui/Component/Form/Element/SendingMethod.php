<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

class SendingMethod extends AbstractSelect
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'sending_method') {
            $shippingMethod = $data['shipping_method'];
            $codes = explode('_', $shippingMethod);

            $this->defaultWaySending->setCode($shippingMethod);

            $defaultSendingPoint = $this->configProvider->getConfigData(
                $codes[0] . '/' . $codes[1] . '/default_sending_point'
            );

            if (!$defaultSendingPoint) {
                $url = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/carriers');
                $this->messageManager->addComplexWarningMessage(
                    'warningInpostMessage',
                    [
                        'content' => 'The service does not have a '
                            . 'default drop point selected. If you send the parcel in a way '
                            . 'other than "Pickup by courier", please select the point in the',
                        'url' => $url
                    ]
                );
            }
            $config['options'] = $this->defaultWaySending->toOptionArray();

            if (isset($data['sending_method'])) {
                $config['default'] = $data['sending_method'];
            } else {
                $default = $this->configProvider->getConfigData($codes[0] . '/' . $codes[1] . '/default_way_sending');
                $config['default'] = $default;
            }

            $this->setData('config', (array)$config);
        }
    }
}
