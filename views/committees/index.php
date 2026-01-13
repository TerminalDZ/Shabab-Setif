<?php
$title = 'إدارة اللجان';
ob_start();
?>

<div class="px-4 py-6 md:px-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">إدارة اللجان</h1>
            <p class="text-gray-500 mt-2">الهيكل التنظيمي ولجان الجمعية</p>
        </div>
        <?php if ($currentUser->isAdmin()): ?>
            <button onclick="openModal('addCommitteeModal')"
                class="px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary-dark transition-colors shadow-lg shadow-primary/30 flex items-center gap-2 font-bold">
                <i class="bi bi-plus-lg"></i>
                <span>لجنة جديدة</span>
            </button>
        <?php endif; ?>
    </div>

    <!-- Committees Grid -->
    <div id="committeesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Loading -->
        <div class="col-span-full py-12 text-center">
            <div class="inline-block w-8 h-8 border-4 border-primary/20 border-t-primary rounded-full animate-spin">
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Add Committee Modal -->
<div id="addCommitteeModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            onclick="closeModal('addCommitteeModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-2xl text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-primary px-4 py-3 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">إضافة لجنة جديدة</h3>
                <button onclick="closeModal('addCommitteeModal')" class="text-white hover:text-white/80"><i
                        class="bi bi-x-lg"></i></button>
            </div>
            <form id="addCommitteeForm" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700">اسم اللجنة</label>
                    <input type="text" name="name" required
                        class="mt-1 block w-full rounded-xl border-gray-300 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700">الوصف</label>
                    <textarea name="description" rows="3"
                        class="mt-1 block w-full rounded-xl border-gray-300 focus:ring-primary focus:border-primary"></textarea>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('addCommitteeModal')"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">إلغاء</button>
                    <button type="submit"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark shadow-lg shadow-primary/30">إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Committee Modal -->
<div id="editCommitteeModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            onclick="closeModal('editCommitteeModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-2xl text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                <h3 class="font-bold text-gray-800">تعديل اللجنة</h3>
                <button onclick="closeModal('editCommitteeModal')" class="text-gray-400 hover:text-gray-600"><i
                        class="bi bi-x-lg"></i></button>
            </div>
            <form id="editCommitteeForm" class="p-6 space-y-4">
                <input type="hidden" name="committee_id" id="editCommitteeId">
                <div>
                    <label class="block text-sm font-bold text-gray-700">اسم اللجنة</label>
                    <input type="text" name="name" id="editCommitteeName" required
                        class="mt-1 block w-full rounded-xl border-gray-300 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700">الوصف</label>
                    <textarea name="description" id="editCommitteeDesc" rows="3"
                        class="mt-1 block w-full rounded-xl border-gray-300 focus:ring-primary focus:border-primary"></textarea>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('editCommitteeModal')"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">إلغاء</button>
                    <button type="submit"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark shadow-lg shadow-primary/30">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const isAdmin = <?= $currentUser->isAdmin() ? 'true' : 'false' ?>;

    function loadCommittees() {
        $.get('/api/committees', function (r) {
            let html = '';
            if (r.data && r.data.length > 0) {
                r.data.forEach(c => {
                    html += `
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg transition-all group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary to-green-700 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg shadow-primary/20 group-hover:scale-110 transition-transform">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            ${isAdmin ? `
                            <div class="relative group/menu">
                                <button class="text-gray-400 hover:text-gray-600 p-1"><i class="bi bi-three-dots-vertical"></i></button>
                                <div class="hidden group-hover/menu:block absolute left-0 top-full mt-1 w-32 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-10">
                                    <button onclick="editCommittee(${c.id}, '${c.name}', '${c.description || ''}')" class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">تعديل</button>
                                    <button onclick="deleteCommittee(${c.id})" class="block w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50">حذف</button>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">${c.name}</h3>
                        <p class="text-gray-500 text-sm mb-4 h-10 overflow-hidden text-ellipsis line-clamp-2">${c.description || 'لا يوجد وصف'}</p>
                        
                        <div class="flex items-center gap-3 mt-auto pt-4 border-t border-gray-50">
                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100 flex items-center gap-1">
                                <i class="bi bi-person"></i> ${c.member_count || 0}
                            </span>
                            <span class="px-2 py-1 bg-purple-50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100 flex items-center gap-1">
                                <i class="bi bi-calendar-event"></i> ${c.activity_count || 0}
                            </span>
                        </div>
                    </div>
                    `;
                });
            } else {
                html = '<div class="col-span-full py-12 text-center text-gray-400"><i class="bi bi-inbox text-5xl mb-3 block"></i><p>لا توجد لجان</p></div>';
            }
            $('#committeesGrid').html(html);
        });
    }

    // Handlers
    $('#addCommitteeForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '/api/committees', method: 'POST',
            data: $(this).serialize() + '&_csrf_token=' + csrfToken,
            success: r => {
                Swal.fire('تم', 'تمت الإضافة بنجاح', 'success');
                closeModal('addCommitteeModal');
                this.reset();
                loadCommittees();
            },
            error: xhr => Swal.fire('خطأ', xhr.responseJSON?.message, 'error')
        });
    });

    $('#editCommitteeForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: `/api/committees/${$('#editCommitteeId').val()}`, method: 'POST',
            data: $(this).serialize() + '&_csrf_token=' + csrfToken,
            success: r => {
                Swal.fire('تم', 'تم التعديل بنجاح', 'success');
                closeModal('editCommitteeModal');
                loadCommittees();
            },
            error: xhr => Swal.fire('خطأ', xhr.responseJSON?.message, 'error')
        });
    });

    window.editCommittee = function (id, name, desc) {
        $('#editCommitteeId').val(id);
        $('#editCommitteeName').val(name);
        $('#editCommitteeDesc').val(desc);
        openModal('editCommitteeModal');
    }

    window.deleteCommittee = function (id) {
        confirmDelete('سيتم حذف اللجنة', () => {
            $.ajax({
                url: `/api/committees/${id}`, method: 'DELETE',
                data: { _csrf_token: csrfToken },
                success: r => {
                    Swal.fire('تم الحذف', '', 'success');
                    loadCommittees();
                }
            });
        });
    }

    // Init
    loadCommittees();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>