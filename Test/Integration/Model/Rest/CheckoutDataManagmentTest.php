<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Test\Integration\Model\Rest;

use Dhl\ShippingCore\Api\Data\Selection\AssignedServiceSelectionInterface;
use Dhl\ShippingCore\Api\Data\Selection\ServiceSelectionInterface;
use Dhl\ShippingCore\Api\Rest\CheckoutDataManagementInterface;
use Dhl\ShippingCore\Model\Checkout\CheckoutData;
use Dhl\ShippingCore\Model\QuoteServiceSelectionRepository;
use Dhl\ShippingCore\Model\Rest\CheckoutDataManagement;
use Dhl\ShippingCore\Test\Integration\Fixture\QuoteFixture;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
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
        /** @var CheckoutDataManagementInterface $subject */
        $subject = $this->objectManager->create(CheckoutDataManagement::class);
        $result = $subject->getData('DE', '04229');

        self::assertInstanceOf(CheckoutData::class, $result);
    }

    /**
     * @magentoDataFixture createQuoteFixture
     *
     */
    public function testSetServiceSelection()
    {
        /** @var CheckoutDataManagementInterface $subject */
        $subject = $this->objectManager->create(CheckoutDataManagement::class);
        /** @var ServiceSelectionInterface $serviceSelection */
        $serviceSelection = $this->objectManager->create(
            ServiceSelectionInterface::class,
            [
                'serviceCode' => 'testServiceCode',
                'inputCode' => 'testInputCode',
                'value' => 'testValue',
            ]
        );

        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test01', 'reserved_order_id');

        $quoteId = (string)$quote->getId();
        $subject->updateServiceSelection($quoteId, [$serviceSelection]);

        $addressMngt = $this->objectManager->create(ShippingAddressManagementInterface::class);
        $addressId = $addressMngt->get($quoteId)->getId();

        /** @var QuoteServiceSelectionRepository $serviceSelectionRepo */
        $serviceSelectionRepo = $this->objectManager->get(QuoteServiceSelectionRepository::class);
        $filterBuilder = $this->objectManager->create(FilterBuilder::class);
        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);

        /** @var ServiceSelectionInterface $storedServiceSelection */
        $addressFilter = $filterBuilder
            ->setField(AssignedServiceSelectionInterface::PARENT_ID)
            ->setValue($quote->getShippingAddress()->getId())
            ->setConditionType('eq')
            ->create();

        $searchCriteria = $searchCriteriaBuilder->addFilter($addressFilter)->create();
        $storedServiceSelection = $serviceSelectionRepo->getList($searchCriteria)->fetchItem();
        $this->assertEquals($storedServiceSelection->getInputValue(), $serviceSelection->getInputValue());
        $this->assertEquals($storedServiceSelection->getInputCode(), $serviceSelection->getInputCode());
        $this->assertEquals($storedServiceSelection->getServiceCode(), $serviceSelection->getServiceCode());
    }
}
