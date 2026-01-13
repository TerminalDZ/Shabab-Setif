<?php
/**
 * Shabab Setif - User Model
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Models;

use App\Database;

class User extends BaseModel
{
    protected static string $table = 'users';

    protected static array $fillable = [
        'full_name',
        'email',
        'phone',
        'role',
        'member_card_id',
        'password_hash',
        'points_balance',
        'committee_id',
        'avatar',
        'is_active'
    ];

    protected static array $hidden = ['password_hash'];

    /**
     * Find user by email
     */
    public static function findByEmail(string $email): ?self
    {
        return self::findBy('email', strtolower(trim($email)));
    }

    /**
     * Find user by member card ID
     */
    public static function findByCardId(string $cardId): ?self
    {
        return self::findBy('member_card_id', $cardId);
    }

    /**
     * Generate unique member card ID
     */
    public static function generateMemberCardId(): string
    {
        $prefix = defined('CARD_PREFIX') ? CARD_PREFIX : 'SS';
        $year = defined('CARD_YEAR') ? CARD_YEAR : date('Y');

        // Get the last card number for this year
        $sql = "SELECT member_card_id FROM users 
                WHERE member_card_id LIKE ? 
                ORDER BY member_card_id DESC LIMIT 1";

        $stmt = self::db()->prepare($sql);
        $stmt->execute(["{$prefix}-{$year}-%"]);
        $last = $stmt->fetch();

        if ($last) {
            // Extract number and increment
            $parts = explode('-', $last['member_card_id']);
            $number = (int) end($parts) + 1;
        } else {
            $number = 1;
        }

        return sprintf('%s-%s-%03d', $prefix, $year, $number);
    }

    /**
     * Create new user with auto-generated card ID and hashed password
     */
    public static function register(array $data): self
    {
        // Generate member card ID
        $cardId = self::generateMemberCardId();

        // Hash password (default is card ID)
        $password = $data['password'] ?? $cardId;

        $userData = [
            'full_name' => $data['full_name'],
            'email' => strtolower(trim($data['email'])),
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'] ?? 'member',
            'member_card_id' => $cardId,
            'password_hash' => password_hash($password, PASSWORD_ALGO, PASSWORD_OPTIONS),
            'points_balance' => 0,
            'committee_id' => $data['committee_id'] ?? null,
            'is_active' => 1
        ];

        return self::create($userData);
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    /**
     * Update password
     */
    public function updatePassword(string $newPassword): bool
    {
        return $this->update([
            'password_hash' => password_hash($newPassword, PASSWORD_ALGO, PASSWORD_OPTIONS)
        ]);
    }

    /**
     * Add points to user
     */
    public function addPoints(int $points, string $reason, string $referenceType = 'manual', ?int $referenceId = null, ?int $addedBy = null): bool
    {
        Database::beginTransaction();

        try {
            // Update points balance
            $newBalance = $this->points_balance + $points;
            $this->update(['points_balance' => $newBalance]);

            // Log the points
            PointsLog::create([
                'user_id' => $this->id,
                'points' => $points,
                'reason' => $reason,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'added_by' => $addedBy,
                'date_logged' => date('Y-m-d')
            ]);

            Database::commit();
            return true;
        } catch (\Exception $e) {
            Database::rollback();
            return false;
        }
    }

    /**
     * Get user's committee
     */
    public function committee(): ?Committee
    {
        if (!$this->committee_id) {
            return null;
        }
        return Committee::find($this->committee_id);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is committee head
     */
    public function isHead(): bool
    {
        return $this->role === 'head';
    }

    /**
     * Check if user can manage (admin or head)
     */
    public function canManage(): bool
    {
        return in_array($this->role, ['admin', 'head']);
    }

    /**
     * Get monthly leaderboard
     */
    public static function getMonthlyLeaderboard(int $month = null, int $year = null, int $limit = 10): array
    {
        $month = $month ?? (int) date('m');
        $year = $year ?? (int) date('Y');

        $sql = "SELECT u.id, u.full_name, u.avatar, u.committee_id, 
                       COALESCE(SUM(p.points), 0) as monthly_points
                FROM users u
                LEFT JOIN points_log p ON u.id = p.user_id 
                    AND MONTH(p.date_logged) = ? 
                    AND YEAR(p.date_logged) = ?
                WHERE u.is_active = 1
                GROUP BY u.id
                ORDER BY monthly_points DESC
                LIMIT ?";

        return self::raw($sql, [$month, $year, $limit]);
    }

    /**
     * Get yearly leaderboard
     */
    public static function getYearlyLeaderboard(int $year = null, int $limit = 10): array
    {
        $year = $year ?? (int) date('Y');

        $sql = "SELECT u.id, u.full_name, u.avatar, u.committee_id,
                       COALESCE(SUM(p.points), 0) as yearly_points
                FROM users u
                LEFT JOIN points_log p ON u.id = p.user_id 
                    AND YEAR(p.date_logged) = ?
                WHERE u.is_active = 1
                GROUP BY u.id
                ORDER BY yearly_points DESC
                LIMIT ?";

        return self::raw($sql, [$year, $limit]);
    }

    /**
     * Get member of the month
     */
    public static function getMemberOfMonth(int $month = null, int $year = null): ?array
    {
        $leaderboard = self::getMonthlyLeaderboard($month, $year, 1);
        return $leaderboard[0] ?? null;
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): bool
    {
        $sql = "UPDATE users SET last_login_at = NOW() WHERE id = ?";
        $stmt = self::db()->prepare($sql);
        return $stmt->execute([$this->id]);
    }

    /**
     * Get user statistics
     */
    public function getStats(): array
    {
        // Activities attended
        $sql = "SELECT COUNT(*) as count FROM attendance WHERE user_id = ? AND status = 'present'";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$this->id]);
        $activitiesAttended = $stmt->fetch()['count'];

        // Monthly points
        $sql = "SELECT COALESCE(SUM(points), 0) as total FROM points_log 
                WHERE user_id = ? AND MONTH(date_logged) = MONTH(CURRENT_DATE()) 
                AND YEAR(date_logged) = YEAR(CURRENT_DATE())";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$this->id]);
        $monthlyPoints = $stmt->fetch()['total'];

        // Rank this month
        $monthlyLeaderboard = self::getMonthlyLeaderboard(null, null, 1000);
        $rank = 1;
        foreach ($monthlyLeaderboard as $member) {
            if ($member['id'] == $this->id)
                break;
            $rank++;
        }

        return [
            'points_balance' => $this->points_balance,
            'monthly_points' => (int) $monthlyPoints,
            'activities_attended' => (int) $activitiesAttended,
            'monthly_rank' => $rank
        ];
    }
}
