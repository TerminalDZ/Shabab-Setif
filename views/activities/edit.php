<?php
$title = 'تعديل النشاط: ' . htmlspecialchars($activity->title);
ob_start();
?>

<div class="px-4 py-6 md:px-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-l from-primary to-green-600">
                تعديل النشاط
            </h1>
            <p class="text-gray-500 mt-2">تحديث بيانات النشاط: <?= htmlspecialchars($activity->title) ?></p>
        </div>
        <a href="/activities/<?= $activity->id ?>"
            class="flex items-center gap-2 text-gray-500 hover:text-primary transition-colors bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100">
            <i class="bi bi-arrow-right"></i>
            <span>رجوع للتفاصيل</span>
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="p-6 md:p-8">
            <form id="editActivityForm" class="space-y-6">
                <!-- Main Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">عنوان النشاط <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="title" required value="<?= htmlspecialchars($activity->title) ?>"
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">اللجنة المنظمة</label>
                        <div class="relative">
                            <select name="committee_id"
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none appearance-none cursor-pointer">
                                <option value="">بدون لجنة (عام)</option>
                                <?php foreach ($committees as $committee): ?>
                                    <option value="<?= $committee['id'] ?>" <?= ($activity->committee_id == $committee['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($committee['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div
                                class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-500">
                                <i class="bi bi-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">التاريخ <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="date" required value="<?= $activity->date ?>"
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">التوقيت</label>
                        <input type="time" name="time" value="<?= $activity->time ?>"
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">المكان</label>
                        <div class="relative">
                            <input type="text" name="location"
                                value="<?= htmlspecialchars($activity->location ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none pl-10">
                            <i class="bi bi-geo-alt absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">الحالة</label>
                        <div class="relative">
                            <select name="status"
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none appearance-none cursor-pointer">
                                <option value="upcoming" <?= $activity->status === 'upcoming' ? 'selected' : '' ?>>قادم
                                    (مفتوح للتسجيل)</option>
                                <option value="completed" <?= $activity->status === 'completed' ? 'selected' : '' ?>>مكتمل
                                </option>
                                <option value="cancelled" <?= $activity->status === 'cancelled' ? 'selected' : '' ?>>ملغي
                                </option>
                            </select>
                            <div
                                class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-500">
                                <i class="bi bi-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">نقاط النشاط</label>
                        <div class="relative">
                            <input type="number" name="points_value" value="<?= $activity->points_value ?>" min="0"
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none pl-10">
                            <i class="bi bi-star absolute left-4 top-1/2 -translate-y-1/2 text-yellow-500 text-lg"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">وصف النشاط</label>
                    <textarea name="description" rows="4"
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none resize-none"><?= htmlspecialchars($activity->description ?? '') ?></textarea>
                </div>

                <!-- Current Images -->
                <?php $currentImages = json_decode($activity->images_json ?? '[]', true); ?>
                <?php if (!empty($currentImages)): ?>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">الصور الحالية</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <?php foreach ($currentImages as $img): ?>
                                <div class="relative group rounded-xl overflow-hidden" id="img-container-<?= md5($img) ?>">
                                    <img src="<?= $img ?>"
                                        class="w-full h-32 object-cover transition-transform group-hover:scale-105" alt="">
                                    <button type="button" onclick="removeExistingImage('<?= $img ?>', '<?= md5($img) ?>')"
                                        class="absolute top-2 left-2 bg-red-500 text-white rounded-full p-1.5 shadow-lg transform scale-0 group-hover:scale-100 transition-all hover:bg-red-600">
                                        <i class="bi bi-trash text-sm"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="remove_images" id="removeImagesInput" value="">
                    </div>
                <?php endif; ?>

                <!-- Dropzone Image Upload -->
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">إضافة صور جديدة</label>
                    <div id="activityDropzone"
                        class="dropzone rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 hover:bg-white hover:border-primary/50 transition-all cursor-pointer min-h-[150px] flex items-center justify-center">
                        <div class="dz-message text-center">
                            <div
                                class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center text-primary mx-auto mb-4">
                                <i class="bi bi-cloud-arrow-up text-3xl"></i>
                            </div>
                            <h3 class="font-bold text-gray-700">اسحب الصور هنا أو اضغط للرفع</h3>
                            <span class="text-sm text-gray-500 mt-2 block">الحد الأقصى: 5 ميغابايت (JPG, PNG)</span>
                        </div>
                    </div>
                </div>

                <!-- Delete & Submit -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                    <button type="button" onclick="deleteActivity()"
                        class="text-red-500 hover:text-red-700 font-bold px-4 py-2 transition-colors flex items-center gap-2">
                        <i class="bi bi-trash"></i>
                        <span>حذف النشاط</span>
                    </button>

                    <div class="flex items-center gap-4">
                        <a href="/activities/<?= $activity->id ?>"
                            class="px-6 py-3 rounded-xl font-bold text-gray-500 hover:bg-gray-100 transition-colors">إلغاء</a>
                        <button type="submit" id="submitBtn"
                            class="px-8 py-3 rounded-xl font-bold bg-primary text-white shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:bg-primary-dark transition-all transform hover:-translate-y-0.5">
                            إنشاء التعديلات
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    Dropzone.autoDiscover = false;
    const activityId = <?= $activity->id ?>;
    let removedImages = [];

    function removeExistingImage(path, hash) {
        // Since we don't have a direct endpoint to remove one image without saving, 
        // we'll track removals and send them on submit (or we can assume user wants to remove immediately?
        // Logic in edit.php lines 98 showed a hidden input 'remove_images'.
        // I will replicate that logic: track arrays of paths to remove.
        // Wait, typical logic is: send list of images to KEEP or list to REMOVE.
        // The previous code had: <input type="hidden" name="remove_images">

        // I'll update the Remove Array
        removedImages.push(path);
        document.getElementById('removeImagesInput').value = JSON.stringify(removedImages);

        // Hide visually
        document.getElementById('img-container-' + hash).style.display = 'none';

        // Optional: Show toast
        showToast('info', 'سيتم حذف الصورة عند الحفظ');
    }

    $(document).ready(function () {
        const form = document.querySelector('#editActivityForm');
        let formSubmitting = false;

        const myDropzone = new Dropzone("#activityDropzone", {
            url: "/activities/" + activityId + "/update", // It's actually POST /api/activities/{id} or proper route?
            // ActivityController: update(id) handles it. View calls /activities/{id}/update usually? 
            // Router: POST /activities/{id}/update -> ActivityController::update
            // Previous JS used: fetch(`/api/activities/${activityId}`, method: POST)
            // Let's check Router.php to be sure if I can see it?
            // Assuming standard path. I'll use /activities/${activityId}/update which routes to update.
            // Wait, previous JS used /api/activities/${activityId} with POST.
            // I'll use the same URL I used in previous JS: /api/activities/${activityId} (mapped to update? likely via POST).
            // Actually, best to use the URL that definitely works.
            url: "/api/activities/" + activityId,

            paramName: "images",
            maxFilesize: 5,
            uploadMultiple: true,
            parallelUploads: 5,
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            autoProcessQueue: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dictRemoveFile: "حذف",
            dictCancelUpload: "إلغاء",
            init: function () {
                var dz = this;

                $('#editActivityForm').on('submit', function (e) {
                    e.preventDefault();
                    if (formSubmitting) return;

                    const btn = $('#submitBtn');
                    formSubmitting = true;
                    btn.prop('disabled', true).html('جاري الحفظ...');

                    if (dz.getQueuedFiles().length > 0) {
                        dz.processQueue();
                    } else {
                        submitFormManually(new FormData(form));
                    }
                });

                this.on("sendingmultiple", function (file, xhr, formData) {
                    const data = new FormData(form);
                    for (let pair of data.entries()) {
                        formData.append(pair[0], pair[1]);
                    }
                });

                this.on("successmultiple", function (files, response) {
                    handleSuccess(response);
                });

                this.on("errormultiple", function (files, response) {
                    handleError(response);
                    btnReset();
                });
            }
        });

        function submitFormManually(formData) {
            $.ajax({
                url: '/api/activities/' + activityId,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: handleSuccess,
                error: function (xhr) {
                    handleError(xhr.responseJSON || { message: 'حدث خطأ' });
                    btnReset();
                }
            });
        }

        function handleSuccess(response) {
            if (response.success) {
                Swal.fire({ title: 'تم الحفظ', text: response.message, icon: 'success', timer: 1500, showConfirmButton: false })
                    .then(() => window.location.href = '/activities/' + activityId);
            } else {
                handleError(response);
                btnReset();
            }
        }

        function handleError(response) {
            Swal.fire('خطأ', response.message || 'حدث خطأ أثناء الحفظ', 'error');
        }

        function btnReset() {
            formSubmitting = false;
            $('#submitBtn').prop('disabled', false).text('حفظ التعديلات');
        }

        window.deleteActivity = function () {
            confirmDelete('سيتم حذف النشاط نهائياً', function () {
                $.ajax({
                    url: '/api/activities/' + activityId,
                    type: 'DELETE',
                    data: { _csrf_token: $('meta[name="csrf-token"]').attr('content') },
                    success: function (r) {
                        if (r.success) window.location.href = '/activities';
                    }
                });
            });
        }
    });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>