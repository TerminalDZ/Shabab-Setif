<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="/dashboard" class="sidebar-brand">
            <div class="sidebar-logo">SS</div>
            <span class="sidebar-brand-text">شباب سطيف</span>
        </a>
    </div>

    <nav class="sidebar-nav">
        <a href="/dashboard" class="nav-link <?= $_SERVER['REQUEST_URI'] === '/dashboard' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i>
            <span>لوحة التحكم</span>
        </a>

        <a href="#activitiesSubmenu" class="nav-link" data-bs-toggle="collapse"
            aria-expanded="<?= str_starts_with($_SERVER['REQUEST_URI'], '/activities') ? 'true' : 'false' ?>">
            <i class="bi bi-calendar-event"></i>
            <span>الأنشطة</span>
            <i class="bi bi-chevron-down nav-arrow"></i>
        </a>
        <div class="collapse <?= str_starts_with($_SERVER['REQUEST_URI'], '/activities') ? 'show' : '' ?>"
            id="activitiesSubmenu">
            <div class="sub-menu">
                <a href="/activities" class="nav-link <?= $_SERVER['REQUEST_URI'] === '/activities' ? 'active' : '' ?>">
                    <span>كل الأنشطة</span>
                </a>
                <a href="/activities/feed"
                    class="nav-link <?= $_SERVER['REQUEST_URI'] === '/activities/feed' ? 'active' : '' ?>">
                    <span>آخر الأنشطة</span>
                </a>
                <?php if ($currentUser->canManage()): ?>
                    <a href="/activities/create"
                        class="nav-link <?= $_SERVER['REQUEST_URI'] === '/activities/create' ? 'active' : '' ?>">
                        <span>نشاط جديد</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($currentUser->canManage()): ?>
            <a href="/users" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/users') ? 'active' : '' ?>">
                <i class="bi bi-people"></i>
                <span>إدارة الأعضاء</span>
            </a>
        <?php endif; ?>

        <?php if ($currentUser->isAdmin()): ?>
            <a href="/committees" class="nav-link <?= $_SERVER['REQUEST_URI'] === '/committees' ? 'active' : '' ?>">
                <i class="bi bi-diagram-3"></i>
                <span>اللجان</span>
            </a>
        <?php endif; ?>

        <hr class="mx-3 opacity-25">

        <a href="/profile" class="nav-link <?= $_SERVER['REQUEST_URI'] === '/profile' ? 'active' : '' ?>">
            <i class="bi bi-person"></i>
            <span>ملفي الشخصي</span>
        </a>

        <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#leaderboardModal">
            <i class="bi bi-trophy"></i>
            <span>لوحة الصدارة</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-points">
            <i class="bi bi-star-fill"></i>
            <span>رصيد النقاط:</span>
            <strong><?= number_format($currentUser->points_balance ?? 0) ?></strong>
        </div>
    </div>
</aside>