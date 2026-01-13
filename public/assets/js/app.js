/**
 * Shabab Setif - Main Application JavaScript
 * jQuery-based AJAX handling with SweetAlert2
 */

$(document).ready(function () {
    // CSRF Token for all AJAX requests
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': csrfToken }
    });

    // Mobile Sidebar Toggle
    $('.sidebar-toggle').on('click', function () {
        $('.sidebar').toggleClass('active');
        $('.sidebar-overlay').toggleClass('active');
    });

    $('.sidebar-overlay').on('click', function () {
        $('.sidebar').removeClass('active');
        $(this).removeClass('active');
    });

    // Initialize Tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));

    // Leaderboard Modal
    $('#leaderboardModal').on('show.bs.modal', function () {
        loadLeaderboard('monthly');
    });

    $('#leaderboardTabs button').on('click', function () {
        $('#leaderboardTabs button').removeClass('active');
        $(this).addClass('active');
        loadLeaderboard($(this).data('type'));
    });

    // Global AJAX Error Handler
    $(document).ajaxError(function (event, xhr) {
        if (xhr.status === 401) {
            Swal.fire({
                icon: 'warning',
                title: 'انتهت الجلسة',
                text: 'يرجى تسجيل الدخول مرة أخرى',
                confirmButtonText: 'تسجيل الدخول'
            }).then(() => window.location.href = '/login');
        } else if (xhr.status === 403) {
            Swal.fire('غير مصرح', 'ليس لديك صلاحية لهذا الإجراء', 'error');
        } else if (xhr.status === 500) {
            Swal.fire('خطأ', 'حدث خطأ في الخادم', 'error');
        }
    });
});

// Load Leaderboard Data
function loadLeaderboard(type) {
    $('#leaderboardContent').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');

    $.get('/api/dashboard/leaderboard', { type: type, limit: 10 }, function (response) {
        if (response.success) {
            let html = '<div class="list-group list-group-flush">';
            const pointsKey = type === 'yearly' ? 'yearly_points' : 'monthly_points';

            response.data.forEach((member, index) => {
                const rankClass = index < 3 ? `rank-${index + 1}` : '';
                html += `
                    <div class="list-group-item d-flex align-items-center gap-3">
                        <span class="rank ${rankClass}">${index + 1}</span>
                        <img src="${member.avatar || '/assets/images/default-avatar.png'}" class="member-avatar-sm" alt="">
                        <span class="flex-grow-1 fw-medium">${member.full_name}</span>
                        <span class="badge bg-primary"><i class="bi bi-star-fill me-1"></i>${member[pointsKey]}</span>
                    </div>`;
            });

            html += '</div>';
            $('#leaderboardContent').html(html);
        }
    });
}

// Toast Notification Helper
function showToast(icon, title) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
    Toast.fire({ icon: icon, title: title });
}

// Confirm Delete Helper
function confirmDelete(message, callback) {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: message || 'لا يمكن التراجع عن هذا الإجراء',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء'
    }).then(result => {
        if (result.isConfirmed && typeof callback === 'function') {
            callback();
        }
    });
}

// Format Date Helper
function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('ar-DZ', { year: 'numeric', month: 'long', day: 'numeric' });
}

// Number Format Helper
function formatNumber(num) {
    return new Intl.NumberFormat('ar-DZ').format(num);
}
