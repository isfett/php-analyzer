<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Service;

use Isfett\PhpAnalyzer\Exception\NodeRepresentationClassDoesNotExistException;
use Isfett\PhpAnalyzer\Node\Representation\RepresentationInterface;
use PhpParser\Node;

/**
 * Class NodeRepresentationService
 */
class NodeRepresentationService
{
    /** @var string */
    private const FORMAT_NAMESPACE = '%s\\%s';

    /** @var string */
    private const NAMESPACE_NODE = 'Node';

    /** @var string */
    private const NAMESPACE_NODE_REPRESENTATION = 'Isfett\\PhpAnalyzer\\Node\\Representation';

    /** @var string */
    private const NAMESPACE_PHP_PARSER = 'PhpParser';

    /** @var string */
    private const NAMESPACE_SEPARATOR = '\\';

    /**
     * @param array $arguments
     *
     * @return array
     */
    public function representationForArguments(array $arguments): array
    {
        if (!count($arguments)) {
            return [];
        }

        $representedArguments = [];

        foreach ($arguments as $key => $argument) {
            $representedArguments[$key] = $this->representationForNode($argument);
        }

        return $representedArguments;
    }

    /**
     * @param Node $node
     *
     * @return string|null
     * @throws NodeRepresentationClassDoesNotExistException
     */
    public function representationForNode(Node $node): ?string
    {
        $representationClassname = $this->transformClassname(get_class($node));
        if (!class_exists($representationClassname)) {
            $representationClassname = $this->getAbstractClassname($representationClassname);
        }

        if (!class_exists($representationClassname)) {
            throw new NodeRepresentationClassDoesNotExistException($node);
        }

        /** @var RepresentationInterface $representationClass */
        $representationClass = new $representationClassname($this, $node);

        return $representationClass->representation();
    }

    /**
     * @param string $originalClassname
     *
     * @return string
     */
    private function getAbstractClassname(string $originalClassname): string
    {
        $classnameWithNamespaces = explode(self::NAMESPACE_SEPARATOR, $originalClassname);

        array_pop($classnameWithNamespaces);

        return implode(self::NAMESPACE_SEPARATOR, $classnameWithNamespaces);
    }

    /**
     * @param string $classname
     *
     * @return string
     */
    private function transformClassname(string $classname): string
    {
        $classWithNamespaces = explode(self::NAMESPACE_SEPARATOR, $classname);

        $keyPhpParser = array_search(self::NAMESPACE_PHP_PARSER, $classWithNamespaces, true);
        unset($classWithNamespaces[$keyPhpParser]);

        $keyNode = array_search(self::NAMESPACE_NODE, $classWithNamespaces, true);
        unset($classWithNamespaces[$keyNode]);

        return sprintf(
            self::FORMAT_NAMESPACE,
            self::NAMESPACE_NODE_REPRESENTATION,
            implode(self::NAMESPACE_SEPARATOR, $classWithNamespaces)
        );
    }
}
