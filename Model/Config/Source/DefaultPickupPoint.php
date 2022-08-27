<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Smartmage\Inpost\Model\ApiShipx\Service\Point\GetDispatchPoints;

class DefaultPickupPoint implements OptionSourceInterface
{
    /**
     * @var \Smartmage\Inpost\Model\ApiShipx\Service\Point\GetDispatchPoints
     */
    protected $getDispatchPoints;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var PsrLoggerInterface
     */
    protected $logger;

    /**
     * DefaultPickupPoint constructor.
     * @param \Smartmage\Inpost\Model\ApiShipx\Service\Point\GetDispatchPoints $getDispatchPoints
     * @param \Magento\Framework\Message\ManagerInterface $messageManagerInterface
     */
    public function __construct(
        GetDispatchPoints $getDispatchPoints,
        MessageManagerInterface $messageManagerInterface,
        PsrLoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->getDispatchPoints = $getDispatchPoints;
        $this->messageManager = $messageManagerInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() : array
    {
        $data = [];

        try {
            $dispatchPoints = $this->getDispatchPoints->getAllDispatchPoints();
            if (isset($dispatchPoints['items'])) {
                foreach ($dispatchPoints['items'] as $item) {
                    $data[] = [
                        'value' => $item['id'],
                        'label' => $item['name'] . ' ' . $item['address']['city'] . ' '
                            . $item['address']['post_code'] .
                            ' ' . $item['address']['street'] . ' ' . $item['address']['building_number']
                    ];
                }
            } else {
                $data[] = [
                    'value' => '',
                    'label' => __("You Don't have any dispatch points")
                ];
            }
        } catch (\Exception $e) {
            $this->logger->info(print_r($e, true));
        }

        return $data;
    }
}
