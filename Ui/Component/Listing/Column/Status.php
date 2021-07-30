<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Smartmage\Inpost\Model\Config\Source\Status as StatusSource;

/**
 * Class Status for rendering url to tracking
 */
class Status extends Column
{

    protected $statusSource;

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
        StatusSource $statusSource,
        array $components = [],
        array $data = []
    ) {
        $this->statusSource = $statusSource;
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
                if (isset($item['status'])) {

                    if (isset($item['tracking_number_tmp'])) {
                        $item['status'] =
                            '<a target="blank" href="https://inpost.pl/sledzenie-przesylek?number='
                            . $item['tracking_number_tmp'] . '">' . $this->statusSource->getStatusLabel($item['status']) . '</a>';
                    } else {
                        $item['status'] = $this->statusSource->getStatusLabel($item['status']);
                    }
                }
            }
        }

        return $dataSource;
    }
}
