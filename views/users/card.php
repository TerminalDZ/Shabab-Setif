<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بطاقة عضوية - <?= htmlspecialchars($user->full_name) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">
    <style>
        body {
            background: #f3f4f6;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @media print {
            body {
                background: white;
            }

            .no-print {
                display: none !important;
            }

            .card-container {
                box-shadow: none !important;
                margin: 0;
            }
        }

        .card-bg-pattern {
            background-image: radial-gradient(#ffffff 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.1;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4 font-tajawal">

    <div class="space-y-8 text-center">
        <!-- The Card -->
        <div
            class="card-container relative w-[400px] h-[250px] bg-gradient-to-br from-primary to-green-800 rounded-[20px] shadow-2xl overflow-hidden text-white text-right mx-auto transform transition-transform hover:scale-105 duration-300">
            <!-- Background Elements -->
            <div class="absolute inset-0 card-bg-pattern"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-black/10 rounded-full blur-2xl -ml-10 -mb-10"></div>

            <!-- Content -->
            <div class="relative z-10 p-6 h-full flex flex-col justify-between">
                <!-- Header -->
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-xl font-bold tracking-tight">شباب سطيف</h1>
                        <p class="text-[10px] text-white/80 font-light">جمعية شبانية تطوعية</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-white rounded-xl shadow-lg flex items-center justify-center text-primary font-bold text-lg">
                        SS
                    </div>
                </div>

                <!-- Body -->
                <div class="flex items-center gap-4 mt-2">
                    <div class="relative">
                        <div class="w-20 h-20 rounded-full p-1 bg-white/20">
                            <img src="<?= $user->avatar ?? '/assets/images/default-avatar.png' ?>"
                                class="w-full h-full rounded-full object-cover bg-white">
                        </div>
                        <?php if ($user->role === 'admin'): ?>
                            <div
                                class="absolute -bottom-1 -right-1 bg-yellow-400 text-yellow-900 rounded-full p-1 shadow-sm">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 16 16">
                                    <path
                                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                </svg></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold mb-0.5"><?= htmlspecialchars($user->full_name) ?></h2>
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] bg-white/20 border border-white/10">
                            <?= $committee ? htmlspecialchars($committee->name) : ($user->role === 'admin' ? 'الإدارة العامة' : 'عضو نشط') ?>
                        </span>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-between items-end mt-auto">
                    <div>
                        <p class="text-[8px] text-white/60 mb-0.5">رقم العضوية</p>
                        <p
                            class="text-lg font-mono font-bold tracking-widest text-yellow-400 shadow-black/5 drop-shadow-sm">
                            <?= $user->member_card_id ?></p>
                    </div>
                    <div class="text-left">
                        <p class="text-[8px] text-white/60">تاريخ الإصدار</p>
                        <p class="text-xs font-medium"><?= date('Y-m-d') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <div class="no-print space-y-4">
            <button onclick="window.print()"
                class="px-8 py-3 bg-gray-900 text-white rounded-xl font-bold shadow-xl hover:bg-black transition-transform hover:-translate-y-1 flex items-center justify-center gap-2 mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer"
                    viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z" />
                    <path
                        d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z" />
                </svg>
                <span>طباعة البطاقة</span>
            </button>
            <p class="text-sm text-gray-500">نصيحة: تأكد من تفعيل "Background Graphics" في إعدادات الطباعة</p>
        </div>
    </div>

</body>

</html>