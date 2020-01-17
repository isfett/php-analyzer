<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\DAO\OccurrenceList;
use PhpParser\Node\Expr;

/**
 * Class AbstractProcessor
 */
abstract class AbstractProcessor implements Processor\ProcessorInterface
{
    /** @var string */
    private const BINARY_OP_LEFT = 'left';

    /** @var string */
    private const BINARY_OP_RIGHT = 'right';

    /** @var array */
    protected const BINARY_OP_SIDES = [self::BINARY_OP_LEFT, self::BINARY_OP_RIGHT];

    /** @var string */
    protected const EMPTY_STRING = '';

    /** @var string */
    protected const NAMESPACE_SEPARATOR = '\\';

    /** @var string */
    protected const NODE_ATTRIBUTE_PARENT = 'parent';

    /** @var string */
    protected const NODE_PROPERTY_NAME = 'name';

    /** @var string */
    private const REPLACE_SEARCH = 'Processor';

    /** @var OccurrenceList */
    protected $nodeOccurrenceList;

    /**
     * @param OccurrenceList $nodeOccurrenceList
     */
    public function setNodeOccurrenceList(OccurrenceList $nodeOccurrenceList): void
    {
        $this->nodeOccurrenceList = $nodeOccurrenceList;
    }

    /**
     * @param Occurrence $occurrence
     *
     * @return void
     */
    protected function markOccurrenceAsAffected(Occurrence $occurrence): void
    {
        $occurrence->addAffectedByProcessor($this->getProcessorName(static::class));
    }

    /**
     * @param Expr $node
     *
     * @return Expr\BooleanNot
     */
    protected function negate(Expr $node): Expr\BooleanNot
    {
        return new Expr\BooleanNot($node, $node->getAttributes());
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
    private function getProcessorName(string $classname): string
    {
        return str_replace(self::REPLACE_SEARCH, self::EMPTY_STRING, $this->getClassnameWithoutNamespace($classname));
    }
}
