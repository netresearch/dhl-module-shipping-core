<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Model\Webapi;

use Dhl\ShippingCore\Api\CheckoutManagementInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\Selection\AssignedSelectionInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\Selection\SelectionInterface;
use Dhl\ShippingCore\Model\Checkout\ShippingData;
use Dhl\ShippingCore\Model\ShippingOption\Selection\QuoteSelectionRepository;
use Dhl\ShippingCore\Model\Webapi\CheckoutManagement;
use Dhl\ShippingCore\Test\Integration\Fixture\QuoteFixture;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class CheckoutDataManagmentTest
 *
 * @package Dhl\ShippingCore\Test\Model\Rest
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class CheckoutDataManagmentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
    }

    public static function createQuoteFixture()
    {
        QuoteFixture::createQuote();
    }

    public function testGetData()
    {
        /** @var CheckoutManagementInterface $subject */
        $subject = $this->objectManager->create(CheckoutManagement::class);
        $result = $subject->getCheckoutData('DE', '04229');

        self::assertInstanceOf(ShippingData::class, $result);
    }

    /**
     * @magentoDataFixture createQuoteFixture
     */
    public function testSetServiceSelection()
    {
        /** @var CheckoutManagementInterface $subject */
        $subject = $this->objectManager->create(CheckoutManagement::class);
        /** @var SelectionInterface $shippingOptionSelection */
        $shippingOptionSelection = $this->objectManager->create(
            SelectionInterface::class,
            [
                'shippingOptionCode' => 'testShippingOptionCode',
                'inputCode' => 'testInputCode',
                'inputValue' => 'testValue',
            ]
        );

        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test01', 'reserved_order_id');

        $quoteId = (int)$quote->getId();
        $subject->updateShippingOptionSelections($quoteId, [$shippingOptionSelection]);

        $addressMngt = $this->objectManager->create(ShippingAddressManagementInterface::class);
        $addressId = $addressMngt->get($quoteId)->getId();

        /** @var QuoteSelectionRepository $shippingOptionSelectionRepo */
        $shippingOptionSelectionRepo = $this->objectManager->get(QuoteSelectionRepository::class);
        $filterBuilder = $this->objectManager->create(FilterBuilder::class);
        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);

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
