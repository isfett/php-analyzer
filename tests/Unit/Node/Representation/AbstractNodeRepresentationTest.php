<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation;

use Isfett\PhpAnalyzer\Service\NodeRepresentationService;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class AbstractNodeRepresentationTest
 */
abstract class AbstractNodeRepresentationTest extends AbstractNodeTestCase
{
    /** @var MockObject|NodeRepresentationService */
    protected $nodeRepresentationService;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->nodeRepresentationService = $this->createMock(NodeRepresentationService::class);
    }
}
