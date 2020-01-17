<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Exception;

use Isfett\PhpAnalyzer\Finder\Finder;

/**
 * Class InvalidProcessorNameException
 */
class InvalidProcessorNameException extends \LogicException
{
    /** @var string */
    private const EMPTY_STRING = '';

    /** @var int */
    private const ERROR_CODE = 0;

    /** @var string */
    private const ERROR_MESSAGE = 'Processor with name %s does not exist. Possible Processors are: %s';

    /** @var string */
    private const EXCLUDE_INTERFACE = 'ProcessorInterface.php';

    /** @var string */
    private const PATH = '/Node/Processor/';

    /** @var string */
    private const PROCESSOR_NAME_DELIMITER = ', ';

    /** @var string */
    private const SUFFIX_FILENAME = 'Processor.php';

    /**
     * InvalidProcessorNameException constructor.
     *
     * @param string          $processorName
     * @param string          $prefix
     * @param \Throwable|null $previous
     */
    public function __construct(string $processorName, string $prefix, ?\Throwable $previous = null)
    {
        $possibleProcessorNames = $this->getPossibleProcessorNames($prefix);
        parent::__construct(
            sprintf(
                self::ERROR_MESSAGE,
                $processorName,
                implode(self::PROCESSOR_NAME_DELIMITER, $possibleProcessorNames)
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
    private function getPossibleProcessorNames(string $prefix): array
    {
        $finder = new Finder(
            [dirname(__DIR__) . self::PATH . $prefix],
            [],
            [],
            [],
            [self::EXCLUDE_INTERFACE],
            []
        );
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
