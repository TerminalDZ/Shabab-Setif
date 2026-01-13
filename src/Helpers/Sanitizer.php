<?php
/**
 * Shabab Setif - Input Sanitizer
 * 
 * Provides XSS protection and input sanitization
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Helpers;

class Sanitizer
{
    /**
     * Sanitize string input (XSS protection)
     */
    public static function clean(mixed $input): mixed
    {
        if (is_array($input)) {
            return array_map([self::class, 'clean'], $input);
        }

        if (is_string($input)) {
            $input = trim($input);
            $input = stripslashes($input);
            $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return $input;
    }

    /**
     * Sanitize for database (removes HTML)
     */
    public static function sanitize(string $input): string
    {
        $input = trim($input);
        $input = strip_tags($input);
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Validate email
     */
    public static function email(string $email): string|false
    {
        $email = trim(strtolower($email));
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate and sanitize phone number
     */
    public static function phone(string $phone): string
    {
        return preg_replace('/[^0-9+]/', '', $phone);
    }

    /**
     * Sanitize integer
     */
    public static function int(mixed $value): int
    {
        return (int) filter_var($value, FILTER_VALIDATE_INT);
    }

    /**
     * Sanitize float
     */
    public static function float(mixed $value): float
    {
        return (float) filter_var($value, FILTER_VALIDATE_FLOAT);
    }

    /**
     * Sanitize filename
     */
    public static function filename(string $filename): string
    {
        // Remove any path components
        $filename = basename($filename);
        // Remove special characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        return $filename;
    }

    /**
     * Sanitize slug
     */
    public static function slug(string $string): string
    {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        $string = preg_replace('/-+/', '-', $string);
        return trim($string, '-');
    }

    /**
     * Decode HTML entities (for display)
     */
    public static function decode(string $input): string
    {
        return html_entity_decode($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Get clean POST data
     */
    public static function post(string $key, mixed $default = null): mixed
    {
        if (!isset($_POST[$key])) {
            return $default;
        }
        return self::clean($_POST[$key]);
    }

    /**
     * Get clean GET data
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (!isset($_GET[$key])) {
            return $default;
        }
        return self::clean($_GET[$key]);
    }

    /**
     * Get JSON body data
     */
    public static function json(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true) ?? [];
        return self::clean($data);
    }
}
