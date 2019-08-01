<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\DAO;

/**
 * Class ConditionList
 */
class ConditionList
{
    /**
     * @var array<Condition>
     */
    protected $conditions = [];

    /**
     * @return array<Condition>
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * @param Condition $condition
     *
     * @return void
     */
    public function addCondition(Condition $condition): void
    {
        $this->conditions[] = $condition;
    }
}
