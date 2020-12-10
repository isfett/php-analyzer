<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\DAO\OccurrenceList;
use Isfett\PhpAnalyzer\Node\Visitor\VisitorInterface;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class AbstractVisitor
 */
abstract class AbstractVisitor extends NodeVisitorAbstract implements VisitorInterface
{
    /** @var string */
    protected const EMPTY_STRING = '';

    /** @var string */
    protected const NAMESPACE_SEPARATOR = '\\';

    /** @var string */
    protected const NODE_ATTRIBUTE_PARENT = 'parent';

    /** @var string */
    private const REPLACE_SEARCH = 'Visitor';

    /** @var SplFileInfo */
    protected $file;

    /** @var OccurrenceList */
    private $nodeOccurrenceList;

    /**
     * Visitor constructor.
     */
    public function __construct()
    {
        $this->nodeOccurrenceList = new OccurrenceList();
    }

    /**
     * @return OccurrenceList
     */
    public function getNodeOccurrenceList(): OccurrenceList
    {
        return $this->nodeOccurrenceList;
    }

    /**
     * @param SplFileInfo $file
     */
    public function setFile(SplFileInfo $file): void
    {
        $this->file = $file;
    }

    /**
     * @param Node $node
     *
     * @return void
     */
    protected function addNodeOccurrence(Node $node): void
    {
        $node = $this->replaceUnaryNumbers($node);
        $occurrence = new Occurrence($node, $this->file);
        $occurrence->setFoundByVisitor($this->getVisitorName(static::class));
        $this->nodeOccurrenceList->addOccurrence($occurrence);
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    protected function isNumber(Node $node): bool
    {
        if ($node instanceof Node\Expr\UnaryMinus || $node instanceof Node\Expr\UnaryPlus) {
            $node = $node->expr;
        }

        return $node instanceof Node\Scalar\LNumber || $node instanceof Node\Scalar\DNumber;
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    protected function isString(Node $node): bool
    {
        return $node instanceof Node\Scalar\String_;
    }

    /**
     * @param Node $node
     *
     * @return Node
     */
    protected function replaceUnaryNumbers(Node $node): Node
    {
        if ($node instanceof Node\Expr\UnaryPlus || $node instanceof Node\Expr\UnaryMinus) {
            $node = $node->expr;
        }

        return $node;
    }

    /**
     * @param string $classname
     *
     * @return string
     */
    private function getClassnameWithoutNamespace(string $classname): string
    {
        $classWithNamespaces = explode(self::NAMESPACE_SEPARATOR, $classname);

        return end($classWithNamespaces);
    }

    /**
     * @param string $classname
     *
     * @return string
     */
    private function getVisitorName(string $classname): string
    {
        return str_replace(self::REPLACE_SEARCH, self::EMPTY_STRING, $this->getClassnameWithoutNamespace($classname));
    }
}
