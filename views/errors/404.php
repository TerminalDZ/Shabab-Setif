<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - غير موجود | شباب سطيف</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        * {
            font-family: 'Tajawal', sans-serif;
        }

        body {
            min-height: 100vh;
            background: #f1f3f5;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-container {
            text-align: center;
            max-width: 400px;
        }

        .error-icon {
            font-size: 80px;
            color: #d62828;
            margin-bottom: 20px;
        }

        .error-code {
            font-size: 100px;
            font-weight: 800;
            color: #212529;
            line-height: 1;
            margin-bottom: 8px;
        }

        .error-text {
            font-size: 22px;
            font-weight: 600;
            color: #212529;
            margin-bottom: 8px;
        }

        .error-desc {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 24px;
        }

        .btn-home {
            background: #d62828;
            color: white;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-home:hover {
            background: #b71c1c;
            color: white;
        }

        @media (max-width: 576px) {
            .error-code {
                font-size: 80px;
            }

            .error-text {
                font-size: 18px;
            }

            .error-icon {
                font-size: 60px;
            }
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-icon"><i class="bi bi-exclamation-triangle"></i></div>
        <div class="error-code">404</div>
        <div class="error-text">الصفحة غير موجودة</div>
        <p class="error-desc">الصفحة التي تبحث عنها غير موجودة أو تم نقلها.</p>
        <a href="/dashboard" class="btn-home">
            <i class="bi bi-house"></i>
            العودة للرئيسية
        </a>
    </div>
</body>

</html>