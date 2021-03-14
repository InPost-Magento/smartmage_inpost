<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\ApiShipx\Service\DispatchOrder;

use Magento\Framework\App\Response\Http;
use Smartmage\Inpost\Model\ApiShipx\AbstractService;
use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\ApiShipx\ErrorHandler;
use Smartmage\Inpost\Model\ConfigProvider;

/**
 * Class Create
 * @package Smartmage\Inpost\Model\ApiShipx\Service\Point
 */
class Create extends AbstractService
{
    /**
     * @var int
     */
    protected $method = CURLOPT_POST;

    /**
     * @var int
     */
    protected $successResponseCode = Http::STATUS_CODE_201;

    /**
     * @var string
     */
    protected $callUri;
    /**
     * @var
     */
    protected $requestBody;

    /**
     * GetDispatchPoints constructor.
     * @param \Smartmage\Inpost\Model\ConfigProvider $configProvider
     * @param ErrorHandler $errorHandler
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        ConfigProvider $configProvider,
        ErrorHandler $errorHandler
    ) {
        $organizationId = $configProvider->getOrganizationId();
        $this->callUri = 'v1/organizations/' . $organizationId . '/dispatch_orders';
        parent::__construct($configProvider, $errorHandler);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function createDispatchOrders($dispatchData)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inpost.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $this->createBody($dispatchData);
        $this->call($this->requestBody);

        //throw if api fail
        if ($this->callResult[CallResult::STRING_STATUS] != CallResult::STATUS_SUCCESS) {
            throw new \Exception(
                $this->callResult[CallResult::STRING_MESSAGE],
                $this->callResult[CallResult::STRING_RESPONSE_CODE]
            );
        }

        return $this->callResult;
    }

    /**
     * @param $data
     */
    public function createBody($data)
    {
        $this->requestBody['shipments'] = $data['shipments'];
        $this->requestBody['dispatch_point_id'] = $data['dispatch_point_id'];
    }
}
