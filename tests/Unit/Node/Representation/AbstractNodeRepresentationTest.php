<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation;

use Isfett\PhpAnalyzer\Node\Representation;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class AbstractNodeRepresentationTest
 */
abstract class AbstractNodeRepresentationTest extends AbstractNodeTestCase
{
    /** @var MockObject|Representation */
    protected $representation;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->representation = $this->createMock(Representation::class);
        //$this->representation->method('getRepresentationForNode')->willReturn('');
        //$this->representation->method('getArguments')->willReturn([]);
    }
}
