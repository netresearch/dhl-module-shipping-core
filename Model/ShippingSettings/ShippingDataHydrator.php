<?php
/**
 * See LICENSE.md for license details.
 */
namespace Dhl\ShippingCore\Model\ShippingSettings;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterfaceFactory;
use Dhl\ShippingCore\Api\ShippingSettings\CheckoutManagementInterface;
use Dhl\ShippingCore\Model\ShippingSettings\Data\CarrierData;
use Dhl\ShippingCore\Model\ShippingSettings\Data\Comment;
use Dhl\ShippingCore\Model\ShippingSettings\Data\Compatibility;
use Dhl\ShippingCore\Model\ShippingSettings\Data\Input;
use Dhl\ShippingCore\Model\ShippingSettings\Data\ItemCombinationRule;
use Dhl\ShippingCore\Model\ShippingSettings\Data\ItemShippingOptions;
use Dhl\ShippingCore\Model\ShippingSettings\Data\Metadata;
use Dhl\ShippingCore\Model\ShippingSettings\Data\Option;
use Dhl\ShippingCore\Model\ShippingSettings\Data\Route;
use Dhl\ShippingCore\Model\ShippingSettings\Data\ShippingOption;
use Dhl\ShippingCore\Model\ShippingSettings\Data\ValidationRule;
use Magento\Framework\Webapi\ServiceOutputProcessor;

class ShippingDataHydrator
{
    const CLASSMAP = [
        'carriers' => [
            'type' => 'array',
            'className' => CarrierData::class,
        ],
        'metadata' => [
            'type' => 'object',
            'className' => Metadata::class,
        ],
        'commentsBefore' => [
            'type' => 'array',
            'className' => Comment::class,
        ],
        'commentsAfter' => [
            'type' => 'array',
            'className' => Comment::class,
        ],
        'comment' => [
            'type' => 'object',
            'className' => Comment::class,
        ],
        'packageOptions' => [
            'type' => 'array',
            'className' => ShippingOption::class,
        ],
        'serviceOptions' => [
            'type' => 'array',
            'className' => ShippingOption::class,
        ],
        'shippingOptions' => [
            'type' => 'array',
            'className' => ShippingOption::class,
        ],
        'itemOptions' => [
            'type' => 'array',
            'className' => ItemShippingOptions::class,
        ],
        'inputs' => [
            'type' => 'array',
            'className' => Input::class,
        ],
        'options' => [
            'type' => 'array',
            'className' => Option::class,
        ],
        'validationRules' => [
            'type' => 'array',
            'className' => ValidationRule::class,
        ],
        'itemCombinationRule' => [
            'type' => 'object',
            'className' => ItemCombinationRule::class,
        ],
        'routes' => [
            'type' => 'array',
            'className' => Route::class,
        ],
        'compatibilityData' => [
            'type' => 'array',
            'className' => Compatibility::class,
        ]
    ];

    /**
     * @var ShippingDataInterfaceFactory
     */
    private $shippingDataFactory;

    /**
     * @var ServiceOutputProcessor
     */
    private $outputProcessor;

    /**
     * ShippingDataHydrator constructor.
     *
     * @param ShippingDataInterfaceFactory $shippingDataFactory
     * @param ServiceOutputProcessor $outputProcessor
     */
    public function __construct(
        ShippingDataInterfaceFactory $shippingDataFactory,
        ServiceOutputProcessor $outputProcessor
    ) {
        $this->shippingDataFactory = $shippingDataFactory;
        $this->outputProcessor = $outputProcessor;
    }

    /**
     * Convert a plain nested array of scalar types into a ShippingDataInterface object.
     *
     * Note: For M2.2 compatibility, created types must not have constructors with required values. Only populate
     * entities through setters.
     *
     * @param mixed[] $data
     * @return ShippingDataInterface
     * @throws \RuntimeException
     */
    public function toObject(array $data): ShippingDataInterface
    {
        return $this->shippingDataFactory->create(
            ['carriers' => $this->recursiveToObject('carriers', $data['carriers'])]
        );
    }

    /**
     * Convert a ShippingDataInterface object into a plain nested array of scalar types.
     *
     * @param ShippingDataInterface $data
     * @return array
     */
    public function toArray(ShippingDataInterface $data): array
    {
        return $this->outputProcessor->process(
            $data,
            CheckoutManagementInterface::class,
            'getCheckoutData'
        );
    }

    /**
     * @param string $key
     * @param mixed $data
     * @return mixed
     */
    private function recursiveToObject(string $key, $data)
    {
        if (array_key_exists($key, self::CLASSMAP)) {
            $className = self::CLASSMAP[$key]['className'];
            $type = self::CLASSMAP[$key]['type'];

            if ($type === 'array') {
                $result = [];
                foreach ($data as $arrayKey => $arrayItem) {
                    $result[$arrayKey] = new $className();
                    foreach ($arrayItem as $property => $value) {
                        $result[$arrayKey]->{'set' . ucfirst($property)}(
                            $this->recursiveToObject($property, $value)
                        );
                    }
                }
            } else {
                $result = new $className();
                foreach ($data as $property => $value) {
                    $result->{'set' . ucfirst($property)}(
                        $this->recursiveToObject($property, $value)
                    );
                }
            }
        } else {
            $result = $data;
        }

        return $result;
    }
}
