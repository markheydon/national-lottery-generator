<?php

/**
 * Unit tests for SetForLifeDownload class.
 */

declare(strict_types=1);

namespace Tests\Unit\Lottery;

use App\Services\Lottery\SetForLifeDownload;

/**
 * Unit tests for SetForLifeDownload class.
 *
 * @package Tests\Unit\Lottery
 */
class SetForLifeDownloadTest extends DownloaderTestCase
{
    /**
     * @inheritdoc
     */
    protected function download($failDownload = false, $failRename = false): string
    {
        return SetForLifeDownload::download($failDownload, $failRename);
    }
}
