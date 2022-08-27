<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\ApiShipx\Service\Point;

use Magento\Framework\App\Response\Http;
use Smartmage\Inpost\Model\ApiShipx\AbstractService;
use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\ApiShipx\ErrorHandler;
use Smartmage\Inpost\Model\ConfigProvider;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

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
     * @var PsrLoggerInterface
     */
    protected $logger;

    /**
     * GetDispatchPoints constructor.
     * @param \Smartmage\Inpost\Model\ConfigProvider $configProvider
     * @param ErrorHandler $errorHandler
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        PsrLoggerInterface $logger,
        ConfigProvider $configProvider,
        ErrorHandler $errorHandler
    ) {
        $this->logger = $logger;
        $organizationId = $configProvider->getOrganizationId();
        $this->callUri = 'v1/organizations/' . $organizationId . '/dispatch_points';
        parent::__construct($logger, $configProvider, $errorHandler);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getAllDispatchPoints()
    {
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
        $this->logger->info('getAllDispatchPoints');
        $this->logger->info(print_r($result, true));
        return $this->callResult;
    }
}
