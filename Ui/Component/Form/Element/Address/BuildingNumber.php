<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element\Address;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Smartmage\Inpost\Model\Order\Processor as OrderProcessor;

class BuildingNumber extends \Magento\Ui\Component\Form\Element\Input
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
        OrderProcessor $orderProcessor,
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->priceCurrency = $priceCurrency;
        $this->orderProcessor = $orderProcessor;
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

        if (isset($config['dataScope']) && $config['dataScope'] == 'building_number') {
            if (isset($data['building_number'])) {
                $config['default'] = $data['building_number'];
            } else {
                $config['default'] = $this->orderProcessor->setOrder($order)->getBuildingNumber();
            }
            $this->setData('config', (array)$config);
        }
    }
}
