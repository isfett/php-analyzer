<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\DAO;

use PhpParser\Node;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Occurrence
 */
class Occurrence
{
    /** @var Node */
    private $node;

    /** @var SplFileInfo */
    private $file;

    /** @var bool */
    private $isFlipped = false;

    /** @var array */
    private $affectedByProcessors = [];

    /**
     * Condition constructor.
     *
     * @param Node        $node
     * @param SplFileInfo $file
     */
    public function __construct(Node $node, SplFileInfo $file)
    {
        $this->node = $node;
        $this->file = $file;
    }

    /**
     * @return Node
     */
    public function getNode(): Node
    {
        return $this->node;
    }

    /**
     * @param Node $node
     */
    public function setNode(Node $node): void
    {
        $this->node = $node;
    }

    /**
     * @return SplFileInfo
     */
    public function getFile(): SplFileInfo
    {
        return $this->file;
    }

    /**
     * @return bool
     */
    public function isFlipped(): bool
    {
        return $this->isFlipped;
    }

    /**
     * @param bool $isFlipped
     */
    public function setIsFlipped(bool $isFlipped): void
    {
        $this->isFlipped = $isFlipped;
    }

    /**
     * @return array
     */
    public function getAffectedByProcessors(): array
    {
        return $this->affectedByProcessors;
    }

    /**
     * @param string $processorName
     *
     * @return void
     */
    public function addAffectedByProcessor(string $processorName): void
    {
        if (!in_array($processorName, $this->affectedByProcessors, true)) {
            $this->affectedByProcessors[] = $processorName;
        }
    }
}
