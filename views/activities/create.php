<!-- Create Activity Page - Enhanced -->
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-plus me-2"></i>إنشاء نشاط جديد
                    </h5>
                </div>
                <form id="createActivityForm" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label">عنوان النشاط <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="title" required
                                    placeholder="مثال: دوري كرة القدم السنوي">
                            </div>

                            <div class="col-12">
                                <label class="form-label">وصف النشاط</label>
                                <textarea class="form-control" name="description" rows="4"
                                    placeholder="وصف تفصيلي عن النشاط..."></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">التاريخ <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="date" required min="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">الوقت</label>
                                <input type="time" class="form-control" name="time">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">النقاط</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="points_value" value="10" min="1"
                                        max="100">
                                    <span class="input-group-text"><i class="bi bi-star-fill text-warning"></i></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">المكان</label>
                                <input type="text" class="form-control" name="location"
                                    placeholder="مثال: ملعب الجمعية">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">اللجنة المنظمة</label>
                                <select class="form-select" name="committee_id">
                                    <option value="">نشاط عام (للجميع)</option>
                                    <?php foreach ($committees as $c): ?>
                                        <option value="<?= $c->id ?>"><?= htmlspecialchars($c->name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Images disabled -->
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-between">
                        <a href="/activities" class="btn btn-secondary">
                            <i class="bi bi-arrow-right me-1"></i>رجوع
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-lg me-1"></i>إنشاء النشاط
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Image preview
    document.getElementById('imagesInput').addEventListener('change', function () {
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

    // Submit form
    document.getElementById('createActivityForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>جاري الإنشاء...';

        const formData = new FormData(this);
        formData.append('_csrf_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch('/api/activities', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم بنجاح',
                        text: 'تم إنشاء النشاط بنجاح',
                        confirmButtonText: 'عرض النشاط'
                    }).then(() => {
                        window.location.href = '/activities/' + data.data.id;
                    });
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(err => {
                Swal.fire('خطأ', err.message || 'حدث خطأ', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>إنشاء النشاط';
            });
    });
</script>