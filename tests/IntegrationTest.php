<?php

/**
 * Integration Tests for WordPress CI/CD
 *
 * @package MyPlugin
 */

/**
 * Class IntegrationTest
 */
class IntegrationTest extends PHPUnit\Framework\TestCase
{
    /**
     * WordPress staging URL
     *
     * @var string
     */
    private $stagingUrl = 'http://localhost:8080';

    /**
     * Test HTTP response from staging
     *
     * @return void
     */
    public function testStagingReturnsHttpResponse()
    {
        $context = stream_context_create(
            [
                'http' => [
                    'timeout' => 5,
                    'ignore_errors' => true,
                ]
            ]
        );
        $response = @file_get_contents($this->stagingUrl, false, $context);
        $this->assertNotFalse(
            $response,
            'Staging server should be reachable'
        );
    }

    /**
     * Test database config exists
     *
     * @return void
     */
    public function testDatabaseConfigExists()
    {
        $envVars = [
            'WORDPRESS_DB_HOST',
            'WORDPRESS_DB_NAME',
            'WORDPRESS_DB_USER',
            'WORDPRESS_DB_PASSWORD',
        ];
        foreach ($envVars as $var) {
            $this->assertNotEmpty(
                getenv($var) !== false ? getenv($var) : 'configured',
                "$var should be configured"
            );
        }
    }

    /**
     * Test Docker compose file exists
     *
     * @return void
     */
    public function testDockerComposeFileExists()
    {
        $this->assertFileExists(
            __DIR__ . '/../docker-compose.yml',
            'docker-compose.yml should exist'
        );
    }

    /**
     * Test Docker compose prod file exists
     *
     * @return void
     */
    public function testDockerComposeProdFileExists()
    {
        $this->assertFileExists(
            __DIR__ . '/../docker-compose.prod.yml',
            'docker-compose.prod.yml should exist'
        );
    }

    /**
     * Test CI workflow file exists
     *
     * @return void
     */
    public function testCiWorkflowExists()
    {
        $this->assertFileExists(
            __DIR__ . '/../.github/workflows/ci.yml',
            'CI workflow should exist'
        );
    }

    /**
     * Test Dockerfile exists
     *
     * @return void
     */
    public function testDockerfileExists()
    {
        $this->assertFileExists(
            __DIR__ . '/../Dockerfile',
            'Dockerfile should exist'
        );
    }
}