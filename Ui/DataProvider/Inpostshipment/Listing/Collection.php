<?php
namespace Smartmage\Inpost\Ui\DataProvider\Inpostshipment\Listing;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    /**
     * Override _initSelect to add custom columns
     *
     * @return void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
             ->joinleft(
                 ['cpe' => new \Zend_Db_Expr('(SELECT * FROM `smartmage_inpost_shipment_order_link`)')],
                 'main_table.shipment_id = cpe.shipment_id',
                 ['increment_id' => 'increment_id']
             );
//              ->joinleft(
//                 ['cpev' => new \Zend_Db_Expr('(SELECT * FROM `catalog_product_entity_varchar` GROUP BY row_id, attribute_id)')],
//                 'cpe.row_id = cpev.row_id and cpev.attribute_id = 73',
//                 ['product_name' => 'value']
//             )->joinleft(
//                ['cpei' => $this->getTable('catalog_product_entity_int')],
//                'cpe.row_id = cpei.row_id and cpei.attribute_id = 401 and cpei.store_id = 0',
//                ['product_season_id' => 'value']
//            )->joinleft(
//                ['eaov' => $this->getTable('eav_attribute_option_value')],
//                'eaov.option_id = cpei.value and eaov.store_id = 0',
//                ['product_season' => 'value']
//            );
//        $this->addFilterToMap('alert_stock_id', 'main_table.alert_stock_id');
//        $this->addFilterToMap('product_name', 'cpev.value');
//        $this->addFilterToMap('product_season', 'eaov.value');
    }

}
