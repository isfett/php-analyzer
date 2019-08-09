<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\ConditionList;

use Isfett\PhpAnalyzer\DAO\Condition;
use Isfett\PhpAnalyzer\DAO\CountedCondition;
use Isfett\PhpAnalyzer\Node\ConditionList\FlipChecking;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;

/**
 * Class FlipCheckingTest
 */
class FlipCheckingTest extends AbstractNodeTestCase
{
    /** @var FlipChecking */
    private $conditionList;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->conditionList = new FlipChecking();
    }

    /**
     * @return void
     */
    public function testAddCondition(): void
    {
        $this->assertCount(0, $this->conditionList->getConditions());

        $condition = new Condition('$x === $y', $this->createFakeOccurrence());
        $this->conditionList->addCondition($condition);
        $this->assertCount(1, $this->conditionList->getConditions());
        $this->assertEquals('$x === $y', $this->conditionList->getConditions()[0]->getCondition());


        $condition = new Condition('$x === $a', $this->createFakeOccurrence());
        $this->conditionList->addCondition($condition);
        $this->assertCount(2, $this->conditionList->getConditions());
        $this->assertEquals('$x === $y', $this->conditionList->getConditions()[0]->getCondition());
        $this->assertEquals('$x === $a', $this->conditionList->getConditions()[1]->getCondition());

        // this will get flipped
        $condition = new Condition('$y === $x', $this->createFakeOccurrence());
        $this->conditionList->addCondition($condition);
        $this->assertCount(3, $this->conditionList->getConditions());
        $this->assertEquals('$x === $y', $this->conditionList->getConditions()[0]->getCondition());
        $this->assertEquals('$x === $a', $this->conditionList->getConditions()[1]->getCondition());
        $this->assertEquals('$x === $y', $this->conditionList->getConditions()[2]->getCondition());
    }
}
