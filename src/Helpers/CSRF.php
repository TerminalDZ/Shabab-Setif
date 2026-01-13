<?php
/**
 * Shabab Setif - CSRF Token Handler
 * 
 * Provides CSRF protection for forms
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Helpers;

class CSRF
{
    private const TOKEN_NAME = '_csrf_token';
    private const TOKEN_LIFETIME = 3600; // 1 hour

    /**
     * Generate a new CSRF token
     */
    public static function generate(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::TOKEN_NAME] = [
            'token' => $token,
            'expires' => time() + self::TOKEN_LIFETIME
        ];
        return $token;
    }

    /**
     * Get current token or generate new one
     */
    public static function token(): string
    {
        if (
            !isset($_SESSION[self::TOKEN_NAME]) ||
            $_SESSION[self::TOKEN_NAME]['expires'] < time()
        ) {
            return self::generate();
        }
        return $_SESSION[self::TOKEN_NAME]['token'];
    }

    /**
     * Validate CSRF token
     */
    public static function validate(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        if (!isset($_SESSION[self::TOKEN_NAME])) {
            return false;
        }

        $storedData = $_SESSION[self::TOKEN_NAME];

        // Check expiration
        if ($storedData['expires'] < time()) {
            self::invalidate();
            return false;
        }

        // Timing-safe comparison
        return hash_equals($storedData['token'], $token);
    }

    /**
     * Validate and throw exception on failure
     */
    public static function check(): void
    {
        $token = $_POST[self::TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!self::validate($token)) {
            http_response_code(403);
            throw new \Exception('CSRF token validation failed');
        }
    }

    /**
     * Get hidden input field HTML
     */
    public static function field(): string
    {
        return '<input type="hidden" name="' . self::TOKEN_NAME . '" value="' . self::token() . '">';
    }

    /**
     * Get meta tag for AJAX requests
     */
    public static function meta(): string
    {
        return '<meta name="csrf-token" content="' . self::token() . '">';
    }

    /**
     * Invalidate current token
     */
    public static function invalidate(): void
    {
        unset($_SESSION[self::TOKEN_NAME]);
    }
}
