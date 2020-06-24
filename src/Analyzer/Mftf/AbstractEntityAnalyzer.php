<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SemanticVersionChecker\Analyzer\Mftf;

use Magento\SemanticVersionChecker\MftfReport;
use PHPSemVerChecker\Report\Report;

/**
 * Class AbstractEntityAnalyzer
 */
abstract class AbstractEntityAnalyzer
{
    /**
     * @var Report
     */
    protected $report;

    /**
     * Constructor
     *
     * @param Report $report
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Finds matching element in given afterElements using xml attribute identifier
     *
     * @param array $beforeElement
     * @param array $afterElements
     * @param string $elementIdentifier
     * @return array
     */
    public function findMatchingElement($beforeElement, $afterElements, $elementIdentifier)
    {
        if (!isset($beforeElement['attributes'][$elementIdentifier])) {
            return null;
        }
        $beforeFieldKey = $beforeElement['attributes'][$elementIdentifier];
        foreach ($afterElements as $afterElement) {
            if (!isset($afterElement['attributes'][$elementIdentifier])) {
                continue;
            }
            if ($afterElement['attributes'][$elementIdentifier] === $beforeFieldKey) {
                return $afterElement;
            }
        }
        return null;
    }

    /**
     * Finds matching element in given afterElements using xml attribute identifier and value
     *
     * @param array $beforeElement
     * @param array $afterElements
     * @param string $elementIdentifier
     * @return array
     */
    public function findMatchingElementByKeyAndValue($beforeElement, $afterElements, $elementIdentifier)
    {
        $beforeFieldKey = $beforeElement['attributes'][$elementIdentifier];
        $beforeFieldValue = $beforeElement['value'];
        foreach ($afterElements as $afterElement) {
            if ($afterElement['attributes'][$elementIdentifier] === $beforeFieldKey
                    && $afterElement['value'] === $beforeFieldValue) {
                return $afterElement;
            }
        }
        return null;
    }

    /**
     * Matches and validates all attributes of two given xml elements, adding operations according to class passed in
     *
     * @param array $beforeAttributes
     * @param array $afterAttributes
     * @param Report $report
     * @param string $filenames
     * @param string $operationClass
     * @param string $fullOperationTarget
     * @return void
     */
    public function matchAndValidateAttributes(
        $beforeAttributes,
        $afterAttributes,
        $report,
        $filenames,
        $operationClass,
        $fullOperationTarget
    ) {
        foreach ($beforeAttributes as $key => $beforeAttribute) {
            $matchingAttribute = $afterAttributes[$key] ?? null;
            if ($beforeAttribute !== $matchingAttribute) {
                $operation = new $operationClass($filenames, "$fullOperationTarget/$key");
                $report->add(MftfReport::MFTF_REPORT_CONTEXT, $operation);
            }
        }
    }

    /**
     * Matches and validates element name
     *
     * @param array $beforeElement
     * @param array $afterElement
     * @param Report $report
     * @param string $filenames
     * @param string $operationClass
     * @param string $fullOperationTarget
     * @return void
     */
    public function matchAndValidateElementType(
        $beforeElement,
        $afterElement,
        $report,
        $filenames,
        $operationClass,
        $fullOperationTarget
    ) {
        if ($beforeElement['name'] !== $afterElement['name']) {
            $operation = new $operationClass($filenames, $fullOperationTarget);
            $report->add(MftfReport::MFTF_REPORT_CONTEXT, $operation);
        }
    }

    /**
     * Finds all added child elements in afterArray, compared to beforeArray
     *
     * @param array $beforeArray
     * @param array $afterArray
     * @param string $elementIdentifier
     * @param Report $report
     * @param string $filenames
     * @param string $operationClass
     * @param string $fullOperationTarget
     */
    public function findAddedElementsInArray(
        $beforeArray,
        $afterArray,
        $elementIdentifier,
        $report,
        $filenames,
        $operationClass,
        $fullOperationTarget
    ) {
        foreach ($afterArray as $newChild) {
            if (!isset($newChild['attributes'][$elementIdentifier])) {
                continue;
            }
            $beforeFieldKey = $newChild['attributes'][$elementIdentifier];
            $matchingElement = $this->findMatchingElement($newChild, $beforeArray, $elementIdentifier);
            if ($matchingElement === null) {
                $operation = new $operationClass($filenames, $fullOperationTarget . '/' . $beforeFieldKey);
                $report->add(MftfReport::MFTF_REPORT_CONTEXT, $operation);
            }
        }
    }

    /**
     * Finds all added child elements in afterArray, compared to beforeArray, using both key and value for matching
     *
     * @param array $beforeArray
     * @param array $afterArray
     * @param string $elementIdentifier
     * @param Report $report
     * @param string $filenames
     * @param string $operationClass
     * @param string $fullOperationTarget
     */
    public function findAddedElementsInArrayByValue(
        $beforeArray,
        $afterArray,
        $elementIdentifier,
        $report,
        $filenames,
        $operationClass,
        $fullOperationTarget
    ) {
        foreach ($afterArray as $newChild) {
            $beforeFieldKey = $newChild['attributes'][$elementIdentifier];
            $matchingElement = $this->findMatchingElementByKeyAndValue($newChild, $beforeArray, $elementIdentifier);
            if ($matchingElement === null) {
                $operation = new $operationClass($filenames, $fullOperationTarget . '/' . $beforeFieldKey);
                $report->add(MftfReport::MFTF_REPORT_CONTEXT, $operation);
            }
        }
    }

    /**
     * Finds all added entities in a module's entities array by type
     *
     * @param array $beforeArray
     * @param array $afterArray
     * @param string $entityType
     * @param Report $report
     * @param string $operationClass
     * @param string $fullOperationTarget
     * @return void
     */
    public function findAddedEntitiesInModule(
        $beforeArray,
        $afterArray,
        $entityType,
        $report,
        $operationClass,
        $fullOperationTarget
    ) {
        foreach ($afterArray as $newChild) {
            if (!isset($newChild['type']) || $newChild['type'] !== $entityType) {
                continue;
            }
            $beforeFieldKey = $newChild['attributes']['name'];
            $matchingElement = $this->findMatchingElement($newChild, $beforeArray, 'name');
            if ($matchingElement === null) {
                $filenames = implode(', ', $newChild['filePaths']);
                $operation = new $operationClass($filenames, $fullOperationTarget . '/' . $beforeFieldKey);
                $report->add(MftfReport::MFTF_REPORT_CONTEXT, $operation);
            }
        }
    }

    /**
     * Get report
     *
     * @return Report
     */
    protected function getReport(): Report
    {
        return $this->report;
    }
}
