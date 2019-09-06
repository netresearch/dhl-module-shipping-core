<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Api\Data\ShippingOption\RouteInterface;
use Dhl\ShippingCore\Model\Config\Config;

/**
 * Class RouteMatcher
 *
 * @package Dhl\ShippingCore\Model
 * @author   Andreas Müller <andreas.mueller@netresearch.de>
 */
class RouteMatcher
{
    const ORIGIN = 'origin';
    const EXCLUDED = 'excluded';
    const INCLUDED = 'included';

    /**
     * @var Config $config
     */
    private $config;

    /**
     * RouteMatcher constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Checks the given route configuration against the given route
     *
     * @param RouteInterface[] $routes
     * @param string $shippingOrigin
     * @param string $destination
     * @param int|null $scopeId
     *
     * @return bool
     */
    public function match(array $routes, string $shippingOrigin, string $destination, int $scopeId = null): bool
    {
        // no routes means no limitations
        if (empty($routes)) {
            return true;
        }
        // convert routes into specific ruleset
        $rules = $this->createRuleSet($routes, $shippingOrigin, $scopeId);

        // filter rules for matching the route
        foreach ($rules as $index => $rule) {
            if (!$this->matchRule($rule, $shippingOrigin)) {
                unset($rules[$index]);
            }
        }

        // if we have routes/rules and none is matching the given origin is not allowed
        if (empty($rules)) {
            return false;
        }

        // exactly one rule matches, use it
        if (count($rules) === 1) {
            return $this->processRule($rules[0], $shippingOrigin, $destination);
        }

        // determine the most specific route (with fewest origins) to process
        $distinctRule = $this->selectDistinctRule($rules);

        return $this->processRule($distinctRule, $shippingOrigin, $destination);
    }

    /**
     * Create array of rules by routes for further processing
     *
     * @param RouteInterface[] $routes
     * @param string $shippingOrigin
     * @param int|null $scopeId
     *
     * @return string[][] Array with keys self::INCLUDED, self::EXCLUDED and self::ORIGIN, each being an array of
     *     country codes
     */
    private function createRuleSet(array $routes, string $shippingOrigin, int $scopeId = null): array
    {
        $rules = [];

        foreach ($routes as $key => $route) {
            $rules[$key][self::INCLUDED] = $route->getIncludeDestinations();
            $rules[$key][self::EXCLUDED] = $route->getExcludeDestinations();
            $rules[$key][self::ORIGIN] = [$route->getOrigin()];
        }

        foreach ($rules as $index => $rule) {
            foreach ($rule as $key => $countries) {
                if (!empty($countries)) {
                    $rules[$index][$key] = $this->replacePlaceholders($countries, $shippingOrigin, $scopeId);
                }
            }
        }

        return $rules;
    }

    /**
     * Replaces placeholders (eu, domestic) with route specific countries
     *
     * @param string[] $countries
     * @param string $origin
     * @param int|null $scopeId
     *
     * @return string[]
     */
    private function replacePlaceholders(array $countries, string $origin, int $scopeId = null): array
    {
        $replaced = [];
        foreach ($countries as $country) {
            switch ($country) {
                case 'eu':
                    $replaced = $this->config->getEuCountries($scopeId);
                    break;
                case 'domestic':
                    $replaced[] = $origin;
                    break;
                case '':
                    break;
                default:
                    $replaced[] = $country;
            }
        }

        return array_unique($replaced);
    }

    /**
     * Determines if rule is applicable to the given shipping origin
     *
     * @param string[][] $rule
     * @param string $shippingOrigin
     *
     * @return bool
     */
    private function matchRule(array $rule, string $shippingOrigin): bool
    {
        if (empty($rule[self::ORIGIN])) {
            return true;
        }

        return in_array($shippingOrigin, $rule[self::ORIGIN], true);
    }

    /**
     * @param string[][] $rule
     * @param string $shippingOrigin
     * @param string $destination
     *
     * @return bool
     */
    private function processRule(array $rule, string $shippingOrigin, string $destination): bool
    {
        $hasOrigin = !empty($rule[self::ORIGIN]);
        $hasExclusions = !empty($rule[self::EXCLUDED]);
        $hasIncludes = !empty($rule[self::INCLUDED]);

        if ($hasOrigin && !in_array($shippingOrigin, $rule[self::ORIGIN], true)) {
            return false;
        }

        if ($hasExclusions && in_array($destination, $rule[self::EXCLUDED], true)) {
            return false;
        }

        if ($hasIncludes && !in_array($destination, $rule[self::INCLUDED], true)) {
            return false;
        }

        return true;
    }

    /**
     * Get the most distinct rule by rule origin
     *
     * @param array $rules
     *
     * @return array
     */
    private function selectDistinctRule(array $rules): array
    {
        $withOrigin = array_filter(
            $rules,
            function ($rule) {
                return !empty($rule[self::ORIGIN]);
            }
        );
        // if only one rule with origin use it
        if (count($withOrigin) === 1) {
            return array_shift($withOrigin);
        }

        /** sort multiple rules with origin and return the one with fewest (the most specific rule) */
        usort(
            $withOrigin,
            function ($ruleA, $ruleB) {
                return count($ruleA[self::ORIGIN]) <=> count($ruleB[self::ORIGIN]);
            }
        );

        return $withOrigin[0];
    }
}
