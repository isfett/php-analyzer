<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Isfett\PhpAnalyzer\DAO\Configuration\Sort;
use Isfett\PhpAnalyzer\DAO\Configuration\SortField;
use Isfett\PhpAnalyzer\Service\SortService;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;

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
}