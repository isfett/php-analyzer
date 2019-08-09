<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use PhpParser\Node\Expr\Variable;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class VisitorTest
 */
class AbstractVisitorTest extends AbstractNodeTestCase
{
    /**
     * @return void
     */
    public function testAddNodeOccurrence(): void
    {
        // phpcs:disable Symfony.Objects.ObjectInstantiation.Invalid
        $visitor = new class () extends AbstractVisitor {
            /**
             * @return void
             */
            public function add(): void
            {
                $this->addNodeOccurrence(new Variable('test'));
            }
        };
        // phpcs:enable

        $this->assertCount(0, $visitor->getNodeOccurrenceList()->getOccurrences());

        /** @var MockObject|SplFileInfo $file */
        $file = $this->createSplFileInfoMock();
        $visitor->setFile($file);

        $visitor->add();

        $this->assertCount(1, $visitor->getNodeOccurrenceList()->getOccurrences());

        /** @var Occurrence $occurrence */
        $occurrence = $visitor->getNodeOccurrenceList()->getOccurrences()->first();
        $this->assertEquals($file, $occurrence->getFile());
        $this->assertEquals(new Variable('test'), $occurrence->getNode());
    }
}
