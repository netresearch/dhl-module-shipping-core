<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Util;

/**
 * Class ConstantResolver
 *
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ConstantResolver
{
    /**
     * This pattern will match a PHP class Constant reference
     */
    const PHP_CONSTANT_PATTERN = '/^\\/?[a-zA-Z\\\]*::[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*$/';

    /**
     * Turns the reference to a PHP class constant into the value of that constant using Reflection
     *
     * @param string $constantReference
     * @return string
     * @throws \RuntimeException
     */
    public function resolve(string $constantReference): string
    {
        $references = explode('.', $constantReference);
        $resolvedReferences = array_map(function (string $reference) {
            if (preg_match(self::PHP_CONSTANT_PATTERN, $reference) === 1) {
                list($className, $constName) = explode('::', $reference);
                try {
                    $reflection = new \ReflectionClass($className);
                } catch (\ReflectionException $exception) {
                    throw new \RuntimeException("Invalid constant '$reference' referenced in shipping_settings.xml");
                }
                if (isset($reflection->getConstants()[$constName])) {
                    return $reflection->getConstants()[$constName];
                }
                throw new \RuntimeException("Invalid constant '$reference' referenced in shipping_settings.xml");
            }

            return $reference;
        }, $references);

        return implode('.', $resolvedReferences);
    }
}
