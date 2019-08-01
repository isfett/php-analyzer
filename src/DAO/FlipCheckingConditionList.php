<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\DAO;

/**
 * Class FlipCheckingConditionList
 */
class FlipCheckingConditionList extends ConditionList
{
    private const FLIP_OPERATORS = [
        '===',
        '!==',
        '==',
        '!=',
        '>=',
        '<=',
        '>',
        '<',
    ];

    /** @var array */
    private $conditionHashes = [];

    /**
     * @param string $condition
     *
     * @return void
     */
    public function addCondition(Condition $condition): void
    {
        foreach (self::FLIP_OPERATORS as $operator) {
            if (false !== strpos($condition->getCondition(), ' '.$operator.' ')) {
                $flippedCond = $this->flipCondition($condition->getCondition(), $operator);
                $flippedCondHash = md5($flippedCond);
                if (in_array($flippedCondHash, $this->conditionHashes, true)) {
                    $condition->setCondition($flippedCond);
                    $condition->getOccurrence()->setIsFlipped(true);
                    break;
                }
            }
        }

        $this->conditionHashes[] = md5($condition->getCondition());
        $this->conditions[] = $condition;
    }

    /**
     * @param string $condition
     * @param string $operator
     *
     * @return string
     */
    private function flipCondition(string $condition, string $operator): string
    {
        return sprintf(
            '%s %s %s',
            substr($condition, strpos($condition, $operator) + strlen($operator) + 1),
            $operator,
            substr($condition, 0, strpos($condition, $operator) - 1)
        );
    }
}
