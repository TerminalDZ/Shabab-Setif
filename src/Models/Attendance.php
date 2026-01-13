<?php
/**
 * Shabab Setif - Attendance Model
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Models;

use App\Database;

class Attendance extends BaseModel
{
    protected static string $table = 'attendance';

    protected static array $fillable = [
        'activity_id',
        'user_id',
        'status',
        'marked_by',
        'notes'
    ];

    /**
     * Mark attendance for a user
     */
    public static function mark(int $activityId, int $userId, string $status = 'present', ?int $markedBy = null, ?string $notes = null): self|bool
    {
        // Check if already marked
        $existing = self::findBy2('activity_id', $activityId, 'user_id', $userId);

        if ($existing) {
            $existing->update([
                'status' => $status,
                'marked_by' => $markedBy,
                'notes' => $notes
            ]);
            return $existing;
        }

        // Create new attendance record
        $attendance = self::create([
            'activity_id' => $activityId,
            'user_id' => $userId,
            'status' => $status,
            'marked_by' => $markedBy,
            'notes' => $notes
        ]);

        // Award points if present
        if ($status === 'present') {
            $activity = Activity::find($activityId);
            $user = User::find($userId);

            if ($activity && $user) {
                $user->addPoints(
                    $activity->points_value,
                    "حضور نشاط: {$activity->title}",
                    'activity',
                    $activityId,
                    $markedBy
                );
            }
        }

        return $attendance;
    }

    /**
     * Find by two columns
     */
    public static function findBy2(string $col1, mixed $val1, string $col2, mixed $val2): ?self
    {
        $sql = "SELECT * FROM " . static::$table . " WHERE {$col1} = ? AND {$col2} = ?";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$val1, $val2]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return static::hydrate($data);
    }

    /**
     * Bulk mark attendance
     */
    public static function bulkMark(int $activityId, array $attendanceData, int $markedBy): array
    {
        $results = [];

        Database::beginTransaction();

        try {
            foreach ($attendanceData as $userId => $status) {
                $results[] = self::mark($activityId, (int) $userId, $status, $markedBy);
            }

            Database::commit();
            return ['success' => true, 'count' => count($results)];
        } catch (\Exception $e) {
            Database::rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get user
     */
    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    /**
     * Get activity
     */
    public function activity(): ?Activity
    {
        return Activity::find($this->activity_id);
    }

    /**
     * Get attendance history for user
     */
    public static function userHistory(int $userId, int $limit = 20): array
    {
        $sql = "SELECT a.*, a.timestamp as marked_at, act.title as activity_title, act.date as activity_date
                FROM attendance a
                INNER JOIN activities act ON a.activity_id = act.id
                WHERE a.user_id = ?
                ORDER BY a.timestamp DESC
                LIMIT ?";

        return self::raw($sql, [$userId, $limit]);
    }

    /**
     * Get attendance rate for user
     */
    public static function userAttendanceRate(int $userId): float
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present
                FROM attendance WHERE user_id = ?";

        $result = self::raw($sql, [$userId]);

        if (empty($result) || $result[0]['total'] == 0) {
            return 0;
        }

        return round(($result[0]['present'] / $result[0]['total']) * 100, 1);
    }
}
