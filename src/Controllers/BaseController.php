<?php
/**
 * Shabab Setif - Base Controller
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\CSRF;
use App\Helpers\Sanitizer;
use App\Models\User;

abstract class BaseController
{
    protected ?User $currentUser = null;

    public function __construct()
    {
        $this->loadCurrentUser();
    }

    /**
     * Load current logged in user
     */
    protected function loadCurrentUser(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->currentUser = User::find($_SESSION['user_id']);
        }
    }

    /**
     * Check if user is logged in
     */
    protected function isAuthenticated(): bool
    {
        return $this->currentUser !== null;
    }

    /**
     * Require authentication
     */
    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            if ($this->isApiRequest()) {
                $this->json(['success' => false, 'message' => 'غير مصرح'], 401);
            }
            $this->redirect('/login');
        }
    }

    /**
     * Require admin role
     */
    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (!$this->currentUser->isAdmin()) {
            if ($this->isApiRequest()) {
                $this->json(['success' => false, 'message' => 'صلاحيات غير كافية'], 403);
            }
            $this->redirect('/dashboard');
        }
    }

    /**
     * Require admin or head role
     */
    protected function requireManager(): void
    {
        $this->requireAuth();
        if (!$this->currentUser->canManage()) {
            if ($this->isApiRequest()) {
                $this->json(['success' => false, 'message' => 'صلاحيات غير كافية'], 403);
            }
            $this->redirect('/dashboard');
        }
    }

    /**
     * Validate CSRF token
     */
    protected function validateCsrf(): void
    {
        try {
            CSRF::check();
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'انتهت صلاحية الجلسة'], 403);
        }
    }

    /**
     * Render view
     */
    protected function view(string $view, array $data = []): void
    {
        // Make common data available
        $data['currentUser'] = $this->currentUser;
        $data['csrfField'] = CSRF::field();
        $data['csrfToken'] = CSRF::token();

        // Extract data to variables
        extract($data);

        // Build view path
        $viewPath = BASE_PATH . '/views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: {$view}");
        }

        // Start output buffering
        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        // If layout is specified, wrap content
        if (isset($data['layout']) && $data['layout'] !== false) {
            $layoutPath = BASE_PATH . '/views/layouts/' . $data['layout'] . '.php';
            if (file_exists($layoutPath)) {
                include $layoutPath;
                return;
            }
        }

        echo $content;
    }

    /**
     * JSON response
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirect
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Get POST data
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        return Sanitizer::post($key, $default);
    }

    /**
     * Get JSON input
     */
    protected function jsonInput(): array
    {
        return Sanitizer::json();
    }

    /**
     * Get query param
     */
    protected function query(string $key, mixed $default = null): mixed
    {
        return Sanitizer::get($key, $default);
    }

    /**
     * Check if request is API
     */
    protected function isApiRequest(): bool
    {
        $uri = $_SERVER['REQUEST_URI'];
        $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? '';

        return str_starts_with($uri, '/api/') ||
            str_contains($acceptHeader, 'application/json');
    }

    /**
     * Handle file upload
     */
    protected function uploadFile(string $fieldName, string $directory, array $allowedTypes = []): ?string
    {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$fieldName];

        // Validate file type
        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                return null;
            }
        }

        // Create directory if not exists
        $uploadPath = UPLOAD_PATH . '/' . $directory;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $fullPath = $uploadPath . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $fullPath)) {
            return '/uploads/' . $directory . '/' . $filename;
        }

        return null;
    }

    /**
     * Upload multiple files
     */
    protected function uploadMultipleFiles(string $fieldName, string $directory, array $allowedTypes = []): array
    {
        $paths = [];

        if (!isset($_FILES[$fieldName])) {
            return $paths;
        }

        $files = $_FILES[$fieldName];
        $fileCount = count($files['name']);

        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            // Validate file type
            if (!empty($allowedTypes)) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $files['tmp_name'][$i]);
                finfo_close($finfo);

                if (!in_array($mimeType, $allowedTypes)) {
                    continue;
                }
            }

            // Create directory if not exists
            $uploadPath = UPLOAD_PATH . '/' . $directory;
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate unique filename
            $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '_' . $i . '.' . $extension;
            $fullPath = $uploadPath . '/' . $filename;

            if (move_uploaded_file($files['tmp_name'][$i], $fullPath)) {
                $paths[] = '/uploads/' . $directory . '/' . $filename;
            }
        }

        return $paths;
    }
}
