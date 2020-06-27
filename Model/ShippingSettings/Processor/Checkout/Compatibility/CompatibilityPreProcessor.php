<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Processor\Checkout\Compatibility;

use Dhl\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterfaceFactory;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout\GlobalProcessorInterface;

class CompatibilityPreProcessor implements GlobalProcessorInterface
{
    /**
     * @var CompatibilityInterfaceFactory
     */
    private $ruleFactory;

    public function __construct(CompatibilityInterfaceFactory $ruleFactory)
    {
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * Rules without masters should be treated as if there are multiple rules
     * where one of the subjects is the master. This method splits up the rules
     * accordingly (This is much easier to do than to handle master-less rules
     * in the CompatibilityEnforcer).
     * It also converts all codes into compound codes.
     *
     * @example See module-carrier-paket rule with id "preferredNeighbourRequireChildren" for an example of a
     * master-less rule: If any preferredNeighbour input has a value, every other input must have a value as well.
     *
     * @param CarrierDataInterface $carrierData
     * @return CarrierDataInterface
     *
     * @throws \InvalidArgumentException
     */
    public function process(CarrierDataInterface $carrierData): CarrierDataInterface
    {
        $processedRules = [];
        $shippingOptions = array_merge($carrierData->getServiceOptions(), $carrierData->getPackageOptions());

        foreach ($carrierData->getCompatibilityData() as $rule) {
            $masters = $this->convertToCompoundCodes($rule->getMasters(), $shippingOptions);
            if (empty($masters) && !empty($rule->getMasters())) {
                // This rule has none of its masters available at runtime. We remove it so it does not get turned
                // into a masterless rule and changes its semantics in unexpected ways.
                continue;
            }
            $subjects = $this->convertToCompoundCodes($rule->getSubjects(), $shippingOptions);
            if (empty($subjects)) {
                // A rule without any available subjects can do nothing. We remove it to improve performance.
                continue;
            }

            foreach ($masters as $master) {
                if (in_array($master, $subjects)) {
                    throw new \InvalidArgumentException(
                        "Invalid compatibility rule {$rule->getId()}: "
                        . 'A "master" input must not be a "subject" of its own rule.'
                    );
                }
            }

            /**
             * We check if there are no masters in the rule.
             * The convertedMasters list is no good indication
             * for a master-less rule since convertToCompoundCodes
             * filters out services unavailable during runtime.
             */
            if (empty($rule->getMasters())) {
                /** Split up master-less rule */
                foreach ($subjects as $subjectCode) {
                    $newRule = $this->ruleFactory->create();
                    $newRule->setId($rule->getId() . '-' . $subjectCode);
                    $newRule->setMasters([$subjectCode]);
                    $subjectDiff = array_diff($subjects, [$subjectCode]);
                    $newRule->setSubjects($subjectDiff);
                    $newRule->setErrorMessage($rule->getErrorMessage());
                    $newRule->setTriggerValue($rule->getTriggerValue());
                    $newRule->setAction($rule->getAction());
                    $processedRules[$newRule->getId()] = $newRule;
                }
            } else {
                $newRule = $this->ruleFactory->create();
                $newRule->setId($rule->getId());
                $newRule->setMasters($masters);
                $newRule->setSubjects($subjects);
                $newRule->setErrorMessage($rule->getErrorMessage());
                $newRule->setTriggerValue($rule->getTriggerValue());
                $newRule->setAction($rule->getAction());
                $processedRules[$newRule->getId()] = $newRule;
            }
        }

        $carrierData->setCompatibilityData($processedRules);

        return $carrierData;
    }

    /**
     * Normalize all given codes into "compound code" format.
     *
     * A compound code is a string in the format {optionCode}.{inputCode}.
     * For CompatibilityRule masters and subjects it's also valid to pass only an option code as a shortcut for all
     * of the inputs of that option. This method will expand those into separate compound codes for every every input.
     *
     * Codes that are already in compound format remain unchanged. codes for inputs that don't exist (anymore) are
     * stripped.
     *
     * @example ['preferredNeighbour'] will become ['preferredNeighbour.name', 'preferredNeighbour.address']
     * @param string[] $codes                               The list of codes to convert
     * @param ShippingOptionInterface[] $shippingOptions    The list of shipping options matching the codes
     * @return string[]                                     List of compound codes
     *
     */
    private function convertToCompoundCodes(array $codes, array $shippingOptions): array
    {
        $result = [];
        foreach ($codes as $code) {
            if (strpos($code, '.') !== false) {
                $result[] = $code;
                continue;
            }
            foreach ($shippingOptions as $shippingOption) {
                if ($code !== $shippingOption->getCode()) {
                    continue;
                }
                foreach ($shippingOption->getInputs() as $input) {
                    $result[] = $code . '.' . $input->getCode();
                }
            }
        }

        return $result;
    }
}
