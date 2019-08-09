<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Builder;

use Isfett\PhpAnalyzer\Builder\FinderBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

/**
 * Class FinderBuilderTest
 */
class FinderBuilderTest extends TestCase
{
    private $builder;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new FinderBuilder();
    }

    /**
     * @return void
     */
    public function testBuilderWithIncludeFiles(): void
    {
        $finder = $this->builder
            ->setDirectories([dirname(__DIR__)])
            ->setIncludeFiles(['Test.php'])
            ->setExcludes(['*cache*.php'])
            ->setSuffixes(['php'])
            ->setExcludeFiles([ 'src/AppKernel.php'])
            ->setExcludePaths(['vendor'])
            ->getFinder();

        $this->assertCount(1, $this->getPrivateValueFromReflection('names', $finder));
        $this->assertContains('Test.php', $this->getPrivateValueFromReflection('names', $finder));

        $this->assertCount(1, $this->getPrivateValueFromReflection('notNames', $finder));
        $this->assertContains('src/AppKernel.php', $this->getPrivateValueFromReflection('notNames', $finder));

        $this->assertCount(1, $this->getPrivateValueFromReflection('exclude', $finder));
        $this->assertContains('*cache*.php', $this->getPrivateValueFromReflection('exclude', $finder));

        $this->assertCount(1, $this->getPrivateValueFromReflection('dirs', $finder));
        $this->assertContains(dirname(__DIR__), $this->getPrivateValueFromReflection('dirs', $finder));

        $this->assertCount(1, $this->getPrivateValueFromReflection('notPaths', $finder));
        $this->assertContains('vendor', $this->getPrivateValueFromReflection('notPaths', $finder));

        $this->assertCount(0, $this->getPrivateValueFromReflection('filters', $finder));
        $this->assertCount(0, $this->getPrivateValueFromReflection('depths', $finder));
        $this->assertCount(0, $this->getPrivateValueFromReflection('sizes', $finder));
    }

    /**
     * @return void
     */
    public function testBuilder(): void
    {
        $finder = $this->builder
            ->setDirectories([dirname(__DIR__)])
            ->setIncludeFiles([])
            ->setExcludes(['*cache*.php'])
            ->setSuffixes(['php'])
            ->setExcludeFiles([ 'src/AppKernel.php'])
            ->setExcludePaths(['vendor'])
            ->getFinder();

        $this->assertCount(1, $this->getPrivateValueFromReflection('names', $finder));
        $this->assertContains('*.php', $this->getPrivateValueFromReflection('names', $finder));

        $this->assertCount(1, $this->getPrivateValueFromReflection('notNames', $finder));
        $this->assertContains('src/AppKernel.php', $this->getPrivateValueFromReflection('notNames', $finder));

        $this->assertCount(1, $this->getPrivateValueFromReflection('exclude', $finder));
        $this->assertContains('*cache*.php', $this->getPrivateValueFromReflection('exclude', $finder));

        $this->assertCount(1, $this->getPrivateValueFromReflection('dirs', $finder));
        $this->assertContains(dirname(__DIR__), $this->getPrivateValueFromReflection('dirs', $finder));

        $this->assertCount(1, $this->getPrivateValueFromReflection('notPaths', $finder));
        $this->assertContains('vendor', $this->getPrivateValueFromReflection('notPaths', $finder));

        $this->assertCount(0, $this->getPrivateValueFromReflection('filters', $finder));
        $this->assertCount(0, $this->getPrivateValueFromReflection('depths', $finder));
        $this->assertCount(0, $this->getPrivateValueFromReflection('sizes', $finder));
    }

    /**
     * @param string $name
     * @param Finder $finder
     *
     * @return array
     * @throws \ReflectionException
     */
    private function getPrivateValueFromReflection(string $name, Finder $finder): array
    {
        $property = (new \ReflectionClass(Finder::class))->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($finder);
    }
}
