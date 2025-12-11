<?php

/**
 * Unit tests for LottoDownload class.
 */

declare(strict_types=1);

namespace Tests\Unit\Lottery;

use App\Services\Lottery\LottoDownload;

/**
 * Unit tests for LottoDownload class.
 *
 * @package Tests\Unit\Lottery
 */
class LottoDownloadTest extends DownloaderTestCase
{
    /**
     * @inheritdoc
     */
    protected function download($failDownload = false, $failRename = false): string
    {
        return LottoDownload::download($failDownload, $failRename);
    }
}
