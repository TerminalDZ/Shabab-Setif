<?php
$title = 'إدارة الأعضاء';
ob_start();
?>

<div class="px-4 py-6 md:px-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">إدارة الأعضاء</h1>
            <p class="text-gray-500 mt-2">عرض وإدارة قاعدة بيانات أعضاء الجمعية</p>
        </div>
        <?php if ($currentUser->canManage()): ?>
            <button onclick="openModal('addUserModal')"
                class="px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary-dark transition-colors shadow-lg shadow-primary/30 flex items-center gap-2 font-bold">
                <i class="bi bi-person-plus-fill"></i>
                <span>عضو جديد</span>
            </button>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative md:col-span-2">
                <i class="bi bi-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="searchInput" placeholder="بحث بالاسم، البريد أو الهاتف..."
                    class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
            </div>
            <div class="relative">
                <select id="filterRole"
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none appearance-none cursor-pointer">
                    <option value="">كل الأدوار</option>
                    <option value="admin">المدراء</option>
                    <option value="head">رؤساء اللجان</option>
                    <option value="member">الأعضاء</option>
                </select>
                <i
                    class="bi bi-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
            </div>
            <div class="relative">
                <select id="filterCommittee"
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none appearance-none cursor-pointer">
                    <option value="">كل اللجان</option>
                    <?php foreach ($committees as $c): ?>
                        <option value="<?= $c->id ?>"><?= htmlspecialchars($c->name) ?></option>
                    <?php endforeach; ?>
                </select>
                <i
                    class="bi bi-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">العضو</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase hidden md:table-cell">البيانات
                        </th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase hidden lg:table-cell">الدور
                            واللجنة</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">النقاط</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-left"></th>
                    </tr>
                </thead>
                <tbody id="usersTableBody" class="divide-y divide-gray-50">
                    <!-- Loaded via JS -->
                </tbody>
            </table>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="p-8 text-center hidden">
            <div class="inline-block w-8 h-8 border-4 border-primary/20 border-t-primary rounded-full animate-spin">
            </div>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100 flex items-center justify-between" id="paginationControls">
            <span class="text-sm text-gray-500" id="paginationInfo"></span>
            <div class="flex items-center gap-2">
                <button id="prevPageBtn"
                    class="px-3 py-1 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-50"><i
                        class="bi bi-chevron-right"></i></button>
                <button id="nextPageBtn"
                    class="px-3 py-1 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-50"><i
                        class="bi bi-chevron-left"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Add User Modal -->
<div id="addUserModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('addUserModal')">
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-2xl text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-primary px-4 py-3 sm:px-6 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-bold text-white">إضافة عضو جديد</h3>
                <button onclick="closeModal('addUserModal')" class="text-white hover:text-gray-200"><i
                        class="bi bi-x-lg"></i></button>
            </div>
            <form id="addUserForm" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-gray-700">الاسم الكامل</label>
                        <input type="text" name="full_name" required
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-sm font-bold text-gray-700">البريد الإلكتروني</label>
                        <input type="email" name="email" required
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-sm font-bold text-gray-700">الهاتف</label>
                        <input type="tel" name="phone"
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-sm font-bold text-gray-700">الدور</label>
                        <select name="role"
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                            <option value="member">عضو</option>
                            <?php if ($currentUser->isAdmin()): ?>
                                <option value="head">رئيس لجنة</option>
                                <option value="admin">مدير</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-sm font-bold text-gray-700">اللجنة</label>
                        <select name="committee_id"
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                            <option value="">بدون لجنة</option>
                            <?php foreach ($committees as $c): ?>
                                <option value="<?= $c->id ?>"><?= htmlspecialchars($c->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <!-- Simple File Input for now, can be Dropzone later if needed -->
                <div>
                    <label class="block text-sm font-bold text-gray-700">الصورة</label>
                    <input type="file" name="avatar" accept="image/*"
                        class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('addUserModal')"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">إلغاء</button>
                    <button type="submit"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal (Similar Structure) -->
<div id="editUserModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('editUserModal')">
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-2xl text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                <h3 class="font-bold text-gray-800">تعديل بيانات العضو</h3>
                <button onclick="closeModal('editUserModal')" class="text-gray-400 hover:text-gray-600"><i
                        class="bi bi-x-lg"></i></button>
            </div>
            <form id="editUserForm" class="p-6 space-y-4">
                <input type="hidden" name="user_id" id="editUserId">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">الاسم الكامل</label>
                        <input type="text" name="full_name" id="editFullName"
                            class="mt-1 block w-full rounded-xl border-gray-300">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700">البريد</label>
                            <input type="email" name="email" id="editEmail"
                                class="mt-1 block w-full rounded-xl border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">الهاتف</label>
                            <input type="tel" name="phone" id="editPhone"
                                class="mt-1 block w-full rounded-xl border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">الدور</label>
                            <select name="role" id="editRole" class="mt-1 block w-full rounded-xl border-gray-300">
                                <option value="member">عضو</option>
                                <option value="head">رئيس لجنة</option>
                                <option value="admin">مدير</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">اللجنة</label>
                            <select name="committee_id" id="editCommittee"
                                class="mt-1 block w-full rounded-xl border-gray-300">
                                <option value="">بدون</option>
                                <?php foreach ($committees as $c): ?>
                                    <option value="<?= $c->id ?>"><?= htmlspecialchars($c->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('editUserModal')"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">إلغاء</button>
                    <button type="submit"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Points Modal -->
<div id="addPointsModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('addPointsModal')">
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-2xl text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-yellow-500 px-4 py-3 flex justify-between items-center text-white">
                <h3 class="font-bold mb-0">إضافة نقاط</h3>
                <button onclick="closeModal('addPointsModal')" class="text-white hover:text-white/80"><i
                        class="bi bi-x-lg"></i></button>
            </div>
            <form id="addPointsForm" class="p-6">
                <input type="hidden" name="user_id" id="pointsUserId">
                <div class="text-center mb-6">
                    <div
                        class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-2 text-2xl font-bold">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <h4 class="font-bold text-gray-800" id="pointsUserName"></h4>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">عدد النقاط</label>
                        <input type="number" name="points" required placeholder="0"
                            class="mt-1 block w-full rounded-xl border-gray-300 focus:ring-yellow-500 focus:border-yellow-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">السبب</label>
                        <input type="text" name="reason" required placeholder="مثال: تفاعل ممتاز"
                            class="mt-1 block w-full rounded-xl border-gray-300 focus:ring-yellow-500 focus:border-yellow-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">النوع</label>
                        <select name="type"
                            class="mt-1 block w-full rounded-xl border-gray-300 focus:ring-yellow-500 focus:border-yellow-500">
                            <option value="manual">إضافة يدوية</option>
                            <option value="social">تفاعل سوشيال ميديا</option>
                            <option value="bonus">مكافأة</option>
                            <option value="penalty">خصم</option>
                        </select>
                    </div>
                </div>
                <div class="pt-6">
                    <button type="submit"
                        class="w-full py-3 bg-yellow-500 text-white font-bold rounded-xl hover:bg-yellow-600 shadow-lg shadow-yellow-500/30">إضافة
                        النقاط</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentPage = 0;
    const pageSize = 10;
    const canManage = <?= $currentUser->canManage() ? 'true' : 'false' ?>;

    function fetchUsers() {
        const loading = document.getElementById('loadingState');
        const tbody = document.getElementById('usersTableBody');
        loading.classList.remove('hidden');
        tbody.innerHTML = '';

        const params = new URLSearchParams({
            draw: 1,
            start: currentPage * pageSize,
            length: pageSize,
            'search[value]': document.getElementById('searchInput').value,
            role: document.getElementById('filterRole').value,
            committee: document.getElementById('filterCommittee').value
        });

        // Add order
        params.append('order[0][column]', 0);
        params.append('order[0][dir]', 'asc');

        fetch(`/api/users?${params.toString()}`)
            .then(r => r.json())
            .then(data => {
                loading.classList.add('hidden');
                renderTable(data.data);
                updatePagination(data.recordsTotal, data.recordsFiltered);
            });
    }

    function renderTable(users) {
        const tbody = document.getElementById('usersTableBody');
        users.forEach(u => {
            const roleBadges = {
                admin: 'bg-red-100 text-red-700',
                head: 'bg-orange-100 text-orange-700',
                member: 'bg-green-100 text-green-700'
            };
            const roleLabels = { admin: 'مدير', head: 'رئيس لجنة', member: 'عضو' };

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/50 transition-colors group';
            tr.innerHTML = `
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <img src="${u.avatar || '/assets/images/default-avatar.png'}" class="w-10 h-10 rounded-full object-cover border border-gray-100">
                        <div>
                            <div class="font-bold text-gray-800 text-sm">${u.full_name}</div>
                            <div class="text-xs text-primary font-mono">${u.member_card_id}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 hidden md:table-cell">
                    <div class="text-sm text-gray-600">${u.email}</div>
                    <div class="text-xs text-gray-400">${u.phone || ''}</div>
                </td>
                <td class="px-6 py-4 hidden lg:table-cell">
                    <div class="flex flex-col gap-1 text-sm">
                        <span class="inline-flex w-fit px-2 py-0.5 rounded text-xs font-bold ${roleBadges[u.role]}">${roleLabels[u.role]}</span>
                        <span class="text-gray-500 text-xs">${u.committee_name || '-'}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center px-2 py-1 rounded-lg bg-yellow-50 text-yellow-700 font-bold text-xs border border-yellow-100">
                        <i class="bi bi-star-fill mr-1 text-[10px]"></i> ${u.points_balance}
                    </span>
                </td>
                <td class="px-6 py-4 text-left">
                     <div class="relative group/menu inline-block">
                        <button onclick="event.stopPropagation(); toggleRowMenu(${u.id})" class="p-2 text-gray-400 hover:text-primary rounded-lg transition-colors">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <div id="row-menu-${u.id}" class="hidden absolute left-0 top-full mt-1 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-20 text-right">
                            <a href="/users/${u.id}/card" target="_blank" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                <i class="bi bi-credit-card"></i> بطاقة العضوية
                            </a>
                            ${canManage ? `
                            <button onclick="openAddPoints(${u.id}, '${u.full_name}')" class="w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                <i class="bi bi-star"></i> إضافة نقاط
                            </button>
                            <button onclick="editUser(${u.id})" class="w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                <i class="bi bi-pencil"></i> تعديل
                            </button>
                            <div class="border-t border-gray-50 my-1"></div>
                            <button onclick="deleteUser(${u.id})" class="w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                <i class="bi bi-trash"></i> حذف العضو
                            </button>
                            ` : ''}
                        </div>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function updatePagination(total, filtered) {
        document.getElementById('paginationInfo').innerText = `عرض ${(currentPage * pageSize) + 1} - ${Math.min((currentPage + 1) * pageSize, filtered)} من ${filtered}`;
        document.getElementById('prevPageBtn').disabled = currentPage === 0;
        document.getElementById('nextPageBtn').disabled = (currentPage + 1) * pageSize >= filtered;
    }

    // Modal Helpers (using main.php provided openModal is good, but I rewrote explicit ones here for clarity and form handling)
    // Actually, I can use the global openModal/closeModal if compatible, but I defined IDs.
    // Let's stick to the inline `closeModal` defined in main.php if present or define it.
    // main.php `app.js` has `openModal` and `closeModal` which uses logic `document.getElementById(id).classList.remove('hidden')`.
    // My modals in this file have class `hidden`. So it should work matching `app.js` logic.

    window.toggleRowMenu = function (id) {
        document.querySelectorAll('[id^="row-menu-"]').forEach(el => {
            if (el.id !== `row-menu-${id}`) el.classList.add('hidden');
        });
        document.getElementById(`row-menu-${id}`).classList.toggle('hidden');
    }

    // Global click to close menus
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.group/menu')) {
            document.querySelectorAll('[id^="row-menu-"]').forEach(el => el.classList.add('hidden'));
        }
    });

    // Edit User
    window.editUser = function (id) {
        $.get(`/api/users/${id}`, function (r) {
            if (r.success) {
                const u = r.data;
                $('#editUserId').val(u.id);
                $('#editFullName').val(u.full_name);
                $('#editEmail').val(u.email);
                $('#editPhone').val(u.phone);
                $('#editRole').val(u.role);
                $('#editCommittee').val(u.committee_id || '');
                openModal('editUserModal');
            }
        });
    }

    window.openAddPoints = function (id, name) {
        $('#pointsUserId').val(id);
        $('#pointsUserName').text(name);
        openModal('addPointsModal');
    }

    window.deleteUser = function (id) {
        confirmDelete('سيتم حذف العضو نهائياً', () => {
            $.ajax({
                url: `/api/users/${id}`, method: 'DELETE',
                data: { _csrf_token: $('meta[name="csrf-token"]').attr('content') },
                success: r => {
                    if (r.success) fetchUsers();
                }
            });
        });
    }

    // Form Submits
    $('#addUserForm').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('_csrf_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: '/api/users', method: 'POST', data: formData, processData: false, contentType: false,
            success: r => {
                if (r.success) {
                    Swal.fire({ icon: 'success', title: 'تم', text: `رقم البطاقة: ${r.data.member_card_id}` });
                    closeModal('addUserModal');
                    this.reset();
                    fetchUsers();
                }
            },
            error: xhr => Swal.fire('خطأ', xhr.responseJSON?.message, 'error')
        });
    });

    $('#editUserForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: `/api/users/${$('#editUserId').val()}`, method: 'POST',
            data: $(this).serialize() + '&_csrf_token=' + $('meta[name="csrf-token"]').attr('content'),
            success: r => {
                Swal.fire('تم', 'تم التعديل بنجاح', 'success');
                closeModal('editUserModal');
                fetchUsers();
            }
        });
    });

    $('#addPointsForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: `/api/users/${$('#pointsUserId').val()}/points`, method: 'POST',
            data: $(this).serialize() + '&_csrf_token=' + $('meta[name="csrf-token"]').attr('content'),
            success: r => {
                Swal.fire('تم', 'تم إضافة النقاط', 'success');
                closeModal('addPointsModal');
                this.reset();
                fetchUsers();
            }
        });
    });

    // Listeners
    $('#searchInput').on('input', function () {
        clearTimeout(this.timer);
        this.timer = setTimeout(fetchUsers, 300);
    });
    $('#filterRole, #filterCommittee').on('change', fetchUsers);

    $('#prevPageBtn').click(() => { if (currentPage > 0) { currentPage--; fetchUsers(); } });
    $('#nextPageBtn').click(() => { currentPage++; fetchUsers(); });

    // Initial Load
    fetchUsers();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>