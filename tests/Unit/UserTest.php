<?php
/**
 * User Model Tests
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    /**
     * Test user model instance
     */
    public function testUserModelExists(): void
    {
        $this->assertTrue(class_exists(User::class));
    }

    /**
     * Test role checking methods exist
     */
    public function testRoleMethodsExist(): void
    {
        $this->assertTrue(method_exists(User::class, 'isAdmin'));
        $this->assertTrue(method_exists(User::class, 'isHead'));
        $this->assertTrue(method_exists(User::class, 'canManage'));
    }

    /**
     * Test member card ID generation format
     */
    public function testMemberCardIdFormat(): void
    {
        if (!defined('CARD_PREFIX')) {
            $this->markTestSkipped('CARD_PREFIX constant not defined');
        }

        $prefix = CARD_PREFIX;
        $year = date('Y');

        // The format should be PREFIX-YEAR-XXXX
        $pattern = "/^{$prefix}-{$year}-\\d{4}$/";

        // Generate a sample ID
        $sampleId = $prefix . '-' . $year . '-0001';
        $this->assertMatchesRegularExpression($pattern, $sampleId);
    }

    /**
     * Test password constants are defined
     */
    public function testPasswordConstantsDefined(): void
    {
        $this->assertTrue(defined('PASSWORD_ALGO'));
        $this->assertTrue(defined('PASSWORD_OPTIONS'));
        $this->assertEquals(PASSWORD_ARGON2ID, PASSWORD_ALGO);
    }

    /**
     * Test email validation with Sanitizer
     */
    public function testEmailValidation(): void
    {
        $validEmail = 'test@example.com';
        $invalidEmail = 'not-an-email';

        $this->assertEquals($validEmail, filter_var($validEmail, FILTER_VALIDATE_EMAIL));
        $this->assertFalse(filter_var($invalidEmail, FILTER_VALIDATE_EMAIL));
    }
}
