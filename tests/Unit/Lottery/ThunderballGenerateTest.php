<?php

/**
 * Unit tests for ThunderballGenerate class.
 */

declare(strict_types=1);

namespace Tests\Unit\Lottery;

use App\Services\Lottery\ThunderballGenerate;

/**
 * Unit tests for ThunderballGenerate class.
 *
 * @package Tests\Unit\Lottery
 */
class ThunderballGenerateTest extends GenerateTestCase
{
    /**
     * @inheritdoc
     */
    protected function generate(): array
    {
        return ThunderballGenerate::generate();
    }

    /**
     * Test findMostPopularThunderball returns most frequent Thunderball.
     */
    public function testFindMostPopularThunderballReturnsCorrectValue()
    {
        $lines1 = [
            ['mainNumbers' => [1, 2, 3, 4, 5], 'thunderball' => [1]],
            ['mainNumbers' => [6, 7, 8, 9, 10], 'thunderball' => [2]],
        ];
        $lines2 = [
            ['mainNumbers' => [11, 12, 13, 14, 15], 'thunderball' => [1]],
            ['mainNumbers' => [16, 17, 18, 19, 20], 'thunderball' => [1]],
            ['mainNumbers' => [21, 22, 23, 24, 25], 'thunderball' => [3]],
        ];

        $reflection = new \ReflectionClass(ThunderballGenerate::class);
        $method = $reflection->getMethod('findMostPopularThunderball');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$lines1, $lines2]);
        $this->assertEquals(1, $result, 'Should return Thunderball 1 as it appears 3 times');
    }

    /**
     * Test findMostPopularThunderball returns default when no valid data.
     */
    public function testFindMostPopularThunderballReturnsDefaultWhenEmpty()
    {
        $lines1 = [];
        $lines2 = [];

        $reflection = new \ReflectionClass(ThunderballGenerate::class);
        $method = $reflection->getMethod('findMostPopularThunderball');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$lines1, $lines2]);
        $this->assertEquals(1, $result, 'Should return default value 1 when no data');
    }

    /**
     * Test findMostPopularThunderball handles lines without thunderball.
     */
    public function testFindMostPopularThunderballHandlesInvalidLines()
    {
        $lines1 = [
            ['mainNumbers' => [1, 2, 3, 4, 5]],  // No thunderball
            ['mainNumbers' => [6, 7, 8, 9, 10], 'thunderball' => [5]],
        ];
        $lines2 = [
            ['mainNumbers' => [11, 12, 13, 14, 15], 'thunderball' => []],  // Empty thunderball
        ];

        $reflection = new \ReflectionClass(ThunderballGenerate::class);
        $method = $reflection->getMethod('findMostPopularThunderball');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$lines1, $lines2]);
        $this->assertEquals(5, $result, 'Should return Thunderball 5 as it\'s the only valid one');
    }

    /**
     * Test findFirstLineIndexWithThunderball finds correct index.
     */
    public function testFindFirstLineIndexWithThunderballReturnsCorrectIndex()
    {
        $lines = [
            ['mainNumbers' => [1, 2, 3, 4, 5], 'thunderball' => [2]],
            ['mainNumbers' => [6, 7, 8, 9, 10], 'thunderball' => [5]],
            ['mainNumbers' => [11, 12, 13, 14, 15], 'thunderball' => [5]],
            ['mainNumbers' => [16, 17, 18, 19, 20], 'thunderball' => [3]],
        ];

        $reflection = new \ReflectionClass(ThunderballGenerate::class);
        $method = $reflection->getMethod('findFirstLineIndexWithThunderball');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$lines, 5]);
        $this->assertEquals(1, $result, 'Should return index 1 for first line with Thunderball 5');
    }

    /**
     * Test findFirstLineIndexWithThunderball returns null when not found.
     */
    public function testFindFirstLineIndexWithThunderballReturnsNullWhenNotFound()
    {
        $lines = [
            ['mainNumbers' => [1, 2, 3, 4, 5], 'thunderball' => [2]],
            ['mainNumbers' => [6, 7, 8, 9, 10], 'thunderball' => [3]],
        ];

        $reflection = new \ReflectionClass(ThunderballGenerate::class);
        $method = $reflection->getMethod('findFirstLineIndexWithThunderball');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$lines, 10]);
        $this->assertNull($result, 'Should return null when Thunderball 10 not found');
    }

    /**
     * Test findFirstLineIndexWithThunderball returns index 0 when first line matches.
     */
    public function testFindFirstLineIndexWithThunderballReturnsZeroForFirstLine()
    {
        $lines = [
            ['mainNumbers' => [1, 2, 3, 4, 5], 'thunderball' => [7]],
            ['mainNumbers' => [6, 7, 8, 9, 10], 'thunderball' => [3]],
        ];

        $reflection = new \ReflectionClass(ThunderballGenerate::class);
        $method = $reflection->getMethod('findFirstLineIndexWithThunderball');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$lines, 7]);
        $this->assertEquals(0, $result, 'Should return 0 when first line matches (not null)');
    }
}
