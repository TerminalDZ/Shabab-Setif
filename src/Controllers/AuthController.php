<?php
/**
 * Shabab Setif - Auth Controller
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Helpers\CSRF;
use App\Helpers\Mailer;

class AuthController extends BaseController
{
    /**
     * Show login page
     */
    public function showLogin(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
        }

        $this->view('auth/login', [
            'title' => 'تسجيل الدخول',
            'layout' => false
        ]);
    }

    /**
     * Process login
     */
    public function login(): void
    {
        $this->validateCsrf();

        $email = $this->input('email');
        $password = $this->input('password');

        // Validate input
        if (empty($email) || empty($password)) {
            $this->json([
                'success' => false,
                'message' => 'يرجى إدخال البريد الإلكتروني وكلمة المرور'
            ], 400);
        }

        // Find user
        $user = User::findByEmail($email);

        if (!$user) {
            $this->json([
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة'
            ], 401);
        }

        // Check if active
        if (!$user->is_active) {
            $this->json([
                'success' => false,
                'message' => 'الحساب غير مفعل. يرجى التواصل مع الإدارة'
            ], 403);
        }

        // Verify password
        if (!$user->verifyPassword($password)) {
            $this->json([
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة'
            ], 401);
        }

        // Update last login
        $user->updateLastLogin();

        // Set session
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_role'] = $user->role;

        // Regenerate session ID for security
        session_regenerate_id(true);

        $this->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'redirect' => '/dashboard'
        ]);
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        // Clear session
        $_SESSION = [];

        // Destroy session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy session
        session_destroy();

        if ($this->isApiRequest()) {
            $this->json([
                'success' => true,
                'message' => 'تم تسجيل الخروج',
                'redirect' => '/login'
            ]);
        }

        $this->redirect('/login');
    }

    /**
     * Show profile
     */
    public function profile(): void
    {
        $this->requireAuth();

        $stats = $this->currentUser->getStats();
        $pointsHistory = \App\Models\PointsLog::userHistory($this->currentUser->id, 10);
        $attendanceHistory = \App\Models\Attendance::userHistory($this->currentUser->id, 10);

        $this->view('auth/profile', [
            'title' => 'الملف الشخصي',
            'layout' => 'main',
            'stats' => $stats,
            'pointsHistory' => $pointsHistory,
            'attendanceHistory' => $attendanceHistory
        ]);
    }

    /**
     * Update profile
     */
    public function updateProfile(): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $data = [];

        // Update allowed fields
        if ($fullName = $this->input('full_name')) {
            $data['full_name'] = $fullName;
        }

        if ($phone = $this->input('phone')) {
            $data['phone'] = $phone;
        }

        // Avatar upload disabled as per new requirements
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatarPath = $this->uploadFile('avatar', 'avatars', ALLOWED_IMAGE_TYPES);
            if ($avatarPath) {
                $data['avatar'] = $avatarPath;
            }
        }

        if (empty($data)) {
            $this->json([
                'success' => false,
                'message' => 'لا توجد بيانات للتحديث'
            ], 400);
        }

        $this->currentUser->update($data);

        $this->json([
            'success' => true,
            'message' => 'تم تحديث الملف الشخصي بنجاح'
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $currentPassword = $this->input('current_password');
        $newPassword = $this->input('new_password');
        $confirmPassword = $this->input('confirm_password');

        // Validate input
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $this->json([
                'success' => false,
                'message' => 'يرجى ملء جميع الحقول'
            ], 400);
        }

        // Check current password
        if (!$this->currentUser->verifyPassword($currentPassword)) {
            $this->json([
                'success' => false,
                'message' => 'كلمة المرور الحالية غير صحيحة'
            ], 400);
        }

        // Check password confirmation
        if ($newPassword !== $confirmPassword) {
            $this->json([
                'success' => false,
                'message' => 'كلمة المرور الجديدة غير متطابقة'
            ], 400);
        }

        // Check password strength
        if (strlen($newPassword) < 6) {
            $this->json([
                'success' => false,
                'message' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل'
            ], 400);
        }

        // Update password
        $this->currentUser->updatePassword($newPassword);

        $this->json([
            'success' => true,
            'message' => 'تم تغيير كلمة المرور بنجاح'
        ]);
    }

    /**
     * Get current user data (API)
     */
    public function me(): void
    {
        $this->requireAuth();

        $userData = $this->currentUser->toArray();
        $userData['stats'] = $this->currentUser->getStats();

        if ($this->currentUser->committee_id) {
            $committee = $this->currentUser->committee();
            $userData['committee_name'] = $committee ? $committee->name : null;
        }

        $this->json([
            'success' => true,
            'data' => $userData
        ]);
    }
}
