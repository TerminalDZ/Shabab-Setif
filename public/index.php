<?php
/**
 * Shabab Setif - Application Entry Point
 * 
 * @package ShababSetif
 * @version 1.0.0
 */

declare(strict_types=1);

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load Composer autoloader
require BASE_PATH . '/vendor/autoload.php';

// Load configuration
require BASE_PATH . '/config/config.php';
require BASE_PATH . '/config/db.php';

// Use statements
use App\Helpers\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\UserController;
use App\Controllers\ActivityController;
use App\Controllers\CommitteeController;

// Initialize router
$router = new Router();

// ==========================================
// PUBLIC ROUTES
// ==========================================

// Authentication
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/api/auth/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->post('/api/auth/logout', [AuthController::class, 'logout']);

// ==========================================
// AUTHENTICATED ROUTES
// ==========================================

// Dashboard
$router->get('/', [DashboardController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/api/dashboard/stats', [DashboardController::class, 'stats']);
$router->get('/api/dashboard/leaderboard', [DashboardController::class, 'leaderboard']);
$router->get('/api/dashboard/chart', [DashboardController::class, 'chartData']);

// Profile
$router->get('/profile', [AuthController::class, 'profile']);
$router->post('/api/auth/profile', [AuthController::class, 'updateProfile']);
$router->post('/api/auth/password', [AuthController::class, 'changePassword']);
$router->get('/api/auth/me', [AuthController::class, 'me']);

// Users
$router->get('/users', [UserController::class, 'index']);
$router->get('/api/users', [UserController::class, 'list']);
$router->get('/api/users/{id}', [UserController::class, 'show']);
$router->post('/api/users', [UserController::class, 'store']);
$router->post('/api/users/{id}', [UserController::class, 'update']);
$router->delete('/api/users/{id}', [UserController::class, 'destroy']);
$router->post('/api/users/{id}/points', [UserController::class, 'addPoints']);
$router->get('/users/{id}/card', [UserController::class, 'card']);

// Activities
$router->get('/activities', [ActivityController::class, 'index']);
$router->get('/activities/create', [ActivityController::class, 'create']);
$router->get('/activities/feed', [ActivityController::class, 'feed']);
$router->get('/activities/{id}/edit', [ActivityController::class, 'edit']);
$router->get('/activities/{id}', [ActivityController::class, 'detail']);
$router->get('/api/activities', [ActivityController::class, 'list']);
$router->get('/api/activities/{id}', [ActivityController::class, 'show']);
$router->post('/api/activities', [ActivityController::class, 'store']);
$router->post('/api/activities/{id}', [ActivityController::class, 'update']);
$router->delete('/api/activities/{id}', [ActivityController::class, 'destroy']);
$router->post('/api/activities/{id}/attendance', [ActivityController::class, 'markAttendance']);
$router->post('/api/activities/{id}/images', [ActivityController::class, 'uploadImages']);

// Committees
$router->get('/committees', [CommitteeController::class, 'index']);
$router->get('/api/committees', [CommitteeController::class, 'list']);
$router->get('/api/committees/{id}', [CommitteeController::class, 'show']);
$router->post('/api/committees', [CommitteeController::class, 'store']);
$router->post('/api/committees/{id}', [CommitteeController::class, 'update']);
$router->delete('/api/committees/{id}', [CommitteeController::class, 'destroy']);

// ==========================================
// DISPATCH
// ==========================================

try {
    $router->dispatch();
} catch (\Exception $e) {
    // Handle errors
    if (APP_ENV === 'development') {
        http_response_code(500);
        echo '<h1>Error</h1>';
        echo '<pre>' . $e->getMessage() . '</pre>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'حدث خطأ في الخادم'
        ]);
    }
}
