<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\ShippingOption\Config;

use Magento\Framework\Config\ConverterInterface;

/**
 * Class Converter
 */
class Converter implements ConverterInterface
{
    private const ARRAY_NODES = [
        'carriers',
        'packageLevelOptions',
        'itemLevelOptions',
        'routes',
        'inputs',
        'options',
        'destinations',
        'validationRules',
        'commentsBefore',
        'commentsAfter',
        'footnotes',
        'subjects',
        'compatibilityData',
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

        return $this->toArray($source);
    }

    /**
     * Transform Xml to array
     *
     * @param \DOMNode $node
     * @return array|string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function toArray(\DOMNode $node)
    {
        // process scalar types
        if (count($node->childNodes === 1)
            && !$node->firstChild->hasChildNodes()
            && trim($node->firstChild->textContent)
        ) {
            return $this->toScalar($node);
        }

        $result = [];

        // process flat arrays
        if (in_array($node->localName, self::ARRAY_NODES, true)) {
            foreach ($node->childNodes as $childNode) {
                if (trim($childNode->textContent)) {
                    $result[] = $this->toArray($childNode);
                }
            }
            return $result;
        }

        // process nested objects
        foreach ($node->childNodes as $childNode) {
            if (trim($childNode->textContent)) {
                $result[$childNode->localName] = $this->toArray($childNode);
            }
        }
        return $result;
    }

    /**
     * @param \DOMNode $node
     * @return bool|int|string
     */
    private function toScalar(\DOMNode $node)
    {
        if ($node->firstChild->textContent === 'true') {
            $value = true;
        } elseif ($node->firstChild->textContent === 'false') {
            $value = false;
        } elseif ((string)(int)$node->firstChild->textContent === $node->firstChild->textContent) {
            $value = (int)$node->firstChild->textContent;
        } else {
            $value = $node->firstChild->textContent;
        }

        return $value;
    }
}
