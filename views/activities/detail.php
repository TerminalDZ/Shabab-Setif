<?php
$title = htmlspecialchars($activity->title);
ob_start();

// Helper for status badge
$statusColors = ['upcoming' => 'bg-blue-100 text-blue-700', 'completed' => 'bg-green-100 text-green-700', 'cancelled' => 'bg-red-100 text-red-700'];
$statusLabels = ['upcoming' => 'قادم', 'completed' => 'مكتمل', 'cancelled' => 'ملغي'];
?>

<div class="px-4 py-6 md:px-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold text-gray-800"><?= htmlspecialchars($activity->title) ?></h1>
                <span class="px-3 py-1 rounded-full text-sm font-bold <?= $statusColors[$activity->status] ?>">
                    <?= $statusLabels[$activity->status] ?>
                </span>
            </div>
            <div class="flex items-center gap-4 text-gray-500 mt-2 text-sm">
                <span class="flex items-center gap-1"><i class="bi bi-calendar"></i> <?= $activity->date ?></span>
                <?php if ($activity->time): ?>
                    <span class="flex items-center gap-1"><i class="bi bi-clock"></i> <?= $activity->time ?></span>
                <?php endif; ?>
                <?php if ($activity->location): ?>
                    <span class="flex items-center gap-1"><i class="bi bi-geo-alt"></i>
                        <?= htmlspecialchars($activity->location) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($currentUser->canManage()): ?>
            <div class="flex items-center gap-2">
                <a href="/activities/<?= $activity->id ?>/edit"
                    class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm font-medium flex items-center gap-2">
                    <i class="bi bi-pencil"></i>
                    <span>تعديل</span>
                </a>
                <div class="relative" id="actionsDropdown">
                    <button onclick="toggleDropdown('act-actions-menu')"
                        class="px-3 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <div id="act-actions-menu"
                        class="hidden absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-10">
                        <button onclick="changeStatus('completed')"
                            class="w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-green-600 flex items-center gap-2">
                            <i class="bi bi-check-circle"></i> إكمال النشاط
                        </button>
                        <button onclick="changeStatus('cancelled')"
                            class="w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-600 flex items-center gap-2">
                            <i class="bi bi-x-circle"></i> إلغاء النشاط
                        </button>
                        <div class="border-t border-gray-50 my-1"></div>
                        <button onclick="deleteActivity()"
                            class="w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                            <i class="bi bi-trash"></i> حذف النشاط
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-50">التفاصيل</h3>
                <div class="prose max-w-none text-gray-600 leading-relaxed">
                    <?= nl2br(htmlspecialchars($activity->description ?? 'لا يوجد وصف')) ?>
                </div>
            </div>

            <!-- Gallery -->
            <?php $images = json_decode($activity->images_json ?? '[]', true); ?>
            <?php if (!empty($images)): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-50">
                        <i class="bi bi-images text-primary me-2"></i>معرض الصور
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php foreach ($images as $img): ?>
                            <a href="<?= $img ?>" target="_blank"
                                class="block group rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all">
                                <img src="<?= $img ?>"
                                    class="w-full h-40 object-cover transform group-hover:scale-110 transition-transform duration-500"
                                    alt="">
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar (Stats & Attendance) -->
        <div class="space-y-6">
            <!-- Points Card -->
            <div
                class="bg-gradient-to-br from-primary to-blue-600 rounded-2xl shadow-lg p-6 text-white text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 blur-2xl"></div>
                <div class="relative z-10">
                    <i class="bi bi-star-fill text-yellow-300 text-4xl mb-2 block shadow-sm"></i>
                    <span class="text-5xl font-bold font-mono tracking-tight"><?= $activity->points_value ?></span>
                    <p class="text-white/80 text-sm mt-1 font-medium">نقطة لكل مشارك</p>
                </div>
            </div>

            <!-- Attendance Stats -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">إحصائيات الحضور</h3>
                    <span
                        class="bg-gray-100 text-gray-600 px-2 py-1 rounded-lg text-xs font-bold"><?= $attendance['total'] ?? 0 ?>
                        عضو</span>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4 text-center">
                    <div class="p-3 bg-green-50 rounded-xl">
                        <span class="block text-2xl font-bold text-green-600"><?= $attendance['present'] ?? 0 ?></span>
                        <span class="text-xs text-green-700 font-medium">حاضر</span>
                    </div>
                    <div class="p-3 bg-red-50 rounded-xl">
                        <span class="block text-2xl font-bold text-red-600"><?= $attendance['absent'] ?? 0 ?></span>
                        <span class="text-xs text-red-700 font-medium">غائب</span>
                    </div>
                </div>
            </div>

            <!-- Attendance List -->
            <?php if ($currentUser->canManage()): ?>
                <div
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col max-h-[500px]">
                    <div class="p-4 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 text-sm">تسجيل الحضور</h3>
                    </div>

                    <?php if (!empty($members)): ?>
                        <form id="attendanceForm" class="flex-1 overflow-hidden flex flex-col">
                            <div class="flex-1 overflow-y-auto p-2 custom-scrollbar space-y-1">
                                <?php foreach ($members as $m): ?>
                                    <div
                                        class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg transition-colors group">
                                        <div class="flex items-center gap-3">
                                            <img src="<?= $m->avatar ?? '/assets/images/default-avatar.png' ?>"
                                                class="w-8 h-8 rounded-full object-cover border border-gray-100" alt="">
                                            <span
                                                class="text-sm font-medium text-gray-700"><?= htmlspecialchars($m->full_name) ?></span>
                                        </div>
                                        <select name="attendance[<?= $m->id ?>]"
                                            class="text-xs border-gray-200 bg-gray-50 rounded-lg py-1 px-2 focus:ring-primary focus:border-primary">
                                            <option value="">-</option>
                                            <option value="present" <?= ($attendanceMap[$m->id] ?? '') === 'present' ? 'selected' : '' ?>>حاضر</option>
                                            <option value="absent" <?= ($attendanceMap[$m->id] ?? '') === 'absent' ? 'selected' : '' ?>>غائب</option>
                                        </select>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="p-4 border-t border-gray-100 bg-gray-50">
                                <button type="submit"
                                    class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-sm shadow-md shadow-green-200 transition-all flex items-center justify-center gap-2">
                                    <i class="bi bi-check-lg"></i>
                                    <span>حفظ الحضور</span>
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="p-8 text-center text-gray-400">
                            <i class="bi bi-people text-4xl mb-2 block"></i>
                            <p class="text-sm">لا يوجد أعضاء في هذه القائمة</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const activityId = <?= $activity->id ?>;

    function changeStatus(status) {
        $.post('/api/activities/' + activityId, {
            status: status,
            _csrf_token: $('meta[name="csrf-token"]').attr('content')
        }, function (response) {
            if (response.success) {
                location.reload();
            }
        });
    }

    function deleteActivity() {
        confirmDelete('سيتم حذف النشاط نهائياً', function () {
            $.ajax({
                url: '/api/activities/' + activityId,
                type: 'DELETE',
                data: { _csrf_token: $('meta[name="csrf-token"]').attr('content') },
                success: function (r) {
                    if (r.success) window.location.href = '/activities';
                }
            });
        });
    }

    $('#attendanceForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<span class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin inline-block"></span>');

        $.ajax({
            url: '/api/activities/' + activityId + '/attendance',
            type: 'POST',
            data: $(this).serialize() + '&_csrf_token=' + $('meta[name="csrf-token"]').attr('content'),
            success: function (response) {
                if (response.success) {
                    showToast('success', 'تم حفظ الحضور');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('error', response.message);
                    btn.prop('disabled', false).text('حفظ الحضور');
                }
            },
            error: function () {
                showToast('error', 'حدث خطأ');
                btn.prop('disabled', false).text('حفظ الحضور');
            }
        });
    });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>