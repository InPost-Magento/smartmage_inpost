<?php
namespace Smartmage\Inpost\Block\Adminhtml\Shipment\Create;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Request\Http;
use Magento\Sales\Api\OrderRepositoryInterface;

class StreetInfo extends Template
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
     * Block template
     *
     * @var string
     */
    protected $_template = 'Smartmage_Inpost::shipment/create/street_info.phtml';

    public function __construct(
        Http $request,
        OrderRepositoryInterface $orderRepository,
        Context $context,
        array $data = []
    ) {
        $this->request = $request;
        $this->orderRepository = $orderRepository;

        $params = $this->request->getParams();

        if (strpos($params['shipping_method'], 'inpostlocker') !== false) {
            $this->_template = null;
        }
        parent::__construct($context, $data);

    }
}
