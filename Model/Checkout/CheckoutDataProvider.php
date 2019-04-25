<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout;

/**
 * Class CheckoutDataProvider
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class CheckoutDataProvider
{
    /**
     * @param string $countryCode
     * @param int $storeId
     * @param string $postalCode
     * @return array
     */
    public function getData(string $countryCode, int $storeId, string $postalCode): array
    {
        /** @TODO replace with actual checkout data. */
        return $this->getMockData();
    }

    /**
     * @return array
     */
    private function getMockData(): array
    {
        return [
            'carriers' =>  [
                [
                    'carrierCode' => 'flatrate',
                    'shippingOptions' => [
                        [
                            'code' => 'testService',
                            'label' => 'Testservice',
                            'enabledForCheckout' => true,
                            'enabledForAutocreate' => true,
                            'enabledForPackaging' => true,
                            'availableAtPostalFacility' => true,
                            'packagingReadonly' => false,
                            'sortOrder' => 0,
                            'routes' => [],
                            'inputs' => [
                                [
                                    'code' => 'testinput0',
                                    'label' => 'Test input 0',
                                    'labelVisible' => true,
                                    'inputType' => 'text',
                                ],
                                [
                                    'code' => 'testinput1',
                                    'label' => 'Test input 1',
                                    'labelVisible' => true,
                                    'options' => [
                                        [
                                            'label' => 'Default',
                                            'value' => '',
                                            'disabled' => false,
                                        ],
                                        [
                                            'label' => 'Option 1',
                                            'value' => 'option1',
                                            'disabled' => false,
                                        ],
                                    ],
                                    'tooltip' => 'Test Tooltip.',
                                    'inputType' => 'radioset',
                                ],
                                [
                                    'code' => 'testinput2',
                                    'label' => 'Test input 2',
                                    'labelVisible' => true,
                                    'inputType' => 'text',
                                ],
                            ],
                        ],
                    ],
                    'metadata' => [
                        'title' => 'DHL eCommerce Shipping Settings.',
                        'imageUrl' => '',
                        'commentsBefore' => [
                            [
                                'content' => 'Phasellus urna libero, sollicitudin id leo nec.',
                            ],
                        ],
                        'commentsAfter' => [
                            [
                                'content' => 'An eCommerce test comment below the service selection.',
                            ],
                        ],
                        'footnotes' => [],
                    ],
                    'compatibilityData' => [
                        [
                            'incompatibilityRule' => false,
                            'hideSubjects' => false,
                            'masters' => ['testService.testinput0'],
                            'subjects' => ['testService.testinput1'],
                            'errorMessage' => 'Compatibility of %1 violated',
                        ],
                        [
                            'incompatibilityRule' => false,
                            'hideSubjects' => true,
                            'masters' => ['testService.testinput1'],
                            'subjects' => ['testService.testinput2'],
                            'errorMessage' => 'Compatibility of %1 violated',
                        ]
                    ],
                ],
                [
                    'carrierCode' => 'dhlpaket',
                    'shippingOptions' => [
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
                                [
                                    'code' => 'date',
                                    'label' => 'Wunschtag',
                                    'labelVisible' => false,
                                    'options' => [
                                        [
                                            'label' => 'keiner',
                                            'value' => '',
                                            'disabled' => false,
                                        ],
                                        [
                                            'label' => 'Do, 21.',
                                            'value' => '2019-02-21',
                                            'disabled' => false,
                                        ],
                                        [
                                            'label' => 'So, 24.',
                                            'value' => '2019-02-24',
                                            'disabled' => true,
                                        ],
                                        [
                                            'label' => 'Mo, 25.',
                                            'value' => '2019-02-25',
                                            'disabled' => false,
                                        ],
                                    ],
                                    'tooltip' => 'Sie haben die Möglichkeit einen der angezeigten Tage als Wunschtag für die Lieferung Ihrer Waren auszuwählen. Andere Tage sind aufgrund der Lieferprozesse aktuell nicht möglich.',
                                    'sortOrder' => 0,
                                    'validationRules' => [
                                            [
                                                'name' => 'dhl_not_allowed_with_parcelshop',
                                            ],
                                    ],
                                    'inputType' => 'date',
                                    'comment' => [
                                        'content' => 'Für diesen ShippingOption fallen zusätzliche Versandkosten in Höhe von <strong>3,00 €</strong> inkl. MwSt. an.',
                                        'footnoteId' => 'footnote-combined-cost',
                                    ],
                                ],
                            ],
                        ],
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
                                [
                                    'code' => 'time',
                                    'label' => 'Wunschzeit',
                                    'labelVisible' => false,
                                    'options' => [
                                        [
                                            'label' => 'keine',
                                            'value' => '',
                                            'disabled' => false,
                                        ],
                                        [
                                            'label' => '10:00-12:00',
                                            'value' => '10001200',
                                            'disabled' => false,
                                        ],
                                        [
                                            'label' => '12:00-14:00',
                                            'value' => '12001400',
                                            'disabled' => true,
                                        ],
                                    ],
                                    'tooltip' => 'Damit Sie besser planen können, haben Sie die Möglichkeit eine Wunschzeit für die Lieferung auszuwählen. Sie können eine der dargestellten Zeiten für die Lieferung auswählen.',
                                    'sortOrder' => 0,
                                    'validationRules' => [],
                                    'inputType' => 'time',
                                    'comment' => [
                                        'content' => 'Für diesen ShippingOption fallen zusätzliche Versandkosten in Höhe von <strong>4,00 €</strong> inkl. MwSt. an',
                                        'footnoteId' => 'footnote-combined-cost',
                                    ],
                                ],
                            ],
                        ],
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
                                [
                                    'code' => 'enabled',
                                    'label' => 'Paketankündigung aktivieren',
                                    'labelVisible' => true,
                                    'options' => [],
                                    'tooltip' => 'Ihre E-Mail-Adresse wird bei Aktivierung an DHL übermittelt, worauf DHL eine Paketankündigung zu Ihrer Sendung auslöst. Die E-Mail-Adresse wird ausschließlich für die Ankündigung zu dieser Sendung verwendet.',
                                    'sortOrder' => 0,
                                    'validationRules' => [],
                                    'inputType' => 'checkbox',
                                    'comment' => [
                                        'content' => 'Mit der Aktivierung der Paketankündigung informiert Sie DHL per E-Mail über die geplante Lieferung Ihrer Sendung.',
                                    ],
                                ],
                            ],
                        ],
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
                                [
                                    'code' => 'details',
                                    'label' => 'Wunschort',
                                    'labelVisible' => false,
                                    'options' => [],
                                    'tooltip' => 'Bestimmen Sie einen wettergeschützten und nicht einsehbaren Platz auf Ihrem Grundstück, an dem wir das Paket während Ihrer Abwesenheit hinterlegen dürfen.',
                                    'placeholder' => 'z.B. Garage, Terrasse',
                                    'sortOrder' => 0,
                                    'validationRules' => [
                                        [
                                            'name' => 'maxLength',
                                            'params' => 40,
                                        ],
                                        [
                                            'name' => 'validate-no-html-tags',
                                        ],
                                        [
                                            'name' => 'dhl_filter_special_chars',
                                        ],
                                    ],
                                    'inputType' => 'text',
                                    'defaultValue' => '',
                                ],
                            ],
                        ],
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
                                [
                                    'code' => 'name',
                                    'label' => 'Name des Nachbarn',
                                    'labelVisible' => true,
                                    'options' => [],
                                    'tooltip' => 'Bestimmen Sie eine Person in Ihrer unmittelbaren Nachbarschaft, bei der wir Ihr Paket abgeben dürfen. Diese sollte im gleichen Haus, direkt gegenüber oder nebenan wohnen.',
                                    'placeholder' => 'Vorname, Nachname des Nachbarn',
                                    'sortOrder' => 0,
                                    'validationRules' => [
                                        [
                                            'name' => 'maxLength',
                                            'params' => 40,
                                        ],
                                        [
                                            'name' => 'validate-no-html-tags',
                                        ],
                                        [
                                            'name' => 'dhl_filter_special_chars',
                                        ],
                                    ],
                                    'inputType' => 'text',
                                    'defaultValue' => '',
                                ],
                                [
                                    'code' => 'address',
                                    'label' => 'Adresse des Nachbarn',
                                    'labelVisible' => true,
                                    'options' => [],
                                    'tooltip' => 'Test tooltip',
                                    'placeholder' => 'Strasse, Hausnummer, PLZ, Ort',
                                    'sortOrder' => 0,
                                    'validationRules' => [
                                        [
                                            'name' => 'maxLength',
                                            'params' => 40,
                                        ],
                                        [
                                            'name' => 'validate-no-html-tags',
                                        ],
                                        [
                                            'name' => 'dhl_filter_special_chars',
                                        ],
                                    ],
                                    'inputType' => 'text',
                                ],
                            ],
                        ],
                    ],
                    'metadata' => [
                        'title' => 'DHL Preferred Delivery. Delivered just the way you want.',
                        'imageUrl' => '',
                        'commentsBefore' => [
                            [
                                'content' => 'DHL Preferred Delivery. Delivered just the way you want.',
                            ],
                            [
                                'content' => 'Kurze Anleitung wie das ganze funktioniert. Ut a lorem vel quam finibus venenatis. Phasellus urna libero, sollicitudin id leo nec.',
                            ],
                        ],
                        'commentsAfter' => [
                            [
                                'content' => 'A test comment below the service selection.',
                            ],
                        ],
                        'footnotes' => [
                            [
                                'content' => 'When booked together, the price of Preferred Day and Preferred Time is <strong>11 €</strong>.',
                                'footnoteId' => 'footnote-combined-cost',
                                'subjects' => ['preferredTime', 'preferredDay'],
                                'subjectsMustBeSelected' => true,
                                'subjectsMustBeAvailable' => true,
                            ],
                        ],
                    ],
                    'compatibilityData' => [
                        [
                            'incompatibilityRule' => true,
                            'subjects' => ['preferredLocation', 'preferredNeighbour'],
                            'errorMessage' => 'Please choose only one of %1.'
                        ],
                        [
                            'incompatibilityRule' => false,
                            'subjects' => ['preferredNeighbour.name', 'preferredNeighbour.address'],
                            'errorMessage' => 'Some values for Preferred Neighbour service are missing.'
                        ],
                        [
                            'incompatibilityRule' => false,
                            'subjects' => ['preferredDay', 'preferredTime'],
                            'errorMessage' => 'Services %1 require each other.'
                        ],
                    ],
                ],
            ],
        ];
    }
}
