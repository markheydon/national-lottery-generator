<?php

/**
 * Unit tests for SetForLifeGenerate class.
 */

declare(strict_types=1);

namespace Tests\Unit\Lottery;

use App\Services\Lottery\SetForLifeGenerate;

/**
 * Unit tests for SetForLifeGenerate class.
 *
 * @package Tests\Unit\Lottery
 */
class SetForLifeGenerateTest extends GenerateTestCase
{
    /**
     * @inheritdoc
     */
    protected function generate(): array
    {
        return SetForLifeGenerate::generate();
    }

    /**
     * Test findMostPopularLifeBall returns most frequent Life Ball.
     */
    public function testFindMostPopularLifeBallReturnsCorrectValue()
    {
        $lines1 = [
            ['mainNumbers' => [1, 2, 3, 4, 5], 'lifeBall' => [1]],
            ['mainNumbers' => [6, 7, 8, 9, 10], 'lifeBall' => [2]],
        ];
        $lines2 = [
            ['mainNumbers' => [11, 12, 13, 14, 15], 'lifeBall' => [1]],
            ['mainNumbers' => [16, 17, 18, 19, 20], 'lifeBall' => [1]],
            ['mainNumbers' => [21, 22, 23, 24, 25], 'lifeBall' => [3]],
        ];

        $reflection = new \ReflectionClass(SetForLifeGenerate::class);
        $method = $reflection->getMethod('findMostPopularLifeBall');

        $result = $method->invokeArgs(null, [$lines1, $lines2]);
        $this->assertEquals(1, $result, 'Should return Life Ball 1 as it appears 3 times');
    }

    /**
     * Test findMostPopularLifeBall returns default when no valid data.
     */
    public function testFindMostPopularLifeBallReturnsDefaultWhenEmpty()
    {
        $lines1 = [];
        $lines2 = [];

        $reflection = new \ReflectionClass(SetForLifeGenerate::class);
        $method = $reflection->getMethod('findMostPopularLifeBall');

        $result = $method->invokeArgs(null, [$lines1, $lines2]);
        $this->assertEquals(1, $result, 'Should return default value 1 when no data');
    }

    /**
     * Test findMostPopularLifeBall handles lines without life ball.
     */
    public function testFindMostPopularLifeBallHandlesInvalidLines()
    {
        $lines1 = [
            ['mainNumbers' => [1, 2, 3, 4, 5]],  // No life ball
            ['mainNumbers' => [6, 7, 8, 9, 10], 'lifeBall' => [5]],
        ];
        $lines2 = [
            ['mainNumbers' => [11, 12, 13, 14, 15], 'lifeBall' => []],  // Empty life ball
        ];

        $reflection = new \ReflectionClass(SetForLifeGenerate::class);
        $method = $reflection->getMethod('findMostPopularLifeBall');

        $result = $method->invokeArgs(null, [$lines1, $lines2]);
        $this->assertEquals(5, $result, 'Should return Life Ball 5 as it\'s the only valid one');
    }

    /**
     * Test findFirstLineIndexWithLifeBall finds correct index.
     */
    public function testFindFirstLineIndexWithLifeBallReturnsCorrectIndex()
    {
        $lines = [
            ['mainNumbers' => [1, 2, 3, 4, 5], 'lifeBall' => [2]],
            ['mainNumbers' => [6, 7, 8, 9, 10], 'lifeBall' => [5]],
            ['mainNumbers' => [11, 12, 13, 14, 15], 'lifeBall' => [5]],
            ['mainNumbers' => [16, 17, 18, 19, 20], 'lifeBall' => [3]],
        ];

        $reflection = new \ReflectionClass(SetForLifeGenerate::class);
        $method = $reflection->getMethod('findFirstLineIndexWithLifeBall');

        $result = $method->invokeArgs(null, [$lines, 5]);
        $this->assertEquals(1, $result, 'Should return index 1 for first line with Life Ball 5');
    }

    /**
     * Test findFirstLineIndexWithLifeBall returns null when not found.
     */
    public function testFindFirstLineIndexWithLifeBallReturnsNullWhenNotFound()
    {
        $lines = [
            ['mainNumbers' => [1, 2, 3, 4, 5], 'lifeBall' => [2]],
            ['mainNumbers' => [6, 7, 8, 9, 10], 'lifeBall' => [3]],
        ];

        $reflection = new \ReflectionClass(SetForLifeGenerate::class);
        $method = $reflection->getMethod('findFirstLineIndexWithLifeBall');

        $result = $method->invokeArgs(null, [$lines, 10]);
        $this->assertNull($result, 'Should return null when Life Ball 10 not found');
    }

    /**
     * Test findFirstLineIndexWithLifeBall returns index 0 when first line matches.
     */
    public function testFindFirstLineIndexWithLifeBallReturnsZeroForFirstLine()
    {
        $lines = [
            ['mainNumbers' => [1, 2, 3, 4, 5], 'lifeBall' => [7]],
            ['mainNumbers' => [6, 7, 8, 9, 10], 'lifeBall' => [3]],
        ];

        $reflection = new \ReflectionClass(SetForLifeGenerate::class);
        $method = $reflection->getMethod('findFirstLineIndexWithLifeBall');

        $result = $method->invokeArgs(null, [$lines, 7]);
        $this->assertEquals(0, $result, 'Should return 0 when first line matches (not null)');
    }
}
