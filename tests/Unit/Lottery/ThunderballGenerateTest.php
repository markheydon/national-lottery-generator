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
}
