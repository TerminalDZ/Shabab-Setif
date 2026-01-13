<?php
$title = 'الملف الشخصي';
ob_start();
?>

<div class="px-4 py-6 md:px-8 space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 text-center">
                    <div class="relative w-32 h-32 mx-auto mb-4 group">
                        <img src="<?= $currentUser->avatar ?? '/assets/images/default-avatar.png' ?>"
                            class="w-full h-full rounded-full object-cover border-4 border-white shadow-md">
                        <button onclick="openModal('editProfileModal')"
                            class="absolute bottom-0 right-0 bg-primary text-white w-8 h-8 rounded-full shadow-lg flex items-center justify-center hover:bg-primary-dark transition-colors"
                            title="تعديل الصورة">
                            <i class="bi bi-camera"></i>
                        </button>
                    </div>

                    <h2 class="text-xl font-bold text-gray-800 mb-1"><?= htmlspecialchars($currentUser->full_name) ?>
                    </h2>
                    <p class="text-gray-500 text-sm mb-3"><?= $currentUser->email ?></p>

                    <span
                        class="inline-flex px-3 py-1 rounded-full text-xs font-bold <?= $currentUser->role === 'admin' ? 'bg-red-100 text-red-700' : ($currentUser->role === 'head' ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700') ?>">
                        <?= $currentUser->role === 'admin' ? 'مدير' : ($currentUser->role === 'head' ? 'رئيس لجنة' : 'عضو') ?>
                    </span>

                    <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-500 mb-1">رقم بطاقة العضوية</p>
                        <code
                            class="text-lg font-mono font-bold text-primary tracking-wider"><?= $currentUser->member_card_id ?></code>
                    </div>

                    <a href="/users/<?= $currentUser->id ?>/card" target="_blank"
                        class="block w-full mt-4 py-2.5 bg-white border border-primary text-primary hover:bg-primary hover:text-white rounded-xl font-bold transition-all text-sm">
                        <i class="bi bi-credit-card me-2"></i>عرض البطاقة الرقمية
                    </a>
                </div>
            </div>

            <!-- Stats -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-50">
                    <h3 class="font-bold text-gray-800"><i class="bi bi-bar-chart me-2 text-primary"></i>إحصائياتي</h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center">
                                <i class="bi bi-star-fill"></i></div>
                            <span class="text-sm font-medium text-gray-700">رصيد النقاط</span>
                        </div>
                        <span
                            class="font-bold text-lg text-gray-800"><?= number_format($stats['points_balance'] ?? 0) ?></span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                <i class="bi bi-trophy-fill"></i></div>
                            <span class="text-sm font-medium text-gray-700">الترتيب الشهري</span>
                        </div>
                        <span class="font-bold text-lg text-gray-800">#<?= $stats['monthly_rank'] ?? '-' ?></span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                <i class="bi bi-calendar-check-fill"></i></div>
                            <span class="text-sm font-medium text-gray-700">الأنشطة المكتملة</span>
                        </div>
                        <span class="font-bold text-lg text-gray-800"><?= $stats['activities_attended'] ?? 0 ?></span>
                    </div>
                </div>
            </div>

            <button onclick="openModal('changePasswordModal')"
                class="w-full py-3 text-red-600 hover:bg-red-50 rounded-xl font-medium transition-colors text-sm flex items-center justify-center gap-2">
                <i class="bi bi-key"></i>
                <span>تغيير كلمة المرور</span>
            </button>
        </div>

        <!-- History -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Points History -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800"><i class="bi bi-clock-history me-2 text-primary"></i>سجل النقاط
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">التاريخ</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">النقاط</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">السبب</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">النوع</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if (!empty($pointsHistory)): ?>
                                <?php foreach ($pointsHistory as $log): ?>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            <?= date('Y-m-d', strtotime($log['created_at'])) ?></td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $log['points'] >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                                <?= $log['points'] >= 0 ? '+' : '' ?>        <?= $log['points'] ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($log['reason']) ?></td>
                                        <td class="px-6 py-4"><span
                                                class="bg-gray-100 text-gray-600 px-2 py-1 rounded-md text-xs"><?= $log['reference_type'] ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-400">لا توجد سجلات</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Attendance History -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800"><i class="bi bi-calendar-check me-2 text-primary"></i>سجل الحضور
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">النشاط</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">التاريخ</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if (!empty($attendanceHistory)): ?>
                                <?php foreach ($attendanceHistory as $att): ?>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                            <?= htmlspecialchars($att['activity_title'] ?? '-') ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            <?= date('Y-m-d', strtotime($att['marked_at'])) ?></td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $att['status'] === 'present' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                                <?= $att['status'] === 'present' ? 'حاضر' : 'غائب' ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-400">لا توجد سجلات</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('editProfileModal')">
    </div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-2xl bg-white text-right shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-primary px-4 py-3 sm:px-6 flex justify-between items-center">
                    <h3 class="text-base font-semibold leading-6 text-white">تعديل الملف الشخصي</h3>
                    <button type="button" class="text-white hover:text-gray-200"
                        onclick="closeModal('editProfileModal')">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <form id="editProfileForm" enctype="multipart/form-data">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700">الاسم الكامل</label>
                            <input type="text" name="full_name" value="<?= htmlspecialchars($currentUser->full_name) ?>"
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">الهاتف</label>
                            <input type="tel" name="phone" value="<?= $currentUser->phone ?>"
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">الصورة الشخصية</label>
                            <div id="avatarDropzone"
                                class="dropzone rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 hover:bg-white hover:border-primary/50 transition-all cursor-pointer min-h-[100px] flex items-center justify-center p-4">
                                <div class="dz-message text-center">
                                    <span class="text-sm text-gray-500">اضغط لتغيير الصورة</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit" id="saveProfileBtn"
                            class="inline-flex w-full justify-center rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark sm:mr-3 sm:w-auto">حفظ
                            التغييرات</button>
                        <button type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                            onclick="closeModal('editProfileModal')">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeModal('changePasswordModal')"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-2xl bg-white text-right shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">تغيير كلمة المرور</h3>
                    <form id="changePasswordForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">كلمة المرور الحالية</label>
                            <input type="password" name="current_password" required
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">كلمة المرور الجديدة</label>
                            <input type="password" name="new_password" required
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">تأكيد كلمة المرور</label>
                            <input type="password" name="confirm_password" required
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div class="pt-4 flex justify-end gap-2">
                            <button type="button"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
                                onclick="closeModal('changePasswordModal')">إلغاء</button>
                            <button type="submit"
                                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">تغيير</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    Dropzone.autoDiscover = false;

    // Avatar Dropzone
    let avatarDropzone;
    $(document).ready(function () {
        avatarDropzone = new Dropzone("#avatarDropzone", {
            url: "/api/auth/profile", // Use same endpoint, but we handle it manually
            autoProcessQueue: false,
            uploadMultiple: false,
            maxFiles: 1,
            paramName: "avatar",
            acceptedFiles: "image/*",
            addRemoveLinks: true,
            dictDefaultMessage: "اضغط هنا لرفع صورة",
            dictRemoveFile: "حذف",
            init: function () {
                this.on("addedfile", function () {
                    if (this.files[1] != null) {
                        this.removeFile(this.files[0]);
                    }
                });
            }
        });

        $('#editProfileForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $('#saveProfileBtn');
            btn.prop('disabled', true).text('جاري الحفظ...');

            const formData = new FormData(this);
            formData.append('_csrf_token', csrfToken);

            // If dropzone has file, append it manually
            if (avatarDropzone.files.length > 0) {
                formData.append('avatar', avatarDropzone.files[0]);
            }

            $.ajax({
                url: '/api/auth/profile',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: r => {
                    if (r.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم',
                            text: r.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('خطأ', r.message, 'error');
                        btn.prop('disabled', false).text('حفظ التغييرات');
                    }
                },
                error: xhr => {
                    Swal.fire('خطأ', xhr.responseJSON?.message || 'خطأ غير متوقع', 'error');
                    btn.prop('disabled', false).text('حفظ التغييرات');
                }
            });
        });

        $('#changePasswordForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '/api/auth/password', method: 'POST', data: $(this).serialize() + '&_csrf_token=' + csrfToken,
                success: r => {
                    Swal.fire({ icon: 'success', title: 'تم', text: r.message, timer: 1500, showConfirmButton: false });
                    closeModal('changePasswordModal');
                    this.reset();
                },
                error: xhr => Swal.fire('خطأ', xhr.responseJSON?.message, 'error')
            });
        });
    });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>