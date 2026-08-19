<?php

namespace Tests\Unit;

use App\Support\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_it_strips_scripts_and_event_handlers(): void
    {
        $html = '<p onclick="alert(1)">Hallo <script>alert(1)</script><strong>Wien</strong></p>';

        $clean = HtmlSanitizer::clean($html);

        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringContainsString('<strong>Wien</strong>', $clean);
    }

    public function test_it_rejects_javascript_urls_and_forces_noopener_on_blank_targets(): void
    {
        $html = '<a href="javascript:alert(1)" target="_blank" rel="dofollow">x</a><a href="https://umzugland.at" target="_blank">y</a>';

        $clean = HtmlSanitizer::clean($html);

        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('dofollow', $clean);
        $this->assertStringContainsString('noopener', $clean);
        $this->assertStringContainsString('noreferrer', $clean);
        $this->assertStringContainsString('https://umzugland.at', $clean);
    }
}
