<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Service;

use Isfett\PhpAnalyzer\Exception\NodeRepresentationClassDoesNotExistException;
use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;
use Isfett\PhpAnalyzer\Node\Representation\RepresentationInterface;
use PhpParser\Node;

/**
 * Class NodeRepresentationService
 */
class NodeRepresentationService
{
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
     * @param array $arguments
     *
     * @return array
     */
    public function representationForArguments(array $arguments): array
    {
        if (0 === count($arguments)) {
            return [];
        }

        $representedArguments = [];

        foreach ($arguments as $key => $argument) {
            $representedArguments[$key] = $this->representationForNode($argument);
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

        $keyNode = array_search('Node', $classWithNamespaces, true);
        unset($classWithNamespaces[$keyNode]);

        return sprintf(
            '%s\\%s',
            'Isfett\\PhpAnalyzer\\Node\\Representation',
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
