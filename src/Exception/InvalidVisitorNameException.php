<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Exception;

use Isfett\PhpAnalyzer\Finder\Finder;

/**
 * Class InvalidVisitorNameException
 */
class InvalidVisitorNameException extends \LogicException
{
    /** @var string */
    private const EMPTY_STRING = '';

    /** @var int */
    private const ERROR_CODE = 0;

    /** @var string */
    private const ERROR_MESSAGE = 'Visitor with name %s does not exist. Possible Visitors are: %s';

    /** @var string */
    private const EXCLUDE_INTERFACE = 'VisitorInterface.php';

    /** @var string */
    private const PATH = '/Node/Visitor/';

    /** @var string */
    private const SUFFIX_FILENAME = 'Visitor.php';

    /** @var string */
    private const VISITOR_NAME_DELIMITER = ', ';

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
        parent::__construct(
            sprintf(
                self::ERROR_MESSAGE,
                $visitorName,
                implode(self::VISITOR_NAME_DELIMITER, $possibleVisitorNames)
            ),
            self::ERROR_CODE,
            $previous
        );
    }

    /**
     * @param string $prefix
     *
     * @return array
     */
    private function getPossibleVisitorNames(string $prefix): array
    {
        $finder = new Finder([dirname(__DIR__) . self::PATH . $prefix], [], [], [], [self::EXCLUDE_INTERFACE], []);
        $finder->sortByName();
        $possibleVisitorNames = [];
        foreach ($finder->getIterator() as $file) {
            $possibleVisitorNames[] = str_replace(
                self::SUFFIX_FILENAME,
                self::EMPTY_STRING,
                $file->getRelativePathname()
            );
        }

        return $possibleVisitorNames;
    }
}
