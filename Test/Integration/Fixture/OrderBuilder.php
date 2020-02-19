<?php

namespace Dhl\ShippingCore\Test\Integration\Fixture;

use Dhl\ShippingCore\Api\LabelStatus\LabelStatusManagementInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use TddWizard\Fixtures\Catalog\ProductBuilder;
use TddWizard\Fixtures\Checkout\CartBuilder;
use TddWizard\Fixtures\Customer\CustomerBuilder;
use TddWizard\Fixtures\Sales\OrderBuilder as TddWizard;

/**
 * Builder to be used by fixtures
 */
class OrderBuilder
{
    /**
     * @var TddWizard
     */
    private $builder;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var LabelStatusManagementInterface
     */
    private $labelStatusManagement;

    /**
     * @var string
     */
    private $labelStatus;

    /**
     * @var float[]
     */
    private $additionalFees;

    public function __construct(
        TddWizard $builder,
        OrderRepositoryInterface $orderRepository,
        LabelStatusManagementInterface $labelStatusManagement
    ) {
        $this->builder = $builder;
        $this->orderRepository = $orderRepository;
        $this->labelStatusManagement = $labelStatusManagement;

        $this->labelStatus = '';
        $this->additionalFees = [];
    }

    public static function anOrder(ObjectManagerInterface $objectManager = null): OrderBuilder
    {
        if ($objectManager === null) {
            $objectManager = Bootstrap::getObjectManager();
        }

        return new self(
            TddWizard::anOrder(),
            $objectManager->create(OrderRepositoryInterface::class),
            $objectManager->create(LabelStatusManagementInterface::class)
        );
    }

    public function withProducts(ProductBuilder ...$productBuilders): OrderBuilder
    {
        $builder = clone $this;
        $builder->builder = $builder->builder->withProducts(...$productBuilders);

        return $builder;
    }

    public function withCustomer(CustomerBuilder $customerBuilder): OrderBuilder
    {
        $builder = clone $this;
        $builder->builder = $builder->builder->withCustomer($customerBuilder);

        return $builder;
    }

    public function withCart(CartBuilder $cartBuilder): OrderBuilder
    {
        $builder = clone $this;
        $builder->builder = $builder->builder->withCart($cartBuilder);

        return $builder;
    }

    public function withShippingMethod(string $shippingMethod): OrderBuilder
    {
        $builder = clone $this;
        $builder->builder = $builder->builder->withShippingMethod($shippingMethod);

        return $builder;
    }

    public function withPaymentMethod(string $paymentMethod): OrderBuilder
    {
        $builder = clone $this;
        $builder->builder = $builder->builder->withPaymentMethod($paymentMethod);

        return $builder;
    }

    public function withLabelStatus(string $labelStatus): OrderBuilder
    {
        $builder = clone $this;
        $builder->labelStatus = $labelStatus;

        return $builder;
    }

    public function withAdditionalFee(string $code, float $value): OrderBuilder
    {
        $builder = clone $this;
        $builder->additionalFees[$code] = $value;

        return $builder;
    }

    /**
     * @return OrderInterface
     * @throws \Exception
     */
    public function build(): OrderInterface
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->builder->build();

        if (!empty($this->additionalFees)) {
            $order->addData($this->additionalFees);
            $this->orderRepository->save($order);
        }

        switch ($this->labelStatus) {
            case LabelStatusManagementInterface::LABEL_STATUS_PARTIAL:
                $this->labelStatusManagement->setLabelStatusPartial($order);
                break;
            case LabelStatusManagementInterface::LABEL_STATUS_PROCESSED:
                $this->labelStatusManagement->setLabelStatusProcessed($order);
                break;
            case LabelStatusManagementInterface::LABEL_STATUS_FAILED:
                $this->labelStatusManagement->setLabelStatusFailed($order);
                break;
            default:
                $this->labelStatusManagement->setInitialStatus($order);
        }

        return $order;
    }
}
