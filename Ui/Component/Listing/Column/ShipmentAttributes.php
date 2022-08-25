<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Smartmage\Inpost\Model\Config\Source\Size as SizeConfig;

class ShipmentAttributes extends Column
{
    /**
     * @var \Smartmage\Inpost\Model\Config\Source\Size
     */
    protected $sizeConfig;

    /**
     * ShipmentAttributes constructor.
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Smartmage\Inpost\Model\Config\Source\Size $sizeConfig
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SizeConfig $sizeConfig,
        array $components = [],
        array $data = []
    ) {
        $this->sizeConfig = $sizeConfig;
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
                if (isset($item['shipment_attributes'])) {
                    $item['shipment_attributes'] = $this->sizeConfig->getSizeLabel($item['shipment_attributes']);
                }
            }
        }
        return $dataSource;
    }
}
