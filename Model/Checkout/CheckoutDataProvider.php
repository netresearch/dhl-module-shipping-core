<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\Checkout\CarrierDataInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Checkout\ServiceMetadataInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Service\CommentInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Service\InputInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Service\OptionInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Service\ServiceInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Service\ValidationRuleInterfaceFactory;
use Magento\Framework\Api\AttributeInterfaceFactory;

/**
 * Class CheckoutDataProvider
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2019 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 */
class CheckoutDataProvider
{
    /**
     * @var CheckoutDataInterfaceFactory
     */
    private $checkoutDataFactory;

    /**
     * @var CarrierDataInterfaceFactory
     */
    private $carrierDataFactory;

    /**
     * @var ServiceInterfaceFactory
     */
    private $serviceDataFactory;

    /**
     * @var ServiceMetadataInterfaceFactory
     */
    private $serviceMetadataFactory;

    /**
     * @var InputInterfaceFactory
     */
    private $inputFactory;

    /**
     * @var OptionInterfaceFactory
     */
    private $optionFactory;

    /**
     * @var CommentInterfaceFactory
     */
    private $commentFactory;

    /**
     * @var ValidationRuleInterfaceFactory
     */
    private $validationRuleFactory;

    /**
     * @var AttributeInterfaceFactory
     */
    private $attributeValueFactory;

    /**
     * CheckoutDataProvider constructor.
     *
     * @param CheckoutDataInterfaceFactory $checkoutDataFactory
     * @param CarrierDataInterfaceFactory $carrierDataFactory
     * @param ServiceInterfaceFactory $serviceDataFactory
     * @param ServiceMetadataInterfaceFactory $serviceMetadataFactory
     * @param InputInterfaceFactory $inputFactory
     * @param OptionInterfaceFactory $optionFactory
     * @param CommentInterfaceFactory $commentFactory
     * @param ValidationRuleInterfaceFactory $validationRuleFactory
     * @param AttributeInterfaceFactory $attributeValueFactory
     */
    public function __construct(
        CheckoutDataInterfaceFactory $checkoutDataFactory,
        CarrierDataInterfaceFactory $carrierDataFactory,
        ServiceInterfaceFactory $serviceDataFactory,
        ServiceMetadataInterfaceFactory $serviceMetadataFactory,
        InputInterfaceFactory $inputFactory,
        OptionInterfaceFactory $optionFactory,
        CommentInterfaceFactory $commentFactory,
        ValidationRuleInterfaceFactory $validationRuleFactory,
        AttributeInterfaceFactory $attributeValueFactory
    ) {
        $this->checkoutDataFactory = $checkoutDataFactory;
        $this->carrierDataFactory = $carrierDataFactory;
        $this->serviceDataFactory = $serviceDataFactory;
        $this->serviceMetadataFactory = $serviceMetadataFactory;
        $this->inputFactory = $inputFactory;
        $this->optionFactory = $optionFactory;
        $this->commentFactory = $commentFactory;
        $this->validationRuleFactory = $validationRuleFactory;
        $this->attributeValueFactory = $attributeValueFactory;
    }

    /**
     * @param string $countryCode
     * @param int $storeId
     * @param string $postalCode
     * @return CheckoutDataInterface
     */
    public function getData(string $countryCode, int $storeId, string $postalCode) : CheckoutDataInterface
    {
        return $this->checkoutDataFactory->create(
            [
                'carriers' => [
                    $this->carrierDataFactory->create(
                        [
                            'carrierCode' => 'dhlpaket',
                            'serviceData' => [
                                $this->serviceDataFactory->create(
                                    [
                                        'code' => 'preferredDay',
                                        'label' => 'Wunschtag',
                                        'enabledForCheckout' => true,
                                        'enabledForAutocreate' => true,
                                        'enabledForPackaging' => true,
                                        'availableAtPostalFacility' => true,
                                        'packagingReadonly' => true,
                                        'sortOrder' => 10,
                                        'routes' => [],
                                        'inputs' => [
                                            $this->inputFactory->create(
                                                [
                                                    'code' => 'date',
                                                    'label' => 'Wunschtag: Lieferung zum gewüschten Tag',
                                                    'options' => [
                                                        $this->optionFactory->create(
                                                            [
                                                                'label' => 'keiner',
                                                                'value' => '',
                                                                'disabled' => false,
                                                            ]
                                                        ),
                                                        $this->optionFactory->create(
                                                            [
                                                                'label' => 'Do, 21.',
                                                                'value' => '2019-02-21',
                                                                'disabled' => false,
                                                            ]
                                                        ),
                                                        $this->optionFactory->create(
                                                            [
                                                                'label' => 'So, 24.',
                                                                'value' => '2019-02-24',
                                                                'disabled' => true,
                                                            ]
                                                        ),
                                                        $this->optionFactory->create(
                                                            [
                                                                'label' => 'Mo, 25.',
                                                                'value' => '2019-02-25',
                                                                'disabled' => false,
                                                            ]
                                                        ),
                                                    ],
                                                    'tooltip' => 'Sie haben die Möglichkeit einen der angezeigten Tage als Wunschtag für die Lieferung Ihrer Waren auszuwählen. Andere Tage sind aufgrund der Lieferprozesse aktuell nicht möglich.',
                                                    'placeholder' => '',
                                                    'sortOrder' => 0,
                                                    'validationRules' => [
                                                        $this->validationRuleFactory->create(
                                                            [
                                                                'name' => 'dhl_not_allowed_with_parcelshop',
                                                            ]
                                                        ),
                                                    ],
                                                    'inputType' => 'date',
                                                    'defaultValue' => null,
                                                    'comment' => $this->commentFactory->create(
                                                        [
                                                            'content' => 'Für diesen Service fallen zusätzliche Versandkosten in Höhe von <b>3,00 €</b> inkl. MwSt. an.',
                                                            'hasFootnote' => true,
                                                        ]
                                                    )
                                                ]
                                            )
                                        ]
                                    ]
                                ),
                                $this->serviceDataFactory->create(
                                    [
                                        'code' => 'parcelAnnouncement',
                                        'label' => 'Paketankündigung',
                                        'enabledForCheckout' => true,
                                        'enabledForAutocreate' => true,
                                        'enabledForPackaging' => true,
                                        'availableAtPostalFacility' => true,
                                        'packagingReadonly' => true,
                                        'sortOrder' => 40,
                                        'routes' => [],
                                        'inputs' => [
                                            $this->inputFactory->create(
                                                [
                                                    'code' => 'enabled',
                                                    'label' => 'Paketankündigung aktivieren',
                                                    'options' => [],
                                                    'tooltip' => 'Ihre E-Mail-Adresse wird bei Aktivierung an DHL übermittelt, worauf DHL eine Paketankündigung zu Ihrer Sendung auslöst. Die E-Mail-Adresse wird ausschließlich für die Ankündigung zu dieser Sendung verwendet.',
                                                    'placeholder' => '',
                                                    'sortOrder' => 0,
                                                    'validationRules' => [],
                                                    'inputType' => 'checkbox',
                                                    'defaultValue' => false,
                                                    'comment' => $this->commentFactory->create(
                                                        [
                                                            'content' => 'Mit der Aktivierung der Paketankündigung informiert Sie DHL per E-Mail über die geplante Lieferung Ihrer Sendung.',
                                                            'hasFootnote' => false,
                                                        ]
                                                    )
                                                ]
                                            )
                                        ]
                                    ]
                                ),
                                $this->serviceDataFactory->create(
                                    [
                                        'code' => 'preferredLocation',
                                        'label' => 'Wunschort: Lieferung an den gewüschten Ablageort',
                                        'enabledForCheckout' => true,
                                        'enabledForAutocreate' => false,
                                        'enabledForPackaging' => false,
                                        'availableAtPostalFacility' => false,
                                        'packagingReadonly' => true,
                                        'sortOrder' => 60,
                                        'routes' => [],
                                        'inputs' => [
                                            $this->inputFactory->create(
                                                [
                                                    'code' => 'details',
                                                    'label' => 'Wunschort',
                                                    'options' => [],
                                                    'tooltip' => 'Bestimmen Sie einen wettergeschützten und nicht einsehbaren Platz auf Ihrem Grundstück, an dem wir das Paket während Ihrer Abwesenheit hinterlegen dürfen.',
                                                    'placeholder' => 'z.B. Garage, Terrasse',
                                                    'sortOrder' => 0,
                                                    'validationRules' => [
                                                        $this->validationRuleFactory->create(
                                                            [
                                                                'name' => 'maxLength',
                                                                'params' => 40,
                                                            ]
                                                        ),
                                                        $this->validationRuleFactory->create(
                                                            [
                                                                'name' => 'validate-no-html-tags',
                                                            ]
                                                        ),
                                                        $this->validationRuleFactory->create(
                                                            [
                                                                'name' => 'dhl_filter_special_chars',
                                                            ]
                                                        ),
                                                    ],
                                                    'inputType' => 'text',
                                                    'defaultValue' => '',
                                                ]
                                            ),
                                        ]
                                    ]
                                ),
                                $this->serviceDataFactory->create(
                                    [
                                        'code' => 'preferredNeighbour',
                                        'label' => 'Wunschnachbar: Lieferung an den Nachbar Ihrer Wahl',
                                        'enabledForCheckout' => true,
                                        'enabledForAutocreate' => true,
                                        'enabledForPackaging' => true,
                                        'availableAtPostalFacility' => true,
                                        'packagingReadonly' => true,
                                        'sortOrder' => 70,
                                        'routes' => [],
                                        'inputs' => [
                                            $this->inputFactory->create(
                                                [
                                                    'code' => 'name',
                                                    'label' => 'Name des Nachbarn',
                                                    'options' => [],
                                                    'tooltip' => 'Bestimmen Sie eine Person in Ihrer unmittelbaren Nachbarschaft, bei der wir Ihr Paket abgeben dürfen. Diese sollte im gleichen Haus, direkt gegenüber oder nebenan wohnen.',
                                                    'placeholder' => 'Vorname, Nachname des Nachbarn',
                                                    'sortOrder' => 0,
                                                    'validationRules' => [
                                                        $this->validationRuleFactory->create(
                                                            [
                                                                'name' => 'maxLength',
                                                                'params' => 40,
                                                            ]
                                                        ),
                                                        $this->validationRuleFactory->create(
                                                            [
                                                                'name' => 'validate-no-html-tags',
                                                            ]
                                                        ),
                                                        $this->validationRuleFactory->create(
                                                            [
                                                                'name' => 'dhl_filter_special_chars',
                                                            ]
                                                        ),
                                                    ],
                                                    'inputType' => 'text',
                                                    'defaultValue' => '',
                                                ]
                                            ),
                                            $this->inputFactory->create(
                                                [
                                                    'code' => 'address',
                                                    'label' => 'Adresse des Nachbarn',
                                                    'options' => [],
                                                    'tooltip' => 'Test tooltip',
                                                    'placeholder' => 'Strasse, Hausnummer, PLZ, Ort',
                                                    'sortOrder' => 0,
                                                    'validationRules' => [
                                                        $this->validationRuleFactory->create(
                                                            [
                                                                'name' => 'maxLength',
                                                                'params' => 40,
                                                            ]
                                                        ),
                                                        $this->validationRuleFactory->create(
                                                            [
                                                                'name' => 'validate-no-html-tags',
                                                            ]
                                                        ),
                                                        $this->validationRuleFactory->create(
                                                            [
                                                                'name' => 'dhl_filter_special_chars',
                                                            ]
                                                        ),
                                                    ],
                                                    'inputType' => 'text',
                                                    'defaultValue' => '',
                                                ]
                                            )
                                        ]
                                    ]
                                ),
                            ],
                            'serviceMetadata' => $this->serviceMetadataFactory->create(
                                [
                                    'title' => 'DHL Shipping: Wunschpaket',
                                    'imageUrl' => '',
                                    'commentsBefore' => [
                                        $this->commentFactory->create(
                                            [
                                                'content' => 'DHL Preferred Delivery. Delivered just the way you want.',
                                                'hasFootnote' => false,
                                            ]
                                        ),
                                        $this->commentFactory->create(
                                            [
                                                'content' => 'Kurze Anleitung wie das ganze funktioniert. Ut a lorem vel quam finibus venenatis. Phasellus urna libero, sollicitudin id leo nec.',
                                                'hasFootnote' => false,
                                            ]
                                        )
                                    ],
                                    'commentsAfter' => [
                                        $this->commentFactory->create(
                                            [
                                                'content' => 'Gemeinsame kosten sind blubb.',
                                                'hasFootnote' => true,
                                            ]
                                        ),
                                    ],
                                ]
                            ),
                            'serviceCompatibilityData' => [],
                        ]
                    )
                ]
            ]
        );
    }
}
