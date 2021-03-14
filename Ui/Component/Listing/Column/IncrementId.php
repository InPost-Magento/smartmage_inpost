<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class IncrementId
 * @package Smartmage\Inpost\Ui\Component\Listing\Column
 */
class IncrementId extends Column
{
    private $orderInterface;
    private $urlBuilder;
    /**
     * TrackingNumber constructor.
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderInterface $orderInterface,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->orderInterface = $orderInterface;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['increment_id'])) {
                    $url = $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $item['order_id']]);
                    $item['increment_id'] =
                        '<a target="blank" href="' . $url . '">' . $item['increment_id'] . '</a>';
                }
            }
        }

        return $dataSource;
    }
}
