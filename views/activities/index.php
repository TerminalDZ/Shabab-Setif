<?php
$title = 'إدارة الأنشطة';
ob_start();
?>

<div class="px-4 py-6 md:px-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">إدارة الأنشطة</h1>
            <p class="text-gray-500 mt-2">عرض وإدارة أنشطة الجمعية والفعاليات</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="flex bg-gray-100 p-1 rounded-xl">
                <button class="px-4 py-2 bg-white text-primary shadow-sm rounded-lg font-bold text-sm transition-all">
                    <i class="bi bi-list"></i>
                </button>
                <a href="/activities/feed"
                    class="px-4 py-2 text-gray-500 hover:text-primary rounded-lg font-bold text-sm transition-all">
                    <i class="bi bi-grid"></i>
                </a>
            </div>
            <?php if ($currentUser->canManage()): ?>
                <a href="/activities/create"
                    class="px-4 py-2 bg-primary text-white rounded-xl hover:bg-primary-dark transition-colors shadow-lg shadow-primary/30 flex items-center gap-2 font-bold">
                    <i class="bi bi-plus-lg"></i>
                    <span class="hidden sm:inline">نشاط جديد</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="relative">
                <i class="bi bi-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="searchInput" placeholder="بحث عن نشاط..."
                    class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
            </div>
            <div class="relative">
                <select id="filterStatus"
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none appearance-none cursor-pointer">
                    <option value="">كل الحالات</option>
                    <option value="upcoming">قادم (مفتوح)</option>
                    <option value="completed">مكتمل</option>
                    <option value="cancelled">ملغي</option>
                </select>
                <i
                    class="bi bi-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
            </div>
            <div class="relative">
                <select id="filterCommittee"
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none appearance-none cursor-pointer">
                    <option value="">كل اللجان</option>
                    <?php foreach ($committees ?? [] as $c): ?>
                        <option value="<?= $c->id ?>"><?= htmlspecialchars($c->name) ?></option>
                    <?php endforeach; ?>
                </select>
                <i
                    class="bi bi-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
            </div>
        </div>
    </div>

    <!-- Content Grid/Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">النشاط</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th
                            class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">
                            المكان</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">
                            النقاط</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">
                            الحالة</th>
                        <th
                            class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center hidden lg:table-cell">
                            الحضور</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-left"></th>
                    </tr>
                </thead>
                <tbody id="activitiesTableBody" class="divide-y divide-gray-50">
                    <!-- Loaded via JS -->
                </tbody>
            </table>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="p-8 text-center hidden">
            <div class="inline-block w-8 h-8 border-4 border-primary/20 border-t-primary rounded-full animate-spin">
            </div>
            <p class="mt-2 text-sm text-gray-500">جاري التحميل...</p>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="p-12 text-center hidden">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                <i class="bi bi-calendar-x text-3xl"></i>
            </div>
            <h3 class="font-bold text-gray-800">لا توجد أنشطة</h3>
            <p class="text-gray-500 text-sm mt-1">حاول تغيير خيارات البحث أو الفلترة</p>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100 flex items-center justify-between" id="paginationControls">
            <span class="text-sm text-gray-500" id="paginationInfo">عرض 0-0 من 0</span>
            <div class="flex items-center gap-2">
                <button id="prevPageBtn"
                    class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="bi bi-chevron-right"></i>
                </button>
                <button id="nextPageBtn"
                    class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="bi bi-chevron-left"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const canManage = <?= $currentUser->canManage() ? 'true' : 'false' ?>;
    let currentPage = 0;
    const pageSize = 10;
    let debounceTimer;

    function fetchActivities() {
        const loading = document.getElementById('loadingState');
        const tbody = document.getElementById('activitiesTableBody');
        const empty = document.getElementById('emptyState');

        loading.classList.remove('hidden');
        tbody.innerHTML = '';
        empty.classList.add('hidden');

        const params = new URLSearchParams({
            draw: 1,
            start: currentPage * pageSize,
            length: pageSize,
            'search[value]': document.getElementById('searchInput').value,
            status: document.getElementById('filterStatus').value,
            committee: document.getElementById('filterCommittee').value
        });

        // Add order param (default date desc)
        params.append('order[0][column]', 1);
        params.append('order[0][dir]', 'desc');

        fetch(`/api/activities?${params.toString()}`)
            .then(r => r.json())
            .then(data => {
                loading.classList.add('hidden');

                if (data.data && data.data.length > 0) {
                    renderTable(data.data);
                    updatePagination(data.recordsTotal, data.recordsFiltered);
                } else {
                    empty.classList.remove('hidden');
                    updatePagination(0, 0);
                }
            })
            .catch(err => {
                loading.classList.add('hidden');
                console.error(err);
                showToast('error', 'فشل تحميل البيانات');
            });
    }

    function renderTable(items) {
        const tbody = document.getElementById('activitiesTableBody');
        const statusConfig = {
            upcoming: { label: 'قادم', class: 'bg-blue-50 text-blue-700' },
            completed: { label: 'مكتمل', class: 'bg-green-50 text-green-700' },
            cancelled: { label: 'ملغي', class: 'bg-red-50 text-red-700' }
        };

        items.forEach(item => {
            const status = statusConfig[item.status] || statusConfig.upcoming;

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/50 transition-colors group';
            tr.innerHTML = `
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 font-bold shrink-0">
                            ${item.title.substring(0, 2)}
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-sm">${item.title}</div>
                            <div class="text-xs text-gray-500">${item.creator_name || 'النظام'}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    <span class="block font-medium">${item.date}</span>
                    <span class="text-xs text-gray-400">${item.time || ''}</span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500 hidden md:table-cell">
                    ${item.location ? `<span class="flex items-center gap-1"><i class="bi bi-geo-alt text-gray-400"></i> ${item.location}</span>` : '-'}
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-100">
                        <i class="bi bi-star-fill mr-1 text-[10px]"></i>
                        ${item.points_value}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold ${status.class}">
                        ${status.label}
                    </span>
                </td>
                <td class="px-6 py-4 text-center hidden lg:table-cell">
                    <span class="text-sm font-bold text-gray-700">${item.attendee_count}</span>
                </td>
                <td class="px-6 py-4 text-left">
                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="/activities/${item.id}" class="p-2 text-gray-500 hover:text-primary hover:bg-primary/5 rounded-lg transition-colors">
                            <i class="bi bi-eye"></i>
                        </a>
                        ${canManage ? `
                        <div class="relative group/menu">
                            <button onclick="toggleRowMenu(${item.id})" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <div id="row-menu-${item.id}" class="hidden absolute left-0 top-full mt-1 w-40 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-20">
                                <a href="/activities/${item.id}/edit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-right">تعديل</a>
                                <button onclick="quickUpdateStatus(${item.id}, 'completed')" class="w-full block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-right">إكمال</button>
                                <div class="border-t border-gray-50 my-1"></div>
                                <button onclick="quickDelete(${item.id})" class="w-full block px-4 py-2 text-sm text-red-600 hover:bg-red-50 text-right">حذف</button>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function updatePagination(total, filtered) {
        document.getElementById('paginationInfo').textContent =
            `عرض ${(currentPage * pageSize) + 1} - ${Math.min((currentPage + 1) * pageSize, filtered)} من ${filtered}`;

        document.getElementById('prevPageBtn').disabled = currentPage === 0;
        document.getElementById('nextPageBtn').disabled = (currentPage + 1) * pageSize >= filtered;
    }

    // Toggle Row Menu logic
    window.toggleRowMenu = function (id) {
        // Hide others
        document.querySelectorAll('[id^="row-menu-"]').forEach(el => {
            if (el.id !== `row-menu-${id}`) el.classList.add('hidden');
        });
        document.getElementById(`row-menu-${id}`).classList.toggle('hidden');
    }

    window.quickUpdateStatus = function (id, status) {
        $.post(`/api/activities/${id}`, {
            status: status,
            _csrf_token: $('meta[name="csrf-token"]').attr('content')
        }, function (r) {
            if (r.success) fetchActivities();
        });
    }

    window.quickDelete = function (id) {
        confirmDelete('سيتم حذف النشاط', () => {
            $.ajax({
                url: `/api/activities/${id}`,
                type: 'DELETE',
                data: { _csrf_token: $('meta[name="csrf-token"]').attr('content') },
                success: function (r) {
                    if (r.success) fetchActivities();
                }
            });
        });
    }

    // Handlers
    document.getElementById('searchInput').addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchActivities, 300);
    });
    document.getElementById('filterStatus').addEventListener('change', fetchActivities);
    document.getElementById('filterCommittee').addEventListener('change', fetchActivities);

    document.getElementById('prevPageBtn').addEventListener('click', () => {
        if (currentPage > 0) {
            currentPage--;
            fetchActivities();
        }
    });

    document.getElementById('nextPageBtn').addEventListener('click', () => {
        currentPage++;
        fetchActivities();
    });

    // Initial load
    fetchActivities();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>