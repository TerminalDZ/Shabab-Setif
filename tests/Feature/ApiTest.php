<?php
/**
 * API Feature Tests
 */

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    private string $baseUrl = 'https://chababsetif.iba';

    /**
     * Test login page is accessible
     */
    public function testLoginPageAccessible(): void
    {
        $ch = curl_init($this->baseUrl . '/login');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $httpCode);
        $this->assertStringContainsString('تسجيل الدخول', $response);
    }

    /**
     * Test unauthenticated dashboard redirects to login
     */
    public function testDashboardRequiresAuth(): void
    {
        $ch = curl_init($this->baseUrl . '/dashboard');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HEADER => true
        ]);

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Should redirect to login (302)
        $this->assertEquals(302, $httpCode);
    }

    /**
     * Test API returns JSON for unauthenticated request
     */
    public function testApiReturnsJsonForUnauth(): void
    {
        $ch = curl_init($this->baseUrl . '/api/users');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => ['Accept: application/json']
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(401, $httpCode);

        $data = json_decode($response, true);
        $this->assertIsArray($data);
        $this->assertFalse($data['success']);
    }

    /**
     * Test CSRF token is present on login page
     */
    public function testCsrfTokenPresent(): void
    {
        $ch = curl_init($this->baseUrl . '/login');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $this->assertStringContainsString('csrf-token', $response);
    }

    /**
     * Test 404 page for non-existent route
     */
    public function testNotFoundPage(): void
    {
        $ch = curl_init($this->baseUrl . '/non-existent-page-12345');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(404, $httpCode);
        $this->assertStringContainsString('404', $response);
    }

    /**
     * Test static assets are accessible
     */
    public function testCssAssetAccessible(): void
    {
        $ch = curl_init($this->baseUrl . '/assets/css/app.css');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_NOBODY => true
        ]);

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $httpCode);
    }
}
