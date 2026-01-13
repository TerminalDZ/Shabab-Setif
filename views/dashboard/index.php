<?php
$title = 'لوحة التحكم';
ob_start();
?>

<div class="px-4 py-8 md:px-8 space-y-8">

    <!-- Welcome Section -->
    <div
        class="bg-gradient-to-r from-primary to-green-800 rounded-3xl p-8 text-white relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -ml-20 -mt-20"></div>
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-black/10 rounded-full blur-3xl -mr-20 -mb-20"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold mb-2">مرحباً، <?= htmlspecialchars($currentUser->full_name) ?> 👋</h1>
                <p class="text-white/80">نتمنى لك يوماً مليئاً بالنشاط والإنجازات مع شباب سطيف</p>
            </div>
            <div class="flex gap-4">
                <div
                    class="bg-white/10 backdrop-blur-sm p-4 rounded-2xl border border-white/10 text-center min-w-[100px]">
                    <span
                        class="block text-2xl font-bold text-yellow-300 shadow-black/5 drop-shadow-md"><?= number_format($stats['points_balance'] ?? 0) ?></span>
                    <span class="text-xs text-white/80">نقطة</span>
                </div>
                <div
                    class="bg-white/10 backdrop-blur-sm p-4 rounded-2xl border border-white/10 text-center min-w-[100px]">
                    <span
                        class="block text-2xl font-bold text-white shadow-black/5 drop-shadow-md">#<?= $stats['monthly_rank'] ?? '-' ?></span>
                    <span class="text-xs text-white/80">الترتيب</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div
            class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center group hover:border-primary/50 transition-colors">
            <div
                class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="bi bi-people-fill text-xl"></i>
            </div>
            <span class="text-2xl font-bold text-gray-800"><?= number_format($stats['total_members'] ?? 0) ?></span>
            <span class="text-xs text-gray-500">عضو مسجل</span>
        </div>
        <div
            class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center group hover:border-green-500/50 transition-colors">
            <div
                class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="bi bi-calendar-event text-xl"></i>
            </div>
            <span class="text-2xl font-bold text-gray-800"><?= number_format($stats['total_activities'] ?? 0) ?></span>
            <span class="text-xs text-gray-500">نشاط كلي</span>
        </div>
        <div
            class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center group hover:border-yellow-500/50 transition-colors">
            <div
                class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="bi bi-calendar-check text-xl"></i>
            </div>
            <span
                class="text-2xl font-bold text-gray-800"><?= number_format($stats['monthly_activities'] ?? 0) ?></span>
            <span class="text-xs text-gray-500">نشاط هذا الشهر</span>
        </div>
        <div
            class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center group hover:border-purple-500/50 transition-colors">
            <div
                class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="bi bi-diagram-3 text-xl"></i>
            </div>
            <span class="text-2xl font-bold text-gray-800"><?= number_format($stats['total_committees'] ?? 0) ?></span>
            <span class="text-xs text-gray-500">لجنة</span>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Right Column (Charts & Activities) -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Chart -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-gray-800">إحصائيات النشاط</h3>
                    <div class="flex bg-gray-50 p-1 rounded-lg">
                        <button class="px-3 py-1 text-xs font-bold rounded-md bg-white text-primary shadow-sm"
                            onclick="switchChart('activities')">الأنشطة</button>
                        <button class="px-3 py-1 text-xs font-bold rounded-md text-gray-500 hover:text-gray-900"
                            onclick="switchChart('attendance')">الحضور</button>
                    </div>
                </div>
                <div class="h-64 relative">
                    <canvas id="statsChart"></canvas>
                </div>
            </div>

            <!-- Upcoming Activities -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">الأنشطة القادمة</h3>
                    <a href="/activities" class="text-sm text-primary font-bold hover:underline">عرض الكل</a>
                </div>
                <div class="divide-y divide-gray-50">
                    <?php if (!empty($upcomingActivities)): ?>
                        <?php foreach (array_slice($upcomingActivities, 0, 3) as $act): ?>
                            <a href="/activities/<?= $act['id'] ?>"
                                class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-colors group">
                                <div
                                    class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex flex-col items-center justify-center shrink-0 border border-blue-100">
                                    <span
                                        class="text-lg font-bold leading-none"><?= date('d', strtotime($act['date'])) ?></span>
                                    <span
                                        class="text-[10px] uppercase font-bold"><?= date('M', strtotime($act['date'])) ?></span>
                                </div>
                                <div class="flex-grow">
                                    <h4 class="font-bold text-gray-800 group-hover:text-primary transition-colors">
                                        <?= htmlspecialchars($act['title']) ?></h4>
                                    <p class="text-xs text-gray-500 flex items-center gap-2 mt-1">
                                        <?php if ($act['time']): ?><span><i class="bi bi-clock"></i>
                                                <?= $act['time'] ?></span><?php endif; ?>
                                        <?php if ($act['location']): ?><span><i class="bi bi-geo-alt"></i>
                                                <?= htmlspecialchars($act['location']) ?></span><?php endif; ?>
                                    </p>
                                </div>
                                <span
                                    class="bg-yellow-50 text-yellow-700 px-2 py-1 rounded-lg text-xs font-bold border border-yellow-100">
                                    <?= $act['points_value'] ?> نقطة
                                </span>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-8 text-center text-gray-400">
                            <i class="bi bi-calendar-x text-3xl mb-2 block"></i>
                            <p class="text-sm">لا توجد أنشطة قادمة</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Left Column (Leaderboard & Member of Month) -->
        <div class="space-y-8">
            <!-- Member of Month -->
            <div
                class="bg-gradient-to-b from-yellow-50 to-white rounded-2xl shadow-sm border border-yellow-100 p-6 text-center relative overflow-hidden">
                <div
                    class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-yellow-400 to-transparent">
                </div>
                <h3 class="font-bold text-yellow-700 mb-6 flex items-center justify-center gap-2">
                    <i class="bi bi-trophy-fill text-yellow-500"></i> عضو الشهر
                </h3>
                <div class="relative w-24 h-24 mx-auto mb-4">
                    <div class="absolute inset-0 bg-yellow-400 rounded-full animate-pulse opacity-20"></div>
                    <img src="<?= $memberOfMonth['avatar'] ?? '/assets/images/default-avatar.png' ?>"
                        class="w-full h-full rounded-full object-cover border-4 border-white shadow-md relative z-10">
                    <div
                        class="absolute -bottom-2 -right-2 bg-yellow-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow border-2 border-white z-20">
                        <i class="bi bi-crown-fill text-xs"></i>
                    </div>
                </div>
                <h4 class="text-lg font-bold text-gray-800">
                    <?= htmlspecialchars($memberOfMonth['full_name'] ?? '...') ?></h4>
                <p class="text-yellow-600 font-bold text-sm mt-1">
                    <?= number_format($memberOfMonth['monthly_points'] ?? 0) ?> نقطة</p>
            </div>

            <!-- Leaderboard -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-50">
                    <h3 class="font-bold text-gray-800">صدارة الترتيب</h3>
                </div>
                <div class="p-2 space-y-1">
                    <?php foreach (array_slice($leaderboard, 0, 5) as $i => $m): ?>
                        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-gray-50 transition-colors">
                            <span
                                class="w-6 h-6 flex items-center justify-center text-xs font-bold rounded-full <?= $i < 3 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-500' ?>"><?= $i + 1 ?></span>
                            <img src="<?= $m['avatar'] ?? '/assets/images/default-avatar.png' ?>"
                                class="w-8 h-8 rounded-full object-cover">
                            <div class="flex-grow">
                                <h5 class="text-sm font-bold text-gray-800"><?= htmlspecialchars($m['full_name']) ?></h5>
                            </div>
                            <span class="text-xs font-mono font-bold text-gray-600"><?= $m['monthly_points'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('statsChart').getContext('2d');
    let chart;

    function loadChart(type) {
        fetch(`/api/dashboard/chart?type=${type}`)
            .then(r => r.json())
            .then(data => {
                if (chart) chart.destroy();

                const color = type === 'activities' ? '#10b981' : '#3b82f6';
                const bg = type === 'activities' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(59, 130, 246, 0.1)';

                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels || [],
                        datasets: [{
                            label: data.label,
                            data: data.data || [],
                            borderColor: color,
                            backgroundColor: bg,
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 0,
                            pointHoverRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { grid: { borderDash: [2, 2], color: '#f3f4f6' }, beginAtZero: true },
                            x: { grid: { display: false } }
                        }
                    }
                });
            });
    }

    loadChart('activities');
    window.switchChart = (t) => loadChart(t);
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>