<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node;

use Isfett\PhpAnalyzer\Exception\NodeRepresentationClassDoesNotExistException;
use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;
use PhpParser\Node;

/**
 * Class Representation
 */
class Representation
{
    /**
     * @param Node $node
     *
     * @return string|null
     * @throws NodeRepresentationClassDoesNotExistException
     */
    public function getRepresentationForNode(Node $node): ?string
    {
        $representationClassname = $this->transformClassname(get_class($node));
        if (!class_exists($representationClassname)) {
            $representationClassname = $this->getAbstractClassname($representationClassname);
        }

        if (!class_exists($representationClassname)) {
            throw new NodeRepresentationClassDoesNotExistException($node);
        }

        /** @var AbstractRepresentation $representationClass */
        $representationClass = new $representationClassname($this, $node);

        return $representationClass->getRepresentation();
    }

    /**
     * @param array $arguments
     *
     * @return array
     */
    public function getArguments(array $arguments): array
    {
        if (0 === count($arguments)) {
            return [];
        }

        $representedArguments = [];

        foreach ($arguments as $key => $argument) {
            $representedArguments[$key] = $this->getRepresentationForNode($argument);
        }

        return $representedArguments;
    }

    /**
     * @param string $classname
     *
     * @return string
     */
    private function transformClassname(string $classname): string
    {
        $classWithNamespaces = explode('\\', $classname);

        $keyPhpParser = array_search('PhpParser', $classWithNamespaces, true);
        unset($classWithNamespaces[$keyPhpParser]);

        $keyPhpParser = array_search('Node', $classWithNamespaces, true);
        unset($classWithNamespaces[$keyPhpParser]);

        return sprintf(
            '%s\\%s',
            __CLASS__,
            implode('\\', $classWithNamespaces)
        );
    }

    /**
     * @param string $originalClassname
     *
     * @return string
     */
    private function getAbstractClassname(string $originalClassname): string
    {
        $classnameWithNamespaces = explode('\\', $originalClassname);

        unset($classnameWithNamespaces[array_key_last($classnameWithNamespaces)]);

        return implode('\\', $classnameWithNamespaces);
    }
}
