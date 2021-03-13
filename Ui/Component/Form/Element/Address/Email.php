<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element\Address;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class Email extends \Magento\Ui\Component\Form\Element\Input
{
    /**
     * @var Http
     */
    protected $request;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * OrderDetails constructor.
     * @param Http $request
     * @param OrderRepositoryInterface $orderRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param ContextInterface $context
     * @param array $components
     * @param array $data
     */
    public function __construct(
        Http $request,
        OrderRepositoryInterface $orderRepository,
        PriceCurrencyInterface $priceCurrency,
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->priceCurrency = $priceCurrency;
    }

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
        $order = $this->orderRepository->get($data['order_id']);

        /*<item name="config" xsi:type="array">
                    <item name="label" xsi:type="string" translate="true">Length</item>
                    <item name="dataType" xsi:type="string">text</item>
                    <item name="formElement" xsi:type="string">input</item>
                    <item name="dataScope" xsi:type="string">length</item>
                    <item name="class" xsi:type="string">Smartmage\Inpost\Ui\Component\Form\Element\Length</item>
                    <item name="validation" xsi:type="array">
                        <item name="required-entry" xsi:type="boolean">true</item>
                        <item name="validate-digits" xsi:type="boolean">true</item>
                        <item name="validate-greater-than-zero" xsi:type="boolean">true</item>
                    </item>
                </item>*/

        if (isset($config['dataScope']) && $config['dataScope'] == 'email') {
            if (strpos($data['shipping_method'], 'inpostlocker') !== false) {
                $config['validation']['required-entry'] = true;
            }
            if (isset($data['email'])) {
                $config['default'] = $data['email'];
            } else {
                $config['default'] = $order->getCustomerEmail();
            }
            $this->setData('config', (array)$config);
        }
    }
}
