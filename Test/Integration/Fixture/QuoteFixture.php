<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Fixture;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class QuoteFixture
 *
 */
class QuoteFixture
{
    public static function createQuote()
    {
        self:: rollback();

        Bootstrap::getInstance()->loadArea('frontend');
        $objectManager = Bootstrap::getObjectManager();
        $product = $objectManager->create(Product::class);
        $product->setTypeId('simple')
                ->setId(1)
                ->setAttributeSetId(4)
                ->setName('Simple Product')
                ->setSku('simple')
                ->setPrice(10)
                ->setTaxClassId(0)
                ->setMetaTitle('meta title')
                ->setMetaKeyword('meta keyword')
                ->setMetaDescription('meta description')
                ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                ->setStockData(
                    [
                        'qty' => 100,
                        'is_in_stock' => 1,
                        'manage_stock' => 1,
                    ]
                )->save();

        $productRepository = $objectManager->create(ProductRepositoryInterface::class);
        $product = $productRepository->get('simple');

        $addressData = [
            'region' => 'Sachsen',
            'region_id' => '91',
            'postcode' => '04229',
            'lastname' => 'lastname',
            'firstname' => 'firstname',
            'street' => 'Teststrasse',
            'city' => 'Leipzig',
            'email' => 'test@netrestest.de',
            'telephone' => '11111111',
            'country_id' => 'DE',
        ];
        $billingAddress = $objectManager->create(Address::class, ['data' => $addressData]);
        $billingAddress->setAddressType('billing');
        $shippingAddress = clone $billingAddress;
        $shippingAddress->setId(null)->setAddressType('shipping');

        $store = $objectManager->create(StoreManagerInterface::class)->getStore();

        $quote = $objectManager->create(Quote::class);
        $quote->setCustomerIsGuest(true)
              ->setStoreId($store->getId())
              ->setReservedOrderId('test01')
              ->setBillingAddress($billingAddress)
              ->setShippingAddress($shippingAddress)
              ->addProduct($product);
        $quote->getPayment()->setMethod('checkmo');
        $quote->setIsMultiShipping('1');
        $quote->collectTotals();

        $quoteRepository = $objectManager->create(CartRepositoryInterface::class);
        $quoteRepository->save($quote);
        $shippingAddressManagement = $objectManager->create(ShippingAddressManagementInterface::class);
        $quoteId = $quote->getId();
        $shippingAddressManagement->assign($quoteId, $shippingAddress);
        $quoteIdMask = $objectManager->create(QuoteIdMaskFactory::class)->create();
        $quoteIdMask->setQuoteId($quote->getId());
        $quoteIdMask->setDataChanges(true);
        $quoteIdMask->save();
    }

    public static function rollback()
    {
        $objectManager = Bootstrap::getObjectManager();
        $registry = $objectManager->get(Registry::class);
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        /** @var $quote \Magento\Quote\Model\Quote */
        $quote = $objectManager->create(Quote::class);
        $quote->load('test01', 'reserved_order_id');
        if ($quote->getId()) {
            $quote->delete();
        }

        /** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
        $productRepository = $objectManager->create(ProductRepositoryInterface::class);

        try {
            $product = $productRepository->get('simple', false, null, true);
            $productRepository->delete($product);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            //Product already removed
        }

        /** @var \Magento\CatalogInventory\Model\StockRegistryStorage $stockRegistryStorage */
        $stockRegistryStorage = $objectManager->get(\Magento\CatalogInventory\Model\StockRegistryStorage::class);
        $stockRegistryStorage->removeStockItem(1);

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);
    }
}
