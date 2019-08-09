<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Builder;

use Isfett\PhpAnalyzer\DAO\ConditionList;
use Isfett\PhpAnalyzer\Node\ConditionList\FlipChecking;

/**
 * Class ConditionListBuilder
 */
class ConditionListBuilder implements ConditionListBuilderInterface
{
    /** @var bool */
    private $isFlipCheckingAware = false;

    /**
     * @return ConditionList
     */
    public function getConditionList(): ConditionList
    {
        if ($this->isFlipCheckingAware) {
            return new FlipChecking();
        }

        return new ConditionList();
    }

    /**
     * @param bool $flipCheckingAwareness
     *
     * @return ConditionListBuilderInterface
     */
    public function setIsFlipCheckingAware(bool $flipCheckingAwareness): ConditionListBuilderInterface
    {
        $this->isFlipCheckingAware = $flipCheckingAwareness;

        return $this;
    }
}
