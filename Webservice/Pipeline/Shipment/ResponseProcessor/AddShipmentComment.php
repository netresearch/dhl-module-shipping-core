<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Webservice\Pipeline\Shipment\ResponseProcessor;

use Dhl\ShippingCore\Api\Data\ShipmentResponse\LabelResponseInterface;
use Dhl\ShippingCore\Api\Data\ShipmentResponse\ShipmentErrorResponseInterface;
use Dhl\ShippingCore\Api\Pipeline\ShipmentResponseProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Api\Data\ShipmentCommentInterface;
use Magento\Sales\Api\Data\ShipmentCommentInterfaceFactory;
use Magento\Sales\Api\ShipmentCommentRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class AddShipmentComment
 *
 * Add order comment if bulk label creation gave an error.
 *
 * @package Dhl\ShippingCore\Webservice
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class AddShipmentComment implements ShipmentResponseProcessorInterface
{
    /**
     * @var ShipmentCommentInterfaceFactory
     */
    private $commentFactory;

    /**
     * @var ShipmentCommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AddShipmentComment constructor.
     *
     * @param ShipmentCommentInterfaceFactory $commentFactory
     * @param ShipmentCommentRepositoryInterface $commentRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        ShipmentCommentInterfaceFactory $commentFactory,
        ShipmentCommentRepositoryInterface $commentRepository,
        LoggerInterface $logger
    ) {
        $this->commentFactory = $commentFactory;
        $this->commentRepository = $commentRepository;
        $this->logger = $logger;
    }

    /**
     * Perform actions after receiving the "create shipments" response.
     *
     * @param LabelResponseInterface[] $labelResponses
     * @param ShipmentErrorResponseInterface[] $errorResponses
     */
    public function processResponse(array $labelResponses, array $errorResponses)
    {
        array_walk(
            $errorResponses,
            function (ShipmentErrorResponseInterface $errorResponse) {
                $comment = $this->commentFactory->create(['data' => [
                    ShipmentCommentInterface::COMMENT => $errorResponse->getErrors(),
                    ShipmentCommentInterface::PARENT_ID => $errorResponse->getSalesShipment()->getEntityId(),
                    ShipmentCommentInterface::IS_VISIBLE_ON_FRONT => false,
                    ShipmentCommentInterface::IS_CUSTOMER_NOTIFIED => false,
                ]]);

                try {
                    $this->commentRepository->save($comment);
                } catch (CouldNotSaveException $exception) {
                    $this->logger->error($exception->getLogMessage(), ['exception' => $exception]);
                }
            }
        );
    }
}
