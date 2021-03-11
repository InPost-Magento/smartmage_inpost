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
class CreateDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * CreateDataProvider constructor.
     * @param $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        OrderRepositoryInterface $orderRepository,
        array $meta = [],
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        //$orderId = $this->request->getParam('order_id');
        //$order = $this->orderRepository->get($orderId);

        //$data[]['items']['order_details'] = '123';
        //$this->request->getParam('service_id')
        //[1 => ['shipment_fieldset' => $item]]


        if (isset($this->loadedData)) {
            //return $this->loadedData;
        }

        //$this->loadedData['0']['shipment_fieldset'] = ['target_locker' => '123'];
        //$this->loadedData[0]['target_locker'] = '123';
        //return $this->loadedData;
        return [];
    }

    /**
     * Get meta information
     *
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        return $meta;
    }
}
