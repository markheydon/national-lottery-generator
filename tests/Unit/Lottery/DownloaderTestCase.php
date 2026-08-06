<?php

declare(strict_types=1);

namespace Tests\Unit\Lottery;

use PHPUnit\Framework\TestCase;

abstract class DownloaderTestCase extends TestCase
{
    /**
     * @return string Generated download output for use tests.
     */
    abstract protected function download($failDownload = false, $failRename = false): string;

    public function testDownloadOK(): void
    {
        $result = $this->download();
        $this->assertEmpty($result);
    }

    public function testDownloadFailed(): void
    {
        $result = $this->download(true, false);
        $this->assertStringContainsString('failed', $result);
    }

    public function testDownloadRenameFailed(): void
    {
        $result = $this->download(false, true);
        $this->assertStringContainsString('failed', $result);
    }
}
