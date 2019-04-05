<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Controller\Adminhtml\Recipient\Street;

use Dhl\ShippingCore\Api\RecipientStreetInterface;
use Dhl\ShippingCore\Api\RecipientStreetRepositoryInterface;
use Dhl\ShippingCore\Model\RecipientStreet;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;

/**
 * Edit Controller
 *
 * @package Dhl\ShippingCore\Controller\Recipient\Street
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class Save extends Action
{
    const ADMIN_RESOURCE = 'Magento_Sales::actions_edit';

    /**
     * @var RecipientStreetRepositoryInterface
     */
    private $recipientStreetRepository;

    /**
     * Save constructor.
     * @param RecipientStreetRepositoryInterface $recipientStreetRepository
     * @param Context $context
     */
    public function __construct(
        RecipientStreetRepositoryInterface $recipientStreetRepository,
        Context $context
    ) {
        $this->recipientStreetRepository = $recipientStreetRepository;
        parent::__construct($context);
    }

    /**
     * Update the edited recipient street.
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $recipientStreetId = (int) $this->getRequest()->getParam('order_address_id');
        $orderId = (int) $this->getRequest()->getParam('order_id');

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);

        if (empty($recipientStreetId)) {
            return $resultRedirect;
        }

        try {
            /** @var RecipientStreet $street */
            $street = $this->recipientStreetRepository->get($recipientStreetId);
            $street->setData([
                RecipientStreetInterface::ORDER_ADDRESS_ID => $recipientStreetId,
                RecipientStreetInterface::NAME => (string) $this->getRequest()->getParam('name'),
                RecipientStreetInterface::NUMBER => (string) $this->getRequest()->getParam('number'),
                RecipientStreetInterface::SUPPLEMENT => (string) $this->getRequest()->getParam('supplement'),
            ]);
            $this->recipientStreetRepository->save($street);
            $this->messageManager->addSuccessMessage(__('Recipient street was successfully updated.'));
        } catch (\Exception $exception) {
            $message = __('An error occurred while updating the recipient street.');
            $this->messageManager->addExceptionMessage($exception, $message);

            $resultRedirect->setPath('*/*/edit');
        }

        return $resultRedirect;
    }
}
