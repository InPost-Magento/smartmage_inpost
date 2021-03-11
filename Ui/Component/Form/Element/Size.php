<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Sanitizer;
use Magento\Sales\Api\OrderRepositoryInterface;
use Smartmage\Inpost\Model\Config\Source\DefaultWaySending;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\Config\Source\Size as SizeSource;

class Size extends \Magento\Ui\Component\Form\Element\Select
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
     * @var SizeSource
     */
    protected $size;

    /**
     * Size constructor.
     * @param Http $request
     * @param OrderRepositoryInterface $orderRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param ContextInterface $context
     * @param SizeSource $size
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
        SizeSource $size,
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
        $this->size = $size;
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
        $shippingMethod = $data['shipping_method'];

        $this->size->setShippingMethod($shippingMethod);

        if (isset($config['dataScope']) && $config['dataScope'] == 'size') {
            $config['options'] = $this->size->toOptionArray();
            $this->setData('config', (array)$config);
        }
    }
}
