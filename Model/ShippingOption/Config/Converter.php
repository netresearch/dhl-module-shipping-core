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
    /**
     * A list of xml node names whose children should be treated as plain arrays
     */
    const ARRAY_NODES = [
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
        if ($this->containsScalar($node)) {
            return $this->toScalar($node);
        }
        if ($this->containsArray($node)) {
            $result = [];
            foreach ($node->childNodes as $childNode) {
                /** @var \DomNode $childNode */
                if (trim($childNode->textContent) && in_array($childNode->nodeType, [1, 3], true)) {
                    if ($childNode->hasAttributes()) {
                        $key = $childNode->attributes->item(0)->localName;
                        $value = $childNode->attributes->item(0)->textContent;
                        $result[$value] = $this->toArray($childNode);
                        $result[$value][$key] = $value;
                    } else {
                        $result[] = $this->toArray($childNode);
                    }
                }
            }
            return $result;
        }

        $result = [];
        foreach ($node->childNodes as $childNode) {
            if (trim($childNode->textContent) && in_array($childNode->nodeType, [1, 3], true)) {
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

    /**
     * @param \DomNode $node
     * @return bool
     */
    private function containsScalar(\DomNode $node): bool
    {
        $hasOneChild = count($node->childNodes === 1);
        if (!$hasOneChild || !$node->hasChildNodes()) {
            return false;
        }

        $hasNoGrandchild = !$node->firstChild->hasChildNodes();
        $isNotEmpty = trim($node->firstChild->textContent);

        return $hasNoGrandchild && $isNotEmpty;
    }

    /**
     * @param \DOMNode $node
     * @return bool
     */
    private function containsArray(\DOMNode $node): bool
    {
        return in_array($node->localName, self::ARRAY_NODES, true);
    }
}
