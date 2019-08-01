<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Builder;

use Isfett\PhpAnalyzer\Builder\ProcessorBuilder;
use Isfett\PhpAnalyzer\Exception\InvalidProcessorNameException;
use PHPUnit\Framework\TestCase;

/**
 * Class ProcessorBuilderTest
 */
class ProcessorBuilderTest extends TestCase
{
    /** @var ProcessorBuilder */
    private $builder;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new ProcessorBuilder();
    }

    /**
     * @return void
     */
    public function testBuilder(): void
    {
        $processors = $this->builder
            ->setNames('SplitIsset')
            ->getProcessors();

        $this->assertCount(1, $processors);
    }

    /**
     * @return void
     */
    public function testBuilderMultiple(): void
    {
        $visitors = $this->builder
            ->setNames('SplitIsset,NegateBooleanNot')
            ->getProcessors();

        $this->assertCount(2, $visitors);
    }

    /**
     * @return void
     */
    public function testBuilderMultipleWithSpace(): void
    {
        $visitors = $this->builder
            ->setNames('SplitIsset, NegateBooleanNot')
            ->getProcessors();

        $this->assertCount(2, $visitors);
    }

    /**
     * @return void
     */
    public function testBuilderWillThrowExceptionIfInvalidName(): void
    {
        $this->expectException(InvalidProcessorNameException::class);
        $this->expectExceptionMessage(
            'Processor with name ThisNameWillNeverExist does not exist. Possible processor-names are: '
        );

        $this->builder
            ->setNames('SplitIsset, NegateBooleanNot, ThisNameWillNeverExist')
            ->getProcessors();
    }

    /**
     * @return void
     */
    public function testBuilderWithEmptyName(): void
    {
        $visitors = $this->builder
            ->setNames('')
            ->getProcessors();

        $this->assertCount(0, $visitors);
    }
}
