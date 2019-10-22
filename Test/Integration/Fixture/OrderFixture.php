<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Fixture;

use Dhl\ShippingCore\Api\LabelStatusManagementInterface;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\AddressInterface;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipOrderInterface;
use Magento\TestFramework\Helper\Bootstrap;
use TddWizard\Fixtures\Catalog\ProductBuilder;
use TddWizard\Fixtures\Catalog\ProductFixture;
use TddWizard\Fixtures\Checkout\CartBuilder;
use TddWizard\Fixtures\Checkout\CustomerCheckout;
use TddWizard\Fixtures\Customer\AddressBuilder;
use TddWizard\Fixtures\Customer\CustomerBuilder;
use TddWizard\Fixtures\Customer\CustomerFixture;

/**
 * Class OrderFixture
 *
 * @package Dhl\Test\Integration\Fixture
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class OrderFixture
{
    private static $createdEntities = [
        'products' => [],
        'customers' => [],
        'orders' => [],
    ];

    /**
     * @param AddressInterface $recipientData
     * @param ProductInterface[] $productData
     * @param string $carrierCode
     * @return OrderInterface
     * @throws \Exception
     */
    public static function createOrder(
        AddressInterface $recipientData,
        array $productData,
        string $carrierCode
    ): OrderInterface {

        // set up logged-in customer
        $shippingAddressBuilder = AddressBuilder::anAddress()
                                                ->withFirstname('François')
                                                ->withLastname('Češković')
                                                ->withCompany(null)
                                                ->withCountryId($recipientData->getCountryId())
                                                ->withRegionId($recipientData->getRegionId())
                                                ->withCity($recipientData->getCity())
                                                ->withPostcode($recipientData->getPostcode())
                                                ->withStreet($recipientData->getStreet());

        $customer = CustomerBuilder::aCustomer()
                                   ->withFirstname('François')
                                   ->withLastname('Češković')
                                   ->withAddresses(
                                       $shippingAddressBuilder->asDefaultBilling(),
                                       $shippingAddressBuilder->asDefaultShipping()
                                   )
                                   ->build();

        self::$createdEntities['customers'][] = $customer;
        $customerFixture = new CustomerFixture($customer);
        $customerFixture->login();

        // place order
        $cartBuilder = CartBuilder::forCurrentSession();
        $cartBuilder = self::addProductsToCart($productData, $cartBuilder);
        $cart = $cartBuilder->build();

        $checkout = CustomerCheckout::fromCart($cart);

        $order = $checkout
            ->withShippingMethodCode($carrierCode)
            ->placeOrder();
        self::$createdEntities['orders'][] = $order;

        $cart->getCheckoutSession()->clearQuote();

        return $order;
    }

    /**
     * @param AddressInterface $recipientData
     * @param ProductInterface[] $productData
     * @param string $carrierCode
     * @return OrderInterface
     * @throws \Exception
     */
    public static function createProcessedOrder(
        AddressInterface $recipientData,
        array $productData,
        string $carrierCode
    ) {
        $order = self::createOrder($recipientData, $productData, $carrierCode);
        /** @var ShipOrderInterface $shipOrder */
        $shipOrder = Bootstrap::getObjectManager()->get(ShipOrderInterface::class);
        $shipOrder->execute($order->getEntityId(), [], false);
        /** @var LabelStatusManagementInterface $labelStatusMgmnt */
        $labelStatusMgmnt = Bootstrap::getObjectManager()->get(LabelStatusManagementInterface::class);
        $labelStatusMgmnt->setLabelStatusFailed($order);

        return $order;
    }

    /**
     * Rollback for created order, customer and product entities
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public static function rollbackFixtureEntities()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var OrderInterface $order */
        foreach (self::$createdEntities['orders'] as $order) {
            /** @var OrderRepositoryInterface $orderRepo */
            $orderRepo = $objectManager->get(OrderRepositoryInterface::class);
            $orderRepo->delete($order);
        }
        self::$createdEntities['orders'] = [];

        /** @var CustomerInterface $customer */
        foreach (self::$createdEntities['customers'] as $customer) {
            /** @var CustomerRepositoryInterface $customerRepo */
            $customerRepo = $objectManager->get(CustomerRepositoryInterface::class);
            $customerRepo->delete($customer);
        }
        self::$createdEntities['customers'] = [];

        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        foreach (self::$createdEntities['products'] as $product) {
            /** @var ProductRepositoryInterface $productRepo */
            $productRepo = $objectManager->get(ProductRepositoryInterface::class);
            $productRepo->deleteById($product);
        }
        self::$createdEntities['products'] = [];
    }

    /**
     * @param ProductInterface[] $productData
     * @param CartBuilder $cartBuilder
     * @return CartBuilder
     * @throws \Exception
     */
    private static function addProductsToCart(array $productData, CartBuilder $cartBuilder): CartBuilder
    {
        foreach ($productData as $productDatum) {
            if ($productDatum->getType() === Type::TYPE_SIMPLE) {
                // set up product
                $productBuilder = ProductBuilder::aSimpleProduct();
                $productBuilder = $productBuilder
                    ->withSku($productDatum->getSku())
                    ->withPrice($productDatum->getPrice())
                    ->withWeight($productDatum->getWeight())
                    ->withCustomAttributes($productDatum->getCustomAttributes())
                    ->withName($productDatum->getDescription());
                $product = $productBuilder->build();

                self::$createdEntities['products'][] = $product;
                $productFixture = new ProductFixture($product);
                $cartBuilder = $cartBuilder->withSimpleProduct(
                    $productFixture->getSku(),
                    $productDatum->getCheckoutQty()
                );
            } else {
                throw new \InvalidArgumentException('Only simple product data fixtures are currently supported.');
            }
        }

        return $cartBuilder;
    }
}
