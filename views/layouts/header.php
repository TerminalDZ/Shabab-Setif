<!-- Header -->
<header
    class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-8 shadow-sm z-10 sticky top-0">
    <!-- Left Side: Toggle & Search -->
    <div class="flex items-center gap-4">
        <button type="button"
            class="md:hidden text-gray-500 hover:text-primary transition-colors p-2 -ml-2 rounded-lg hover:bg-gray-100"
            id="sidebarToggle">
            <i class="bi bi-list text-2xl"></i>
        </button>

        <div class="hidden md:flex items-center relative">
            <i class="bi bi-search absolute right-3 text-gray-400"></i>
            <input type="text" placeholder="بحث..."
                class="w-64 pl-4 pr-10 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none"
                id="globalSearch">
        </div>
    </div>

    <!-- Right Side: Actions -->
    <div class="flex items-center gap-2 md:gap-4">
        <!-- Notifications -->
        <div class="relative" id="notificationsDropdown">
            <button
                class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-primary hover:bg-gray-50 rounded-xl transition-all relative"
                onclick="toggleDropdown('notifications-menu')">
                <i class="bi bi-bell text-xl"></i>
                <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
            </button>

            <!-- Dropdown Menu -->
            <div id="notifications-menu"
                class="hidden absolute left-0 mt-3 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 transform origin-top-left transition-all">
                <div class="flex items-center justify-between px-4 py-2 mb-2 border-b border-gray-50">
                    <span class="font-bold text-gray-800">الإشعارات</span>
                    <button class="text-xs text-primary hover:text-primary-dark font-medium">عرض الكل</button>
                </div>
                <div class="px-2 space-y-1">
                    <a href="#" class="block p-3 rounded-xl hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">نشاط جديد: دوري كرة القدم</p>
                                <span class="text-xs text-gray-500 mt-1 block">منذ 5 دقائق</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- User Menu -->
        <div class="relative" id="userDropdown">
            <button
                class="flex items-center gap-3 pl-2 pr-1 py-1 rounded-xl hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100"
                onclick="toggleDropdown('user-menu')">
                <img src="<?= $currentUser->avatar ?? '/assets/images/default-avatar.png' ?>"
                    alt="<?= htmlspecialchars($currentUser->full_name) ?>"
                    class="w-8 h-8 rounded-full object-cover shadow-sm ring-2 ring-white">
                <div class="hidden md:block text-right">
                    <p class="text-sm font-bold text-gray-800 leading-none">
                        <?= htmlspecialchars($currentUser->full_name) ?></p>
                </div>
                <i class="bi bi-chevron-down text-gray-400 text-xs hidden md:block"></i>
            </button>

            <!-- Dropdown Menu -->
            <div id="user-menu"
                class="hidden absolute left-0 mt-3 w-60 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 transform origin-top-left transition-all">
                <div class="px-4 py-3 border-b border-gray-50 mb-2">
                    <p class="text-sm font-bold text-gray-800"><?= htmlspecialchars($currentUser->full_name) ?></p>
                    <p class="text-xs text-gray-500 truncate"><?= $currentUser->email ?></p>
                </div>

                <div class="px-2 space-y-1">
                    <a href="/profile"
                        class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-50 hover:text-primary transition-colors">
                        <i class="bi bi-person text-lg"></i>
                        <span>الملف الشخصي</span>
                    </a>
                    <a href="/users/<?= $currentUser->id ?>/card" target="_blank"
                        class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-50 hover:text-primary transition-colors">
                        <i class="bi bi-credit-card text-lg"></i>
                        <span>بطاقة العضوية</span>
                    </a>
                    <?php if ($currentUser->isAdmin()): ?>
                        <a href="/committees"
                            class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-50 hover:text-primary transition-colors">
                            <i class="bi bi-gear text-lg"></i>
                            <span>الإعدادات</span>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="mt-2 pt-2 border-t border-gray-50 px-2">
                    <button onclick="logout()"
                        class="w-full flex items-center gap-3 px-3 py-2 text-sm text-red-600 rounded-lg hover:bg-red-50 hover:text-red-700 transition-colors text-right">
                        <i class="bi bi-box-arrow-right text-lg"></i>
                        <span>تسجيل الخروج</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function logout() {
        Swal.fire({
            title: 'تسجيل الخروج',
            text: 'هل أنت متأكد من تسجيل الخروج؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d62828',
            cancelButtonColor: '#718096',
            confirmButtonText: 'نعم، خروج',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // Using a form submission or direct fetch would be better, but location.href works for GET logout if route allows, usually POST is standard
                // The route is POST /api/auth/logout. We need to submit a form.
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/api/auth/logout';
                // Add CSRF
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = 'csrf_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        })
    }
</script>