<?php
/**
 * Shabab Setif - Activity Model
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Models;

class Activity extends BaseModel
{
    protected static string $table = 'activities';

    protected static array $fillable = [
        'title',
        'description',
        'date',
        'time',
        'location',
        'points_value',
        'created_by',
        'committee_id',
        'images_json',
        'status'
    ];

    /**
     * Get creator user
     */
    public function creator(): ?User
    {
        return User::find($this->created_by);
    }

    /**
     * Get committee
     */
    public function committee(): ?Committee
    {
        if (!$this->committee_id) {
            return null;
        }
        return Committee::find($this->committee_id);
    }

    /**
     * Get all attendees
     */
    public function attendees(): array
    {
        $sql = "SELECT u.*, a.status as attendance_status, a.timestamp as marked_at
                FROM users u
                INNER JOIN attendance a ON u.id = a.user_id
                WHERE a.activity_id = ?
                ORDER BY u.full_name";

        return self::raw($sql, [$this->id]);
    }

    /**
     * Get attendance count
     */
    public function getAttendanceCount(): array
    {
        $sql = "SELECT 
                    COUNT(CASE WHEN status = 'present' THEN 1 END) as present,
                    COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent,
                    COUNT(CASE WHEN status = 'excused' THEN 1 END) as excused,
                    COUNT(*) as total
                FROM attendance WHERE activity_id = ?";

        $result = self::raw($sql, [$this->id]);
        return $result[0] ?? ['present' => 0, 'absent' => 0, 'excused' => 0, 'total' => 0];
    }

    /**
     * Get images as array
     */
    public function getImages(): array
    {
        if (empty($this->images_json)) {
            return [];
        }
        return json_decode($this->images_json, true) ?? [];
    }

    /**
     * Add image to activity
     */
    public function addImage(string $imagePath): bool
    {
        $images = $this->getImages();
        $images[] = $imagePath;

        return $this->update([
            'images_json' => json_encode($images)
        ]);
    }

    /**
     * Get upcoming activities
     */
    public static function upcoming(int $limit = 5): array
    {
        $sql = "SELECT a.*, c.name as committee_name, u.full_name as creator_name
                FROM activities a
                LEFT JOIN committees c ON a.committee_id = c.id
                LEFT JOIN users u ON a.created_by = u.id
                WHERE a.date >= CURRENT_DATE() AND a.status != 'cancelled'
                ORDER BY a.date ASC, a.time ASC
                LIMIT ?";

        return self::raw($sql, [$limit]);
    }

    /**
     * Get recent activities (completed)
     */
    public static function recent(int $limit = 10): array
    {
        $sql = "SELECT a.*, c.name as committee_name, u.full_name as creator_name,
                       (SELECT COUNT(*) FROM attendance WHERE activity_id = a.id AND status = 'present') as attendee_count
                FROM activities a
                LEFT JOIN committees c ON a.committee_id = c.id
                LEFT JOIN users u ON a.created_by = u.id
                WHERE a.status = 'completed'
                ORDER BY a.date DESC
                LIMIT ?";

        return self::raw($sql, [$limit]);
    }

    /**
     * Get activities for this month
     */
    public static function thisMonth(): array
    {
        $sql = "SELECT a.*, c.name as committee_name
                FROM activities a
                LEFT JOIN committees c ON a.committee_id = c.id
                WHERE MONTH(a.date) = MONTH(CURRENT_DATE()) 
                AND YEAR(a.date) = YEAR(CURRENT_DATE())
                ORDER BY a.date DESC";

        return self::raw($sql);
    }

    /**
     * Get activity statistics
     */
    public static function getStats(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
                    COUNT(CASE WHEN status = 'upcoming' THEN 1 END) as upcoming,
                    COUNT(CASE WHEN MONTH(date) = MONTH(CURRENT_DATE()) AND YEAR(date) = YEAR(CURRENT_DATE()) THEN 1 END) as this_month
                FROM activities";

        $result = self::raw($sql);
        return $result[0] ?? ['total' => 0, 'completed' => 0, 'upcoming' => 0, 'this_month' => 0];
    }

    /**
     * Mark activity as completed
     */
    public function complete(): bool
    {
        return $this->update(['status' => 'completed']);
    }

    /**
     * Cancel activity
     */
    public function cancel(): bool
    {
        return $this->update(['status' => 'cancelled']);
    }
}
