<!-- Activity Edit Page -->
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title"><i class="bi bi-pencil me-2"></i>تعديل النشاط</h2>
                    <p class="text-muted mb-0">تعديل بيانات النشاط</p>
                </div>
                <a href="/activities/<?= $activity->id ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-right me-1"></i>رجوع
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form id="editActivityForm" enctype="multipart/form-data">
                        <input type="hidden" name="activity_id" value="<?= $activity->id ?>">

                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label">عنوان النشاط <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title"
                                    value="<?= htmlspecialchars($activity->title) ?>" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">وصف النشاط</label>
                                <textarea class="form-control" name="description"
                                    rows="4"><?= htmlspecialchars($activity->description ?? '') ?></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">التاريخ <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="date" value="<?= $activity->date ?>"
                                    required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">الوقت</label>
                                <input type="time" class="form-control" name="time" value="<?= $activity->time ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">النقاط</label>
                                <input type="number" class="form-control" name="points_value"
                                    value="<?= $activity->points_value ?>" min="1" max="100">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">المكان</label>
                                <input type="text" class="form-control" name="location"
                                    value="<?= htmlspecialchars($activity->location ?? '') ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحالة</label>
                                <select class="form-select" name="status">
                                    <option value="upcoming" <?= $activity->status === 'upcoming' ? 'selected' : '' ?>>قادم
                                    </option>
                                    <option value="completed" <?= $activity->status === 'completed' ? 'selected' : '' ?>>
                                        مكتمل</option>
                                    <option value="cancelled" <?= $activity->status === 'cancelled' ? 'selected' : '' ?>>
                                        ملغي</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">اللجنة المنظمة</label>
                                <select class="form-select" name="committee_id">
                                    <option value="">نشاط عام</option>
                                    <?php foreach ($committees as $c): ?>
                                        <option value="<?= $c->id ?>" <?= $activity->committee_id == $c->id ? 'selected' : '' ?>><?= htmlspecialchars($c->name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Current Images -->
                            <?php
                            $currentImages = json_decode($activity->images_json ?? '[]', true);
                            if (!empty($currentImages)):
                                ?>
                                <div class="col-12">
                                    <label class="form-label">الصور الحالية</label>
                                    <div class="row g-2" id="currentImages">
                                        <?php foreach ($currentImages as $i => $img): ?>
                                            <div class="col-4 col-md-2 position-relative" data-image="<?= $img ?>">
                                                <img src="<?= $img ?>" class="img-fluid rounded"
                                                    style="height:80px;width:100%;object-fit:cover;">
                                                <button type="button"
                                                    class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                                    onclick="removeImage(this)" style="padding:2px 6px;font-size:10px;">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <input type="hidden" name="remove_images" id="removeImages" value="">
                                </div>
                            <?php endif; ?>

                            <!-- Images disabled -->
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-danger" onclick="deleteActivity()">
                                <i class="bi bi-trash me-1"></i>حذف النشاط
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-check-lg me-1"></i>حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const activityId = <?= $activity->id ?>;
    let removedImages = [];

    // Image preview for new uploads
    document.getElementById('imagesInput')?.addEventListener('change', function () {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';

        if (this.files.length > 5) {
            Swal.fire('تنبيه', 'يمكنك اختيار 5 صور كحد أقصى', 'warning');
            this.value = '';
            return;
        }

        Array.from(this.files).forEach((file, i) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.innerHTML += `
                <div class="col-4 col-md-2">
                    <img src="${e.target.result}" class="img-fluid rounded" style="height:80px;width:100%;object-fit:cover;">
                </div>`;
            };
            reader.readAsDataURL(file);
        });
    });

    // Remove existing image
    function removeImage(btn) {
        const parent = btn.closest('[data-image]');
        const imagePath = parent.dataset.image;
        removedImages.push(imagePath);
        document.getElementById('removeImages').value = JSON.stringify(removedImages);
        parent.remove();
    }

    // Submit form
    document.getElementById('editActivityForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...';

        const formData = new FormData(this);
        formData.append('_csrf_token', csrfToken);

        fetch(`/api/activities/${activityId}`, {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحفظ',
                        text: 'تم تحديث النشاط بنجاح',
                        confirmButtonText: 'عرض النشاط'
                    }).then(() => {
                        window.location.href = '/activities/' + activityId;
                    });
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(err => {
                Swal.fire('خطأ', err.message || 'حدث خطأ', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>حفظ التعديلات';
            });
    });

    // Delete activity
    function deleteActivity() {
        Swal.fire({
            title: 'حذف النشاط؟',
            text: 'هل أنت متأكد من حذف هذا النشاط نهائياً؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d62828',
            cancelButtonColor: '#6c757d',
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
                            Swal.fire('تم الحذف', data.message, 'success')
                                .then(() => window.location.href = '/activities');
                        }
                    });
            }
        });
    }
</script>