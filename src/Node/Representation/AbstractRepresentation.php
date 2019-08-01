<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation;

use Isfett\PhpAnalyzer\Service\NodeRepresentationService;
use PhpParser\Node;

/**
 * Class AbstractRepresentation
 */
abstract class AbstractRepresentation implements RepresentationInterface
{
    /** @var NodeRepresentationService */
    protected $nodeRepresentationService;

    /** @var Node */
    protected $node;

    /**
     * AbstractRepresentation constructor.
     *
     * @param NodeRepresentationService $nodeRepresentationService
     * @param Node                      $node
     */
    public function __construct(NodeRepresentationService $nodeRepresentationService, Node $node)
    {
        $this->nodeRepresentationService = $nodeRepresentationService;
        $this->node = $node;
    }

    /**
     * @param Node $node
     *
     * @return string|null
     */
    protected function representate(Node $node): ?string
    {
        return $this->nodeRepresentationService->representationForNode($node);
    }

    /**
     * @param array  $arguments
     * @param string $implodeBy
     *
     * @return string
     */
    protected function arguments(array $arguments, string $implodeBy = ', '): string
    {
        return implode($implodeBy, $this->nodeRepresentationService->representationForArguments($arguments));
    }
}
