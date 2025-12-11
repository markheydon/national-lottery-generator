<?php

/**
 * Unit tests for EuromillionsDownload class.
 */

declare(strict_types=1);

namespace Tests\Unit\Lottery;

use App\Services\Lottery\EuromillionsDownload;

/**
 * Unit tests for EuromillionsDownload class.
 *
 * @package Tests\Unit\Lottery
 */
class EuromillionsDownloadTest extends DownloaderTestCase
{
    /**
     * @inheritdoc
     */
    protected function download($failDownload = false, $failRename = false): string
    {
        return EuromillionsDownload::download($failDownload, $failRename);
    }
}
