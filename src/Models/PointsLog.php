<?php
/**
 * Shabab Setif - Points Log Model
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Models;

class PointsLog extends BaseModel
{
    protected static string $table = 'points_log';

    protected static array $fillable = [
        'user_id',
        'points',
        'reason',
        'reference_type',
        'reference_id',
        'added_by',
        'date_logged'
    ];

    /**
     * Get user
     */
    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    /**
     * Get who added the points
     */
    public function addedBy(): ?User
    {
        if (!$this->added_by) {
            return null;
        }
        return User::find($this->added_by);
    }

    /**
     * Get user's points history
     */
    public static function userHistory(int $userId, int $limit = 20): array
    {
        $sql = "SELECT p.*, u.full_name as added_by_name
                FROM points_log p
                LEFT JOIN users u ON p.added_by = u.id
                WHERE p.user_id = ?
                ORDER BY p.created_at DESC
                LIMIT ?";

        return self::raw($sql, [$userId, $limit]);
    }

    /**
     * Get user's monthly points
     */
    public static function userMonthlyPoints(int $userId, int $month = null, int $year = null): int
    {
        $month = $month ?? (int) date('m');
        $year = $year ?? (int) date('Y');

        $sql = "SELECT COALESCE(SUM(points), 0) as total 
                FROM points_log 
                WHERE user_id = ? 
                AND MONTH(date_logged) = ? 
                AND YEAR(date_logged) = ?";

        $result = self::raw($sql, [$userId, $month, $year]);
        return (int) ($result[0]['total'] ?? 0);
    }

    /**
     * Get user's yearly points
     */
    public static function userYearlyPoints(int $userId, int $year = null): int
    {
        $year = $year ?? (int) date('Y');

        $sql = "SELECT COALESCE(SUM(points), 0) as total 
                FROM points_log 
                WHERE user_id = ? 
                AND YEAR(date_logged) = ?";

        $result = self::raw($sql, [$userId, $year]);
        return (int) ($result[0]['total'] ?? 0);
    }

    /**
     * Get points breakdown by type
     */
    public static function userPointsBreakdown(int $userId): array
    {
        $sql = "SELECT reference_type, SUM(points) as total, COUNT(*) as count
                FROM points_log
                WHERE user_id = ?
                GROUP BY reference_type
                ORDER BY total DESC";

        return self::raw($sql, [$userId]);
    }

    /**
     * Get recent points activity (for dashboard)
     */
    public static function recentActivity(int $limit = 10): array
    {
        $sql = "SELECT p.*, u.full_name, u.avatar
                FROM points_log p
                INNER JOIN users u ON p.user_id = u.id
                ORDER BY p.created_at DESC
                LIMIT ?";

        return self::raw($sql, [$limit]);
    }

    /**
     * Get points distribution by day (for charts)
     */
    public static function dailyDistribution(int $days = 30): array
    {
        $sql = "SELECT DATE(date_logged) as date, SUM(points) as total
                FROM points_log
                WHERE date_logged >= DATE_SUB(CURRENT_DATE(), INTERVAL ? DAY)
                GROUP BY DATE(date_logged)
                ORDER BY date ASC";

        return self::raw($sql, [$days]);
    }

    /**
     * Get points distribution by type (for pie chart)
     */
    public static function typeDistribution(): array
    {
        $sql = "SELECT reference_type, SUM(points) as total
                FROM points_log
                GROUP BY reference_type
                ORDER BY total DESC";

        return self::raw($sql);
    }
}
