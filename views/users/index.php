<!-- Users Management Page - Enhanced -->
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="page-title"><i class="bi bi-people me-2"></i>إدارة الأعضاء</h2>
            <p class="text-muted mb-0">عرض وإدارة أعضاء الجمعية</p>
        </div>
        <?php if ($currentUser->canManage()): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-plus-lg me-1"></i>إضافة عضو
            </button>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <select class="form-select" id="filterRole">
                        <option value="">كل الأدوار</option>
                        <option value="admin">المدراء</option>
                        <option value="head">رؤساء اللجان</option>
                        <option value="member">الأعضاء</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterCommittee">
                        <option value="">كل اللجان</option>
                        <?php foreach ($committees as $c): ?>
                            <option value="<?= $c->id ?>"><?= htmlspecialchars($c->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="بحث بالاسم أو البريد...">
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100"
                        onclick="$('#usersTable').DataTable().ajax.reload()">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="usersTable" class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>العضو</th>
                            <th class="d-none d-md-table-cell">البريد</th>
                            <th>رقم البطاقة</th>
                            <th class="d-none d-lg-table-cell">الدور</th>
                            <th class="d-none d-lg-table-cell">اللجنة</th>
                            <th>النقاط</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>إضافة عضو جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addUserForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الهاتف</label>
                            <input type="tel" class="form-control" name="phone" placeholder="0555123456">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الدور</label>
                            <select class="form-select" name="role">
                                <option value="member">عضو</option>
                                <?php if ($currentUser->isAdmin()): ?>
                                    <option value="head">رئيس لجنة</option>
                                    <option value="admin">مدير</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">اللجنة</label>
                            <select class="form-select" name="committee_id">
                                <option value="">بدون لجنة</option>
                                <?php foreach ($committees as $c): ?>
                                    <option value="<?= $c->id ?>"><?= htmlspecialchars($c->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الصورة</label>
                            <input type="file" class="form-control" name="avatar" accept="image/*">
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        سيتم إنشاء رقم بطاقة العضوية تلقائياً وإرسالها عبر البريد
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>تعديل بيانات العضو</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm">
                <input type="hidden" name="user_id" id="editUserId">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">الاسم الكامل</label>
                            <input type="text" class="form-control" name="full_name" id="editFullName">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" name="email" id="editEmail">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الهاتف</label>
                            <input type="tel" class="form-control" name="phone" id="editPhone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الدور</label>
                            <select class="form-select" name="role" id="editRole">
                                <option value="member">عضو</option>
                                <option value="head">رئيس لجنة</option>
                                <option value="admin">مدير</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">اللجنة</label>
                            <select class="form-select" name="committee_id" id="editCommittee">
                                <option value="">بدون</option>
                                <?php foreach ($committees as $c): ?>
                                    <option value="<?= $c->id ?>"><?= htmlspecialchars($c->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Points Modal -->
<div class="modal fade" id="addPointsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-star me-2"></i>إضافة نقاط</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addPointsForm">
                <input type="hidden" name="user_id" id="pointsUserId">
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <h6 class="text-muted" id="pointsUserName"></h6>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">عدد النقاط <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="points" required>
                        <small class="text-muted">استخدم سالب للخصم</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">السبب <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="reason" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">النوع</label>
                        <select class="form-select" name="type">
                            <option value="manual">إضافة يدوية</option>
                            <option value="social">تفاعل سوشيال ميديا</option>
                            <option value="office_visit">زيارة المقر</option>
                            <option value="bonus">مكافأة</option>
                            <option value="penalty">خصم</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-plus-lg me-1"></i>إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        // DataTable
        const table = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/api/users',
                type: 'GET',
                data: d => {
                    d.role = $('#filterRole').val();
                    d.committee = $('#filterCommittee').val();
                }
            },
            columns: [
                {
                    data: null,
                    render: d => `
                    <div class="d-flex align-items-center">
                        <img src="${d.avatar || '/assets/images/default-avatar.png'}" class="member-avatar-sm me-2" alt="">
                        <div>
                            <strong>${d.full_name}</strong>
                            <small class="d-block d-md-none text-muted">${d.email}</small>
                        </div>
                    </div>`
                },
                { data: 'email', className: 'd-none d-md-table-cell' },
                { data: 'member_card_id', render: d => `<code class="text-primary">${d}</code>` },
                {
                    data: 'role_label',
                    className: 'd-none d-lg-table-cell',
                    render: (d, t, r) => {
                        const colors = { admin: 'danger', head: 'warning', member: 'success' };
                        return `<span class="badge bg-${colors[r.role] || 'secondary'}">${d}</span>`;
                    }
                },
                { data: 'committee_name', className: 'd-none d-lg-table-cell', render: d => d || '-' },
                { data: 'points_balance', render: d => `<span class="badge bg-success"><i class="bi bi-star-fill me-1"></i>${d}</span>` },
                {
                    data: null,
                    orderable: false,
                    render: d => `
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/users/${d.id}/card" target="_blank"><i class="bi bi-credit-card me-2"></i>بطاقة العضوية</a></li>
                            <li><button class="dropdown-item" onclick="openAddPoints(${d.id}, '${d.full_name}')"><i class="bi bi-star me-2"></i>إضافة نقاط</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item" onclick="editUser(${d.id})"><i class="bi bi-pencil me-2"></i>تعديل</button></li>
                            <li><button class="dropdown-item text-danger" onclick="deleteUser(${d.id})"><i class="bi bi-trash me-2"></i>حذف</button></li>
                        </ul>
                    </div>`
                }
            ],
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json' },
            order: [[0, 'asc']],
            pageLength: 10,
            responsive: true
        });

        // Filters
        $('#filterRole, #filterCommittee').on('change', () => table.ajax.reload());
        $('#searchInput').on('keyup', function () { table.search(this.value).draw(); });

        // Add User
        $('#addUserForm').on('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('_csrf_token', csrfToken);

            $.ajax({
                url: '/api/users', method: 'POST', data: formData, processData: false, contentType: false,
                success: r => {
                    Swal.fire('تم بنجاح', `رقم البطاقة: ${r.data.member_card_id}`, 'success');
                    $('#addUserModal').modal('hide');
                    this.reset();
                    table.ajax.reload();
                },
                error: xhr => Swal.fire('خطأ', xhr.responseJSON?.message || 'حدث خطأ', 'error')
            });
        });

        // Edit User
        $('#editUserForm').on('submit', function (e) {
            e.preventDefault();
            const userId = $('#editUserId').val();

            $.ajax({
                url: `/api/users/${userId}`, method: 'POST',
                data: $(this).serialize() + '&_csrf_token=' + csrfToken,
                success: r => {
                    Swal.fire('تم', r.message, 'success');
                    $('#editUserModal').modal('hide');
                    table.ajax.reload();
                },
                error: xhr => Swal.fire('خطأ', xhr.responseJSON?.message, 'error')
            });
        });

        // Add Points
        $('#addPointsForm').on('submit', function (e) {
            e.preventDefault();
            const userId = $('#pointsUserId').val();

            $.ajax({
                url: `/api/users/${userId}/points`, method: 'POST',
                data: $(this).serialize() + '&_csrf_token=' + csrfToken,
                success: r => {
                    Swal.fire('تم', r.message, 'success');
                    $('#addPointsModal').modal('hide');
                    this.reset();
                    table.ajax.reload();
                },
                error: xhr => Swal.fire('خطأ', xhr.responseJSON?.message, 'error')
            });
        });
    });

    function editUser(id) {
        $.get(`/api/users/${id}`, function (r) {
            if (r.success) {
                const u = r.data;
                $('#editUserId').val(u.id);
                $('#editFullName').val(u.full_name);
                $('#editEmail').val(u.email);
                $('#editPhone').val(u.phone);
                $('#editRole').val(u.role);
                $('#editCommittee').val(u.committee_id || '');
                $('#editUserModal').modal('show');
            }
        });
    }

    function openAddPoints(id, name) {
        $('#pointsUserId').val(id);
        $('#pointsUserName').text(name);
        $('#addPointsModal').modal('show');
    }

    function deleteUser(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم أرشفة العضو من النظام',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e63946',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، أرشف',
            cancelButtonText: 'إلغاء'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/users/${id}`, method: 'DELETE',
                    data: { _csrf_token: $('meta[name="csrf-token"]').attr('content') },
                    success: r => {
                        Swal.fire('تم', r.message, 'success');
                        $('#usersTable').DataTable().ajax.reload();
                    },
                    error: xhr => Swal.fire('خطأ', xhr.responseJSON?.message, 'error')
                });
            }
        });
    }
</script>