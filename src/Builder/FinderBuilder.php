<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Builder;

use Isfett\PhpAnalyzer\Finder\Finder;

/**
 * Class FinderBuilder
 */
class FinderBuilder implements FinderBuilderInterface
{
    /** @var array */
    private $directories = [];

    /** @var array */
    private $excludeFiles = [];

    /** @var array */
    private $excludePaths = [];

    /** @var array */
    private $excludes = [];

    /** @var array */
    private $includeFiles = [];

    /** @var array */
    private $suffixes = [];

    /**
     * @return Finder
     */
    public function getFinder(): Finder
    {
        return new Finder(
            $this->directories,
            $this->includeFiles,
            $this->excludes,
            $this->excludePaths,
            $this->excludeFiles,
            $this->suffixes
        );
    }

    /**
     * @param array $directories
     *
     * @return FinderBuilderInterface
     */
    public function setDirectories(array $directories): FinderBuilderInterface
    {
        $this->directories = $directories;

        return $this;
    }

    /**
     * @param array $excludeFiles
     *
     * @return FinderBuilderInterface
     */
    public function setExcludeFiles(array $excludeFiles): FinderBuilderInterface
    {
        $this->excludeFiles = $excludeFiles;

        return $this;
    }

    /**
     * @param array $excludePaths
     *
     * @return FinderBuilderInterface
     */
    public function setExcludePaths(array $excludePaths): FinderBuilderInterface
    {
        $this->excludePaths = $excludePaths;

        return $this;
    }

    /**
     * @param array $excludes
     *
     * @return FinderBuilderInterface
     */
    public function setExcludes(array $excludes): FinderBuilderInterface
    {
        $this->excludes = $excludes;

        return $this;
    }

    /**
     * @param array $includeFiles
     *
     * @return FinderBuilderInterface
     */
    public function setIncludeFiles(array $includeFiles): FinderBuilderInterface
    {
        $this->includeFiles = $includeFiles;

        return $this;
    }

    /**
     * @param array $suffixes
     *
     * @return FinderBuilderInterface
     */
    public function setSuffixes(array $suffixes): FinderBuilderInterface
    {
        $this->suffixes = $suffixes;

        return $this;
    }
}
