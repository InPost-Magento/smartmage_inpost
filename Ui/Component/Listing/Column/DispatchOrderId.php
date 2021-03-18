<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class TrackingNumber
 * @package Smartmage\Inpost\Ui\Component\Listing\Column
 */
class DispatchOrderId extends Column
{
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
        array $components = [],
        array $data = []
    ) {
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
                if ($item['sending_method'] != 'dispatch_order') {
                    $item['dispatch_order_id'] = __('Not applicable');
                }
            }
        }

        return $dataSource;
    }
}
