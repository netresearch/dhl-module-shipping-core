<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption\Config;

use Dhl\ShippingCore\Model\Packaging\PackagingDataProvider;
use Magento\Framework\Config\ConverterInterface;

/**
 * Class Converter
 */
class Converter implements ConverterInterface
{
    /**
     * A list of xml node names whose children should be treated as plain arrays
     */
    const ARRAY_NODES = [
        'carriers',
        'routes',
        'requiredItemIds',
        'inputs',
        'options',
        'includeDestinations',
        'excludeDestinations',
        'validationRules',
        'commentsBefore',
        'commentsAfter',
        'footnotes',
        'subjects',
        'masters',
        'compatibilityData',
        'shippingOptions',
        PackagingDataProvider::GROUP_SERVICE,
        PackagingDataProvider::GROUP_ITEM,
        PackagingDataProvider::GROUP_PACKAGE,
    ];

    /**
     * Convert configuration
     *
     * @param \DOMDocument|null $source
     * @return mixed[]
     */
    public function convert($source): array
    {
        if ($source === null) {
            return [];
        }
        $xmlElement = simplexml_import_dom($source);

        return [$xmlElement->getName() => $this->toArray($xmlElement)];
    }

    /**
     * Recursively transform an XML Element to a nested array of scalar values.
     *
     * @param \SimpleXMLElement $xmlElement
     * @return string[]|string
     */
    private function toArray(\SimpleXMLElement $xmlElement)
    {
        $result = [];

        if ($this->containsArray($xmlElement)) {
            foreach ($xmlElement->children() as $childElement) {
                if ($childElement->attributes()) {
                    $result += $this->handleAttributes($childElement);
                } elseif ($childElement->count()) {
                    $result[] = $this->toArray($childElement);
                } else {
                    $result[] = $this->toScalar($childElement);
                }
            }
        } else {
            foreach ($xmlElement->children() as $childElement) {
                if ($childElement->count()) {
                    $result[$childElement->getName()] = $this->toArray($childElement);
                } else {
                    $result[$childElement->getName()] = $this->toScalar($childElement);
                }
            }
        }

        return $result;
    }

    /**
     * Transform XML Element to a string
     *
     * @param \SimpleXMLElement $xmlElement
     * @return bool|int|string
     */
    private function toScalar(\SimpleXMLElement $xmlElement)
    {
        if ((string) $xmlElement === 'true') {
            $value = true;
        } elseif ((string) $xmlElement === 'false') {
            $value = false;
        } elseif ((string) $xmlElement === '0' || (int) (string) $xmlElement !== 0) {
            $value = (int) (string) $xmlElement;
        } else {
            $value = trim((string) $xmlElement);
        }

        return $value;
    }

    /**
     * @param \SimpleXMLElement $xmlElement
     * @return bool
     */
    private function containsArray(\SimpleXMLElement $xmlElement): bool
    {
        return in_array($xmlElement->getName(), self::ARRAY_NODES, true);
    }

    /**
     * Handle XML Element attributes. Will use first attribute as array key.
     * Other attributes are appended to the children.
     *
     * @param \SimpleXMLElement $childElement
     * @return array
     */
    private function handleAttributes(\SimpleXMLElement $childElement): array
    {
        $result = [];
        $attributes = [];

        foreach ($childElement->attributes() as $attribute) {
            $key = $attribute->getName();
            $value = (string) $attribute;

            $attributes[$key] = $value;
        }

        // Keep original behaviour to use the first attribute value
        // as the key in the result set, so we need to remove all other attributes.
        // As the order of attributes may differ, we need to specify a black list
        // of attribute names.
        $blackList = ['defaultConfigValue', 'available'];
        $keys = array_diff_key($attributes, array_flip($blackList));
        $firstKey = key($keys);
        $firstValue = $attributes[$firstKey];

        $result[$firstValue] = $this->toArray($childElement);
        if (is_array($result[$firstValue])) {
            $result[$firstValue] = array_merge($result[$firstValue], $attributes);
        } else {
            $result[$firstValue] = $attributes;
        }

        return $result;
    }
}
