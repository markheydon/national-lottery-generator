<?php

/** Base Test Case for Download lottery unit tests. */

declare(strict_types=1);

namespace Tests\Unit\Lottery;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase as LaravelTestCase;

abstract class DownloaderTestCase extends LaravelTestCase
{
    /**
     * Configure deterministic HTTP response for download tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Keep downloader writes isolated from real local storage.
        Storage::fake('local');

        Http::fake([
            '*' => Http::response("DrawNumber,DrawDate\n1,2026-01-01\n", 200),
        ]);
    }

    /**
     * Generated download output for use tests.
     *
     * @param bool $failDownload Simulate failed download (for testing).
     * @param bool $failRename Simulate failed rename of temp file (for testing).
     *
     * @return string Generated download output for use tests.
     */
    abstract protected function download($failDownload = false, $failRename = false): string;

    /**
     * Tests the download() methods works without error.
     *
     * Only thing that would stop this working is network issues,
     * in theory at least.
     */
    public function testDownloadOK()
    {
        $result = $this->download();
        $this->assertEmpty($result);
    }

    /**
     * Tests the download() method reports error on failed download.
     */
    public function testDownloadFailed()
    {
        $result = $this->download(true, false);
        $this->assertStringContainsString('failed', $result);
    }

    /**
     * Test the download() method reports error on failed renaming of temp file.
     */
    public function testDownloadRenameFailed()
    {
        // Create current file first so rename branch is exercised deterministically.
        $this->assertEmpty($this->download());

        $result = $this->download(false, true);
        $this->assertStringContainsString('failed', $result);
    }
}
