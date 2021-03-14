<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\Order\Processor as OrderProcessor;

class Weight extends \Magento\Ui\Component\Form\Element\Input
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
     * @var OrderProcessor
     */
    protected $orderProcessor;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * Weight constructor.
     * @param Http $request
     * @param OrderRepositoryInterface $orderRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param OrderProcessor $orderProcessor
     * @param ContextInterface $context
     * @param ConfigProvider $configProvider
     * @param array $components
     * @param array $data
     */
    public function __construct(
        Http $request,
        OrderRepositoryInterface $orderRepository,
        PriceCurrencyInterface $priceCurrency,
        OrderProcessor $orderProcessor,
        ContextInterface $context,
        ConfigProvider $configProvider,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->priceCurrency = $priceCurrency;
        $this->orderProcessor = $orderProcessor;
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
        $order = $this->orderRepository->get($data['order_id']);

        if (isset($config['dataScope']) && $config['dataScope'] == 'weight') {
            $config['label'] = __('Weight') . ' (' . __($this->configProvider->getWeightUnit()) . ')';
            if (isset($data['weight'])) {
                $config['default'] = $data['weight'];
            } else {
                $config['default'] = $this->orderProcessor->setOrder($order)->getOrderWeight();
            }

            $this->setData('config', (array)$config);
        }
    }
}
