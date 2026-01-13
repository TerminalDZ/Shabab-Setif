<?php
/**
 * Shabab Setif - Dashboard Controller
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Models\Activity;
use App\Models\Committee;
use App\Models\PointsLog;
use App\Models\Attendance;

class DashboardController extends BaseController
{
    /**
     * Main dashboard
     */
    public function index(): void
    {
        $this->requireAuth();

        // Get statistics
        $stats = [
            'total_members' => User::count(['is_active' => 1]),
            'total_activities' => Activity::count(),
            'activities_this_month' => count(Activity::thisMonth()),
            'total_committees' => Committee::count()
        ];

        // Member of the month
        $memberOfMonth = User::getMemberOfMonth();

        // Monthly leaderboard
        $monthlyLeaderboard = User::getMonthlyLeaderboard(null, null, 5);

        // Upcoming activities
        $upcomingActivities = Activity::upcoming(5);

        // Recent activities
        $recentActivities = Activity::recent(5);

        // Recent points activity
        $recentPoints = PointsLog::recentActivity(5);

        // User specific stats
        $userStats = $this->currentUser->getStats();

        $this->view('dashboard/index', [
            'title' => 'لوحة التحكم',
            'layout' => 'main',
            'stats' => array_merge($stats, $userStats),
            'memberOfMonth' => $memberOfMonth,
            'leaderboard' => $monthlyLeaderboard,
            'upcomingActivities' => $upcomingActivities,
            'recentActivities' => $recentActivities,
            'recentPoints' => $recentPoints
        ]);
    }

    /**
     * Get dashboard stats (API)
     */
    public function stats(): void
    {
        $this->requireAuth();

        $stats = [
            'total_members' => User::count(['is_active' => 1]),
            'total_activities' => Activity::count(),
            'activities_this_month' => count(Activity::thisMonth()),
            'total_committees' => Committee::count()
        ];

        $this->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get leaderboard data (API)
     */
    public function leaderboard(): void
    {
        $this->requireAuth();

        $type = $this->query('type', 'monthly');
        $limit = (int) $this->query('limit', 10);

        if ($type === 'yearly') {
            $data = User::getYearlyLeaderboard(null, $limit);
        } else {
            $data = User::getMonthlyLeaderboard(null, null, $limit);
        }

        $this->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get chart data (API)
     */
    public function chartData(): void
    {
        $this->requireAuth();

        $type = $this->query('type', 'activities');

        switch ($type) {
            case 'activities':
                $data = $this->getActivitiesChartData();
                break;
            case 'points':
                $data = $this->getPointsChartData();
                break;
            case 'attendance':
                $data = $this->getAttendanceChartData();
                break;
            default:
                $data = [];
        }

        $this->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get activities chart data
     */
    private function getActivitiesChartData(): array
    {
        $sql = "SELECT 
                    MONTH(date) as month,
                    COUNT(*) as count
                FROM activities
                WHERE YEAR(date) = YEAR(CURRENT_DATE())
                GROUP BY MONTH(date)
                ORDER BY month";

        $results = Activity::raw($sql);

        $months = [
            'يناير',
            'فبراير',
            'مارس',
            'أبريل',
            'مايو',
            'يونيو',
            'يوليو',
            'أغسطس',
            'سبتمبر',
            'أكتوبر',
            'نوفمبر',
            'ديسمبر'
        ];

        $data = array_fill(0, 12, 0);
        foreach ($results as $row) {
            $data[$row['month'] - 1] = (int) $row['count'];
        }

        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'عدد الأنشطة',
                    'data' => $data,
                    'backgroundColor' => 'rgba(102, 126, 234, 0.5)',
                    'borderColor' => 'rgba(102, 126, 234, 1)',
                    'borderWidth' => 2
                ]
            ]
        ];
    }

    /**
     * Get points chart data
     */
    private function getPointsChartData(): array
    {
        $distribution = PointsLog::typeDistribution();

        $labels = [];
        $data = [];
        $colors = [
            'activity' => '#667eea',
            'manual' => '#f093fb',
            'social' => '#4facfe',
            'office_visit' => '#43e97b',
            'bonus' => '#fa709a',
            'penalty' => '#ff5c5c'
        ];

        $typeNames = [
            'activity' => 'حضور الأنشطة',
            'manual' => 'إضافة يدوية',
            'social' => 'التفاعل الاجتماعي',
            'office_visit' => 'زيارة المقر',
            'bonus' => 'مكافآت',
            'penalty' => 'خصومات'
        ];

        $backgroundColors = [];

        foreach ($distribution as $row) {
            $labels[] = $typeNames[$row['reference_type']] ?? $row['reference_type'];
            $data[] = (int) $row['total'];
            $backgroundColors[] = $colors[$row['reference_type']] ?? '#999999';
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors
                ]
            ]
        ];
    }

    /**
     * Get attendance chart data
     */
    private function getAttendanceChartData(): array
    {
        $sql = "SELECT 
                    a.title,
                    COUNT(CASE WHEN att.status = 'present' THEN 1 END) as present,
                    COUNT(CASE WHEN att.status = 'absent' THEN 1 END) as absent
                FROM activities a
                LEFT JOIN attendance att ON a.id = att.activity_id
                WHERE a.status = 'completed'
                ORDER BY a.date DESC
                LIMIT 10";

        $results = Activity::raw($sql);
        $results = array_reverse($results);

        $labels = [];
        $presentData = [];
        $absentData = [];

        foreach ($results as $row) {
            $labels[] = mb_substr($row['title'], 0, 15) . '...';
            $presentData[] = (int) $row['present'];
            $absentData[] = (int) $row['absent'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'حاضر',
                    'data' => $presentData,
                    'backgroundColor' => 'rgba(67, 233, 123, 0.7)',
                    'borderColor' => 'rgba(67, 233, 123, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'غائب',
                    'data' => $absentData,
                    'backgroundColor' => 'rgba(255, 92, 92, 0.7)',
                    'borderColor' => 'rgba(255, 92, 92, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];
    }
}
