/**
 * Shabab Setif - Main Application JavaScript
 * Tailwind CSS + jQuery Edition
 */

$(document).ready(function () {
    // CSRF Setup
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });

    // Sidebar Toggle
    $('#sidebarToggle').on('click', toggleSidebar);

    // Global Click Outside for Dropdowns
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.relative').length) {
            $('.absolute.z-50').addClass('hidden');
        }
    });

    // AJAX Error Handling
    $(document).ajaxError(function (event, xhr) {
        if (xhr.status === 401) window.location.href = '/login';
        else if (xhr.status === 403) showToast('error', 'غير مصرح لك');
        else if (xhr.status === 500) showToast('error', 'خطأ في الخادم');
    });

    // Initial Leaderboard Load
    $('#leaderboardModal').on('show', function () {
        switchLeaderboardTab('monthly');
    });
});

// Sidebar Logic
function toggleSidebar() {
    const sidebar = $('#sidebar');
    const overlay = $('#sidebarOverlay');

    // Toggle translate class
    sidebar.toggleClass('translate-x-full');
    overlay.toggleClass('hidden');
}

// Dropdown Logic
window.toggleDropdown = function (id) {
    $('.absolute.z-50').not('#' + id).addClass('hidden');
    $('#' + id).toggleClass('hidden');
}

// Modal Logic
window.openModal = function (id) {
    $('#' + id).removeClass('hidden');
    if (id === 'leaderboardModal') switchLeaderboardTab('monthly');
}

window.closeModal = function (id) {
    $('#' + id).addClass('hidden');
}

// Enable standard Bootstrap-like data-toggle for modals if needed, 
// but we use onclick="openModal('...')" now.
$(document).on('click', '[data-bs-toggle="modal"]', function (e) {
    e.preventDefault();
    const target = $(this).data('bs-target');
    if (target) {
        const id = target.replace('#', '');
        openModal(id);
    }
});

// Leaderboard Logic
window.switchLeaderboardTab = function (type) {
    // Update Tabs
    if (type === 'monthly') {
        $('#tab-monthly').addClass('text-primary border-b-2 border-primary').removeClass('text-gray-500');
        $('#tab-yearly').removeClass('text-primary border-b-2 border-primary').addClass('text-gray-500');
    } else {
        $('#tab-yearly').addClass('text-primary border-b-2 border-primary').removeClass('text-gray-500');
        $('#tab-monthly').removeClass('text-primary border-b-2 border-primary').addClass('text-gray-500');
    }

    // Load Content
    $('#leaderboardContent').html('<div class="flex justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div></div>');

    $.get('/api/dashboard/leaderboard', { type: type, limit: 10 }, function (response) {
        if (response.success) {
            let html = '<div class="space-y-2">';
            const pointsKey = type === 'yearly' ? 'yearly_points' : 'monthly_points';

            response.data.forEach((member, index) => {
                let rankColor = 'bg-gray-100 text-gray-600';
                if (index === 0) rankColor = 'bg-yellow-100 text-yellow-700';
                if (index === 1) rankColor = 'bg-gray-200 text-gray-700';
                if (index === 2) rankColor = 'bg-orange-100 text-orange-700';

                html += `
                    <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-8 h-8 rounded-full ${rankColor} flex items-center justify-center font-bold text-sm shrink-0">
                            ${index + 1}
                        </div>
                        <img src="${member.avatar || '/assets/images/default-avatar.png'}" class="w-10 h-10 rounded-full object-cover border border-gray-100" alt="">
                        <div class="flex-grow">
                            <h4 class="font-bold text-gray-800 text-sm">${member.full_name}</h4>
                            <span class="text-xs text-gray-500">مشجع وفي</span>
                        </div>
                        <div class="text-primary font-bold bg-primary/10 px-3 py-1 rounded-full text-xs">
                            ${member[pointsKey]} نقطة
                        </div>
                    </div>`;
            });

            html += '</div>';
            $('#leaderboardContent').html(html);
        } else {
            $('#leaderboardContent').html('<div class="text-center text-red-500 py-4">فشل تحميل البيانات</div>');
        }
    });
}

// Toast Helper (SweetAlert2)
window.showToast = function (icon, title) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
    Toast.fire({ icon: icon, title: title });
}

// Confirm Delete Helper
window.confirmDelete = function (message, callback) {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: message || 'لا يمكن التراجع عن هذا الإجراء',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d62828',
        cancelButtonColor: '#718096',
        confirmButtonText: 'نعم، حذف',
        cancelButtonText: 'إلغاء',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'px-6 py-2 rounded-lg',
            cancelButton: 'px-6 py-2 rounded-lg'
        }
    }).then(result => {
        if (result.isConfirmed && typeof callback === 'function') {
            callback();
        }
    });
}
