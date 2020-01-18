<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Builder;

use Isfett\PhpAnalyzer\Builder\SortConfigurationBuilder;
use Isfett\PhpAnalyzer\DAO\Configuration\Sort;
use Isfett\PhpAnalyzer\DAO\Configuration\SortField;
use Isfett\PhpAnalyzer\Exception\InvalidSortArgumentException;
use Isfett\PhpAnalyzer\Exception\InvalidSortConfigurationException;
use PHPUnit\Framework\TestCase;

/**
 * Class SortConfigurationBuilderTest
 */
class SortConfigurationBuilderTest extends TestCase
{
    /** @var SortConfigurationBuilder */
    private $builder;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new SortConfigurationBuilder();
    }

    /**
     * @return void
     */
    public function testGetSortConfigurationWithOneField(): void
    {
        $sortConfiguration = $this->builder
            ->addSortField('count', 'desc')
            ->getSortConfiguration();

        $this->assertInstanceOf(Sort::class, $sortConfiguration);
        $this->assertNull($sortConfiguration->getFirstResult());
        $this->assertNull($sortConfiguration->getMaxResults());
        $this->assertCount(1, $sortConfiguration->getFields());
        $this->assertInstanceOf(SortField::class, $sortConfiguration->getFields()->first());
        $this->assertSame('count', $sortConfiguration->getFields()->first()->getField());
        $this->assertSame('DESC', $sortConfiguration->getFields()->first()->getDirection());
    }

    /**
     * @return void
     */
    public function testGetSortConfigurationWithTwoFields(): void
    {
        $sortConfiguration = $this->builder
            ->addSortField('count', 'desc')
            ->addSortField('name', 'asc')
            ->getSortConfiguration();

        $this->assertInstanceOf(Sort::class, $sortConfiguration);
        $this->assertNull($sortConfiguration->getFirstResult());
        $this->assertNull($sortConfiguration->getMaxResults());
        $this->assertCount(2, $sortConfiguration->getFields());

        /** @var SortField $field */
        foreach ($sortConfiguration->getFields() as $key => $field) {
            $this->assertInstanceOf(SortField::class, $field);
            if (0 === $key) {
                $this->assertSame('count', $field->getField());
                $this->assertSame('DESC', $field->getDirection());
            } elseif (1 === $key) {
                $this->assertSame('name', $field->getField());
                $this->assertSame('ASC', $field->getDirection());
            }
        }
    }

    /**
     * @return void
     */
    public function testGetSortConfigurationWithOneFieldAndMaximumEntries(): void
    {
        $sortConfiguration = $this->builder
            ->setMaxResults(10)
            ->addSortField('count', 'desc')
            ->getSortConfiguration();

        $this->assertInstanceOf(Sort::class, $sortConfiguration);
        $this->assertSame(10, $sortConfiguration->getMaxResults());
        $this->assertNull($sortConfiguration->getFirstResult());
    }

    /**
     * @return void
     */
    public function testGetSortConfigurationWithOneFieldAndFirstResult(): void
    {
        $sortConfiguration = $this->builder
            ->setFirstResult(10)
            ->addSortField('count', 'desc')
            ->getSortConfiguration();

        $this->assertInstanceOf(Sort::class, $sortConfiguration);
        $this->assertSame(10, $sortConfiguration->getFirstResult());
        $this->assertNull($sortConfiguration->getMaxResults());
    }

    /**
     * @return void
     */
    public function testGetSortConfigurationWithOneFieldAndFirstResultAndMaximumEntries(): void
    {
        $sortConfiguration = $this->builder
            ->setFirstResult(10)
            ->setMaxResults(10)
            ->addSortField('count', 'desc')
            ->getSortConfiguration();

        $this->assertInstanceOf(Sort::class, $sortConfiguration);
        $this->assertSame(10, $sortConfiguration->getFirstResult());
        $this->assertSame(10, $sortConfiguration->getMaxResults());
    }

    /**
     * @return void
     */
    public function testGetSortConfigurationWillThrowAnExceptionWhenTheSortDirectionOfOneFieldIsInvalid(): void
    {
        $this->expectException(InvalidSortArgumentException::class);
        $this->expectExceptionMessage("Sort direction with 'foo' is invalid, use 'asc' or 'desc' instead");

        $this->builder
            ->addSortField('count', 'foo')
            ->getSortConfiguration();
    }

    /**
     * @return void
     */
    public function testGetSortConfigurationWillThrowAnExceptionNoSortFieldsAreDeclared(): void
    {
        $this->expectException(InvalidSortConfigurationException::class);
        $this->expectExceptionMessage('You need to add at least one sort field');

        $this->builder
            ->setMaxResults(10)
            ->getSortConfiguration();
    }
}
