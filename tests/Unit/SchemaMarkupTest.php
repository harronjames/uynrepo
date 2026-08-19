<?php

namespace Tests\Unit;

use App\Support\GermanExcerpt;
use App\Support\SchemaMarkup;
use PHPUnit\Framework\TestCase;

class SchemaMarkupTest extends TestCase
{
    public function test_valid_json_ld_is_accepted_and_escaped_for_html(): void
    {
        $json = '{"@context":"https://schema.org","@type":"FAQPage","name":"</script><script>alert(1)"}';

        $this->assertTrue(SchemaMarkup::isValid($json));

        $safe = SchemaMarkup::toSafeScript($json);

        $this->assertNotNull($safe);
        $this->assertStringNotContainsString('</script>', (string) $safe);
        $this->assertTrue(SchemaMarkup::containsArticleType('{"@type":"BlogPosting"}'));
        $this->assertFalse(SchemaMarkup::containsArticleType('{"@type":"FAQPage"}'));
    }

    public function test_invalid_json_is_rejected(): void
    {
        $this->assertFalse(SchemaMarkup::isValid('{not json'));
        $this->assertFalse(SchemaMarkup::isValid('["just a string"]'));
        $this->assertTrue(SchemaMarkup::isValid(''));
    }

    public function test_german_excerpt_does_not_cut_mid_word(): void
    {
        $html = '<p>Die Halteverbotszone in Wien muss rechtzeitig über die MA 46 beantragt werden, sonst drohen Strafen.</p>';
        $excerpt = GermanExcerpt::fromHtml($html, 40);

        $this->assertLessThanOrEqual(50, mb_strlen($excerpt));
        $this->assertStringNotContainsString('<p>', $excerpt);
        $this->assertStringContainsString('…', $excerpt);
    }
}
