<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بطاقة العضوية |
        <?= htmlspecialchars($user->full_name) ?>
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
        }

        body {
            background: #f0f0f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card-container {
            perspective: 1000px;
        }

        .membership-card {
            width: 400px;
            height: 250px;
            background: #2d6a4f;
            border-radius: 20px;
            padding: 25px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.4);
        }

        .card-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            z-index: 1;
        }

        .logo {
            font-size: 24px;
            font-weight: 800;
        }

        .logo span {
            display: block;
            font-size: 12px;
            font-weight: 400;
            opacity: 0.8;
        }

        .member-photo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.5);
            object-fit: cover;
            background: rgba(255, 255, 255, 0.2);
        }

        .card-body {
            margin-top: 20px;
            position: relative;
            z-index: 1;
        }

        .member-name {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .member-role {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 15px;
        }

        .card-id {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 3px;
            font-family: monospace;
        }

        .card-footer {
            position: absolute;
            bottom: 20px;
            left: 25px;
            right: 25px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            opacity: 0.7;
        }

        .print-btn {
            margin-top: 20px;
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
        }

        @media print {
            .print-btn {
                display: none;
            }

            body {
                background: white;
            }
        }
    </style>
</head>

<body>
    <div class="card-container">
        <div class="membership-card">
            <div class="card-pattern"></div>
            <div class="card-header">
                <div class="logo">شباب سطيف<span>جمعية شبابية تطوعية</span></div>
                <img src="<?= $user->avatar ?? '/assets/images/default-avatar.png' ?>" class="member-photo" alt="">
            </div>
            <div class="card-body">
                <div class="member-name">
                    <?= htmlspecialchars($user->full_name) ?>
                </div>
                <div class="member-role">
                    <?= $committee ? htmlspecialchars($committee->name) : 'عضو' ?>
                </div>
                <div class="card-id">
                    <?= $user->member_card_id ?>
                </div>
            </div>
            <div class="card-footer">
                <span>تاريخ الانضمام:
                    <?= date('Y-m-d', strtotime($user->created_at)) ?>
                </span>
                <span>بطاقة عضوية رسمية</span>
            </div>
        </div>
        <center><button class="print-btn" onclick="window.print()"><i class="bi bi-printer"></i> طباعة البطاقة</button>
        </center>
    </div>
</body>

</html>