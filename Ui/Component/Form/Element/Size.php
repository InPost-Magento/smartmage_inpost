<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Smartmage\Inpost\Model\Config\Source\DefaultWaySending;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\Config\Source\Size as SizeSource;

class Size extends AbstractSelect
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
        $shippingMethod = $data['shipping_method'];

        $this->size->setShippingMethod($shippingMethod);

        if (isset($config['dataScope']) && $config['dataScope'] == 'size') {
            $config['options'] = $this->size->toOptionArray();
            if (isset($data['size'])) {
                $config['default'] = $data['size'];
            }
            $this->setData('config', (array)$config);
        }
    }
}
