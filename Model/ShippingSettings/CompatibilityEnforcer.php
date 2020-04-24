<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings;

use Dhl\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Dhl\ShippingCore\Model\ShippingSettings\Processor\Checkout\Compatibility\PreProcessor;
use Magento\Framework\Exception\LocalizedException;

class CompatibilityEnforcer
{
    /**
     * @var PreProcessor
     */
    private $preProcessor;

    public function __construct(PreProcessor $preProcessor)
    {
        $this->preProcessor = $preProcessor;
    }

    /**
     * Applies all compatibility rules.
     *
     * It will apply all rules until all inputs are "stable", i.e. until applying the rules does not modify any input.
     * This is neccessary because rules can affect each other, the application of one rule triggering another rule
     * and so on.
     *
     * There is an (arbitrary) limit of 5 iterations to avoid infinite loops or overly convoluted rule setups.
     *
     * @param CarrierDataInterface $carrierData
     * @return CarrierDataInterface
     * @throws LocalizedException           Thrown if a required input is missing a value
     * @throws \InvalidArgumentException    Thrown if configured rules override each other or are otherwise invalid
     */
    public function enforce(CarrierDataInterface $carrierData): CarrierDataInterface
    {
        $carrierData = $this->preProcessor->process($carrierData);
        for ($iteration = 0; $iteration <= 5; $iteration++) {
            $inputsModified = $this->processRules($carrierData);
            if (!$inputsModified) {
                return $carrierData;
            }
        }

        throw new \InvalidArgumentException(
            'Shipping option compatibility rules could not be resolved to a stable state. ' .
            'You probably configured rules that conflict with each other or are overly complicated.'
        );
    }
    /**
     * @param CarrierDataInterface $carrierData
     * @return bool
     * @throws LocalizedException   Thrown if a required input is missing a value
     */
    private function processRules(CarrierDataInterface $carrierData): bool
    {
        $inputsModified = false;

        $compatibilityRules = $carrierData->getCompatibilityData();
        foreach ($compatibilityRules as $rule) {
            /** @var InputInterface[] $masterInputs */
            $masterInputs = [];
            foreach ($rule->getMasters() as $master) {
                if ($input = $this->getInputByCode($master, $carrierData)) {
                    $masterInputs[] = $input;
                }
            }

            /** @var InputInterface[] $subjectInputs */
            $subjectInputs = [];
            foreach ($rule->getSubjects() as $subject) {
                if ($input = $this->getInputByCode($subject, $carrierData)) {
                    $subjectInputs[] = $input;
                }
            }

            if ($this->processRule($masterInputs, $subjectInputs, $rule)) {
                $inputsModified = true;
            }
        }

        return $inputsModified;
    }

    /**
     * @param InputInterface[] $masterInputs
     * @param InputInterface[] $subjectInputs   Will be mutated according to the rule
     * @param CompatibilityInterface $rule
     * @return bool                             Returns "true" if any subject inputs were modified by applying the rule
     * @throws LocalizedException               Thrown if a required input is missing a value
     */
    private function processRule(
        array $masterInputs,
        array $subjectInputs,
        CompatibilityInterface $rule
    ) : bool {
        $inputModified = false;
        foreach ($masterInputs as $masterInput) {
            $valueMatches = false;
            if ($rule->getTriggerValue() === '*' && $masterInput->getDefaultValue() !== '') {
                $valueMatches = true;
            }
            if ($masterInput->getDefaultValue() === $rule->getTriggerValue()) {
                $valueMatches = true;
            }
            if ($valueMatches) {
                $actions = [CompatibilityInterface::ACTION_DISABLE, CompatibilityInterface::ACTION_HIDE ];
                if (in_array($rule->getAction(), $actions, true)) {
                    foreach ($subjectInputs as $input) {
                        if ($input->getDefaultValue() !== '') {
                            $input->setDefaultValue('');
                            $inputModified = true;
                        }
                    }
                }
                if ($rule->getAction() === CompatibilityInterface::ACTION_DISABLE) {
                    foreach ($subjectInputs as $input) {
                        if (!$input->isDisabled()) {
                            $input->setDisabled(true);
                            $inputModified = true;
                        }
                    }
                }

                if ($rule->getAction() === CompatibilityInterface::ACTION_REQUIRE) {
                    foreach ($subjectInputs as $input) {
                        if (!$input->getDefaultValue()) {
                            throw new LocalizedException(__($rule->getErrorMessage()));
                        }
                    }
                }
            }
        }

        return $inputModified;
    }

    /**
     * @param string $compoundCode
     * @param CarrierDataInterface $carrierData
     * @return InputInterface|null
     */
    private function getInputByCode(
        string $compoundCode,
        CarrierDataInterface $carrierData
    ) {
        /** @var ShippingOptionInterface[] $shippingOptions */
        $shippingOptions = array_merge(
            $carrierData->getServiceOptions(),
            $carrierData->getPackageOptions()
        );

        list($optionCode, $inputCode) = explode('.', $compoundCode);
        foreach ($shippingOptions as $option) {
            if ($optionCode !== $option->getCode()) {
                continue;
            }
            foreach ($option->getInputs() as $input) {
                if ($input->getCode() === $inputCode) {
                    return $input;
                }
            }
        }

        return null;
    }
}
