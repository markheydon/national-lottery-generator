<?php

/**
 * Unit tests for EuromillionsGenerate class.
 */

declare(strict_types=1);

namespace Tests\Unit\Lottery;

use App\Services\Lottery\EuromillionsGenerate;

/**
 * Unit tests for EuromillionsGenerate class.
 *
 * @package Tests\Unit\Lottery
 */
class EuromillionsGenerateTest extends GenerateTestCase
{
    /**
     * @inheritdoc
     */
    protected function generate(): array
    {
        return EuromillionsGenerate::generate();
    }
}
