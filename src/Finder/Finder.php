<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Finder;

use Symfony\Component\Finder\Finder as SymfonyFinder;

/**
 * Class Finder
 */
class Finder extends SymfonyFinder
{
    /** @var string */
    private const IS_DIR = 'is_dir';

    /** @var string */
    private const WILDCARD_GLOB = '*.';

    /**
     * Finder constructor.
     *
     * @param array $directories
     * @param array $includeFiles
     * @param array $exclude
     * @param array $excludePaths
     * @param array $excludeFiles
     * @param array $suffixes
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        array $directories,
        array $includeFiles,
        array $exclude,
        array $excludePaths,
        array $excludeFiles,
        array $suffixes
    ) {
        parent::__construct();
        $dirs = array_filter($directories, self::IS_DIR);

        $this
            ->files()
            ->in($dirs)
            ->exclude($exclude)
            ->ignoreDotFiles(true)
            ->ignoreVCS(true);

        foreach ($includeFiles as $includeFile) {
            $this->name($includeFile);
        }

        if (!count($includeFiles)) {
            $this->addWildcardIncludes($suffixes);
        }

        foreach ($excludePaths as $notPath) {
            $this->notPath($notPath);
        }

        foreach ($excludeFiles as $notName) {
            $this->notName($notName);
        }
    }

    /**
     * @param array $suffixes
     *
     * @return void
     */
    private function addWildcardIncludes(array $suffixes): void
    {
        foreach ($suffixes as $suffix) {
            $this->name(self::WILDCARD_GLOB . $suffix);
        }
    }
}
