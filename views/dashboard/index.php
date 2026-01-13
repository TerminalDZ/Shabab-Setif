<!-- Dashboard Page -->
<div class="container-fluid py-4">
    <!-- Welcome Card -->
    <div class="welcome-card mb-4">
        <div class="welcome-content">
            <h2>مرحباً، <?= htmlspecialchars($currentUser->full_name) ?> 👋</h2>
            <p>نتمنى لك يوماً مليئاً بالنشاط والإنجازات</p>
        </div>
        <div class="welcome-stats">
            <div class="mini-stat">
                <i class="bi bi-star-fill"></i>
                <div>
                    <span class="stat-value"><?= number_format($stats['points_balance'] ?? 0) ?></span>
                    <span class="stat-label">نقاطك</span>
                </div>
            </div>
            <div class="mini-stat">
                <i class="bi bi-trophy-fill"></i>
                <div>
                    <span class="stat-value">#<?= $stats['monthly_rank'] ?? '-' ?></span>
                    <span class="stat-label">ترتيبك الشهري</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-info">
                    <h3><?= number_format($stats['total_members'] ?? 0) ?></h3>
                    <p>إجمالي الأعضاء</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-card-success">
                <div class="stat-icon"><i class="bi bi-calendar-event"></i></div>
                <div class="stat-info">
                    <h3><?= number_format($stats['total_activities'] ?? 0) ?></h3>
                    <p>إجمالي الأنشطة</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
                <div class="stat-info">
                    <h3><?= number_format($stats['monthly_activities'] ?? 0) ?></h3>
                    <p>أنشطة هذا الشهر</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-card-info">
                <div class="stat-icon"><i class="bi bi-diagram-3"></i></div>
                <div class="stat-info">
                    <h3><?= number_format($stats['total_committees'] ?? 0) ?></h3>
                    <p>اللجان</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-4">
            <!-- Member of the Month -->
            <div class="card member-of-month-card mb-4">
                <div class="card-body text-center">
                    <div class="mom-avatar">
                        <img src="<?= $memberOfMonth['avatar'] ?? '/assets/images/default-avatar.png' ?>" alt="">
                        <div class="mom-crown"><i class="bi bi-trophy-fill"></i></div>
                    </div>
                    <h5 class="mom-name"><?= htmlspecialchars($memberOfMonth['full_name'] ?? 'لا يوجد') ?></h5>
                    <p class="mom-points"><?= number_format($memberOfMonth['monthly_points'] ?? 0) ?> نقطة</p>
                    <p class="mom-label">عضو الشهر</p>
                </div>
            </div>

            <!-- Quick Leaderboard -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0"><i class="bi bi-trophy me-2"></i>الأوائل</h6>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#leaderboardModal" class="small">عرض الكل</a>
                </div>
                <ul class="leaderboard-list">
                    <?php foreach (array_slice($leaderboard, 0, 5) as $i => $member): ?>
                        <li class="leaderboard-item">
                            <span class="rank rank-<?= $i + 1 ?>"><?= $i + 1 ?></span>
                            <img src="<?= $member['avatar'] ?? '/assets/images/default-avatar.png' ?>"
                                class="member-avatar-sm" alt="">
                            <span class="member-name"><?= htmlspecialchars($member['full_name']) ?></span>
                            <span class="member-points"><?= number_format($member['monthly_points'] ?? 0) ?></span>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($leaderboard)): ?>
                        <li class="leaderboard-item text-center text-muted py-4">لا توجد بيانات</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-8">
            <!-- Chart -->
            <div class="card mb-4">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h6 class="card-title mb-0"><i class="bi bi-bar-chart me-2"></i>إحصائيات النشاط</h6>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary active" data-chart="activities">الأنشطة</button>
                        <button class="btn btn-outline-primary" data-chart="attendance">الحضور</button>
                        <button class="btn btn-outline-primary" data-chart="points">النقاط</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="statsChart" height="300"></canvas>
                </div>
            </div>

            <!-- Upcoming Activities -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0"><i class="bi bi-calendar3 me-2"></i>الأنشطة القادمة</h6>
                    <a href="/activities" class="small">عرض الكل</a>
                </div>
                <div class="activities-list">
                    <?php foreach (array_slice($upcomingActivities, 0, 4) as $activity): ?>
                        <a href="/activities/<?= $activity['id'] ?>" class="activity-item">
                            <div class="activity-date">
                                <span class="day"><?= date('d', strtotime($activity['date'])) ?></span>
                                <span class="month"><?= date('M', strtotime($activity['date'])) ?></span>
                            </div>
                            <div class="activity-info">
                                <h6><?= htmlspecialchars($activity['title']) ?></h6>
                                <div class="activity-meta">
                                    <?php if ($activity['time']): ?>
                                        <span><i class="bi bi-clock"></i><?= $activity['time'] ?></span>
                                    <?php endif; ?>
                                    <?php if ($activity['location']): ?>
                                        <span><i class="bi bi-geo-alt"></i><?= htmlspecialchars($activity['location']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="badge bg-warning text-dark"><i
                                    class="bi bi-star-fill me-1"></i><?= $activity['points_value'] ?></span>
                        </a>
                    <?php endforeach; ?>
                    <?php if (empty($upcomingActivities)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-calendar-x display-4 d-block mb-2"></i>
                            <p>لا توجد أنشطة قادمة</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize Chart
    const ctx = document.getElementById('statsChart').getContext('2d');
    let chart;

    function loadChart(type) {
        fetch(`/api/dashboard/chart?type=${type}`)
            .then(r => r.json())
            .then(data => {
                if (chart) chart.destroy();

                const colors = {
                    activities: { bg: 'rgba(230, 57, 70, 0.2)', border: '#e63946' },
                    attendance: { bg: 'rgba(45, 106, 79, 0.2)', border: '#2d6a4f' },
                    points: { bg: 'rgba(244, 162, 97, 0.2)', border: '#f4a261' }
                };

                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels || [],
                        datasets: [{
                            label: data.label || 'البيانات',
                            data: data.data || [],
                            backgroundColor: colors[type]?.bg || colors.activities.bg,
                            borderColor: colors[type]?.border || colors.activities.border,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: colors[type]?.border || colors.activities.border
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            });
    }

    // Initial load
    loadChart('activities');

    // Chart type buttons
    document.querySelectorAll('[data-chart]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('[data-chart]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            loadChart(this.dataset.chart);
        });
    });
</script>