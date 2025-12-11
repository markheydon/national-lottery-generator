<?php
/**
 * Unit tests for ThunderballDownload class.
 */

declare(strict_types=1);

namespace Tests\Unit\Lottery;

use App\Services\Lottery\ThunderballDownload;

/**
 * Unit tests for ThunderballDownload class.
 *
 * @package Tests\Unit\Lottery
 */
class ThunderballDownloadTest extends DownloaderTestCase
{
    /**
     * @inheritdoc
     */
    protected function download($failDownload = false, $failRename = false): string
    {
        return ThunderballDownload::download($failDownload, $failRename);
    }
}
