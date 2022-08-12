<?php

declare(strict_types=1);

namespace HNV\Http\MessageTests;

use PHPUnit\Framework\{
    Attributes,
    TestCase,
};

/**
 * @internal
 */
#[Attributes\Small]
class SimpleTest extends TestCase
{
    #[Attributes\Test]
    public function testsAreAvailable(): void
    {
        static::assertTrue(
            true,
            'Tests are running!'
        );
    }
}
