<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\ConditionList;

use Isfett\PhpAnalyzer\DAO\Condition;
use Isfett\PhpAnalyzer\DAO\ConditionList;
use Isfett\PhpAnalyzer\Node\Representation\Expr\BinaryOp;

/**
 * Class FlipChecking
 */
class FlipChecking extends ConditionList
{
    private const FLIP_OPERATORS = [
        BinaryOp::OPERATOR_SIGN_IDENTICAL,
        BinaryOp::OPERATOR_SIGN_NOT_IDENTICAL,
        BinaryOp::OPERATOR_SIGN_EQUAL,
        BinaryOp::OPERATOR_SIGN_NOT_EQUAL,
    ];

    /** @var array */
    private $conditionHashes = [];

    /**
     * @param Condition $condition
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

        parent::addCondition($condition);
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
