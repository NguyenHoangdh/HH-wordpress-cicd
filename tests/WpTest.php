<?php

use PHPUnit\Framework\TestCase;

class WpTest extends TestCase
{
    // Test 1: Kiểm thử logic sanitize dữ liệu đầu vào (Unit Test)
    public function testSanitizeRemovesHtmlTags()
    {
        $input  = '<p>Hello World</p>';
        $output = strip_tags($input);
        $this->assertStringNotContainsString('<p>', $output);
        $this->assertEquals('Hello World', trim($output));
    }

    // Test 2: Kiểm thử cấu hình hạ tầng - docker-compose.yml (Integration/IaC Test)
    public function testDockerComposeHasRequiredServices()
    {
        $filePath = dirname(__DIR__) . '/docker-compose.yml';
        $this->assertFileExists($filePath, 'Tệp docker-compose.yml phải tồn tại');

        $content = file_get_contents($filePath);
        
        // Kiểm tra xem có định nghĩa service wordpress và db không
        $this->assertStringContainsString('wordpress:', $content, 'Thiếu service wordpress trong docker-compose.yml');
        $this->assertStringContainsString('db:', $content, 'Thiếu service db trong docker-compose.yml');
        
        // Kiểm tra các biến môi trường quan trọng của WordPress
        $this->assertStringContainsString('WORDPRESS_DB_HOST', $content, 'Thiếu WORDPRESS_DB_HOST');
        $this->assertStringContainsString('WORDPRESS_DB_PASSWORD', $content, 'Thiếu WORDPRESS_DB_PASSWORD');
    }

    // Test 3: Kiểm thử cấu hình Production - docker-compose.prod.yml (IaC Test)
    public function testProductionDockerComposeHasCorrectImage()
    {
        $filePath = dirname(__DIR__) . '/docker-compose.prod.yml';
        $this->assertFileExists($filePath, 'Tệp docker-compose.prod.yml phải tồn tại');

        $content = file_get_contents($filePath);
        
        // Kiểm tra xem có định nghĩa image wordpress production không
        $this->assertStringContainsString('hoang2204/hh-wordpress:latest', $content, 'Môi trường Production phải sử dụng image hoang2204/hh-wordpress:latest');
    }
}
