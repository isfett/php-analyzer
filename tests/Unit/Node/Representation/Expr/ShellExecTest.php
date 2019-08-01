<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\ShellExec;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class ShellExecTest
 */
class ShellExecTest extends AbstractNodeRepresentationTest
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function testGetRepresentation(): void
    {
        $node = new Node\Expr\ShellExec(
            [
                $this->createVariableNode('command'),
            ],
            $this->getNodeAttributes()
        );

        $this->representation
            ->method('getArguments')
            ->willReturn(['$command']);

        $representation = new ShellExec($this->representation, $node);

        $this->assertEquals('shell_exec($command)', $representation->getRepresentation());
    }
}
