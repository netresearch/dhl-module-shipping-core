<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Controller\Adminhtml\Order\Shipment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Controller Save
 *
 * @package Dhl\ShippingCore\Controller
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @link    https://www.netresearch.de/

 */
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
        $data          = $this->getRequest()->getParam('data');
        $data          = $this->jsonSerializer->unserialize($data);
        $shipmentItems = [];
        $packages      = [];

        foreach ($data as $packageDetails) {
            $packageItems = [];

            foreach ($packageDetails['items'] as $itemId => $itemDetails) {
                if (isset($itemDetails['itemCustoms'], $itemDetails['itemCustoms']['customsValue'])) {
                    $itemCustomsValue = $itemDetails['itemCustoms']['customsValue'];
                    unset($itemDetails['itemCustoms']['customsValue']);
                } else {
                    $itemCustomsValue = '';
                }

                $packageItem = [
                    'qty'           => $itemDetails['details']['qty'] ?? '1',
                    'customs_value' => $itemCustomsValue,
                    'price'         => $itemDetails['details']['price'],
                    'name'          => $itemDetails['details']['productName'],
                    'weight'        => $itemDetails['details']['weight'] ?? '',
                    'product_id'    => $itemDetails['details']['productId'],
                    'order_item_id' => $itemId,
                    'customs'       => $itemDetails['itemCustoms'] ?? [],
                ];

                $packageItems[$itemId]  = $packageItem;
                $shipmentItems[$itemId] = $packageItem['qty'];
            }

            // set to orig packaging popup property names and unset them from customs array
            $customsValue = $packageDetails['packageCustoms']['customsValue'] ?? '';
            $contentType = $packageDetails['packageCustoms']['contentType'] ?? '';
            $contentTypeOther = $packageDetails['packageCustoms']['explanation'] ?? '';
            unset(
                $packageDetails['packageCustoms']['customsValue'],
                $packageDetails['packageCustoms']['contentType'],
                $packageDetails['packageCustoms']['explanation']
            );

            $packages[$packageDetails['packageId']] = [
                'params'=> [
                    'container'          => '',
                    'weight'             => $packageDetails['packageWeight']['weight'] ?? '',
                    'weight_units'       => $packageDetails['packageWeight']['weightUnit'],
                    'length'             => $packageDetails['packageSize']['length'] ?? '',
                    'width'              => $packageDetails['packageSize']['width'] ?? '',
                    'height'             => $packageDetails['packageSize']['height'] ?? '',
                    'dimension_units'    => $packageDetails['packageSize']['sizeUnit'],
                    'content_type'       => $contentType,
                    'content_type_other' => $contentTypeOther,
                    'customs_value'      => $customsValue,
                    'customs'            => $packageDetails['packageCustoms'] ?? [],
                ],

                'items' => $packageItems,
            ];
        }

        $shipment = [
            'shipment' => [
                'comment_text'          => '',
                'create_shipping_label' => '1',
                'items'                 => $shipmentItems,
            ],
            'packages' => $packages,
        ];

        /** @var Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $resultForward->setController('order_shipment')
            ->setModule('admin')
            ->setParams($shipment)
            ->forward('save');

        return $resultForward;
    }
}
