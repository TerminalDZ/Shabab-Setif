<!-- Header -->
<header class="top-header">
    <div class="header-content">
        <button class="sidebar-toggle" type="button">
            <i class="bi bi-list"></i>
        </button>

        <div class="header-search d-none d-md-block">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" placeholder="بحث..." id="globalSearch">
            </div>
        </div>

        <div class="header-right">
            <!-- Notifications -->
            <div class="dropdown">
                <button class="btn btn-link position-relative" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-0 badge rounded-pill bg-danger"
                        style="font-size:10px;">3</span>
                </button>
                <div class="dropdown-menu dropdown-menu-start notification-dropdown">
                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                        <span>الإشعارات</span>
                        <a href="#" class="text-primary small">عرض الكل</a>
                    </div>
                    <a href="#" class="dropdown-item">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-calendar-plus"></i>
                            </div>
                            <div>
                                <p class="mb-1">نشاط جديد: دوري كرة القدم</p>
                                <small class="text-muted">منذ 5 دقائق</small>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="dropdown-item">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <div>
                                <p class="mb-1">حصلت على 10 نقاط جديدة!</p>
                                <small class="text-muted">منذ ساعة</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- User Menu -->
            <div class="dropdown">
                <button class="btn d-flex align-items-center gap-2 p-0" data-bs-toggle="dropdown">
                    <img src="<?= $currentUser->avatar ?? '/assets/images/default-avatar.png' ?>" alt=""
                        class="user-avatar">
                    <span class="user-name d-none d-sm-inline"><?= htmlspecialchars($currentUser->full_name) ?></span>
                    <i class="bi bi-chevron-down text-muted d-none d-sm-inline"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-start">
                    <li class="dropdown-header">
                        <strong><?= htmlspecialchars($currentUser->full_name) ?></strong>
                        <small class="d-block text-muted"><?= $currentUser->email ?></small>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="/profile"><i class="bi bi-person me-2"></i>الملف الشخصي</a></li>
                    <li><a class="dropdown-item" href="/users/<?= $currentUser->id ?>/card" target="_blank"><i
                                class="bi bi-credit-card me-2"></i>بطاقة العضوية</a></li>
                    <?php if ($currentUser->isAdmin()): ?>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="/committees"><i class="bi bi-gear me-2"></i>الإعدادات</a></li>
                    <?php endif; ?>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="logout()"><i
                                class="bi bi-box-arrow-right me-2"></i>تسجيل الخروج</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

<script>
    function logout() {
        Swal.fire({
            title: 'تسجيل الخروج',
            text: 'هل أنت متأكد من تسجيل الخروج؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'نعم، خروج',
            cancelButtonText: 'إلغاء'
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = '/logout';
            }
        });
    }
</script>