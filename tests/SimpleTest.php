<?php
declare(strict_types=1);

namespace HNV\Http\MessageTests;

use Throwable;
use PHPUnit\Framework\TestCase;
/** ***********************************************************************************************
 * PSR-7 messages first test.
 *
 * @package HNV\Psr\Http\Tests\Message
 * @author  Hvorostenko
 *************************************************************************************************/
class SimpleTest extends TestCase
{
    /** **********************************************************************
     * Test tests are available
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testTestsAreAvailable(): void
    {
        self::assertTrue(
            true,
            'Tests are running!'
        );
    }
}