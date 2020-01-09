<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Controller\Adminhtml\Shipment;

use Dhl\ShippingCore\Api\Data\TrackResponse\TrackErrorResponseInterface;
use Dhl\ShippingCore\Api\Data\TrackResponse\TrackResponseInterface;
use Dhl\ShippingCore\Model\BulkShipment\BulkShipmentManagement;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Cancel
 *
 */
class Cancel extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::ship';

    /**
     * @var BulkShipmentManagement
     */
    private $bulkShipmentManagement;

    /**
     * Cancel constructor.
     *
     * @param Context $context
     * @param BulkShipmentManagement $bulkShipmentManagement
     */
    public function __construct(Context $context, BulkShipmentManagement $bulkShipmentManagement)
    {
        $this->bulkShipmentManagement = $bulkShipmentManagement;

        parent::__construct($context);
    }

    /**
     * Cancel shipment order, delete tracks and shipping label.
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $shipmentId = (int) $this->getRequest()->getParam('shipment_id');

        $result = $this->bulkShipmentManagement->cancelLabels([$shipmentId]);
        $processResult = function (array $trackingNumbers, TrackResponseInterface $trackResponse) {
            $trackingNumber = $trackResponse->getTrackNumber();
            if ($trackResponse instanceof TrackErrorResponseInterface) {
                // collect errors
                $trackingNumbers['error'][] = $trackingNumber;
                $this->messageManager->addErrorMessage($trackResponse->getErrors());
            } else {
                // collect successfully cancelled tracks
                $trackingNumbers['success'][] = $trackingNumber;
            }

            return $trackingNumbers;
        };

        $trackResponses = array_reduce($result, $processResult, ['success' => [], 'error' => []]);

        if (empty($trackResponses['error']) && !empty($trackResponses['success'])) {
            // no errors during cancellation
            $msg = __('The shipment was successfully cancelled.');
            $this->messageManager->addSuccessMessage($msg);
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('adminhtml/order_shipment/view', ['shipment_id' => $shipmentId]);
    }
}
