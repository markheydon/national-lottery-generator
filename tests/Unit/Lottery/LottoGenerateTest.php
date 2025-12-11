<?php
/**
 * Unit tests for LottoGenerate class.
 */

declare(strict_types=1);

namespace Tests\Unit\Lottery;

use App\Services\Lottery\LottoGenerate;

/**
 * Unit tests for LottoGenerate class.
 *
 * @package Tests\Unit\Lottery
 */
class LottoGenerateTest extends GenerateTestCase
{
    /**
     * @inheritdoc
     */
    protected function generate(): array
    {
        return LottoGenerate::generate();
    }
}
