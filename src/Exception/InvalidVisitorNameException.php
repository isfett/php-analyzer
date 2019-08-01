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
     * @param \Throwable|null $previous
     */
    public function __construct(string $visitorName, ?\Throwable $previous = null)
    {
        $possibleVisitorNames = $this->getPossibleVisitorNames();
        parent::__construct(sprintf(
            'Visitor with name %s does not exist. Possible visitor-names are: %s',
            $visitorName,
            implode(', ', $possibleVisitorNames)
        ), 0, $previous);
    }

    /**
     * @return array
     */
    private function getPossibleVisitorNames(): array
    {
        $finder = new Finder([dirname(__DIR__) . '/Node/Visitor'], [], [], [], ['VisitorInterface.php'], []);
        $finder->sortByName();
        $possibleVisitorNames = [];
        foreach ($finder->files() as $file) {
            $possibleVisitorNames[] = str_replace('ConditionVisitor.php', '', $file->getRelativePathname());
        }

        return $possibleVisitorNames;
    }
}
