<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Finder;

use Isfett\PhpAnalyzer\Finder\Finder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class FinderTest
 */
class FinderTest extends TestCase
{
    /** @var Finder */
    private $finder;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->finder = new Finder([dirname(__DIR__)], [], [], [], [], []);
    }

    /**
     * @return void
     */
    public function testFinderDefaultParameters(): void
    {
        $property = (new \ReflectionClass(\Symfony\Component\Finder\Finder::class))->getProperty('ignore');
        $property->setAccessible(true);
        $this->assertSame(3, $property->getValue($this->finder));
        // 3 is the sum of the constants
        // const IGNORE_VCS_FILES = 1;
        // const IGNORE_DOT_FILES = 2;
    }
}
