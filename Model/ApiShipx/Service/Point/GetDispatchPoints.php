<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\ApiShipx\Service\Point;

use Magento\Framework\App\Response\Http;
use Smartmage\Inpost\Model\ApiShipx\AbstractService;
use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\ApiShipx\ErrorHandler;
use Smartmage\Inpost\Model\ConfigProvider;

/**
 * Class GetDispatchPoints
 * @package Smartmage\Inpost\Model\ApiShipx\Service\Point
 */
class GetDispatchPoints extends AbstractService
{
    /**
     * @var int
     */
    protected $method = CURLOPT_HTTPGET;

    /**
     * @var int
     */
    protected $successResponseCode = Http::STATUS_CODE_200;

    /**
     * @var string
     */
    protected $callUri;

    /**
     * GetDispatchPoints constructor.
     * @param \Smartmage\Inpost\Model\ConfigProvider $configProvider
     * @param ErrorHandler $errorHandler
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        ConfigProvider $configProvider,
        ErrorHandler $errorHandler
    )
    {
        $organizationId = $configProvider->getOrganizationId();
        $this->callUri = '/v1/organizations/' . $organizationId . '/dispatch_points';
        parent::__construct($configProvider, $errorHandler);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getAllDispatchPoints()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inpost.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $result = $this->call();

        if ($this->callResult[CallResult::STRING_STATUS] != CallResult::STATUS_SUCCESS) {
            throw new \Exception(
                $this->callResult[CallResult::STRING_MESSAGE],
                $this->callResult[CallResult::STRING_RESPONSE_CODE]
            );
        }
        if (isset($result['items']) && !empty($result['items'])) {
            $this->callResult['items'] = $result['items'];
        }
        $logger->info('getAllDispatchPoints');
        $logger->info($result);
        return $this->callResult;
    }
}
