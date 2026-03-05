<?php

/**
 * Unit tests for SetForLifeGenerate class.
 */

declare(strict_types=1);

namespace Tests\Unit\Lottery;

use App\Services\Lottery\SetForLifeGenerate;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for SetForLifeGenerate class.
 *
 * @package Tests\Unit\Lottery
 */
class SetForLifeGenerateTest extends TestCase
{
    /**
     * Test findMostPopularLifeBall returns most frequent Life Ball.
     */
    public function testFindMostPopularLifeBallReturnsCorrectValue(): void
    {
        $lines1 = [
            ['mainNumbers' => [1, 2, 3, 4, 5], 'lifeBall' => [1]],
            ['mainNumbers' => [6, 7, 8, 9, 10], 'lifeBall' => [2]],
        ];
        $lines2 = [
            ['mainNumbers' => [11, 12, 13, 14, 15], 'lifeBall' => [1]],
            ['mainNumbers' => [16, 17, 18, 19, 20], 'lifeBall' => [1]],
        ];

        $reflection = new \ReflectionClass(SetForLifeGenerate::class);
        $method = $reflection->getMethod('findMostPopularLifeBall');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$lines1, $lines2]);
        $this->assertEquals(1, $result);
    }

    /**
     * Test findMostPopularLifeBall returns default when no valid data.
     */
    public function testFindMostPopularLifeBallReturnsDefaultWhenEmpty(): void
    {
        $reflection = new \ReflectionClass(SetForLifeGenerate::class);
        $method = $reflection->getMethod('findMostPopularLifeBall');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [[], []]);
        $this->assertEquals(1, $result);
    }

    /**
     * Test findFirstLineIndexWithLifeBall finds correct index.
     */
    public function testFindFirstLineIndexWithLifeBallReturnsCorrectIndex(): void
    {
        $lines = [
            ['mainNumbers' => [1, 2, 3, 4, 5], 'lifeBall' => [2]],
            ['mainNumbers' => [6, 7, 8, 9, 10], 'lifeBall' => [5]],
            ['mainNumbers' => [11, 12, 13, 14, 15], 'lifeBall' => [5]],
        ];

        $reflection = new \ReflectionClass(SetForLifeGenerate::class);
        $method = $reflection->getMethod('findFirstLineIndexWithLifeBall');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$lines, 5]);
        $this->assertEquals(1, $result);
    }

    /**
     * Test findFirstLineIndexWithLifeBall returns null when not found.
     */
    public function testFindFirstLineIndexWithLifeBallReturnsNullWhenNotFound(): void
    {
        $lines = [
            ['mainNumbers' => [1, 2, 3, 4, 5], 'lifeBall' => [2]],
            ['mainNumbers' => [6, 7, 8, 9, 10], 'lifeBall' => [3]],
        ];

        $reflection = new \ReflectionClass(SetForLifeGenerate::class);
        $method = $reflection->getMethod('findFirstLineIndexWithLifeBall');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$lines, 10]);
        $this->assertNull($result);
    }
}
