<!-- Activities Management Page - Enhanced -->
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="page-title"><i class="bi bi-calendar-event me-2"></i>إدارة الأنشطة</h2>
            <p class="text-muted mb-0">عرض وإدارة أنشطة الجمعية</p>
        </div>
        <div class="d-flex gap-2">
            <div class="btn-group">
                <a href="/activities" class="btn btn-outline-primary active"><i class="bi bi-list"></i></a>
                <a href="/activities/feed" class="btn btn-outline-primary"><i class="bi bi-rss"></i></a>
            </div>
            <?php if ($currentUser->canManage()): ?>
                <a href="/activities/create" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>نشاط جديد
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <select class="form-select" id="filterStatus">
                        <option value="">كل الحالات</option>
                        <option value="upcoming">قادم</option>
                        <option value="completed">مكتمل</option>
                        <option value="cancelled">ملغي</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterCommittee">
                        <option value="">كل اللجان</option>
                        <?php foreach ($committees ?? [] as $c): ?>
                            <option value="<?= $c->id ?>"><?= htmlspecialchars($c->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="بحث...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activities Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="activitiesTable" class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>النشاط</th>
                            <th>التاريخ</th>
                            <th class="d-none d-md-table-cell">المكان</th>
                            <th>النقاط</th>
                            <th>الحالة</th>
                            <th class="d-none d-lg-table-cell">الحضور</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const canManage = <?= $currentUser->canManage() ? 'true' : 'false' ?>;

        const table = $('#activitiesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/api/activities',
                data: d => {
                    d.status = $('#filterStatus').val();
                    d.committee = $('#filterCommittee').val();
                }
            },
            columns: [
                { data: 'title', render: (d, t, r) => `<strong>${d}</strong>` },
                { data: 'date' },
                { data: 'location', className: 'd-none d-md-table-cell', render: d => d || '-' },
                { data: 'points_value', render: d => `<span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>${d}</span>` },
                {
                    data: 'status',
                    render: (d, t, r) => {
                        const labels = { upcoming: 'قادم', completed: 'مكتمل', cancelled: 'ملغي' };
                        const colors = { upcoming: 'primary', completed: 'success', cancelled: 'danger' };
                        return `<span class="badge bg-${colors[d]}">${labels[d]}</span>`;
                    }
                },
                { data: 'attendee_count', className: 'd-none d-lg-table-cell', render: d => `<span class="badge bg-info">${d || 0}</span>` },
                {
                    data: null,
                    orderable: false,
                    render: d => {
                        let html = `<a href="/activities/${d.id}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>`;
                        if (canManage) {
                            html += `
                        <div class="dropdown d-inline-block ms-1">
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/activities/${d.id}"><i class="bi bi-pencil me-2"></i>تعديل</a></li>
                                <li><button class="dropdown-item" onclick="markComplete(${d.id})"><i class="bi bi-check-circle me-2"></i>إكمال</button></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item text-danger" onclick="deleteActivity(${d.id})"><i class="bi bi-trash me-2"></i>حذف</button></li>
                            </ul>
                        </div>`;
                        }
                        return html;
                    }
                }
            ],
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json' },
            order: [[1, 'desc']],
            responsive: true
        });

        $('#filterStatus, #filterCommittee').on('change', () => table.ajax.reload());
        $('#searchInput').on('keyup', function () { table.search(this.value).draw(); });

        window.markComplete = function (id) {
            Swal.fire({
                title: 'إكمال النشاط',
                text: 'هل تريد وضع علامة مكتمل على هذا النشاط؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، أكمل',
                cancelButtonText: 'إلغاء'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/activities/${id}`, method: 'POST',
                        data: { status: 'completed', _csrf_token: csrfToken },
                        success: r => {
                            Swal.fire('تم', 'تم إكمال النشاط', 'success');
                            table.ajax.reload();
                        }
                    });
                }
            });
        };

        window.deleteActivity = function (id) {
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
                    $.ajax({
                        url: `/api/activities/${id}`, method: 'DELETE',
                        data: { _csrf_token: csrfToken },
                        success: r => {
                            Swal.fire('تم', r.message, 'success');
                            table.ajax.reload();
                        },
                        error: xhr => Swal.fire('خطأ', xhr.responseJSON?.message, 'error')
                    });
                }
            });
        };
    });
</script>