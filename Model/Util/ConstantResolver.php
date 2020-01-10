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
     * @return string|false
     */
    public function resolve(string $constantReference)
    {
        if (preg_match(self::PHP_CONSTANT_PATTERN, $constantReference) === 1) {
            list($className, $constName) = explode('::', $constantReference);
            try {
                $reflection = new \ReflectionClass($className);
            } catch (\ReflectionException $exception) {
                return false;
            }
            if (isset($reflection->getConstants()[$constName])) {
                return $reflection->getConstants()[$constName];
            }
        }

        return false;
    }
}