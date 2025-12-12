<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\UIHelperService;
use Tests\TestCase;

class UIHelperServiceTest extends TestCase
{
    private UIHelperService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new UIHelperService;
    }

    /** @test */
    public function it_formats_bytes_using_binary_boundaries(): void
    {
        $this->assertSame('1 KB', $this->service->formatBytes(1024));
        $this->assertSame('1.5 KB', $this->service->formatBytes(1536, precision: 1));
        $this->assertSame('999 B', $this->service->formatBytes(999, precision: 0));
    }
}
