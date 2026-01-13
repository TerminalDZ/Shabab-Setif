<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'شباب سطيف' ?> | شباب سطيف</title>

    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">

    <?= \App\Helpers\CSRF::meta() ?>
</head>

<body>
    <div class="app-wrapper">
        <!-- Sidebar Overlay (Mobile) -->
        <div class="sidebar-overlay"></div>

        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include __DIR__ . '/header.php'; ?>

            <!-- Content -->
            <div class="content-area">
                <?= $content ?? '' ?>
            </div>

            <!-- Footer -->
            <?php include __DIR__ . '/footer.php'; ?>
        </div>
    </div>
    </div>

    <!-- Leaderboard Modal -->
    <div class="modal fade" id="leaderboardModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-trophy-fill me-2"></i>لوحة الصدارة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <ul class="nav nav-pills nav-fill p-3" id="leaderboardTabs">
                        <li class="nav-item">
                            <button class="nav-link active" data-type="monthly">الشهر الحالي</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-type="yearly">السنة الحالية</button>
                        </li>
                    </ul>
                    <div id="leaderboardContent"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/assets/js/app.js"></script>
</body>

</html>