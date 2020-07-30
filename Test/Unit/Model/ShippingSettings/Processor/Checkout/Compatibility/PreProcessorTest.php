<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Unit\Model\ShippingSettings\Processor\Checkout\Compatibility;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterfaceFactory;
use Dhl\ShippingCore\Model\ShippingSettings\Data\CarrierData;
use Dhl\ShippingCore\Model\ShippingSettings\Data\Compatibility;
use Dhl\ShippingCore\Model\ShippingSettings\Data\Input;
use Dhl\ShippingCore\Model\ShippingSettings\Data\ShippingOption;
use Dhl\ShippingCore\Model\ShippingSettings\Processor\Checkout\Compatibility\CompatibilityPreProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PreProcessorTest extends TestCase
{
    /**
     * @var CompatibilityInterfaceFactory | MockObject
     */
    private $mockCompatibilityFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockCompatibilityFactory = $this->getMockBuilder(CompatibilityInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->mockCompatibilityFactory
            ->method('create')
            ->willReturnCallback(static function () {
                return new Compatibility();
            });
    }

    public function testProcess()
    {
        $masterInput = new Input();
        $masterInput->setCode('masterInput');

        $masterOption = new ShippingOption();
        $masterOption->setCode('masterOption');
        $masterOption->setInputs([$masterInput]);

        $subjectInput = new Input();
        $subjectInput->setCode('subjectInput');
        $subjectInput2 = new Input();
        $subjectInput2->setCode('subjectInput2');

        $subjectOption = new ShippingOption();
        $subjectOption->setCode('subjectOption');
        $subjectOption->setInputs([$subjectInput, $subjectInput2]);

        $compatibility = new Compatibility();
        $compatibility->setId('testRule');
        $compatibility->setMasters(['masterOption']); // implicitly sets all inputs
        $compatibility->setSubjects([
            'subjectOption', // implicitly sets all inputs
        ]);
        $compatibility->setAction('disable');
        $compatibility->setTriggerValue('shouldTriggerRule');

        $carrier = new CarrierData();
        $carrier->setPackageOptions([$masterOption]);
        $carrier->setServiceOptions([$subjectOption]);
        $carrier->setCompatibilityData([$compatibility]);

        $subject = new CompatibilityPreProcessor($this->mockCompatibilityFactory);

        $result = $subject->process($carrier);

        $firstResultRule = array_values($result->getCompatibilityData())[0];
        self::assertSame(
            ['masterOption.masterInput'],
            $firstResultRule->getMasters(),
            'Masters were not converted to compound codes properly'
        );
        self::assertNotContains(
            ['masterOption.masterInput'],
            $firstResultRule->getSubjects(),
            'Masters were not removed from subject list'
        );
        self::assertNotContains(
            ['masterOption'],
            $firstResultRule->getSubjects(),
            'Masters were not removed from subject list and not even converted to compound'
        );
        self::assertSame(
            ['subjectOption.subjectInput', 'subjectOption.subjectInput2'],
            $firstResultRule->getSubjects(),
            'Subjects were not converted to compound codes properly'
        );
        self::assertSame(
            'disable',
            $firstResultRule->getAction(),
            'Rule action changed'
        );
        self::assertSame(
            'shouldTriggerRule',
            $firstResultRule->getTriggerValue(),
            'Rule trigger value changed'
        );
    }

    public function testProcessMasterLess()
    {
        $subjectInput = new Input();
        $subjectInput->setCode('subjectInput');
        $subjectInput2 = new Input();
        $subjectInput2->setCode('subjectInput2');

        $subjectOption = new ShippingOption();
        $subjectOption->setCode('subjectOption');
        $subjectOption->setInputs([$subjectInput, $subjectInput2]);

        $compatibility = new Compatibility();
        $compatibility->setId('testRule');
        $compatibility->setSubjects(// rule has no masters, should be split up into one rule per subject
            [
                'subjectOption.subjectInput',
                'subjectOption.subjectInput2',
            ]
        );
        $compatibility->setTriggerValue('someValueThatShouldNotChange');
        $compatibility->setAction('unrequire'); // some action that should not change
        $compatibility->setErrorMessage('someErrorMessageThatShouldNotChange');

        $carrier = new CarrierData();
        $carrier->setServiceOptions([$subjectOption]);
        $carrier->setCompatibilityData([$compatibility]);

        $subject = new CompatibilityPreProcessor($this->mockCompatibilityFactory);

        $result = $subject->process($carrier);

        list($firstResultRule, $secondResultRule) = array_values($result->getCompatibilityData());

        self::assertCount(
            2,
            $result->getCompatibilityData(),
            'Compatibility rule was not split into two'
        );
        self::assertEquals(
            ['subjectOption.subjectInput'],
            array_values($firstResultRule->getMasters()),
            'First rule does not have first subject as master'
        );
        self::assertEquals(
            ['subjectOption.subjectInput2'],
            array_values($firstResultRule->getSubjects()),
            'First rule does not have second subject as subject'
        );
        self::assertSame(
            ['subjectOption.subjectInput2'],
            array_values($secondResultRule->getMasters()),
            'Second rule does not have second subject as master'
        );
        self::assertSame(
            ['subjectOption.subjectInput'],
            array_values($secondResultRule->getSubjects()),
            'Second rule does not have first subject as subject'
        );
        self::assertSame(
            $firstResultRule->getTriggerValue(),
            $secondResultRule->getTriggerValue(),
            'Compatibility rule was not cloned correctly, trigger values are different'
        );
        self::assertSame(
            $firstResultRule->getAction(),
            $secondResultRule->getAction(),
            'Compatibility rule was not cloned correctly, actions are different'
        );
        self::assertSame(
            $firstResultRule->getErrorMessage(),
            $secondResultRule->getErrorMessage(),
            'Compatibility rule was not cloned correctly, error messages are different'
        );
    }

    public function testProcessInvalid()
    {
        $masterInput = new Input();
        $masterInput->setCode('masterInput');

        $masterOption = new ShippingOption();
        $masterOption->setCode('masterOption');
        $masterOption->setInputs([$masterInput]);

        $subjectInput = new Input();
        $subjectInput->setCode('subjectInput');

        $subjectOption = new ShippingOption();
        $subjectOption->setCode('subjectOption');
        $subjectOption->setInputs([$subjectInput]);

        $compatibility = new Compatibility();
        $compatibility->setId('testRule');
        $compatibility->setMasters(['masterOption']);
        $compatibility->setSubjects(
            [
                'subjectOption',
                'masterOption.masterInput', // should trigger an exception
            ]
        );
        $compatibility->setAction('disable');
        $compatibility->setTriggerValue('shouldTriggerRule');

        $carrier = new CarrierData();
        $carrier->setPackageOptions([$masterOption]);
        $carrier->setServiceOptions([$subjectOption]);
        $carrier->setCompatibilityData([$compatibility]);

        $subject = new CompatibilityPreProcessor($this->mockCompatibilityFactory);

        $this->expectException(\InvalidArgumentException::class);

        $subject->process($carrier);
    }
}
