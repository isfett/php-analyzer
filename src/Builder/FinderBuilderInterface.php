<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Builder;

use Isfett\PhpAnalyzer\Finder\Finder;

/**
 * Interface FinderBuilderInterface
 */
interface FinderBuilderInterface
{
    /**
     * @return Finder
     */
    public function getFinder(): Finder;

    /**
     * @param array $directories
     *
     * @return self
     */
    public function setDirectories(array $directories): self;

    /**
     * @param array $files
     *
     * @return FinderBuilderInterface
     */
    public function setExcludeFiles(array $files): self;

    /**
     * @param array $directories
     *
     * @return FinderBuilderInterface
     */
    public function setExcludePaths(array $directories): self;

    /**
     * @param array $excludes
     *
     * @return FinderBuilderInterface
     */
    public function setExcludes(array $excludes): self;

    /**
     * @param array $files
     *
     * @return FinderBuilderInterface
     */
    public function setIncludeFiles(array $files): self;

    /**
     * @param array $directories
     *
     * @return FinderBuilderInterface
     */
    public function setSuffixes(array $directories): self;
}
