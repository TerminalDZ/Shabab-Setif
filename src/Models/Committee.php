<?php
/**
 * Shabab Setif - Committee Model
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Models;

class Committee extends BaseModel
{
    protected static string $table = 'committees';

    protected static array $fillable = [
        'name',
        'description'
    ];

    /**
     * Get all members of this committee
     */
    public function members(): array
    {
        return User::where(['committee_id' => $this->id]);
    }

    /**
     * Get member count
     */
    public function memberCount(): int
    {
        return User::count(['committee_id' => $this->id]);
    }

    /**
     * Get committee head
     */
    public function head(): ?User
    {
        $heads = User::where([
            'committee_id' => $this->id,
            'role' => 'head'
        ]);
        return $heads[0] ?? null;
    }

    /**
     * Get activities for this committee
     */
    public function activities(): array
    {
        return Activity::where(['committee_id' => $this->id], 'date', 'DESC');
    }

    /**
     * Get all committees with member count
     */
    public static function allWithStats(): array
    {
        $sql = "SELECT c.*, 
                       COUNT(DISTINCT u.id) as member_count,
                       COUNT(DISTINCT a.id) as activity_count
                FROM committees c
                LEFT JOIN users u ON c.id = u.committee_id AND u.is_active = 1
                LEFT JOIN activities a ON c.id = a.committee_id
                GROUP BY c.id
                ORDER BY c.name";

        return self::raw($sql);
    }
}
