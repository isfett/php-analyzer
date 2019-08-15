<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Builder;

use Isfett\PhpAnalyzer\Builder\VisitorBuilder;
use Isfett\PhpAnalyzer\Exception\InvalidVisitorNameException;
use PHPUnit\Framework\TestCase;

/**
 * Class VisitorBuilderTest
 */
class VisitorBuilderTest extends TestCase
{
    /** @var VisitorBuilder */
    private $builder;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new VisitorBuilder();
    }

    /**
     * @return void
     */
    public function testBuilder(): void
    {
        $visitors = $this->builder
            ->setPrefix('Condition')
            ->setNames('If')
            ->getVisitors();

        $this->assertCount(1, $visitors);
    }

    /**
     * @return void
     */
    public function testBuilderMultiple(): void
    {
        $visitors = $this->builder
            ->setPrefix('Condition')
            ->setNames('If,Ternary')
            ->getVisitors();

        $this->assertCount(2, $visitors);
    }

    /**
     * @return void
     */
    public function testBuilderMultipleWithSpace(): void
    {
        $visitors = $this->builder
            ->setPrefix('Condition')
            ->setNames('If, Ternary')
            ->getVisitors();

        $this->assertCount(2, $visitors);
    }

    /**
     * @return void
     */
    public function testBuilderWillThrowExceptionIfInvalidName(): void
    {
        $this->expectException(InvalidVisitorNameException::class);
        $this->expectExceptionMessage(
            'Visitor with name ThisNameWillNeverExist does not exist. Possible Visitors are: '
        );

        $this->builder
            ->setPrefix('Condition')
            ->setNames('If, Ternary, ThisNameWillNeverExist')
            ->getVisitors();
    }

    /**
     * @return void
     */
    public function testBuilderWithEmptyName(): void
    {
        $visitors = $this->builder
            ->setPrefix('Condition')
            ->setNames('')
            ->getVisitors();

        $this->assertCount(0, $visitors);
    }
}
