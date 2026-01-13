<!-- Activity Detail Page - Enhanced -->
<div class="container-fluid py-4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h4 class="mb-1"><?= htmlspecialchars($activity->title) ?></h4>
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i><?= $activity->date ?>
                            <?php if ($activity->time): ?>
                                <span class="ms-2"><i class="bi bi-clock me-1"></i><?= $activity->time ?></span>
                            <?php endif; ?>
                        </small>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <span
                            class="badge bg-<?= $activity->status === 'completed' ? 'success' : ($activity->status === 'upcoming' ? 'primary' : 'danger') ?> fs-6">
                            <?= $activity->status === 'completed' ? 'مكتمل' : ($activity->status === 'upcoming' ? 'قادم' : 'ملغي') ?>
                        </span>
                        <?php if ($currentUser->canManage()): ?>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="/activities/<?= $activity->id ?>/edit"><i
                                                class="bi bi-pencil me-2"></i>تعديل</a></li>
                                    <li><button class="dropdown-item" onclick="changeStatus('completed')"><i
                                                class="bi bi-check-circle me-2"></i>إكمال</button></li>
                                    <li><button class="dropdown-item" onclick="changeStatus('cancelled')"><i
                                                class="bi bi-x-circle me-2"></i>إلغاء</button></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><button class="dropdown-item text-danger" onclick="deleteActivity()"><i
                                                class="bi bi-trash me-2"></i>حذف</button></li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body">
                    <?php if ($activity->description): ?>
                        <p class="mb-4"><?= nl2br(htmlspecialchars($activity->description)) ?></p>
                    <?php endif; ?>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="info-box">
                                <i class="bi bi-calendar3 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">التاريخ</small>
                                    <strong><?= $activity->date ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <i class="bi bi-clock text-success"></i>
                                <div>
                                    <small class="text-muted d-block">الوقت</small>
                                    <strong><?= $activity->time ?? 'غير محدد' ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <i class="bi bi-geo-alt text-danger"></i>
                                <div>
                                    <small class="text-muted d-block">المكان</small>
                                    <strong><?= htmlspecialchars($activity->location ?? 'غير محدد') ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                    $images = json_decode($activity->images_json ?? '[]', true);
                    if (!empty($images)):
                        ?>
                        <h6 class="mb-3"><i class="bi bi-images me-2"></i>صور النشاط</h6>
                        <div class="row g-2">
                            <?php foreach ($images as $img): ?>
                                <div class="col-4 col-md-3">
                                    <a href="<?= $img ?>" target="_blank">
                                        <img src="<?= $img ?>" class="img-fluid rounded"
                                            style="height:100px;width:100%;object-fit:cover;" alt="">
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Points Card -->
            <div class="card mb-4 text-center">
                <div class="card-body py-4">
                    <div class="mb-2">
                        <i class="bi bi-star-fill text-warning display-4"></i>
                    </div>
                    <h1 class="display-4 fw-bold text-warning"><?= $activity->points_value ?></h1>
                    <p class="text-muted mb-0">نقطة لكل حاضر</p>
                </div>
            </div>

            <!-- Attendance Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-people me-2"></i>الحضور</h6>
                    <span class="badge bg-primary"><?= $attendance['total'] ?? 0 ?> عضو</span>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-around text-center mb-4">
                        <div>
                            <h3 class="text-success mb-0"><?= $attendance['present'] ?? 0 ?></h3>
                            <small class="text-muted">حاضر</small>
                        </div>
                        <div>
                            <h3 class="text-danger mb-0"><?= $attendance['absent'] ?? 0 ?></h3>
                            <small class="text-muted">غائب</small>
                        </div>
                    </div>

                    <?php if ($currentUser->canManage() && !empty($members)): ?>
                        <hr>
                        <form id="attendanceForm">
                            <div class="attendance-list" style="max-height:350px; overflow-y:auto;">
                                <?php foreach ($members as $m): ?>
                                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= $m->avatar ?? '/assets/images/default-avatar.png' ?>"
                                                class="member-avatar-sm me-2" alt="">
                                            <span><?= htmlspecialchars($m->full_name) ?></span>
                                        </div>
                                        <select name="attendance[<?= $m->id ?>]" class="form-select form-select-sm w-auto">
                                            <option value="">-</option>
                                            <option value="present" <?= ($attendanceMap[$m->id] ?? '') === 'present' ? 'selected' : '' ?>>حاضر</option>
                                            <option value="absent" <?= ($attendanceMap[$m->id] ?? '') === 'absent' ? 'selected' : '' ?>>غائب</option>
                                        </select>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="submit" class="btn btn-success w-100 mt-3">
                                <i class="bi bi-check-lg me-1"></i>حفظ الحضور
                            </button>
                        </form>
                    <?php elseif (empty($members)): ?>
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-people display-4 d-block mb-2"></i>
                            <p>لا يوجد أعضاء</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const activityId = <?= $activity->id ?>;

    // Save attendance
    document.getElementById('attendanceForm')?.addEventListener('submit', function (e) {
        e.preventDefault();

        fetch(`/api/activities/${activityId}/attendance`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(new FormData(this)).toString() + '&_csrf_token=' + csrfToken
        })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('تم', data.message, 'success').then(() => location.reload());
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(err => Swal.fire('خطأ', err.message, 'error'));
    });

    // Change status
    function changeStatus(status) {
        fetch(`/api/activities/${activityId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `status=${status}&_csrf_token=${csrfToken}`
        })
            .then(r => r.json())
            .then(data => {
                if (data.success) location.reload();
            });
    }

    // Delete activity
    function deleteActivity() {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم حذف النشاط نهائياً',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e63946',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`/api/activities/${activityId}`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `_csrf_token=${csrfToken}`
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('تم', data.message, 'success').then(() => window.location = '/activities');
                        }
                    });
            }
        });
    }
</script>