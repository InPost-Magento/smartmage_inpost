<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\Order\Processor as OrderProcessor;

abstract class AbstractInput extends \Magento\Ui\Component\Form\Element\Input
{

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var OrderProcessor
     */
    protected $orderProcessor;

    protected $order;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * AbstractInput constructor.
     * @param Http $request
     * @param OrderProcessor $orderProcessor
     * @param PriceCurrencyInterface $priceCurrency
     * @param ConfigProvider $configProvider
     * @param ContextInterface $context
     * @param array $components
     * @param array $data
     */
    public function __construct(
        Http $request,
        OrderProcessor $orderProcessor,
        PriceCurrencyInterface $priceCurrency,
        ConfigProvider $configProvider,
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);

        $this->request = $request;
        $this->orderProcessor = $orderProcessor;
        $this->priceCurrency = $priceCurrency;
        $this->configProvider = $configProvider;
        $this->order = $orderProcessor->getOrder($request->getParam('order_id'));
    }
}
