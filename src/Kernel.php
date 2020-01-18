<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer;

use Isfett\PhpAnalyzer\DependencyInjection\Compiler;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;

/**
 * Class App
 */
final class Kernel extends SymfonyKernel
{
    /**
     * @return string
     */
    public function getCacheDir(): string
    {
        return __DIR__ . '/../var/cache/' . $this->environment;
    }

    /**
     * @return string
     */
    public function getLogDir(): string
    {
        return __DIR__ . '/../var/logs';
    }

    /**
     * @return array
     */
    public function registerBundles(): array
    {
        return [];
    }

    /**
     * @param LoaderInterface $loader
     *
     * @return void
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/services.yml');
    }

    /**
     * @param ContainerBuilder $containerBuilder
     *
     * @return void
     */
    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass($this->createCollectingCompilerPass());
    }

    /**
     * @return CompilerPassInterface
     */
    private function createCollectingCompilerPass(): CompilerPassInterface
    {
        return new Compiler();
    }
}
