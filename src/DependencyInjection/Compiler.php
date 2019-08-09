<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\DependencyInjection;

use Isfett\PhpAnalyzer\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class Compiler
 */
class Compiler implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $containerBuilder
     *
     * @return void
     */
    public function process(ContainerBuilder $containerBuilder): void
    {
        $applicationDefinition = $containerBuilder->findDefinition(Application::class);

        foreach ($containerBuilder->getDefinitions() as $definition) {
            if (! is_a($definition->getClass(), Command::class, true)) {
                continue;
            }

            $applicationDefinition->addMethodCall('add', [new Reference($definition->getClass())]);
        }
    }
}
