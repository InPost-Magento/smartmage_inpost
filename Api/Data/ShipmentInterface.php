<?php

namespace Smartmage\Inpost\Api\Data;

interface ShipmentInterface
{
    const ENTITY_ID = 'entity_id';
    const SHIPMENT_ID = 'shipment_id';
    const STATUS = 'status';
    const SERVICE = 'service';
    const SHIPMENT_ATTRIBUTES = 'shipment_attributes';
    const SENDING_METHOD = 'sending_method';
    const RECEIVER_DATA = 'receiver_data';
    const REFERENCE = 'reference';

    /**
     * Get Id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get email
     *
     * @return string|null
     */
    public function getShipmentId();

    /**
     * Get product id
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Get website id
     *
     * @return string|null
     */
    public function getService();

    /**
     * Get add date
     *
     * @return string|null
     */
    public function getShipmentsAttributes();

    /**
     * Get send date
     *
     * @return string|null
     */
    public function getSendingMethod();

    /**
     * Get send count
     *
     * @return int|null
     */
    public function getReceiverData();

    /**
     * Get status
     *
     * @return int|null
     */
    public function getReference();

    /**
     * Set Id
     *
     * @param int $entityId
     * @return $this
     */
    public function setId($entityId);

    /**
     * Set email
     *
     * @param int $shipmentId
     * @return $this
     */
    public function setShipmentId($shipmentId);

    /**
     * Set product id
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Set website id
     *
     * @param string $service
     * @return $this
     */
    public function setService($service);

    /**
     * Set add date
     *
     * @param string $shipmentAttributes
     * @return $this
     */
    public function setShipmentAttributes($shipmentAttributes);

    /**
     * Set send date
     *
     * @param string $sendingMethod
     * @return $this
     */
    public function setSendingMethod($sendingMethod);

    /**
     * Set send count
     *
     * @param string $receiverData
     * @return $this
     */
    public function setReceiverData($receiverData);

    /**
     * Set status
     *
     * @param string $reference
     * @return $this
     */
    public function setReference($reference);
}
