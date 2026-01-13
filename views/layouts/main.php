<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'شباب سطيف' ?> | شباب سطيف</title>

    <link rel="icon" type="image/png" href="/assets/images/favicon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">

    <!-- Libraries -->
    <link href="/assets/vendor/dropzone/dropzone.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Chart.js is JS only usually, but some plugins might need CSS. Vanilla Chart.js is fine. -->

    <!-- App CSS (Tailwind) -->
    <link href="/assets/css/app.css" rel="stylesheet">

    <?= \App\Helpers\CSRF::meta() ?>
</head>

<body class="bg-gray-50 font-tajawal text-gray-800 antialiased selection:bg-primary selection:text-white">
    <div class="min-h-screen flex flex-row">

        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar.php'; ?>

        <!-- Main Wrapper -->
        <div
            class="flex-1 flex flex-col min-h-screen transition-all duration-300 relative w-full md:w-[calc(100%-16rem)]">

            <!-- Header -->
            <?php include __DIR__ . '/header.php'; ?>

            <!-- Content -->
            <main class="flex-1 p-4 md:p-6 lg:p-8 overflow-x-hidden">
                <div class="max-w-7xl mx-auto space-y-6">
                    <?= $content ?? '' ?>
                </div>
            </main>

            <!-- Footer -->
            <?php include __DIR__ . '/footer.php'; ?>
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-20 hidden backdrop-blur-sm transition-opacity" onclick="toggleSidebar()"></div>

    <!-- Leaderboard Modal -->
    <div id="leaderboardModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('leaderboardModal')"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-right shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="bg-primary px-4 py-3 sm:px-6 flex justify-between items-center">
                        <h3 class="text-base font-semibold leading-6 text-white" id="modal-title">
                            <i class="bi bi-trophy-fill me-2 text-yellow-300"></i>
                            لوحة الصدارة
                        </h3>
                        <button type="button" class="text-white hover:text-gray-200" onclick="closeModal('leaderboardModal')">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="flex space-x-4 space-x-reverse mb-4 border-b border-gray-100">
                            <button class="flex-1 pb-2 font-medium text-primary border-b-2 border-primary transition-colors" id="tab-monthly" onclick="switchLeaderboardTab('monthly')">الشهر الحالي</button>
                            <button class="flex-1 pb-2 font-medium text-gray-500 hover:text-primary transition-colors" id="tab-yearly" onclick="switchLeaderboardTab('yearly')">السنة الحالية</button>
                        </div>
                        <div id="leaderboardContent" class="min-h-[200px]">
                            <!-- Content loaded via AJAX -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/assets/vendor/dropzone/dropzone-min.js"></script>
    <script src="/assets/js/app.js"></script>
</body>

</html>