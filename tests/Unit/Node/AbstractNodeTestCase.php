<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use PhpParser\Node;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class NodeTestCase
 */
abstract class AbstractNodeTestCase extends TestCase
{
    /**
     * @param Node $node
     *
     * @return Occurrence
     */
    protected function createOccurrence(Node $node): Occurrence
    {
        /** @var SplFileInfo|MockObject $splFileInfo */
        $splFileInfo = $this->createSplFileInfoMock();

        return new Occurrence($node, $splFileInfo);
    }

    /**
     * @return Occurrence
     */
    protected function createFakeOccurrence(): Occurrence
    {
        /** @var SplFileInfo|MockObject $splFileInfo */
        $splFileInfo = $this->createSplFileInfoMock();

        $node = $this->createMock(Node::class);

        return new Occurrence($node, $splFileInfo);
    }

    /**
     * @param string $name
     *
     * @return Node\Expr\Variable
     */
    protected function createVariableNode(string $name = 'mockedVariableName'): Node\Expr\Variable
    {
        return new Node\Expr\Variable($name, $this->getNodeAttributes());
    }

    /**
     * @param string $value
     *
     * @return Node\Scalar\String_
     */
    protected function createScalarStringNode(string $value): Node\Scalar\String_
    {
        return new Node\Scalar\String_($value, $this->getNodeAttributes());
    }

    /**
     * @param string $name
     *
     * @return Node\Name
     */
    protected function createNameNode(string $name = 'mockedName'): Node\Name
    {
        return new Node\Name($name, $this->getNodeAttributes());
    }

    /**
     * @param string $name
     *
     * @return Node\Identifier
     */
    protected function createIdentifierNode(string $name = 'mockedName'): Node\Identifier
    {
        return new Node\Identifier($name, $this->getNodeAttributes());
    }

    /**
     * @param Node\Expr $node
     * @param bool      $byRef
     * @param bool      $unpack
     *
     * @return Node\Arg
     */
    protected function createArgNode(Node\Expr $node, $byRef = false, $unpack = false): Node\Arg
    {
        return new Node\Arg($node, $byRef, $unpack, $this->getNodeAttributes());
    }

    /**
     * @return MockObject
     */
    protected function createSplFileInfoMock(): MockObject
    {
        return $this->createMock(SplFileInfo::class);
    }

    /**
     * @param int $startLine
     * @param int $endLine
     *
     * @return array
     */
    protected function getNodeAttributes(int $startLine = 1, int $endLine = 1): array
    {
        return [
            'startLine' => $startLine,
            'endLine' => $endLine,
        ];
    }
}
