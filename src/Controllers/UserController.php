<?php
/**
 * Shabab Setif - User Controller
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Models\Committee;
use App\Models\PointsLog;
use App\Helpers\Mailer;
use App\Helpers\Sanitizer;

class UserController extends BaseController
{
    /**
     * List all members
     */
    public function index(): void
    {
        $this->requireAuth();

        $committees = Committee::all('name', 'ASC');

        $this->view('users/index', [
            'title' => 'إدارة الأعضاء',
            'layout' => 'main',
            'committees' => $committees
        ]);
    }

    /**
     * Get members list (API - DataTables)
     */
    public function list(): void
    {
        $this->requireAuth();

        // DataTables parameters
        $draw = (int) $this->query('draw', 1);
        $start = (int) $this->query('start', 0);
        $length = (int) $this->query('length', 10);
        $searchValue = $this->query('search')['value'] ?? '';

        // Build query
        $sql = "SELECT u.*, c.name as committee_name 
                FROM users u 
                LEFT JOIN committees c ON u.committee_id = c.id 
                WHERE u.is_active = 1";

        $params = [];

        if (!empty($searchValue)) {
            $sql .= " AND (u.full_name LIKE ? OR u.email LIKE ? OR u.member_card_id LIKE ?)";
            $searchTerm = "%{$searchValue}%";
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }

        // Filter by role if not admin
        if (!$this->currentUser->isAdmin()) {
            $sql .= " AND u.committee_id = ?";
            $params[] = $this->currentUser->committee_id;
        }

        // Get total count
        $countSql = str_replace('u.*, c.name as committee_name', 'COUNT(*) as total', $sql);
        $totalResult = User::raw($countSql, $params);
        $total = $totalResult[0]['total'] ?? 0;

        // Add ordering and pagination
        $sql .= " ORDER BY u.created_at DESC LIMIT {$length} OFFSET {$start}";

        $users = User::raw($sql, $params);

        // Format for DataTables
        $data = array_map(function ($user) {
            return [
                'id' => $user['id'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'member_card_id' => $user['member_card_id'],
                'role' => $user['role'],
                'role_label' => $this->getRoleLabel($user['role']),
                'committee_name' => $user['committee_name'] ?? '-',
                'points_balance' => $user['points_balance'],
                'avatar' => $user['avatar'] ?? '/assets/images/default-avatar.png',
                'created_at' => date('Y-m-d', strtotime($user['created_at']))
            ];
        }, $users);

        $this->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }

    /**
     * Get single member
     */
    public function show(string $id): void
    {
        $this->requireAuth();

        $user = User::find((int) $id);

        if (!$user) {
            $this->json(['success' => false, 'message' => 'العضو غير موجود'], 404);
        }

        $data = $user->toArray();
        $data['stats'] = $user->getStats();
        $data['committee'] = $user->committee()?->toArray();
        $data['points_history'] = PointsLog::userHistory($user->id, 5);

        $this->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Create new member
     */
    public function store(): void
    {
        $this->requireManager();
        $this->validateCsrf();

        // Validate input
        $email = Sanitizer::email($this->input('email'));
        if (!$email) {
            $this->json([
                'success' => false,
                'message' => 'البريد الإلكتروني غير صالح'
            ], 400);
        }

        // Check if email exists
        if (User::findByEmail($email)) {
            $this->json([
                'success' => false,
                'message' => 'البريد الإلكتروني مستخدم مسبقاً'
            ], 400);
        }

        $fullName = $this->input('full_name');
        if (empty($fullName)) {
            $this->json([
                'success' => false,
                'message' => 'الاسم الكامل مطلوب'
            ], 400);
        }

        // Prepare data
        $data = [
            'full_name' => $fullName,
            'email' => $email,
            'phone' => Sanitizer::phone($this->input('phone', '')),
            'role' => $this->input('role', 'member'),
            'committee_id' => $this->input('committee_id') ?: null
        ];

        // Only admin can set admin role
        if ($data['role'] === 'admin' && !$this->currentUser->isAdmin()) {
            $data['role'] = 'member';
        }

        // Create user
        $user = User::register($data);

        // Handle avatar upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatarPath = $this->uploadFile('avatar', 'avatars', ALLOWED_IMAGE_TYPES);
            if ($avatarPath) {
                $user->update(['avatar' => $avatarPath]);
            }
        }

        // Send welcome email
        try {
            $mailer = new Mailer();
            $mailer->sendWelcomeEmail($user->email, $user->full_name, $user->member_card_id);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            error_log("Failed to send welcome email: " . $e->getMessage());
        }

        $this->json([
            'success' => true,
            'message' => 'تم إضافة العضو بنجاح',
            'data' => [
                'id' => $user->id,
                'member_card_id' => $user->member_card_id
            ]
        ]);
    }

    /**
     * Update member
     */
    public function update(string $id): void
    {
        $this->requireManager();
        $this->validateCsrf();

        $user = User::find((int) $id);

        if (!$user) {
            $this->json(['success' => false, 'message' => 'العضو غير موجود'], 404);
        }

        // Check permission
        if (!$this->currentUser->isAdmin() && $user->committee_id !== $this->currentUser->committee_id) {
            $this->json(['success' => false, 'message' => 'غير مصرح لك بتعديل هذا العضو'], 403);
        }

        $data = [];

        if ($fullName = $this->input('full_name')) {
            $data['full_name'] = $fullName;
        }

        if ($phone = $this->input('phone')) {
            $data['phone'] = Sanitizer::phone($phone);
        }

        $email = $this->input('email');
        if ($email && $email !== $user->email) {
            $email = Sanitizer::email($email);
            if (!$email) {
                $this->json(['success' => false, 'message' => 'البريد الإلكتروني غير صالح'], 400);
            }
            if (User::findByEmail($email)) {
                $this->json(['success' => false, 'message' => 'البريد الإلكتروني مستخدم مسبقاً'], 400);
            }
            $data['email'] = $email;
        }

        // Only admin can change role and committee
        if ($this->currentUser->isAdmin()) {
            if ($role = $this->input('role')) {
                $data['role'] = $role;
            }
            if ($committeeId = $this->input('committee_id')) {
                $data['committee_id'] = $committeeId;
            }
        }

        // Handle avatar
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatarPath = $this->uploadFile('avatar', 'avatars', ALLOWED_IMAGE_TYPES);
            if ($avatarPath) {
                $data['avatar'] = $avatarPath;
            }
        }

        if (!empty($data)) {
            $user->update($data);
        }

        $this->json([
            'success' => true,
            'message' => 'تم تحديث بيانات العضو بنجاح'
        ]);
    }

    /**
     * Delete member
     */
    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $user = User::find((int) $id);

        if (!$user) {
            $this->json(['success' => false, 'message' => 'العضو غير موجود'], 404);
        }

        // Prevent self deletion
        if ($user->id === $this->currentUser->id) {
            $this->json(['success' => false, 'message' => 'لا يمكنك حذف حسابك'], 400);
        }

        // Soft delete (deactivate)
        $user->update(['is_active' => 0]);

        $this->json([
            'success' => true,
            'message' => 'تم حذف العضو بنجاح'
        ]);
    }

    /**
     * Add points to member
     */
    public function addPoints(string $id): void
    {
        $this->requireManager();
        $this->validateCsrf();

        $user = User::find((int) $id);

        if (!$user) {
            $this->json(['success' => false, 'message' => 'العضو غير موجود'], 404);
        }

        $points = (int) $this->input('points');
        $reason = $this->input('reason');
        $type = $this->input('type', 'manual');

        if ($points === 0) {
            $this->json(['success' => false, 'message' => 'يرجى إدخال عدد النقاط'], 400);
        }

        if (empty($reason)) {
            $this->json(['success' => false, 'message' => 'يرجى إدخال سبب النقاط'], 400);
        }

        $user->addPoints($points, $reason, $type, null, $this->currentUser->id);

        $this->json([
            'success' => true,
            'message' => 'تم إضافة النقاط بنجاح',
            'new_balance' => $user->points_balance + $points
        ]);
    }

    /**
     * Show membership card
     */
    public function card(string $id): void
    {
        $this->requireAuth();

        $user = User::find((int) $id);

        if (!$user) {
            $this->redirect('/users');
        }

        // Check permission
        if (!$this->currentUser->isAdmin() && $user->id !== $this->currentUser->id) {
            $this->redirect('/dashboard');
        }

        $committee = $user->committee();

        $this->view('users/card', [
            'title' => 'بطاقة العضوية',
            'layout' => false,
            'user' => $user,
            'committee' => $committee
        ]);
    }

    /**
     * Get role label in Arabic
     */
    private function getRoleLabel(string $role): string
    {
        return match ($role) {
            'admin' => 'مدير',
            'head' => 'رئيس لجنة',
            'member' => 'عضو',
            default => $role
        };
    }
}
