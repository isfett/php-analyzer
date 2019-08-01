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
                if (in_array($flippedCondHash, $this->conditionHashes)) {
                    $condition->setCondition($flippedCond);
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
    private function flipCondition(string $condition, string $operator): string {
        return substr($condition, strpos($condition, $operator) + strlen($operator) + 1) .
        ' ' . $operator . ' ' .
        substr($condition, 0, strpos($condition, $operator) - 1);
    }
}
