<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

use Magento\Framework\Exception\NoSuchEntityException;

class Size extends AbstractSelect
{

    /**
     * Prepare component configuration
     *
     * @return void
     * @throws NoSuchEntityException
     */
    public function prepare(): void
    {
        parent::prepare();

        $config = $this->getData('config');
        $data = $this->request->getParams();
        $shippingMethod = $data['shipping_method'];

        $this->size->setShippingMethod($shippingMethod);
        $this->size->setIncludeProductAttribute(false);

        if (isset($config['dataScope']) && $config['dataScope'] == 'size') {
            $config['options'] = $this->size->toOptionArray();
            if (isset($data['size'])) {
                $config['default'] = $data['size'];
            } else {
                $config['default'] = $this->configProvider->getDefaultSize($data['order_id']);
            }
            $this->setData('config', (array)$config);
        }
    }
}
