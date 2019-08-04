<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\ConditionList;

use Isfett\PhpAnalyzer\DAO\Condition;
use Isfett\PhpAnalyzer\DAO\CountedCondition;
use Isfett\PhpAnalyzer\Node\ConditionList\Countable;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;

/**
 * Class CountableTest
 */
class CountableTest extends AbstractNodeTestCase
{
    /** @var Countable */
    private $conditionList;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->conditionList = new Countable();
    }

    /**
     * @return void
     */
    public function testAddCondition(): void
    {
        $this->assertCount(0, $this->conditionList->getCountedConditions());

        $condition = new Condition('$x === $y', $this->createFakeOccurrence());
        $this->conditionList->addCondition($condition);
        $this->assertCount(1, $this->conditionList->getCountedConditions());
        $this->assertCount(1, $this->conditionList->getCountedConditions()->first()->getOccurrences());

        $condition = new Condition('$x === $y', $this->createFakeOccurrence());
        $this->conditionList->addCondition($condition);
        $this->assertCount(1, $this->conditionList->getCountedConditions());
        $this->assertCount(2, $this->conditionList->getCountedConditions()->first()->getOccurrences());

        $condition = new Condition('$a === $b', $this->createFakeOccurrence());
        $this->conditionList->addCondition($condition);
        $this->assertCount(2, $this->conditionList->getCountedConditions());
        foreach ($this->conditionList->getCountedConditions() as $countedCondition) {
            if ('$x === $y' === $countedCondition->getCondition()) {
                $this->assertCount(2, $countedCondition->getOccurrences());
            } else {
                $this->assertCount(1, $countedCondition->getOccurrences());
            }
        }
    }
}
