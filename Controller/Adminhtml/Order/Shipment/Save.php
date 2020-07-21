<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Controller\Adminhtml\Order\Shipment;

use Dhl\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::shipment';

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Json $jsonSerializer
     */
    public function __construct(
        Context $context,
        Json $jsonSerializer
    ) {
        parent::__construct($context);

        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Processes the packaging popup and forwards the processed data to the Magento_Shipping order shipment controller.
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $data = $this->getRequest()->getParam('data');
        $data = $this->jsonSerializer->unserialize($data);

        // email confirmation flag, comment text
        $shipmentData = $data['shipment'] ?? [];
        // packaging popup contents
        $packagesData = $data['packages'] ?? [];

        $shipmentItems = [];
        $packages = [];

        foreach ($packagesData as $packageDetails) {
            $packageItems = [];

            $itemCustomsKey = Codes::ITEM_OPTION_ITEM_CUSTOMS;
            foreach ($packageDetails['items'] as $itemId => $itemDetails) {
                $itemCustomsValue = null;
                if (isset($itemDetails[$itemCustomsKey][Codes::ITEM_INPUT_CUSTOMS_VALUE])) {
                    $itemCustomsValue = $itemDetails[$itemCustomsKey][Codes::ITEM_INPUT_CUSTOMS_VALUE];
                    unset($itemDetails[$itemCustomsKey][Codes::ITEM_INPUT_CUSTOMS_VALUE]);
                }

                $detailsKey = Codes::ITEM_OPTION_DETAILS;
                $packageItem = [
                    'qty' => $itemDetails[$detailsKey][Codes::ITEM_INPUT_QTY] ?? '1',
                    'customs_value' => $itemCustomsValue,
                    'price' => $itemDetails[$detailsKey][Codes::ITEM_INPUT_PRICE] ?? '',
                    'name' => $itemDetails[$detailsKey][Codes::ITEM_INPUT_PRODUCT_NAME] ?? '',
                    'weight' => $itemDetails[$detailsKey][Codes::ITEM_INPUT_WEIGHT] ?? '',
                    'product_id' => $itemDetails[$detailsKey][Codes::ITEM_INPUT_PRODUCT_ID] ?? '',
                    'order_item_id' => $itemId,
                    'customs' => $itemDetails[$itemCustomsKey] ?? [],
                ];

                $packageItems[$itemId] = $packageItem;
                if (isset($shipmentItems[$itemId])) {
                    $shipmentItems[$itemId] += $packageItem['qty'];
                } else {
                    $shipmentItems[$itemId] = $packageItem['qty'];
                }
            }

            $packageParams = $packageDetails['package'];
            // set to orig packaging popup property names and unset them from customs array
            $customsKey = Codes::PACKAGING_OPTION_PACKAGE_CUSTOMS;
            $customsValue = $packageParams[$customsKey][Codes::PACKAGING_INPUT_CUSTOMS_VALUE] ?? null;
            $contentType = $packageParams[$customsKey][Codes::PACKAGING_INPUT_CONTENT_TYPE] ?? '';
            $contentTypeOther = $packageParams[$customsKey][Codes::PACKAGING_INPUT_EXPLANATION] ?? '';
            unset(
                $packageParams[$customsKey][Codes::PACKAGING_INPUT_CUSTOMS_VALUE],
                $packageParams[$customsKey][Codes::PACKAGING_INPUT_CONTENT_TYPE],
                $packageParams[$customsKey][Codes::PACKAGING_INPUT_EXPLANATION]
            );

            $detailsKey = Codes::PACKAGING_OPTION_PACKAGE_DETAILS;
            $packages[$packageDetails['packageId']] = [
                'params' => [
                    'shipping_product' => $packageParams[$detailsKey][Codes::PACKAGING_INPUT_PRODUCT_CODE] ?? '',
                    'container' => '',
                    'weight' => $packageParams[$detailsKey][Codes::PACKAGING_INPUT_WEIGHT] ?? '',
                    'weight_units' => $packageParams[$detailsKey][Codes::PACKAGING_INPUT_WEIGHT_UNIT] ?? '',
                    'length' => $packageParams[$detailsKey][Codes::PACKAGING_INPUT_LENGTH] ?? '',
                    'width' => $packageParams[$detailsKey][Codes::PACKAGING_INPUT_WIDTH] ?? '',
                    'height' => $packageParams[$detailsKey][Codes::PACKAGING_INPUT_HEIGHT] ?? '',
                    'dimension_units' => $packageParams[$detailsKey][Codes::PACKAGING_INPUT_SIZE_UNIT] ?? '',
                    'content_type' => $contentType,
                    'content_type_other' => $contentTypeOther,
                    'customs_value' => $customsValue,
                    'customs' => $packageParams[Codes::PACKAGING_OPTION_PACKAGE_CUSTOMS] ?? [],
                    'services' => $packageDetails['service'] ?? [],
                ],
                'items' => $packageItems,
            ];
        }

        $sendEmail = !empty($shipmentData['sendEmail']) ? $shipmentData['sendEmail'] : null;
        $notifyCustomer = !empty($shipmentData['notifyCustomer']) ? $shipmentData['notifyCustomer'] : null;
        $shipment = [
            'comment_text' => $shipmentData['shipmentComment'] ?? '',
            'send_email' => $sendEmail,
            'comment_customer_notify' => $notifyCustomer,
            'create_shipping_label' => '1',
            'items' => $shipmentItems,
        ];

        /** @var Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $resultForward->setController('order_shipment')
                      ->setModule('admin')
                      ->setParams(['shipment' => $shipment, 'packages' => $packages]);
        if ($this->getRequest()->getParam('shipment_id', false)) {
            $resultForward->forward('createLabel');
        } else {
            $resultForward->forward('save');
        }

        return $resultForward;
    }
}
