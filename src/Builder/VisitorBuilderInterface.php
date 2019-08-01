<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Builder;

use Doctrine\Common\Collections\ArrayCollection;
use Isfett\PhpAnalyzer\Node\VisitorInterface;

/**
 * Interface VisitorBuilderInterface
 */
interface VisitorBuilderInterface
{
    /**
     * @return ArrayCollection<VisitorInterface>
     */
    public function getVisitors(): ArrayCollection;

    /**
     * @param string $names
     *
     * @return self
     */
    public function setNames(string $names): self;
}
