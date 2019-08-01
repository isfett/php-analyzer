<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Finder;

use Symfony\Component\Finder\Finder as SymfonyFinder;

/**
 * Class Finder
 */
class Finder extends SymfonyFinder
{
    /**
     * Finder constructor.
     *
     * @param array $directories
     * @param array $includeFiles
     * @param array $exclude
     * @param array $excludePaths
     * @param array $excludeFiles
     * @param array $suffixes
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
        $dirs = array_filter($directories, 'is_dir');

        $this
            ->files()
            ->in($dirs)
            ->exclude($exclude)
            ->ignoreDotFiles(true)
            ->ignoreVCS(true);

        foreach ($includeFiles as $includeFile) {
            $this->name($includeFile);
        }

        if (0 === count($includeFiles)) {
            foreach ($suffixes as $suffix) {
                $this->name('*.' . $suffix);
            }
        }


        foreach ($excludePaths as $notPath) {
            $this->notPath($notPath);
        }

        foreach ($excludeFiles as $notName) {
            $this->notName($notName);
        }
    }
}
