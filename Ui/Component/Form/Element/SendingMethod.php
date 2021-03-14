<?php

namespace Smartmage\Inpost\Ui\Component\Form\Element;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Message\Manager;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
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

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var Manager
     */
    protected $messageManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * SendingMethod constructor.
     * @param Http $request
     * @param OrderRepositoryInterface $orderRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param ContextInterface $context
     * @param DefaultWaySending $defaultWaySending
     * @param ConfigProvider $configProvider
     * @param Manager $messageManager
     * @param UrlInterface $urlBuilder
     * @param null $options
     * @param array $components
     * @param array $data
     */
    public function __construct(
        Http $request,
        OrderRepositoryInterface $orderRepository,
        PriceCurrencyInterface $priceCurrency,
        ContextInterface $context,
        DefaultWaySending $defaultWaySending,
        ConfigProvider $configProvider,
        Manager $messageManager,
        UrlInterface $urlBuilder,
        $options = null,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $options, $components, $data);
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->priceCurrency = $priceCurrency;
        $this->defaultWaySending = $defaultWaySending;
        $this->configProvider = $configProvider;
        $this->messageManager = $messageManager;
        $this->urlBuilder = $urlBuilder;
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
