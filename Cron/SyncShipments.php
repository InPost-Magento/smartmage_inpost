<?php
declare(strict_types=1);
namespace Smartmage\Inpost\Cron;

use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Search\Multiple as SearchMultiple;

/**
 * Class SyncShipments
 * @package Smartmage\Inpost\Cron
 */
class SyncShipments
{
    /**
     * @var \Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Search\Multiple
     */
    private $searchMultiple;

    /**
     * SyncShipments constructor.
     * @param \Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Search\Multiple $searchMultiple
     */
    public function __construct(SearchMultiple $searchMultiple)
    {
        $this->searchMultiple = $searchMultiple;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function execute()
    {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inpost_cron.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('inpost syn start');
        $this->searchMultiple->getAllShipments();
        $logger->info('inpost syn end');

        return $this;
    }
}
