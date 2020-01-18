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
    /** @var string */
    private const COMMA_WITH_SPACE = ', ';

    /** @var string */
    protected const EMPTY_STRING = '';

    /** @var string */
    protected const EQUAL_SIGN = '=';

    /** @var string */
    protected const NAMESPACE_SEPARATOR = '\\';

    /** @var string */
    protected const NEGATION_SIGN = '!';

    /** @var string */
    protected const REF_SIGN = '&';

    /** @var string */
    protected const SPACE = ' ';

    /** @var string */
    protected const UNDERSCORE = '_';

    /** @var string */
    protected const VARIABLE_SIGN = '$';

    /** @var string */
    protected const VARIADIC_SIGN = '...';

    /** @var Node */
    protected $node;

    /** @var NodeRepresentationService */
    protected $nodeRepresentationService;

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
     * @param array  $arguments
     * @param string $implodeBy
     *
     * @return string
     */
    protected function arguments(array $arguments, string $implodeBy = self::COMMA_WITH_SPACE): string
    {
        return implode($implodeBy, $this->nodeRepresentationService->representationForArguments($arguments));
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
}
