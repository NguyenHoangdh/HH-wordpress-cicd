<?php

use PHPUnit\Framework\TestCase;
//demooooss
class SampleTest extends TestCase
{
    public function test_sanitize_removes_script_tag(): void
{
    $input  = '<script>alert("xss")</script>Hello World';
    $output = strip_tags($input);
    $this->assertStringNotContainsString('<script>', $output);
    $this->assertStringContainsString('Hello World', $output);
}

    public function test_string_is_not_empty(): void
    {
        $title = 'My WordPress Site';
        $this->assertNotEmpty($title);
        $this->assertIsString($title);
    }

    public function test_addition_is_correct(): void
    {
        $this->assertEquals(4, 2 + 2);
        $this->assertGreaterThan(0, 2 + 2);
    }

    public function test_array_count(): void
    {
        $plugins = ['akismet', 'yoast-seo', 'woocommerce'];
        $this->assertCount(3, $plugins);
        $this->assertContains('akismet', $plugins);
    }
}