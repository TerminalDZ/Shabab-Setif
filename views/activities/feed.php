<?php
$title = 'آخر الأنشطة';
ob_start();
?>

<div class="px-4 py-6 md:px-8 max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-l from-primary to-blue-600">آخر
                الأنشطة</h1>
            <p class="text-gray-500 mt-2">تابع أحدث فعاليات وأخبار الجمعية</p>
        </div>
        <div class="flex items-center bg-gray-100 p-1 rounded-xl">
            <a href="/activities"
                class="px-4 py-2 text-gray-500 hover:text-primary rounded-lg font-bold text-sm transition-all">
                <i class="bi bi-list"></i>
            </a>
            <button class="px-4 py-2 bg-white text-primary shadow-sm rounded-lg font-bold text-sm transition-all">
                <i class="bi bi-grid"></i>
            </button>
        </div>
    </div>

    <!-- Feed Grid -->
    <?php if (!empty($activities)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($activities as $activity): ?>
                <?php
                $statusColor = match ($activity['status']) {
                    'upcoming' => 'bg-blue-600 text-white shadow-blue-200',
                    'completed' => 'bg-green-600 text-white shadow-green-200',
                    'cancelled' => 'bg-red-600 text-white shadow-red-200',
                    default => 'bg-gray-600'
                };
                $statusLabel = match ($activity['status']) {
                    'upcoming' => 'قادم',
                    'completed' => 'مكتمل',
                    'cancelled' => 'ملغي',
                    default => '?'
                };
                $images = json_decode($activity['images_json'] ?? '[]', true);
                ?>

                <div
                    class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <!-- Header -->
                    <div class="p-4 flex items-center gap-3 border-b border-gray-50">
                        <img src="<?= $activity['creator_avatar'] ?? '/assets/images/default-avatar.png' ?>"
                            class="w-10 h-10 rounded-full object-cover border border-gray-100">
                        <div class="flex-grow">
                            <h4 class="font-bold text-sm text-gray-800">
                                <?= htmlspecialchars($activity['creator_name'] ?? 'النظام') ?></h4>
                            <span class="text-xs text-gray-400"><?= date('d M Y', strtotime($activity['created_at'])) ?></span>
                        </div>
                        <span class="px-3 py-1 text-xs font-bold rounded-full shadow-lg <?= $statusColor ?>">
                            <?= $statusLabel ?>
                        </span>
                    </div>

                    <!-- Gallery Grid -->
                    <?php if (!empty($images)): ?>
                        <?php if (count($images) === 1): ?>
                            <div class="h-48 overflow-hidden">
                                <img src="<?= $images[0] ?>"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-2 h-48 gap-0.5">
                                <?php foreach (array_slice($images, 0, 4) as $img): ?>
                                    <div class="overflow-hidden">
                                        <img src="<?= $img ?>"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="h-48 bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center">
                            <i class="bi bi-image text-gray-300 text-4xl"></i>
                        </div>
                    <?php endif; ?>

                    <!-- Content -->
                    <div class="p-6">
                        <h3
                            class="text-xl font-bold text-gray-800 mb-2 leading-tight group-hover:text-primary transition-colors">
                            <?= htmlspecialchars($activity['title']) ?>
                        </h3>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2">
                            <?= htmlspecialchars(substr($activity['description'] ?? '', 0, 150)) ?>...
                        </p>

                        <div class="flex flex-wrap gap-2 text-xs font-medium text-gray-600 mb-4">
                            <span class="bg-gray-50 px-2 py-1 rounded-lg border border-gray-100 flex items-center gap-1">
                                <i class="bi bi-calendar text-primary"></i> <?= $activity['date'] ?>
                            </span>
                            <span class="bg-gray-50 px-2 py-1 rounded-lg border border-gray-100 flex items-center gap-1">
                                <i class="bi bi-star text-yellow-500"></i> <?= $activity['points_value'] ?> نقطة
                            </span>
                            <?php if ($activity['attendee_count']): ?>
                                <span class="bg-gray-50 px-2 py-1 rounded-lg border border-gray-100 flex items-center gap-1">
                                    <i class="bi bi-people text-blue-500"></i> <?= $activity['attendee_count'] ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <a href="/activities/<?= $activity['id'] ?>"
                            class="block w-full py-3 text-center bg-gray-50 hover:bg-primary hover:text-white text-gray-600 font-bold rounded-xl transition-colors">
                            عرض التفاصيل
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-20">
            <div
                class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-400 animate-pulse">
                <i class="bi bi-calendar-x text-5xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">لا توجد أنشطة حالياً</h3>
            <p class="text-gray-500 mt-2">تابعنا لاحقاً لمعرفة جديد الجمعية</p>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>