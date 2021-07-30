<?php

namespace Smartmage\Inpost\Ui\DataProvider\Inpostshipment;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class DataProvider
 */
class CreateDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return [];
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        return;
    }



}
