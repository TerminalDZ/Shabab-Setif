<!-- Committees Management Page - Enhanced -->
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="page-title"><i class="bi bi-diagram-3 me-2"></i>إدارة اللجان</h2>
            <p class="text-muted mb-0">عرض وإدارة لجان الجمعية</p>
        </div>
        <?php if ($currentUser->isAdmin()): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCommitteeModal">
                <i class="bi bi-plus-lg me-1"></i>لجنة جديدة
            </button>
        <?php endif; ?>
    </div>

    <div class="row" id="committeesGrid">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
    </div>
</div>

<!-- Add Committee Modal -->
<div class="modal fade" id="addCommitteeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title"><i class="bi bi-diagram-3 me-2"></i>إضافة لجنة جديدة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCommitteeForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اسم اللجنة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea class="form-control" name="description" rows="3"
                            placeholder="وصف مختصر عن اللجنة..."></textarea>
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

<!-- Edit Committee Modal -->
<div class="modal fade" id="editCommitteeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>تعديل اللجنة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCommitteeForm">
                <input type="hidden" name="committee_id" id="editCommitteeId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اسم اللجنة</label>
                        <input type="text" class="form-control" name="name" id="editCommitteeName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea class="form-control" name="description" id="editCommitteeDesc" rows="3"></textarea>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const isAdmin = <?= $currentUser->isAdmin() ? 'true' : 'false' ?>;

        loadCommittees();

        function loadCommittees() {
            $.get('/api/committees', function (r) {
                let html = '';
                if (r.data && r.data.length > 0) {
                    r.data.forEach(c => {
                        html += `
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 committee-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="committee-icon">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    ${isAdmin ? `
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-muted" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><button class="dropdown-item" onclick="editCommittee(${c.id}, '${c.name}', '${c.description || ''}')"><i class="bi bi-pencil me-2"></i>تعديل</button></li>
                                            <li><button class="dropdown-item text-danger" onclick="deleteCommittee(${c.id})"><i class="bi bi-trash me-2"></i>حذف</button></li>
                                        </ul>
                                    </div>
                                    ` : ''}
                                </div>
                                <h5 class="card-title">${c.name}</h5>
                                <p class="card-text text-muted small">${c.description || 'بدون وصف'}</p>
                                <div class="d-flex gap-2 mt-3">
                                    <span class="badge bg-primary"><i class="bi bi-people me-1"></i>${c.member_count || 0} عضو</span>
                                    <span class="badge bg-success"><i class="bi bi-calendar me-1"></i>${c.activity_count || 0} نشاط</span>
                                </div>
                            </div>
                        </div>
                    </div>`;
                    });
                } else {
                    html = '<div class="col-12 text-center py-5"><i class="bi bi-inbox display-1 text-muted"></i><p class="mt-3 text-muted">لا توجد لجان</p></div>';
                }
                $('#committeesGrid').html(html);
            });
        }

        $('#addCommitteeForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '/api/committees', method: 'POST',
                data: $(this).serialize() + '&_csrf_token=' + csrfToken,
                success: r => {
                    Swal.fire('تم', r.message, 'success');
                    $('#addCommitteeModal').modal('hide');
                    this.reset();
                    loadCommittees();
                },
                error: xhr => Swal.fire('خطأ', xhr.responseJSON?.message, 'error')
            });
        });

        $('#editCommitteeForm').on('submit', function (e) {
            e.preventDefault();
            const id = $('#editCommitteeId').val();
            $.ajax({
                url: `/api/committees/${id}`, method: 'POST',
                data: $(this).serialize() + '&_csrf_token=' + csrfToken,
                success: r => {
                    Swal.fire('تم', r.message, 'success');
                    $('#editCommitteeModal').modal('hide');
                    loadCommittees();
                },
                error: xhr => Swal.fire('خطأ', xhr.responseJSON?.message, 'error')
            });
        });

        window.editCommittee = function (id, name, desc) {
            $('#editCommitteeId').val(id);
            $('#editCommitteeName').val(name);
            $('#editCommitteeDesc').val(desc);
            $('#editCommitteeModal').modal('show');
        };

        window.deleteCommittee = function (id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم حذف اللجنة نهائياً',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e63946',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/committees/${id}`, method: 'DELETE',
                        data: { _csrf_token: csrfToken },
                        success: r => {
                            Swal.fire('تم', r.message, 'success');
                            loadCommittees();
                        },
                        error: xhr => Swal.fire('خطأ', xhr.responseJSON?.message, 'error')
                    });
                }
            });
        };
    });
</script>

<style>
    .committee-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .committee-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .committee-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #e63946 0%, #2d6a4f 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 22px;
    }
</style>