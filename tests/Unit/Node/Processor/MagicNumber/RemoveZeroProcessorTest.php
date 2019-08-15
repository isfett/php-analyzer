<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Processor\MagicNumber;

use Isfett\PhpAnalyzer\DAO\OccurrenceList;
use Isfett\PhpAnalyzer\Node\Processor\MagicNumber\RemoveZeroProcessor;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;

/**
 * Class RemoveZeroProcessorTest
 */
class RemoveZeroProcessorTest extends AbstractNodeTestCase
{
    /** @var RemoveZeroProcessor */
    private $processor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->processor = new RemoveZeroProcessor();
    }

    /**
     * @return void
     */
    public function testProcessOnInt(): void
    {
        $node = new LNumber(0);

        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new OccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
        $this->assertEmpty($occurrence->getAffectedByProcessors());

        $this->processor->process($occurrence);

        $this->assertCount(0, $nodeOccurrenceList->getOccurrences());
    }

    /**
     * @return void
     */
    public function testProcessOnDecimal(): void
    {
        $node = new DNumber(0.00);

        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new OccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
        $this->assertEmpty($occurrence->getAffectedByProcessors());

        $this->processor->process($occurrence);

        $this->assertCount(0, $nodeOccurrenceList->getOccurrences());
    }

    /**
     * @return void
     */
    public function testProcessOnIntGreaterZeroWillNotRemoveOccurrence(): void
    {
        $node = new LNumber(1);

        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new OccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
        $this->assertEmpty($occurrence->getAffectedByProcessors());

        $this->processor->process($occurrence);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
    }

    /**
     * @return void
     */
    public function testProcessOnDecimalGreaterZeroWillNotRemoveOccurrence(): void
    {
        $node = new DNumber(0.05);

        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new OccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
        $this->assertEmpty($occurrence->getAffectedByProcessors());

        $this->processor->process($occurrence);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
    }
}
