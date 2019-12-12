<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Util;

use Dhl\ShippingCore\Api\Util\PdfCombinatorInterface;
use Magento\Framework\Exception\RuntimeException;
use Magento\Shipping\Model\Shipping\LabelGenerator;

/**
 * Utility for merging PDF pages into a single PDF binary.
 *
 * @package Dhl\ShippingCore\Util
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class PdfCombinator implements PdfCombinatorInterface
{
    /**
     * @var LabelGenerator
     */
    private $labelGenerator;

    /**
     * PdfCombinator constructor.
     *
     * @param LabelGenerator $labelGenerator
     */
    public function __construct(LabelGenerator $labelGenerator)
    {
        $this->labelGenerator = $labelGenerator;
    }

    /**
     * Combine an array of PDF binary content into one single PDF binary.
     *
     * @param string[] $pdfContent
     * @return string Combined binary PDF file.
     * @throws RuntimeException
     */
    public function combinePdfPages(array $pdfContent): string
    {
        if (empty($pdfContent)) {
            // no data given
            $pdfBinary = '';
        } elseif (count($pdfContent) < 2) {
            // exactly one file/page given, return as-is
            $pdfBinary = $pdfContent[0];
        } else {
            // multiple files/pages given, merge into one pdf file
            try {
                $pdfBinary = $this->labelGenerator->combineLabelsPdf($pdfContent)->render();
            } catch (\Zend_Pdf_Exception $exception) {
                throw new RuntimeException(__('Unable to process PDF content.'), $exception);
            }
        }

        return $pdfBinary;
    }

    /**
     * Combine an array of base64 encoded PDF content into one single PDF binary.
     *
     * @param string[] $pdfContent Base64 encoded PDF content
     * @return string Combined binary PDF file.
     * @throws RuntimeException
     */
    public function combineB64PdfPages(array $pdfContent): string
    {
        $pdfContent = array_map(function ($b64Data) {
            return base64_decode($b64Data);
        }, $pdfContent);

        return $this->combinePdfPages($pdfContent);
    }
}
