<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Model\Webapi;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\AssignedSelectionInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\SelectionInterface;
use Dhl\ShippingCore\Api\ShippingSettings\CheckoutManagementInterface;
use Dhl\ShippingCore\Model\ShippingSettings\CheckoutManagement;
use Dhl\ShippingCore\Model\ShippingSettings\Data\ShippingData;
use Dhl\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\QuoteSelectionRepository;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Catalog\ProductBuilder;
use TddWizard\Fixtures\Catalog\ProductFixture;
use TddWizard\Fixtures\Catalog\ProductFixtureRollback;
use TddWizard\Fixtures\Checkout\CartBuilder;
use TddWizard\Fixtures\Customer\AddressBuilder;
use TddWizard\Fixtures\Customer\CustomerBuilder;
use TddWizard\Fixtures\Customer\CustomerFixture;
use TddWizard\Fixtures\Customer\CustomerFixtureRollback;

/**
 * @magentoAppArea frontend
 */
class CheckoutDataManagementTest extends TestCase
{
    /**
     * @var ProductFixture
     */
    private static $productFixture;

    /**
     * @var CustomerFixture
     */
    private static $customerFixture;

    /**
     * @var Cart
     */
    private static $cart;

    /**
     * Set up data fixture.
     *
     * @throws LocalizedException
     */
    public static function createQuoteFixture()
    {
        /** @var AddressRepositoryInterface $customerAddressRepository */
        $customerAddressRepository = Bootstrap::getObjectManager()->get(AddressRepositoryInterface::class);
        $shippingMethod = 'dhlpaket_flatrate';

        // prepare checkout
        self::$productFixture = new ProductFixture(ProductBuilder::aSimpleProduct()->build());

        $customer = CustomerBuilder::aCustomer()
            ->withAddresses(AddressBuilder::anAddress()->asDefaultBilling()->asDefaultShipping())
            ->build();

        self::$customerFixture = new CustomerFixture($customer);
        self::$customerFixture->login();

        self::$cart = CartBuilder::forCurrentSession()
            ->withSimpleProduct(self::$productFixture->getSku())
            ->withReservedOrderId('test01')
            ->build();

        // select customer's default shipping address in shipping step
        $customerAddressId = self::$cart->getCustomerSession()->getCustomer()->getDefaultShippingAddress()->getId();
        $shippingAddress = self::$cart->getQuote()->getShippingAddress();
        $shippingAddress->importCustomerAddressData($customerAddressRepository->getById($customerAddressId));
        $shippingAddress->setCollectShippingRates(true);
        $shippingAddress->collectShippingRates();
        $shippingAddress->setShippingMethod($shippingMethod);
        $shippingAddress->save();
    }

    public static function createQuoteFixtureRollback()
    {
        try {
            CustomerFixtureRollback::create()->execute(self::$customerFixture);
            ProductFixtureRollback::create()->execute(self::$productFixture);
            self::$cart->getQuote()->delete();
        } catch (\Exception $exception) {
            if (isset($_SERVER['argv'])
                && is_array($_SERVER['argv'])
                && in_array('--verbose', $_SERVER['argv'], true)
            ) {
                $message = sprintf("Error during rollback: %s%s", $exception->getMessage(), PHP_EOL);
                register_shutdown_function('fwrite', STDERR, $message);
            }
        }
    }

    public function testGetData()
    {
        /** @var CheckoutManagementInterface $subject */
        $subject = Bootstrap::getObjectManager()->create(CheckoutManagement::class);
        $result = $subject->getCheckoutData('DE', '04229');

        self::assertInstanceOf(ShippingData::class, $result);
    }

    /**
     * @magentoDataFixture createQuoteFixture
     */
    public function testSetServiceSelection()
    {
        /** @var CheckoutManagementInterface $subject */
        $subject = Bootstrap::getObjectManager()->create(CheckoutManagement::class);
        /** @var SelectionInterface $shippingOptionSelection */
        $shippingOptionSelection = Bootstrap::getObjectManager()->create(
            SelectionInterface::class,
            [
                'shippingOptionCode' => 'testShippingOptionCode',
                'inputCode' => 'testInputCode',
                'inputValue' => 'testValue',
            ]
        );

        $quote = Bootstrap::getObjectManager()->create(Quote::class);
        $quote->load('test01', 'reserved_order_id');

        $quoteId = (int)$quote->getId();
        $subject->updateShippingOptionSelections($quoteId, [$shippingOptionSelection]);

        /** @var QuoteSelectionRepository $shippingOptionSelectionRepo */
        $shippingOptionSelectionRepo = Bootstrap::getObjectManager()->get(QuoteSelectionRepository::class);
        $filterBuilder = Bootstrap::getObjectManager()->create(FilterBuilder::class);
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);

        /** @var SelectionInterface $storedServiceSelection */
        $addressFilter = $filterBuilder
            ->setField(AssignedSelectionInterface::PARENT_ID)
            ->setValue($quote->getShippingAddress()->getId())
            ->setConditionType('eq')
            ->create();

        $searchCriteria = $searchCriteriaBuilder->addFilter($addressFilter)->create();
        $storedServiceSelection = $shippingOptionSelectionRepo->getList($searchCriteria)->fetchItem();
        $this->assertEquals($storedServiceSelection->getInputValue(), $shippingOptionSelection->getInputValue());
        $this->assertEquals($storedServiceSelection->getInputCode(), $shippingOptionSelection->getInputCode());
        $this->assertEquals(
            $storedServiceSelection->getShippingOptionCode(),
            $shippingOptionSelection->getShippingOptionCode()
        );
    }
}
