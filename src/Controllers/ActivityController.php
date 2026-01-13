<?php
/**
 * Shabab Setif - Activity Controller
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Activity;
use App\Models\Committee;
use App\Models\User;
use App\Models\Attendance;

class ActivityController extends BaseController
{
    /**
     * List all activities
     */
    public function index(): void
    {
        $this->requireAuth();

        $committees = Committee::all('name', 'ASC');

        $this->view('activities/index', [
            'title' => 'إدارة الأنشطة',
            'layout' => 'main',
            'committees' => $committees
        ]);
    }

    /**
     * Get activities list (API)
     */
    public function list(): void
    {
        $this->requireAuth();

        $draw = (int) $this->query('draw', 1);
        $start = (int) $this->query('start', 0);
        $length = (int) $this->query('length', 10);
        $searchValue = $this->query('search')['value'] ?? '';
        $status = $this->query('status', '');

        $sql = "SELECT a.*, c.name as committee_name, u.full_name as creator_name,
                       (SELECT COUNT(*) FROM attendance WHERE activity_id = a.id AND status = 'present') as attendee_count
                FROM activities a 
                LEFT JOIN committees c ON a.committee_id = c.id 
                LEFT JOIN users u ON a.created_by = u.id
                WHERE 1=1";

        $params = [];

        if (!empty($searchValue)) {
            $sql .= " AND (a.title LIKE ? OR a.location LIKE ?)";
            $searchTerm = "%{$searchValue}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($status)) {
            $sql .= " AND a.status = ?";
            $params[] = $status;
        }

        // Filter by committee for heads
        if (!$this->currentUser->isAdmin() && $this->currentUser->isHead()) {
            $sql .= " AND a.committee_id = ?";
            $params[] = $this->currentUser->committee_id;
        }

        // Get total
        $countSql = preg_replace('/SELECT .* FROM/', 'SELECT COUNT(*) as total FROM', $sql);
        $countSql = preg_replace('/,\s*\(SELECT COUNT.*?\) as attendee_count/', '', $countSql);
        $totalResult = Activity::raw($countSql, $params);
        $total = $totalResult[0]['total'] ?? 0;

        // Add ordering and pagination
        $sql .= " ORDER BY a.date DESC, a.time DESC LIMIT {$length} OFFSET {$start}";

        $activities = Activity::raw($sql, $params);

        $data = array_map(function ($activity) {
            return [
                'id' => $activity['id'],
                'title' => $activity['title'],
                'date' => $activity['date'],
                'time' => $activity['time'],
                'location' => $activity['location'],
                'points_value' => $activity['points_value'],
                'status' => $activity['status'],
                'status_label' => $this->getStatusLabel($activity['status']),
                'committee_name' => $activity['committee_name'] ?? '-',
                'creator_name' => $activity['creator_name'],
                'attendee_count' => $activity['attendee_count'],
                'images_count' => count(json_decode($activity['images_json'] ?? '[]', true))
            ];
        }, $activities);

        $this->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }

    /**
     * Get single activity
     */
    public function show(string $id): void
    {
        $this->requireAuth();

        $activity = Activity::find((int) $id);

        if (!$activity) {
            $this->json(['success' => false, 'message' => 'النشاط غير موجود'], 404);
        }

        $data = $activity->toArray();
        $data['images'] = $activity->getImages();
        $data['committee'] = $activity->committee()?->toArray();
        $data['creator'] = $activity->creator()?->toArray();
        $data['attendance'] = $activity->getAttendanceCount();
        $data['attendees'] = $activity->attendees();

        $this->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Create activity form
     */
    public function create(): void
    {
        $this->requireManager();

        $committees = Committee::all('name', 'ASC');

        $this->view('activities/create', [
            'title' => 'إنشاء نشاط جديد',
            'layout' => 'main',
            'committees' => $committees
        ]);
    }

    /**
     * Edit activity form
     */
    public function edit(string $id): void
    {
        $this->requireManager();

        $activity = Activity::find((int) $id);

        if (!$activity) {
            $this->redirect('/activities');
        }

        // Check permission
        if (!$this->currentUser->isAdmin() && $activity->created_by !== $this->currentUser->id) {
            $this->redirect('/activities/' . $id);
        }

        $committees = Committee::all('name', 'ASC');

        $this->view('activities/edit', [
            'title' => 'تعديل النشاط',
            'layout' => 'main',
            'activity' => $activity,
            'committees' => $committees
        ]);
    }

    /**
     * Store new activity
     */
    public function store(): void
    {
        $this->requireManager();
        $this->validateCsrf();

        // Validate
        $title = $this->input('title');
        $date = $this->input('date');

        if (empty($title)) {
            $this->json(['success' => false, 'message' => 'عنوان النشاط مطلوب'], 400);
        }

        if (empty($date)) {
            $this->json(['success' => false, 'message' => 'تاريخ النشاط مطلوب'], 400);
        }

        // Prepare data
        $data = [
            'title' => $title,
            'description' => $this->input('description', ''),
            'date' => $date,
            'time' => $this->input('time'),
            'location' => $this->input('location', ''),
            'points_value' => (int) $this->input('points_value', POINTS_ACTIVITY_DEFAULT),
            'created_by' => $this->currentUser->id,
            'committee_id' => $this->input('committee_id') ?: $this->currentUser->committee_id,
            'status' => 'upcoming'
        ];

        $activity = Activity::create($data);

        // Image uploads are disabled as per new requirements
        if (isset($_FILES['images'])) {
            $imagePaths = $this->uploadMultipleFiles('images', 'activities', ALLOWED_IMAGE_TYPES);
            if (!empty($imagePaths)) {
                $activity->update([
                    'images_json' => json_encode($imagePaths)
                ]);
            }
        }

        $this->json([
            'success' => true,
            'message' => 'تم إنشاء النشاط بنجاح',
            'data' => ['id' => $activity->id]
        ]);
    }

    /**
     * Update activity
     */
    public function update(string $id): void
    {
        $this->requireManager();
        $this->validateCsrf();

        $activity = Activity::find((int) $id);

        if (!$activity) {
            $this->json(['success' => false, 'message' => 'النشاط غير موجود'], 404);
        }

        // Check permission
        if (!$this->currentUser->isAdmin() && $activity->created_by !== $this->currentUser->id) {
            $this->json(['success' => false, 'message' => 'غير مصرح لك بتعديل هذا النشاط'], 403);
        }

        $data = [];

        if ($title = $this->input('title'))
            $data['title'] = $title;
        if ($description = $this->input('description'))
            $data['description'] = $description;
        if ($date = $this->input('date'))
            $data['date'] = $date;
        if ($time = $this->input('time'))
            $data['time'] = $time;
        if ($location = $this->input('location'))
            $data['location'] = $location;
        if ($points = $this->input('points_value'))
            $data['points_value'] = (int) $points;
        if ($status = $this->input('status'))
            $data['status'] = $status;
        if ($committeeId = $this->input('committee_id'))
            $data['committee_id'] = $committeeId;

        // Image uploads are disabled as per new requirements
        if (isset($_FILES['images']) && $_FILES['images']['error'][0] !== UPLOAD_ERR_NO_FILE) {
            $newImages = $this->uploadMultipleFiles('images', 'activities', ALLOWED_IMAGE_TYPES);
            $existingImages = $activity->getImages();
            $data['images_json'] = json_encode(array_merge($existingImages, $newImages));
        }

        if (!empty($data)) {
            $activity->update($data);
        }

        $this->json([
            'success' => true,
            'message' => 'تم تحديث النشاط بنجاح'
        ]);
    }

    /**
     * Delete activity
     */
    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $activity = Activity::find((int) $id);

        if (!$activity) {
            $this->json(['success' => false, 'message' => 'النشاط غير موجود'], 404);
        }

        $activity->delete();

        $this->json([
            'success' => true,
            'message' => 'تم حذف النشاط بنجاح'
        ]);
    }

    /**
     * Activity detail page
     */
    public function detail(string $id): void
    {
        $this->requireAuth();

        $activity = Activity::find((int) $id);

        if (!$activity) {
            $this->redirect('/activities');
        }

        $attendance = $activity->getAttendanceCount();
        $attendees = $activity->attendees();
        $committee = $activity->committee();
        $creator = $activity->creator();
        $images = $activity->getImages();

        // Get all members for attendance marking
        $members = [];
        $attendanceMap = [];
        if ($this->currentUser->canManage()) {
            if ($activity->committee_id) {
                $members = User::where(['committee_id' => $activity->committee_id, 'is_active' => 1], 'full_name', 'ASC');
            } else {
                $members = User::where(['is_active' => 1], 'full_name', 'ASC');
            }

            // Build attendance map
            foreach ($attendees as $att) {
                $attendanceMap[$att['id']] = $att['attendance_status'];
            }
        }

        $this->view('activities/detail', [
            'title' => $activity->title,
            'layout' => 'main',
            'activity' => $activity,
            'attendance' => $attendance,
            'attendees' => $attendees,
            'attendanceMap' => $attendanceMap,
            'committee' => $committee,
            'creator' => $creator,
            'images' => $images,
            'members' => $members
        ]);
    }

    /**
     * Activity feed (timeline)
     */
    public function feed(): void
    {
        $this->requireAuth();

        // Get recent activities with all needed data
        $sql = "SELECT a.*, c.name as committee_name, u.full_name as creator_name, u.avatar as creator_avatar,
                       (SELECT COUNT(*) FROM attendance WHERE activity_id = a.id AND status = 'present') as attendee_count
                FROM activities a 
                LEFT JOIN committees c ON a.committee_id = c.id 
                LEFT JOIN users u ON a.created_by = u.id
                ORDER BY a.created_at DESC
                LIMIT 20";

        $activities = Activity::raw($sql);

        $this->view('activities/feed', [
            'title' => 'آخر الأنشطة',
            'layout' => 'main',
            'activities' => $activities
        ]);
    }

    /**
     * Mark attendance
     */
    public function markAttendance(string $id): void
    {
        $this->requireManager();
        $this->validateCsrf();

        $activity = Activity::find((int) $id);

        if (!$activity) {
            $this->json(['success' => false, 'message' => 'النشاط غير موجود'], 404);
        }

        $attendanceData = $this->input('attendance');

        if (empty($attendanceData) || !is_array($attendanceData)) {
            $this->json(['success' => false, 'message' => 'بيانات الحضور غير صالحة'], 400);
        }

        $result = Attendance::bulkMark($activity->id, $attendanceData, $this->currentUser->id);

        if ($result['success']) {
            // Mark activity as completed if it was upcoming
            if ($activity->status === 'upcoming') {
                $activity->complete();
            }

            $this->json([
                'success' => true,
                'message' => 'تم تسجيل الحضور بنجاح',
                'count' => $result['count']
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل الحضور'
            ], 500);
        }
    }

    /**
     * Upload activity images
     */
    public function uploadImages(string $id): void
    {
        $this->requireManager();
        $this->validateCsrf();

        $activity = Activity::find((int) $id);

        if (!$activity) {
            $this->json(['success' => false, 'message' => 'النشاط غير موجود'], 404);
        }

        if (!isset($_FILES['images'])) {
            $this->json(['success' => false, 'message' => 'لم يتم اختيار صور'], 400);
        }

        $newImages = $this->uploadMultipleFiles('images', 'activities', ALLOWED_IMAGE_TYPES);

        if (empty($newImages)) {
            $this->json(['success' => false, 'message' => 'فشل رفع الصور'], 500);
        }

        $existingImages = $activity->getImages();
        $activity->update([
            'images_json' => json_encode(array_merge($existingImages, $newImages))
        ]);

        $this->json([
            'success' => true,
            'message' => 'تم رفع الصور بنجاح',
            'images' => $newImages
        ]);
    }

    /**
     * Get status label
     */
    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'upcoming' => 'قادم',
            'ongoing' => 'جاري',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => $status
        };
    }
}
