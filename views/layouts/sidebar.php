<!-- Sidebar -->
<aside class="fixed top-0 right-0 h-full w-64 bg-primary text-white flex flex-col shadow-xl z-30 transition-transform duration-300 translate-x-full md:translate-x-0" id="sidebar">
    <!-- Brand -->
    <div class="h-16 flex items-center px-6 border-b border-white/10">
        <a href="/dashboard" class="flex items-center gap-3 group">
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-white font-bold text-xl group-hover:bg-white group-hover:text-primary transition-all duration-300">
                SS
            </div>
            <span class="font-bold text-lg tracking-wide">شباب سطيف</span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1 custom-scrollbar">
        <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?= $_SERVER['REQUEST_URI'] === '/dashboard' ? 'bg-white text-primary shadow-lg font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' ?>">
            <i class="bi bi-speedometer2 text-xl"></i>
            <span>لوحة التحكم</span>
        </a>

        <!-- Activities Group -->
        <div class="pt-4 pb-2">
            <h3 class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider mb-2">الأنشطة</h3>
            <div class="space-y-1">
                <a href="/activities" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-colors <?= $_SERVER['REQUEST_URI'] === '/activities' ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' ?>">
                    <i class="bi bi-calendar-event"></i>
                    <span>كل الأنشطة</span>
                </a>
                <a href="/activities/feed" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-colors <?= $_SERVER['REQUEST_URI'] === '/activities/feed' ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' ?>">
                    <i class="bi bi-rss"></i>
                    <span>آخر الأنشطة</span>
                </a>
                <?php if ($currentUser->canManage()): ?>
                <a href="/activities/create" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-colors <?= $_SERVER['REQUEST_URI'] === '/activities/create' ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' ?>">
                    <i class="bi bi-plus-circle"></i>
                    <span>نشاط جديد</span>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($currentUser->canManage()): ?>
        <div class="pt-4 pb-2">
            <h3 class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider mb-2">الإدارة</h3>
            <div class="space-y-1">
                <a href="/users" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-colors <?= str_starts_with($_SERVER['REQUEST_URI'], '/users') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' ?>">
                    <i class="bi bi-people"></i>
                    <span>الأعضاء</span>
                </a>
                <?php if ($currentUser->isAdmin()): ?>
                <a href="/committees" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-colors <?= $_SERVER['REQUEST_URI'] === '/committees' ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' ?>">
                    <i class="bi bi-diagram-3"></i>
                    <span>اللجان</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="pt-4 pb-2">
            <h3 class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider mb-2">شخصي</h3>
            <div class="space-y-1">
                <a href="/profile" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-colors <?= $_SERVER['REQUEST_URI'] === '/profile' ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' ?>">
                    <i class="bi bi-person"></i>
                    <span>ملفي الشخصي</span>
                </a>
                <button type="button" onclick="openModal('leaderboardModal')" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl transition-colors text-white/70 hover:bg-white/5 hover:text-white cursor-pointer hover:bg-white/10">
                    <i class="bi bi-trophy"></i>
                    <span>لوحة الصدارة</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-white/10 bg-black/10">
        <div class="flex items-center justify-between px-2 text-white/90">
            <div class="flex items-center gap-2">
                <i class="bi bi-star-fill text-yellow-400"></i>
                <span class="text-sm font-medium">نقاطي:</span>
            </div>
            <span class="font-bold font-mono text-lg"><?= number_format($currentUser->points_balance ?? 0) ?></span>
        </div>
    </div>
</aside>