<?php
/**
 * Unit tests for EuromillionsHotpicksGenerate class.
 */

declare(strict_types=1);

namespace Tests\Unit\Lottery;

use App\Services\Lottery\EuromillionsHotpicksGenerate;

/**
 * Unit tests for EuromillionsHotpicksGenerate class.
 *
 * @package Tests\Unit\Lottery
 */
class EuromillionsHotpicksGenerateTest extends GenerateTestCase
{
    /**
     * @inheritdoc
     */
    protected function generate(): array
    {
        return EuromillionsHotpicksGenerate::generate();
    }
}
