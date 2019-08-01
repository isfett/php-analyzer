<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node;

use Isfett\PhpAnalyzer\Exception\NodeRepresentationClassDoesNotExistException;
use Isfett\PhpAnalyzer\Node\Representation;
use PhpParser\Node\Stmt\TraitUseAdaptation;
use PhpParser\Node\Expr\Cast\Int_;
use PhpParser\Node\Stmt;

/**
 * Class RepresentationTest
 */
class RepresentationTest extends AbstractNodeTestCase
{
    /** @var Representation */
    private $representation;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->representation = new Representation();
    }

    /**
     * @return void
     */
    public function testGetRepresentationForNode(): void
    {
        $node = $this->createNameNode('test');

        $this->assertEquals('test', $this->representation->getRepresentationForNode($node));
    }

    /**
     * @return void
     */
    public function testGetAbstractRepresentationForNode(): void
    {
        $node = new Int_($this->createVariableNode('i'), $this->getNodeAttributes());

        $this->assertEquals('(int) $i', $this->representation->getRepresentationForNode($node));
    }

    /**
     * @return void
     */
    public function testGetRepresentationForNodeWillThrowExceptionWhenThereIsNoRepresentationClass(): void
    {
        $node = new Stmt\Trait_($this->createIdentifierNode('test'));

        $this->expectException(NodeRepresentationClassDoesNotExistException::class);
        $this->expectExceptionMessage('No Representation for node PhpParser\Node\Stmt\Trait_ found');

        $this->representation->getRepresentationForNode($node);
    }

    /**
     * @return void
     */
    public function testGetArgumentsEmpty(): void
    {
        $this->assertCount(0, $this->representation->getArguments([]));
    }

    /**
     * @return void
     */
    public function testGetArgumentsSingleArgument(): void
    {
        $arguments = [
            $this->createVariableNode('test'),
        ];

        $transformedArguments = $this->representation->getArguments($arguments);

        $this->assertCount(1, $transformedArguments);
        $this->assertEquals('$test', $transformedArguments[0]);
    }

    /**
     * @return void
     */
    public function testGetArgumentsMultipleArguments(): void
    {
        $arguments = [
            $this->createVariableNode('test'),
            $this->createVariableNode('test2'),
        ];

        $transformedArguments = $this->representation->getArguments($arguments);

        $this->assertCount(2, $transformedArguments);
        $this->assertEquals('$test', $transformedArguments[0]);
        $this->assertEquals('$test2', $transformedArguments[1]);
    }
}