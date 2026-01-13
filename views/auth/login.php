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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <?= \App\Helpers\CSRF::meta() ?>

    <style>
        * {
            font-family: 'Tajawal', sans-serif;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: #f1f3f5;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-header {
            background: #d62828;
            padding: 40px 30px 32px;
            text-align: center;
            color: white;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .login-logo i {
            font-size: 36px;
            color: #d62828;
        }

        .login-header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .login-header p {
            opacity: 0.9;
            margin: 0;
            font-size: 14px;
        }

        .login-body {
            padding: 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #212529;
            margin-bottom: 6px;
            display: block;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            height: 48px;
            font-size: 15px;
            padding-right: 44px;
            transition: all 0.2s;
        }

        .input-wrapper .form-control:focus {
            border-color: #d62828;
            box-shadow: 0 0 0 3px rgba(214, 40, 40, 0.1);
        }

        .input-wrapper .input-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 18px;
        }

        .password-toggle {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 4px;
        }

        .btn-login {
            width: 100%;
            height: 48px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            background: #d62828;
            border: none;
            color: white;
            transition: all 0.2s;
        }

        .btn-login:hover {
            background: #b71c1c;
        }

        .btn-login:disabled {
            opacity: 0.7;
        }

        .login-footer {
            text-align: center;
            padding: 16px;
            background: #f8f9fa;
            color: #6c757d;
            font-size: 12px;
            border-top: 1px solid #e9ecef;
        }

        .login-help {
            margin-top: 20px;
            padding: 14px;
            background: #f8f9fa;
            border-radius: 8px;
            border-right: 3px solid #2d6a4f;
        }

        .login-help h6 {
            color: #2d6a4f;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 13px;
        }

        .login-help p {
            margin: 0;
            font-size: 12px;
            color: #495057;
        }

        @media (max-width: 576px) {
            .login-header {
                padding: 32px 20px 24px;
            }

            .login-body {
                padding: 24px;
            }

            .login-logo {
                width: 70px;
                height: 70px;
            }

            .login-logo i {
                font-size: 30px;
            }

            .login-header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h1>شباب سطيف</h1>
                <p>منصة إدارة الجمعية</p>
            </div>

            <div class="login-body">
                <form id="loginForm">
                    <div class="form-group">
                        <label class="form-label">البريد الإلكتروني</label>
                        <div class="input-wrapper">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="أدخل بريدك الإلكتروني" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">كلمة المرور</label>
                        <div class="input-wrapper">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="أدخل كلمة المرور" required>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login" id="loginBtn">
                        <span class="btn-text">تسجيل الدخول</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                    </button>
                </form>

                <div class="login-help">
                    <h6><i class="bi bi-info-circle me-1"></i>للأعضاء الجدد</h6>
                    <p>كلمة المرور الافتراضية هي رقم بطاقة العضوية</p>
                </div>
            </div>

            <div class="login-footer">
                © <?= date('Y') ?> جمعية شباب سطيف
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

            const btn = $('#loginBtn'), btnText = btn.find('.btn-text'), spinner = btn.find('.spinner-border');
            btn.prop('disabled', true);
            btnText.text('جاري الدخول...');
            spinner.removeClass('d-none');

            $.ajax({
                url: '/api/auth/login',
                method: 'POST',
                data: {
                    email: $('#email').val(),
                    password: $('#password').val(),
                    _csrf_token: $('meta[name="csrf-token"]').attr('content')
                },
                success: r => {
                    if (r.success) {
                        Swal.fire({ icon: 'success', title: 'مرحباً!', text: r.message, timer: 1500, showConfirmButton: false })
                            .then(() => window.location.href = r.redirect || '/dashboard');
                    }
                },
                error: xhr => {
                    Swal.fire({ icon: 'error', title: 'خطأ', text: xhr.responseJSON?.message || 'بيانات الدخول غير صحيحة' });
                },
                complete: () => {
                    btn.prop('disabled', false);
                    btnText.text('تسجيل الدخول');
                    spinner.addClass('d-none');
                }
            });
        });
    </script>
</body>

</html>