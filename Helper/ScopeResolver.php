<?php

namespace Sailthru\MageSail\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Webapi\Rest\Request as WebapiRequest;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;

class ScopeResolver extends \Magento\Framework\App\Helper\AbstractHelper
{

    /** @var OrderRepositoryInterface|OrderRepository */
    protected $orderRepo;

    /** @var ShipmentRepositoryInterface|ShipmentRepository  */
    protected $shipmentRepo;

    /** @var StoreManagerInterface|StoreManager */
    protected $storeManager;

    /** @var WebapiRequest  */
    protected $webApiRequest;

    /** @var Emulation  */
    protected $emulation;

    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepo,
        ShipmentRepositoryInterface $shipmentRepo,
        StoreManagerInterface $storeManager,
        WebapiRequest $webApiRequest,
        Emulation $emulation
    ) {
        parent::__construct($context);
        $this->orderRepo = $orderRepo;
        $this->shipmentRepo = $shipmentRepo;
        $this->storeManager = $storeManager;
        $this->webApiRequest = $webApiRequest;
        $this->emulation = $emulation;
    }

    public function setStoreId($storeId = null)
    {
        if ($storeId){
            $this->_request->setParams(["store"=>$storeId]);
        }
    }

    /**
     * Attempts to resolve (simplistically) website scope
     * Returns null if there's an issue.
     */
    public function resolveWebsiteId()
    {
        return $this->_request->getParam('website');
    }

    /**
     * Attempts to resolve store scope based on a few request params.
     * Returns null if there's an issue.
     * @return int|null
     */
    public function resolveRequestedStoreId()
    {
        if ($this->getOrderId() || $this->getShipmentId()) {
            return $this->getFromSalesScope();
        }

        if ($storeId = $this->getStoreIdParam()) {
            return $storeId;
        }

        return null;
    }

    /**
     * Attempts to resolve store scope based on possible sales-related request params
     * @return int|null
     */
    protected function getFromSalesScope()
    {
        $orderId = $this->getOrderId();
        $shipmentId = $this->getShipmentId();
        $entityId = $orderId ?: $shipmentId;
        $repository = $orderId ? $this->orderRepo : $this->shipmentRepo;

        try {
            $entity = $repository->get($entityId);
        } catch (\Exception $e) {
            $this->_logger->error("Error resolving sales scope.", $e);
            return null;
        }

        $this->_logger->info("resolved from order ID");
        return $entity->getStoreId();
    }

    /**
     * Try to retrieve order id from request parameters
     * @return string|null
     */
    protected function getOrderId()
    {
        return $this->_request->getParam('order_id') ?: $this->webApiRequest->getParam('orderId');
    }

    /**
     * Try to retrieve shipment id from request parameters
     * @return string|null
     */
    protected function getShipmentId()
    {
        return $this->_request->getParam('shipment_id');
    }

    /**
     * Try to retrieve store ID from request params
     * @return string|null
     */
    protected function getStoreIdParam()
    {
        return $this->_request->getParam('store');
    }

    public function emulateStore($storeId)
    {
        $this->setStoreId($storeId);
        $this->emulation->startEnvironmentEmulation($storeId);
    }

    public function stopEmulation() {
        $this->emulation->stopEnvironmentEmulation();
    }

}