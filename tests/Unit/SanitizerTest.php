<?php
/**
 * Sanitizer Helper Tests
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Helpers\Sanitizer;

class SanitizerTest extends TestCase
{
    /**
     * Test XSS protection
     */
    public function testXSSProtection(): void
    {
        $malicious = '<script>alert("xss")</script>';
        $cleaned = Sanitizer::clean($malicious);

        $this->assertStringNotContainsString('<script>', $cleaned);
        $this->assertStringContainsString('&lt;script&gt;', $cleaned);
    }

    /**
     * Test email validation - valid email
     */
    public function testValidEmail(): void
    {
        $email = 'user@example.com';
        $result = Sanitizer::email($email);

        $this->assertEquals($email, $result);
    }

    /**
     * Test email validation - invalid email
     */
    public function testInvalidEmail(): void
    {
        $email = 'not-an-email';
        $result = Sanitizer::email($email);

        $this->assertFalse($result);
    }

    /**
     * Test phone sanitization
     */
    public function testPhoneSanitization(): void
    {
        $phone = '+213 555-123-456';
        $result = Sanitizer::phone($phone);

        // Should only contain digits and + sign
        $this->assertMatchesRegularExpression('/^[0-9+]+$/', $result);
    }

    /**
     * Test integer sanitization
     */
    public function testIntegerSanitization(): void
    {
        $this->assertEquals(42, Sanitizer::int('42'));
        $this->assertEquals(0, Sanitizer::int('abc'));
        $this->assertEquals(-10, Sanitizer::int('-10'));
    }

    /**
     * Test filename sanitization
     */
    public function testFilenameSanitization(): void
    {
        $malicious = '../../../etc/passwd';
        $result = Sanitizer::filename($malicious);

        $this->assertStringNotContainsString('..', $result);
        $this->assertStringNotContainsString('/', $result);
    }

    /**
     * Test slug generation
     */
    public function testSlugGeneration(): void
    {
        $input = 'Hello World 123';
        $result = Sanitizer::slug($input);

        $this->assertEquals('hello-world-123', $result);
    }
}
