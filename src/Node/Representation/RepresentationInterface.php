<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation;

/**
 * Interface RepresentationInterface
 */
interface RepresentationInterface
{
    /**
     * @return string
     */
    public function representation(): string;
}
