<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation;

use Isfett\PhpAnalyzer\Node\Representation;
use PhpParser\Node;

/**
 * Class AbstractRepresentation
 */
abstract class AbstractRepresentation implements RepresentationInterface
{
    /** @var Representation */
    protected $representation;

    /** @var Node */
    protected $node;

    /**
     * AbstractRepresentation constructor.
     *
     * @param Representation $representation
     * @param Node           $node
     */
    public function __construct(Representation $representation, Node $node)
    {
        $this->representation = $representation;
        $this->node = $node;
    }

    /**
     * @param Node $node
     *
     * @return string|null
     */
    protected function representation(Node $node): ?string
    {
        return $this->representation->getRepresentationForNode($node);
    }

    /**
     * @param array  $arguments
     *
     * @param string $implodeBy
     *
     * @return string
     */
    protected function arguments(array $arguments, string $implodeBy = ', '): string
    {
        return implode($implodeBy, $this->representation->getArguments($arguments));
    }
}
