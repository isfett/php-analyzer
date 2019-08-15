<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Isfett\PhpAnalyzer\DAO\Configuration\Sort;
use Isfett\PhpAnalyzer\DAO\Configuration\SortField;
use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Service\SortService;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node;

/**
 * Class SortServiceTest
 */
class SortServiceTest extends AbstractNodeTestCase
{
    /** @var SortService */
    private $sortService;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->sortService = new SortService();
    }

    /**
     * @return void
     */
    public function testSortArrayCollection(): void
    {
        $collection = new ArrayCollection([
            0 => [
                'count' => 5,
            ],
            1 => [
                'count' => 77,
            ],
            2 => [
                'count' => 3,
            ],
        ]);

        $sortConfiguration = new Sort(new ArrayCollection([
            new SortField('count', 'DESC'),
        ]));

        $sortedCollection = $this->sortService->sortArrayCollection($collection, $sortConfiguration);
        $this->assertEquals([
            1 => [
                'count' => 77,
            ],
            0 => [
                'count' => 5,
            ],
            2 => [
                'count' => 3,
            ],
        ], $sortedCollection->toArray());
    }

    /**
     * @return void
     */
    public function testSortArrayCollectionWithMultipleFields(): void
    {
        $collection = new ArrayCollection([
            0 => [
                'count' => 100,
                'name' => 'foo',
            ],
            1 => [
                'count' => 100,
                'name' => 'bar',
            ],
            2 => [
                'count' => 100,
                'name' => 'apple',
            ],
        ]);

        $sortConfiguration = new Sort(new ArrayCollection([
            new SortField('count', 'DESC'),
            new SortField('name', 'ASC'),
        ]));

        $sortedCollection = $this->sortService->sortArrayCollection($collection, $sortConfiguration);
        $this->assertEquals([
            2 => [
                'count' => 100,
                'name' => 'apple',
            ],
            1 => [
                'count' => 100,
                'name' => 'bar',
            ],
            0 => [
                'count' => 100,
                'name' => 'foo',
            ],
        ], $sortedCollection->toArray());
    }

    /**
     * @return void
     */
    public function testSortArrayCollectionWithFirstResult(): void
    {
        $collection = new ArrayCollection([
            0 => [
                'count' => 5,
            ],
            1 => [
                'count' => 77,
            ],
            2 => [
                'count' => 3,
            ],
        ]);

        $sortConfiguration = new Sort(new ArrayCollection([
            new SortField('count', 'DESC'),
        ]), 1);

        $sortedCollection = $this->sortService->sortArrayCollection($collection, $sortConfiguration);
        $this->assertEquals([
            0 => [
                'count' => 5,
            ],
            1 => [
                'count' => 3,
            ],
        ], $sortedCollection->toArray());
    }

    /**
     * @return void
     */
    public function testSortArrayCollectionWithMaximumResults(): void
    {
        $collection = new ArrayCollection([
            0 => [
                'count' => 5,
            ],
            1 => [
                'count' => 77,
            ],
            2 => [
                'count' => 3,
            ],
        ]);

        $sortConfiguration = new Sort(new ArrayCollection([
            new SortField('count', 'DESC'),
        ]), null, 2);

        $sortedCollection = $this->sortService->sortArrayCollection($collection, $sortConfiguration);
        $this->assertEquals([
            0 => [
                'count' => 77,
            ],
            1 => [
                'count' => 5,
            ],
        ], $sortedCollection->toArray());
    }

    /**
     * @return void
     */
    public function testSortByNodeValueASC(): void
    {
        $sortConfiguration = new Sort(new ArrayCollection([
            new SortField('value', 'ASC'),
        ]), 2, 3);

        $nodes = new ArrayCollection([
            $this->createOccurrence(new Node\Scalar\LNumber(13)),
            $this->createOccurrence(new Node\Scalar\LNumber(15)),
            $this->createOccurrence(new Node\Scalar\LNumber(11)),
            $this->createOccurrence(new Node\Scalar\LNumber(5)),
            $this->createOccurrence(new Node\Scalar\LNumber(2)),
            $this->createOccurrence(new Node\Scalar\LNumber(99)),
            $this->createOccurrence(new Node\Scalar\LNumber(77)),
        ]);

        $sortedNodes = $this->sortService->sortOccurrenceCollectionByNodeValues($nodes, $sortConfiguration);

        $expectedValues = [5, 11, 13];
        $this->assertCount(3, $sortedNodes);
        /** @var Occurrence $occurrence */
        foreach ($sortedNodes as $key => $occurrence) {
            $node = $occurrence->getNode();
            $this->assertEquals($expectedValues[$key], $node->value);
        }
    }

    /**
     * @return void
     */
    public function testSortByNodeValueDESC(): void
    {
        $sortConfiguration = new Sort(new ArrayCollection([
            new SortField('value', 'DESC'),
        ]), 2, 2);

        $nodes = new ArrayCollection([
            $this->createOccurrence(new Node\Scalar\LNumber(13)),
            $this->createOccurrence(new Node\Scalar\LNumber(15)),
            $this->createOccurrence(new Node\Scalar\LNumber(11)),
            $this->createOccurrence(new Node\Scalar\LNumber(5)),
            $this->createOccurrence(new Node\Scalar\LNumber(2)),
            $this->createOccurrence(new Node\Scalar\LNumber(99)),
            $this->createOccurrence(new Node\Scalar\LNumber(77)),
            $this->createOccurrence(new Node\Scalar\LNumber(77)),
        ]);

        $sortedNodes = $this->sortService->sortOccurrenceCollectionByNodeValues($nodes, $sortConfiguration);

        $expectedValues = [77, 77];
        $this->assertCount(2, $sortedNodes);
        /** @var Occurrence $occurrence */
        foreach ($sortedNodes as $key => $occurrence) {
            $node = $occurrence->getNode();
            $this->assertEquals($expectedValues[$key], $node->value);
        }
    }
}
