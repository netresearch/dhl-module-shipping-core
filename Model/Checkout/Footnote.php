<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\FootnoteInterface;

/**
 * Class Footnote
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class Footnote implements FootnoteInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string[]
     */
    private $subjects;

    /**
     * @var bool
     */
    private $subjectsMustBeSelected = false;

    /**
     * @var bool
     */
    private $subjectsMustBeAvailable = false;

    /**
     * Footnote constructor.
     *
     * @param string $id
     * @param string $content
     * @param string[] $subjects
     * @param bool $subjectsMustBeSelected
     * @param bool $subjectsMustBeAvailable
     */
    public function __construct(
        string $id,
        string $content,
        array $subjects,
        bool $subjectsMustBeSelected = false,
        bool $subjectsMustBeAvailable = false
    ) {
        $this->id = $id;
        $this->content = $content;
        $this->subjects = $subjects;
        $this->subjectsMustBeSelected = $subjectsMustBeSelected;
        $this->subjectsMustBeAvailable = $subjectsMustBeAvailable;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string[]
     */
    public function getSubjects(): array
    {
        return $this->subjects;
    }

    /**
     * @return bool
     */
    public function isSubjectsMustBeSelected(): bool
    {
        return $this->subjectsMustBeSelected;
    }

    /**
     * @return bool
     */
    public function isSubjectsMustBeAvailable(): bool
    {
        return $this->subjectsMustBeAvailable;
    }
}
