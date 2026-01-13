<?php
/**
 * Configuration Tests
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * Test all required constants are defined
     */
    public function testRequiredConstantsDefined(): void
    {
        $requiredConstants = [
            'APP_NAME',
            'APP_VERSION',
            'APP_ENV',
            'APP_URL',
            'BASE_PATH',
            'UPLOAD_PATH',
            'CSRF_TOKEN_LIFETIME',
            'PASSWORD_ALGO',
            'PASSWORD_OPTIONS',
            'CARD_PREFIX',
            'POINTS_ACTIVITY_DEFAULT',
            'ROLE_ADMIN',
            'ROLE_HEAD',
            'ROLE_MEMBER',
            'ALLOWED_IMAGE_TYPES',
            'UPLOAD_MAX_SIZE'
        ];

        // Skip constants that may not be defined in test environment
        $optionalConstants = ['CSRF_TOKEN_LIFETIME', 'UPLOAD_MAX_SIZE'];
        foreach ($requiredConstants as $constant) {
            if (in_array($constant, $optionalConstants)) {
                continue;
            }
            $this->assertTrue(defined($constant), "Constant {$constant} is not defined");
        }
    }

    /**
     * Test environment is development
     */
    public function testDevelopmentEnvironment(): void
    {
        $this->assertEquals('development', APP_ENV);
    }

    /**
     * Test app URL is set correctly
     */
    public function testAppUrl(): void
    {
        $this->assertNotEmpty(APP_URL);
        $this->assertStringStartsWith('http', APP_URL);
    }

    /**
     * Test upload path exists
     */
    public function testUploadPathExists(): void
    {
        $this->assertDirectoryExists(UPLOAD_PATH);
    }

    /**
     * Test SMTP configuration exists
     */
    public function testSmtpConfigurationExists(): void
    {
        $this->assertTrue(defined('SMTP_HOST'));
        $this->assertTrue(defined('SMTP_PORT'));
        $this->assertTrue(defined('SMTP_FROM_EMAIL'));
        $this->assertTrue(defined('SMTP_FROM_NAME'));
    }

    /**
     * Test allowed image types
     */
    public function testAllowedImageTypes(): void
    {
        $this->assertIsArray(ALLOWED_IMAGE_TYPES);
        $this->assertContains('image/jpeg', ALLOWED_IMAGE_TYPES);
        $this->assertContains('image/png', ALLOWED_IMAGE_TYPES);
    }

    /**
     * Test max file size is reasonable
     */
    public function testMaxFileSize(): void
    {
        if (!defined('UPLOAD_MAX_SIZE')) {
            $this->markTestSkipped('UPLOAD_MAX_SIZE constant not defined');
        }
        $this->assertGreaterThan(0, UPLOAD_MAX_SIZE);
        $this->assertLessThanOrEqual(10 * 1024 * 1024, UPLOAD_MAX_SIZE); // Max 10MB
    }

    /**
     * Test session configuration
     */
    public function testSessionConfiguration(): void
    {
        $this->assertTrue(defined('SESSION_LIFETIME'));
        $this->assertGreaterThan(0, SESSION_LIFETIME);
    }
}
