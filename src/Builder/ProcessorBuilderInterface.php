<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Builder;

use Doctrine\Common\Collections\ArrayCollection;
use Isfett\PhpAnalyzer\Node\ProcessorInterface;

/**
 * Interface ProcessorBuilderInterface
 */
interface ProcessorBuilderInterface
{
    /**
     * @return ArrayCollection<ProcessorInterface>
     */
    public function getProcessors(): ArrayCollection;

    /**
     * @param string $names
     *
     * @return self
     */
    public function setNames(string $names): self;
}
