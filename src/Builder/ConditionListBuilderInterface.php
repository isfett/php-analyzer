<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Builder;

use Isfett\PhpAnalyzer\DAO\ConditionList;

/**
 * Interface ConditionListBuilderInterface
 */
interface ConditionListBuilderInterface
{
    /**
     * @return ConditionList
     */
    public function getConditionList(): ConditionList;

    /**
     * @param bool $flipCheckingAwareness
     *
     * @return self
     */
    public function setIsFlipCheckingAware(bool $flipCheckingAwareness): self;
}
