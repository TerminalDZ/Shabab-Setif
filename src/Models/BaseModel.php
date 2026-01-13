<?php
/**
 * Shabab Setif - Base Model
 * 
 * Abstract base class for all models with common CRUD operations
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Models;

use App\Database;
use PDO;

abstract class BaseModel
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [];
    protected static array $hidden = [];

    protected array $attributes = [];
    protected bool $exists = false;

    /**
     * Get PDO instance
     */
    protected static function db(): PDO
    {
        return Database::getInstance();
    }

    /**
     * Find record by ID
     */
    public static function find(int $id): ?static
    {
        $sql = "SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return static::hydrate($data);
    }

    /**
     * Find record by column value
     */
    public static function findBy(string $column, mixed $value): ?static
    {
        $sql = "SELECT * FROM " . static::$table . " WHERE {$column} = ?";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$value]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return static::hydrate($data);
    }

    /**
     * Get all records
     */
    public static function all(string $orderBy = 'id', string $direction = 'DESC'): array
    {
        $sql = "SELECT * FROM " . static::$table . " ORDER BY {$orderBy} {$direction}";
        $stmt = self::db()->query($sql);
        $results = $stmt->fetchAll();

        return array_map(fn($row) => static::hydrate($row), $results);
    }

    /**
     * Get records with conditions
     */
    public static function where(array $conditions, string $orderBy = 'id', string $direction = 'DESC'): array
    {
        $whereClauses = [];
        $params = [];

        foreach ($conditions as $column => $value) {
            if (is_array($value)) {
                $whereClauses[] = "{$column} {$value[0]} ?";
                $params[] = $value[1];
            } else {
                $whereClauses[] = "{$column} = ?";
                $params[] = $value;
            }
        }

        $sql = "SELECT * FROM " . static::$table . " WHERE " . implode(' AND ', $whereClauses) . " ORDER BY {$orderBy} {$direction}";
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();

        return array_map(fn($row) => static::hydrate($row), $results);
    }

    /**
     * Count records
     */
    public static function count(array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) as count FROM " . static::$table;
        $params = [];

        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $column => $value) {
                $whereClauses[] = "{$column} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetch()['count'];
    }

    /**
     * Create new record
     */
    public static function create(array $data): static
    {
        $data = static::filterFillable($data);
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO " . static::$table . " ({$columns}) VALUES ({$placeholders})";
        $stmt = self::db()->prepare($sql);
        $stmt->execute(array_values($data));

        $id = (int) self::db()->lastInsertId();
        return static::find($id);
    }

    /**
     * Update record
     */
    public function update(array $data): bool
    {
        $data = static::filterFillable($data);
        $setClauses = [];

        foreach (array_keys($data) as $column) {
            $setClauses[] = "{$column} = ?";
        }

        $sql = "UPDATE " . static::$table . " SET " . implode(', ', $setClauses) . " WHERE " . static::$primaryKey . " = ?";
        $params = array_values($data);
        $params[] = $this->attributes[static::$primaryKey];

        $stmt = self::db()->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            $this->attributes = array_merge($this->attributes, $data);
        }

        return $result;
    }

    /**
     * Delete record
     */
    public function delete(): bool
    {
        $sql = "DELETE FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?";
        $stmt = self::db()->prepare($sql);
        return $stmt->execute([$this->attributes[static::$primaryKey]]);
    }

    /**
     * Static delete by ID
     */
    public static function destroy(int $id): bool
    {
        $sql = "DELETE FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?";
        $stmt = self::db()->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Paginate results
     */
    public static function paginate(int $page = 1, int $perPage = 15, array $conditions = [], string $orderBy = 'id', string $direction = 'DESC'): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];

        $sql = "SELECT * FROM " . static::$table;

        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $column => $value) {
                $whereClauses[] = "{$column} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $sql .= " ORDER BY {$orderBy} {$direction} LIMIT {$perPage} OFFSET {$offset}";

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();

        $total = static::count($conditions);

        return [
            'data' => array_map(fn($row) => static::hydrate($row), $results),
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => (int) ceil($total / $perPage)
        ];
    }

    /**
     * Filter data by fillable fields
     */
    protected static function filterFillable(array $data): array
    {
        if (empty(static::$fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip(static::$fillable));
    }

    /**
     * Hydrate model from data
     */
    protected static function hydrate(array $data): static
    {
        $instance = new static();
        $instance->attributes = $data;
        $instance->exists = true;
        return $instance;
    }

    /**
     * Get attribute
     */
    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Set attribute
     */
    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Check if attribute exists
     */
    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        $data = $this->attributes;

        foreach (static::$hidden as $field) {
            unset($data[$field]);
        }

        return $data;
    }

    /**
     * Convert to JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * Execute raw query
     */
    public static function raw(string $sql, array $params = []): array
    {
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
