<?php
/**
 * Shabab Setif - Database Connection Singleton
 * 
 * Provides a single PDO instance throughout the application
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static int $transactionDepth = 0;

    // Database Configuration (DDEV defaults)
    private const DB_HOST = 'db';
    private const DB_PORT = 3306;
    private const DB_NAME = 'db';
    private const DB_USER = 'db';
    private const DB_PASS = 'db';
    private const DB_CHARSET = 'utf8mb4';

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
    }

    /**
     * Prevent cloning
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    /**
     * Get the singleton PDO instance
     * 
     * @return PDO
     * @throws PDOException
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                    self::DB_HOST,
                    self::DB_PORT,
                    self::DB_NAME,
                    self::DB_CHARSET
                );

                self::$instance = new PDO($dsn, self::DB_USER, self::DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]);
            } catch (PDOException $e) {
                // Log error in production, show in development
                if (defined('APP_ENV') && APP_ENV === 'development') {
                    throw new PDOException("Database connection failed: " . $e->getMessage());
                }
                throw new PDOException("Database connection failed. Please try again later.");
            }
        }

        return self::$instance;
    }

    /**
     * Execute a query with parameters
     * 
     * @param string $sql
     * @param array $params
     * @return \PDOStatement
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Get last insert ID
     * 
     * @return string
     */
    public static function lastInsertId(): string
    {
        return self::getInstance()->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public static function beginTransaction(): bool
    {
        if (self::$transactionDepth === 0) {
            $result = self::getInstance()->beginTransaction();
            if ($result) {
                self::$transactionDepth++;
            }
            return $result;
        } else {
            self::$transactionDepth++;
            return true;
        }
    }

    /**
     * Commit transaction
     */
    public static function commit(): bool
    {
        if (self::$transactionDepth === 1) {
            $result = self::getInstance()->commit();
            if ($result) {
                self::$transactionDepth--;
            }
            return $result;
        } else {
            if (self::$transactionDepth > 0) {
                self::$transactionDepth--;
            }
            return true;
        }
    }

    /**
     * Rollback transaction
     */
    public static function rollback(): bool
    {
        if (self::$transactionDepth > 0) {
            $result = self::getInstance()->rollBack();
            self::$transactionDepth = 0;
            return $result;
        }
        return false;
    }
}
