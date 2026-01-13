<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | شباب سطيف</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">

    <?= \App\Helpers\CSRF::meta() ?>
</head>

<body class="bg-gray-50 font-tajawal min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <!-- Header -->
            <div class="bg-primary px-8 py-10 text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 blur-xl"></div>
                <div class="relative z-10">
                    <div
                        class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center text-primary mx-auto mb-4 shadow-lg transform rotate-3">
                        <span class="text-3xl font-extrabold">SS</span>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-1">شباب سطيف</h1>
                    <p class="text-white/80 text-sm">منصة إدارة الجمعية</p>
                </div>
            </div>

            <!-- Body -->
            <div class="p-8">
                <form id="loginForm" class="space-y-5">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">البريد الإلكتروني</label>
                        <div class="relative">
                            <input type="email" id="email" required placeholder="example@email.com"
                                class="w-full pl-4 pr-10 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                            <i class="bi bi-envelope absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">كلمة المرور</label>
                        <div class="relative">
                            <input type="password" id="password" required placeholder="••••••••"
                                class="w-full pl-10 pr-10 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                            <i class="bi bi-lock absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <button type="button" onclick="togglePassword()"
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" id="loginBtn"
                            class="w-full py-3.5 bg-primary text-white rounded-xl font-bold shadow-lg shadow-primary/30 hover:bg-primary-dark hover:shadow-primary/50 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <span>تسجيل الدخول</span>
                        </button>
                    </div>
                </form>

                <div class="mt-8 p-4 bg-green-50 rounded-xl border border-green-100 flex items-start gap-3">
                    <i class="bi bi-info-circle-fill text-green-600 mt-0.5"></i>
                    <div>
                        <h6 class="font-bold text-green-800 text-sm mb-1">للأعضاء الجدد</h6>
                        <p class="text-green-700 text-xs leading-relaxed">كلمة المرور الافتراضية هي رقم بطاقة العضوية
                            الخاصة بك.</p>
                    </div>
                </div>
            </div>

            <div class="py-4 bg-gray-50 border-t border-gray-100 text-center text-xs text-gray-500">
                &copy; <?= date('Y') ?> شباب سطيف. جميع الحقوق محفوظة.
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        }

        $('#loginForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $('#loginBtn');
            const originalContent = btn.html();

            btn.prop('disabled', true).html('<span class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>');

            $.ajax({
                url: '/api/auth/login',
                method: 'POST',
                data: {
                    email: $('#email').val(),
                    password: $('#password').val(),
                    _csrf_token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (r) {
                    if (r.success) {
                        window.location.href = r.redirect || '/dashboard';
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: r.message || 'بيانات الدخول غير صحيحة',
                            confirmButtonColor: '#d62828'
                        });
                        btn.prop('disabled', false).html(originalContent);
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: xhr.responseJSON?.message || 'بيانات الدخول غير صحيحة',
                        confirmButtonColor: '#d62828'
                    });
                    btn.prop('disabled', false).html(originalContent);
                }
            });
        });
    </script>
</body>

</html>