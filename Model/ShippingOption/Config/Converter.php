<?php
/**
 * See LICENSE.md for license details.
 */

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
        'compatibilityData',
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
        if ($this->isTextNode($node)) {
            return $this->toScalar($node);
        }
        if ($this->containsArray($node)) {
            $result = [];
            /** @var \DomNode $childNode */
            foreach ($node->childNodes as $childNode) {
                if ($this->isNodeApplicable($childNode)) {
                    if ($childNode->hasAttributes()) {
                        $key = $childNode->attributes->item(0)->localName;
                        $value = $childNode->attributes->item(0)->textContent;
                        $result[$value] = $this->toArray($childNode);
                        $result[$value][$key] = $value;
                    } elseif ($this->isTextNode($childNode)) {
                        if (trim($childNode->textContent)) {
                            $result[] = $childNode->textContent;
                        }
                    } else {
                        $result[] = $this->toArray($childNode);
                    }
                }
            }
            return $result;
        }

        $result = [];
        /** @var \DOMNode $childNode */
        foreach ($node->childNodes as $childNode) {
            if ($this->isNodeApplicable($childNode)) {
                if ($childNode->hasChildNodes()) {
                    $result[$childNode->localName] = $this->toArray($childNode);
                } elseif ($this->containsArray($childNode)) {
                    $result[$childNode->localName] = [];
                } elseif (!$this->isTextNode($childNode)) {
                    $result[$childNode->localName] = $childNode->textContent;
                }
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
        while (!($node instanceof \DOMText)) {
            $node = $node->firstChild;
        }
        if ($node->textContent === 'true') {
            $value = true;
        } elseif ($node->textContent === 'false') {
            $value = false;
        } elseif ((string)(int)$node->textContent === $node->textContent) {
            $value = (int)$node->textContent;
        } else {
            $value = $node->textContent;
        }

        return $value;
    }

    /**
     * @param \DomNode $node
     * @return bool
     */
    private function isTextNode(\DomNode $node): bool
    {
        if ($node instanceof \DOMText) {
            return true;
        }
        if ($node->childNodes->length !== 1) {
            return false;
        }
        if ($node->childNodes->item(0) instanceof \DOMText) {
            return true;
        }

        return $this->isTextNode($node->firstChild);
    }

    /**
     * @param \DOMNode $node
     * @return bool
     */
    private function containsArray(\DOMNode $node): bool
    {
        return in_array($node->localName, self::ARRAY_NODES, true);
    }

    /**
     * @param \DOMNode $node
     * @return bool
     */
    private function isNodeApplicable(\DOMNode $node): bool
    {
        return in_array($node->nodeType, [1, 3, 4], true);
    }
}
