<?php
/**
 * Unit tests for LottoHotpicksGenerate class.
 */

declare(strict_types=1);

namespace Tests\Unit\Lottery;

use App\Services\Lottery\LottoHotpicksGenerate;

/**
 * Unit tests for LottoHotpicksGenerate class.
 *
 * @package Tests\Unit\Lottery
 */
class LottoHotpicksGenerateTest extends GenerateTestCase
{
    /**
     * @inheritdoc
     */
    protected function generate(): array
    {
        return LottoHotpicksGenerate::generate();
    }
}
