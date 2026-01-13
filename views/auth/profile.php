<!-- Profile Page -->
<div class="container-fluid py-4">
    <div class="row">
        <!-- Profile Card -->
        <div class="col-lg-4 mb-4">
            <div class="card profile-card">
                <div class="card-body text-center">
                    <div class="profile-avatar-wrapper">
                        <img src="<?= $currentUser->avatar ?? '/assets/images/default-avatar.png' ?>"
                            alt="<?= htmlspecialchars($currentUser->full_name) ?>" class="profile-avatar">
                        <button class="btn btn-sm btn-primary avatar-edit" data-bs-toggle="modal"
                            data-bs-target="#editProfileModal">
                            <i class="bi bi-camera"></i>
                        </button>
                    </div>
                    <h4 class="mt-3 mb-1">
                        <?= htmlspecialchars($currentUser->full_name) ?>
                    </h4>
                    <p class="text-muted">
                        <?= $currentUser->email ?>
                    </p>

                    <div class="profile-badge mb-3">
                        <span
                            class="badge bg-<?= $currentUser->role === 'admin' ? 'danger' : ($currentUser->role === 'head' ? 'warning' : 'success') ?>">
                            <?= $currentUser->role === 'admin' ? 'مدير' : ($currentUser->role === 'head' ? 'رئيس لجنة' : 'عضو') ?>
                        </span>
                    </div>

                    <div class="member-card-preview p-3 rounded mb-3">
                        <small class="text-muted d-block mb-1">رقم بطاقة العضوية</small>
                        <code class="fs-5 text-primary"><?= $currentUser->member_card_id ?></code>
                    </div>

                    <a href="/users/<?= $currentUser->id ?>/card" target="_blank" class="btn btn-outline-primary w-100">
                        <i class="bi bi-credit-card me-2"></i>عرض البطاقة
                    </a>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>إحصائياتي</h6>
                </div>
                <div class="card-body">
                    <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                        <span><i class="bi bi-star text-warning me-2"></i>النقاط</span>
                        <strong class="text-primary">
                            <?= number_format($stats['points_balance'] ?? 0) ?>
                        </strong>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                        <span><i class="bi bi-trophy text-success me-2"></i>الترتيب الشهري</span>
                        <strong>#
                            <?= $stats['monthly_rank'] ?? '-' ?>
                        </strong>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                        <span><i class="bi bi-calendar-check text-info me-2"></i>الأنشطة المحضورة</span>
                        <strong>
                            <?= $stats['activities_attended'] ?? 0 ?>
                        </strong>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-percent text-danger me-2"></i>نسبة الحضور</span>
                        <strong>
                            <?= number_format($stats['attendance_rate'] ?? 0, 1) ?>%
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity History -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>سجل النقاط</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>النقاط</th>
                                    <th>السبب</th>
                                    <th>النوع</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pointsHistory)): ?>
                                    <?php foreach ($pointsHistory as $log): ?>
                                        <tr>
                                            <td>
                                                <?= date('Y-m-d', strtotime($log['created_at'])) ?>
                                            </td>
                                            <td><span class="badge bg-<?= $log['points'] >= 0 ? 'success' : 'danger' ?>">
                                                    <?= $log['points'] >= 0 ? '+' : '' ?>
                                                    <?= $log['points'] ?>
                                                </span></td>
                                            <td>
                                                <?= htmlspecialchars($log['reason']) ?>
                                            </td>
                                            <td><span class="badge bg-secondary">
                                                    <?= $log['reference_type'] ?>
                                                </span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">لا توجد سجلات</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Attendance History -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-calendar-check me-2"></i>سجل الحضور</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>النشاط</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($attendanceHistory)): ?>
                                    <?php foreach ($attendanceHistory as $att): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($att['activity_title'] ?? '-') ?>
                                            </td>
                                            <td>
                                                <?= $att['marked_at'] ?>
                                            </td>
                                            <td><span
                                                    class="badge bg-<?= $att['status'] === 'present' ? 'success' : 'danger' ?>">
                                                    <?= $att['status'] === 'present' ? 'حاضر' : 'غائب' ?>
                                                </span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">لا توجد سجلات</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل الملف الشخصي</h5><button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <form id="editProfileForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">الاسم الكامل</label><input type="text"
                            class="form-control" name="full_name"
                            value="<?= htmlspecialchars($currentUser->full_name) ?>"></div>
                    <div class="mb-3"><label class="form-label">الهاتف</label><input type="tel" class="form-control"
                            name="phone" value="<?= $currentUser->phone ?>"></div>
                    <!-- Avatar upload disabled -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تغيير كلمة المرور</h5><button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <form id="changePasswordForm">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">كلمة المرور الحالية</label><input type="password"
                            class="form-control" name="current_password" required></div>
                    <div class="mb-3"><label class="form-label">كلمة المرور الجديدة</label><input type="password"
                            class="form-control" name="new_password" required></div>
                    <div class="mb-3"><label class="form-label">تأكيد كلمة المرور</label><input type="password"
                            class="form-control" name="confirm_password" required></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تغيير</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $('#editProfileForm').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('_csrf_token', csrfToken);
        $.ajax({
            url: '/api/auth/profile', method: 'POST', data: formData, processData: false, contentType: false,
            success: r => { Swal.fire('تم', r.message, 'success').then(() => location.reload()); },
            error: xhr => Swal.fire('خطأ', xhr.responseJSON?.message, 'error')
        });
    });

    $('#changePasswordForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '/api/auth/password', method: 'POST', data: $(this).serialize() + '&_csrf_token=' + csrfToken,
            success: r => { Swal.fire('تم', r.message, 'success'); $('#changePasswordModal').modal('hide'); },
            error: xhr => Swal.fire('خطأ', xhr.responseJSON?.message, 'error')
        });
    });
</script>