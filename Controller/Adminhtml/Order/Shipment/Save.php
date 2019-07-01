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
                $packageItem = [
                    'qty'           => $itemDetails['details']['qty'] ?? '1',
                    'customs_value' => '0',
                    'price'         => '',
                    'name'          => $itemDetails['details']['productName'],
                    'weight'        => $itemDetails['details']['weight'] ?? '',
                    'product_id'    => '',
                    'order_item_id' => $itemId,
                    'customs'       => $itemDetails['itemCustoms'] ?? [],
                ];

                $packageItems[$itemId]  = $packageItem;
                $shipmentItems[$itemId] = $packageItem['qty'];
            }

            $packages[$packageDetails['packageId']] = [
                'params'=> [
                    'container'          => '',
                    'weight'             => $packageDetails['packageWeight']['weight'] ?? '',
                    'weight_units'       => $packageDetails['packageWeight']['weightUnit'],
                    'length'             => $packageDetails['packageSize']['length'] ?? '',
                    'width'              => $packageDetails['packageSize']['width'] ?? '',
                    'height'             => $packageDetails['packageSize']['height'] ?? '',
                    'dimension_units'    => $packageDetails['packageSize']['sizeUnit'],
                    'content_type'       => '',
                    'content_type_other' => '',
                    'customs_value'      => '0',
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
