<?php
/**
 * CSRF Helper Tests
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Helpers\CSRF;

class CSRFTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Test CSRF class exists
     */
    public function testCSRFClassExists(): void
    {
        $this->assertTrue(class_exists(CSRF::class));
    }

    /**
     * Test token generation
     */
    public function testTokenGeneration(): void
    {
        $token = CSRF::token();

        $this->assertNotEmpty($token);
        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token)); // 32 bytes = 64 hex chars
    }

    /**
     * Test token consistency in same session
     */
    public function testTokenConsistency(): void
    {
        $token1 = CSRF::token();
        $token2 = CSRF::token();

        $this->assertEquals($token1, $token2);
    }

    /**
     * Test meta tag generation
     */
    public function testMetaTagGeneration(): void
    {
        $meta = CSRF::meta();

        $this->assertStringContainsString('<meta name="csrf-token"', $meta);
        $this->assertStringContainsString(CSRF::token(), $meta);
    }

    /**
     * Test hidden field generation
     */
    public function testHiddenFieldGeneration(): void
    {
        $field = CSRF::field();

        $this->assertStringContainsString('<input type="hidden"', $field);
        $this->assertStringContainsString('_csrf_token', $field);
        $this->assertStringContainsString(CSRF::token(), $field);
    }

    /**
     * Test token validation passes for valid token
     */
    public function testValidTokenValidation(): void
    {
        $token = CSRF::token();
        $_POST['_csrf_token'] = $token;

        $this->assertTrue(CSRF::validate($token));
    }

    /**
     * Test token validation fails for invalid token
     */
    public function testInvalidTokenValidation(): void
    {
        CSRF::token(); // Initialize

        $this->assertFalse(CSRF::validate('invalid_token'));
    }
}
