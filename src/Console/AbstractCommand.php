<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Console;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Kernel;
use Symfony\Component\Console\Command\Command;

/**
 * Class AbstractCommand
 */
abstract class AbstractCommand extends Command
{
    /**
     * @param Occurrence $occurrence
     * @param string     $line
     *
     * @return string
     */
    protected function getFileLink(Occurrence $occurrence, string $line): string
    {
        if (Kernel::VERSION_ID >= 40300) { //symfony 4.3 feature
            return sprintf(
                '<href=file://%s>%s:%s</>',
                $occurrence->getFile()->getPathname(),
                $occurrence->getFile()->getRelativePathname(),
                $line
            );
        }

        return sprintf(
            '%s:%s',
            $occurrence->getFile()->getRelativePathname(),
            $line
        );
    }
}
