<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Sanitizer;
use Magento\Sales\Api\OrderRepositoryInterface;
use Smartmage\Inpost\Model\Config\Source\DefaultWaySending;
use Smartmage\Inpost\Model\ConfigProvider;

class SendingMethod extends \Magento\Ui\Component\Form\Element\Select
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
     * @var DefaultWaySending
     */
    protected $defaultWaySending;

    protected $configProvider;

    /**
     * SendingMethod constructor.
     * @param Http $request
     * @param OrderRepositoryInterface $orderRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param ContextInterface $context
     * @param DefaultWaySending $defaultWaySending
     * @param ConfigProvider $configProvider
     * @param null $options
     * @param array $components
     * @param array $data
     * @param Sanitizer|null $sanitizer
     */
    public function __construct(
        Http $request,
        OrderRepositoryInterface $orderRepository,
        PriceCurrencyInterface $priceCurrency,
        ContextInterface $context,
        DefaultWaySending $defaultWaySending,
        ConfigProvider $configProvider,
        $options = null,
        array $components = [],
        array $data = [],
        ?Sanitizer $sanitizer = null
    ) {
        parent::__construct($context, $options, $components, $data, $sanitizer);
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->priceCurrency = $priceCurrency;
        $this->defaultWaySending = $defaultWaySending;
        $this->configProvider = $configProvider;
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'sending_method') {
            $shippingMethod = $data['shipping_method'];
            $codes = explode('_', $shippingMethod);
            $this->defaultWaySending->setCode(str_replace('cod', '', $codes[1]));
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
