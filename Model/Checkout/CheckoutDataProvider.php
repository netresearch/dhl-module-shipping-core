<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\Checkout\CarrierDataInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Checkout\FootnoteInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Checkout\ServiceMetadataInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Service\CommentInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Service\CompatibilityInterfaceFactory;
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
     * @var CompatibilityInterfaceFactory
     */
    private $compatibilityFactory;

    /**
     * @var FootnoteInterfaceFactory
     */
    private $footnoteFactory;

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
     * @param CompatibilityInterfaceFactory $compatibilityFactory
     * @param FootnoteInterfaceFactory $footnoteFactory
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
        AttributeInterfaceFactory $attributeValueFactory,
        CompatibilityInterfaceFactory $compatibilityFactory,
        FootnoteInterfaceFactory $footnoteFactory
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
        $this->compatibilityFactory = $compatibilityFactory;
        $this->footnoteFactory = $footnoteFactory;
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
                                        'label' => 'Wunschtag: Lieferung zum gewüschten Tag',
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
                                                    'label' => 'Wunschtag',
                                                    'labelVisible' => false,
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
                                                    'defaultValue' => '',
                                                    'comment' => $this->commentFactory->create(
                                                        [
                                                            'content' => 'Für diesen Service fallen zusätzliche Versandkosten in Höhe von <strong>3,00 €</strong> inkl. MwSt. an.',
                                                            'footnoteId' => 'footnote-combined-cost',
                                                        ]
                                                    )
                                                ]
                                            )
                                        ]
                                    ]
                                ),
                                $this->serviceDataFactory->create(
                                    [
                                        'code' => 'preferredTime',
                                        'label' => 'Wunschzeit: Lieferung im gewüschten Zeitfenster',
                                        'enabledForCheckout' => true,
                                        'enabledForAutocreate' => true,
                                        'enabledForPackaging' => true,
                                        'availableAtPostalFacility' => true,
                                        'packagingReadonly' => true,
                                        'sortOrder' => 15,
                                        'routes' => [],
                                        'inputs' => [
                                            $this->inputFactory->create(
                                                [
                                                    'code' => 'time',
                                                    'label' => 'Wunschzeit',
                                                    'labelVisible' => false,
                                                    'options' => [
                                                        $this->optionFactory->create(
                                                            [
                                                                'label' => 'keine',
                                                                'value' => '',
                                                                'disabled' => false,
                                                            ]
                                                        ),
                                                        $this->optionFactory->create(
                                                            [
                                                                'label' => '10:00-12:00',
                                                                'value' => '10001200',
                                                                'disabled' => false,
                                                            ]
                                                        ),
                                                        $this->optionFactory->create(
                                                            [
                                                                'label' => '12:00-14:00',
                                                                'value' => '12001400',
                                                                'disabled' => true,
                                                            ]
                                                        ),
                                                    ],
                                                    'tooltip' => 'Damit Sie besser planen können, haben Sie die Möglichkeit eine Wunschzeit für die Lieferung auszuwählen. Sie können eine der dargestellten Zeiten für die Lieferung auswählen.',
                                                    'placeholder' => '',
                                                    'sortOrder' => 0,
                                                    'validationRules' => [],
                                                    'inputType' => 'time',
                                                    'defaultValue' => '',
                                                    'comment' => $this->commentFactory->create(
                                                        [
                                                            'content' => 'Für diesen Service fallen zusätzliche Versandkosten in Höhe von <strong>4,00 €</strong> inkl. MwSt. an',
                                                            'footnoteId' => 'footnote-combined-cost',
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
                                                    'labelVisible' => true,
                                                    'options' => [],
                                                    'tooltip' => 'Ihre E-Mail-Adresse wird bei Aktivierung an DHL übermittelt, worauf DHL eine Paketankündigung zu Ihrer Sendung auslöst. Die E-Mail-Adresse wird ausschließlich für die Ankündigung zu dieser Sendung verwendet.',
                                                    'placeholder' => '',
                                                    'sortOrder' => 0,
                                                    'validationRules' => [],
                                                    'inputType' => 'checkbox',
                                                    'defaultValue' => '',
                                                    'comment' => $this->commentFactory->create(
                                                        [
                                                            'content' => 'Mit der Aktivierung der Paketankündigung informiert Sie DHL per E-Mail über die geplante Lieferung Ihrer Sendung.',
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
                                                    'labelVisible' => false,
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
                                                    'labelVisible' => true,
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
                                                    'labelVisible' => true,
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
                                    'title' => 'DHL Preferred Delivery. Delivered just the way you want.',
                                    'imageUrl' => '',
                                    'commentsBefore' => [
                                        $this->commentFactory->create(
                                            [
                                                'content' => 'DHL Preferred Delivery. Delivered just the way you want.',
                                            ]
                                        ),
                                        $this->commentFactory->create(
                                            [
                                                'content' => 'Kurze Anleitung wie das ganze funktioniert. Ut a lorem vel quam finibus venenatis. Phasellus urna libero, sollicitudin id leo nec.',
                                            ]
                                        )
                                    ],
                                    'commentsAfter' => [
                                        $this->commentFactory->create(
                                            [
                                                'content' => 'A test comment below the service selection.',
                                            ]
                                        )
                                    ],
                                    'footnotes' => [
                                        $this->footnoteFactory->create(
                                            [
                                                'content' => 'When booked together, the price of Preferred Day and Preferred Time is <strong>11 €</strong>.',
                                                'footnoteId' => 'footnote-combined-cost',
                                                'subjects' => ['preferredTime', 'preferredDay'],
                                                'subjectsMustBeSelected' => true,
                                                'subjectsMustBeAvailable' => true,
                                            ]
                                        )
                                    ],
                                ]
                            ),
                            'serviceCompatibilityData' => [
                                $this->compatibilityFactory->create(
                                    [
                                        'incompatibilityRule' => true,
                                        'subjects' => ['preferredLocation', 'preferredNeighbour'],
                                        'errorMessage' => 'Please choose only one of %1.'
                                    ]
                                ),
                                $this->compatibilityFactory->create(
                                    [
                                        'incompatibilityRule' => false,
                                        'subjects' => ['preferredNeighbour.name', 'preferredNeighbour.address'],
                                        'errorMessage' => 'Some values for Preferred Neighbour service are missing.'
                                    ]
                                ),
                                $this->compatibilityFactory->create(
                                    [
                                        'incompatibilityRule' => false,
                                        'subjects' => ['preferredDay', 'preferredTime'],
                                        'errorMessage' => 'Services %1 require each other.'
                                    ]
                                ),
                            ],
                        ]
                    )
                ]
            ]
        );
    }
}
