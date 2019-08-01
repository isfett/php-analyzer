<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Builder;

use Isfett\PhpAnalyzer\Builder\ConditionListBuilder;
use Isfett\PhpAnalyzer\DAO\ConditionList;
use Isfett\PhpAnalyzer\DAO\FlipCheckingConditionList;
use PHPUnit\Framework\TestCase;

/**
 * Class ConditionListBuilderTest
 */
class ConditionListBuilderTest extends TestCase
{
    /** @var ConditionListBuilder */
    private $builder;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new ConditionListBuilder();
    }

    /**
     * @return void
     */
    public function testGetConditionList(): void
    {
        $conditionList = $this->builder
            ->setIsFlipCheckingAware(false)
            ->getConditionList();

        $this->assertInstanceOf(ConditionList::class, $conditionList);
    }

    /**
     * @return void
     */
    public function testGetFlipCheckingConditionList(): void
    {
        $conditionList = $this->builder
            ->setIsFlipCheckingAware(true)
            ->getConditionList();

        $this->assertInstanceOf(FlipCheckingConditionList::class, $conditionList);
    }
}
