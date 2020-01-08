<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Api\Data\OrderItemAttributesInterface;
use Dhl\ShippingCore\Model\ResourceModel\OrderItemAttributes as OrderItemAttributesResource;
use Magento\Framework\Model\AbstractModel;

/**
 * OrderItemAttributes
 *
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class OrderItemAttributes extends AbstractModel implements OrderItemAttributesInterface
{
    /**
     * Initialize OrderItemAttributes resource model.
     */
    protected function _construct()
    {
        $this->_init(OrderItemAttributesResource::class);
        parent::_construct();
    }

    /**
     * @return int
     */
    public function getItemId(): int
    {
        return (int) $this->getData(self::ITEM_ID);
    }

    /**
     * @return string
     */
    public function getDgCategory(): string
    {
        return (string) $this->getData(self::DG_CATEGORY);
    }

    /**
     * @return string
     */
    public function getTariffNumber(): string
    {
        return (string) $this->getData(self::TARIFF_NUMBER);
    }

    /**
     * @return string
     */
    public function getExportDescription(): string
    {
        return (string) $this->getData(self::EXPORT_DESCRIPTION);
    }

    /**
     * @return string
     */
    public function getCountryOfManufacture(): string
    {
        return (string) $this->getData(self::COUNTRY_OF_MANUFACTURE);
    }

    /**
     * @param int $itemId
     */
    public function setItemId(int $itemId)
    {
        $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * @param string|null $dgCategory
     */
    public function setDgCategory(string $dgCategory = null)
    {
        $this->setData(self::DG_CATEGORY, $dgCategory);
    }

    /**
     * @param string|null $tariffNumber
     */
    public function setTariffNumber(string $tariffNumber = null)
    {
        $this->setData(self::TARIFF_NUMBER, $tariffNumber);
    }

    /**
     * @param string|null $exportDescription
     */
    public function setExportDescription(string $exportDescription = null)
    {
        $this->setData(self::EXPORT_DESCRIPTION, $exportDescription);
    }

    /**
     * @param string|null $countryOfManufacture
     */
    public function setCountryOfManufacture(string $countryOfManufacture = null)
    {
        $this->setData(self::COUNTRY_OF_MANUFACTURE, $countryOfManufacture);
    }
}
