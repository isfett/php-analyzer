<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Exception;

use Isfett\PhpAnalyzer\Finder\Finder;

/**
 * Class InvalidVisitorNameException
 */
class InvalidVisitorNameException extends \LogicException
{
    /**
     * InvalidVisitorNameException constructor.
     *
     * @param string          $visitorName
     * @param string          $prefix
     * @param \Throwable|null $previous
     */
    public function __construct(string $visitorName, string $prefix, ?\Throwable $previous = null)
    {
        $possibleVisitorNames = $this->getPossibleVisitorNames($prefix);
        parent::__construct(sprintf(
            'Visitor with name %s does not exist. Possible Visitors are: %s',
            $visitorName,
            implode(', ', $possibleVisitorNames)
        ), 0, $previous);
    }

    /**
     * @param string $prefix
     *
     * @return array
     */
    private function getPossibleVisitorNames(string $prefix): array
    {
        $finder = new Finder([dirname(__DIR__) . '/Node/Visitor/'.$prefix], [], [], [], ['VisitorInterface.php'], []);
        $finder->sortByName();
        $possibleVisitorNames = [];
        foreach ($finder->getIterator() as $file) {
            $possibleVisitorNames[] = str_replace('Visitor.php', '', $file->getRelativePathname());
        }

        return $possibleVisitorNames;
    }
}
