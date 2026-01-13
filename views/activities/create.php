<?php
$title = 'إنشاء نشاط جديد';
ob_start();
?>

<div class="px-4 py-6 md:px-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-l from-primary to-green-600">
                إنشاء نشاط جديد
            </h1>
            <p class="text-gray-500 mt-2">أضف فعالية أو نشاط جديد للجمعية</p>
        </div>
        <a href="/activities"
            class="flex items-center gap-2 text-gray-500 hover:text-primary transition-colors bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100">
            <i class="bi bi-arrow-right"></i>
            <span>عودة للقائمة</span>
        </a>
    </div>

    <!-- Create Form -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="p-6 md:p-8">
            <form id="createActivityForm" class="space-y-6">
                <!-- Main Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">عنوان النشاط <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="title" required
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none"
                            placeholder="مثال: حملة تشجير بحي المستقبل">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">اللجنة المنظمة</label>
                        <div class="relative">
                            <select name="committee_id"
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none appearance-none cursor-pointer">
                                <option value="">بدون لجنة (عام)</option>
                                <?php foreach ($committees as $committee): ?>
                                    <option value="<?= $committee['id'] ?>"
                                        <?= ($currentUser->committee_id == $committee['id']) ? 'selected' : '' ?>>
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
                        <input type="date" name="date" required
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">التوقيت</label>
                        <input type="time" name="time"
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">المكان</label>
                        <div class="relative">
                            <input type="text" name="location"
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none pl-10"
                                placeholder="مثال: دار الشباب">
                            <i class="bi bi-geo-alt absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">نقاط النشاط</label>
                        <div class="relative">
                            <input type="number" name="points_value" value="10" min="0"
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none pl-10">
                            <i class="bi bi-star absolute left-4 top-1/2 -translate-y-1/2 text-yellow-500 text-lg"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">وصف النشاط</label>
                    <textarea name="description" rows="4"
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none resize-none"
                        placeholder="اكتب وصفاً مختصراً للنشاط..."></textarea>
                </div>

                <!-- Dropzone Image Upload -->
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">صور النشاط</label>
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

                <!-- Submit -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
                    <a href="/activities"
                        class="px-6 py-3 rounded-xl font-bold text-gray-500 hover:bg-gray-100 transition-colors">إلغاء</a>
                    <button type="submit" id="submitBtn"
                        class="px-8 py-3 rounded-xl font-bold bg-primary text-white shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:bg-primary-dark transition-all transform hover:-translate-y-0.5">
                        <span class="inline-block relative">
                            <span class="">إنشاء النشاط</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Initialize Dropzone
    Dropzone.autoDiscover = false;

    $(document).ready(function () {
        // Form Configuration
        const form = document.querySelector('#createActivityForm');
        let formSubmitting = false;

        const myDropzone = new Dropzone("#activityDropzone", {
            url: "/activities/store", // Post URL
            paramName: "images", // The name that will be used to transfer the file
            maxFilesize: 5, // MB
            uploadMultiple: true,
            parallelUploads: 5,
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            autoProcessQueue: false, // Wait for submit
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dictRemoveFile: "حذف",
            dictCancelUpload: "إلغاء",
            init: function () {
                var dz = this;

                // Handle Form Submit
                $('#createActivityForm').on('submit', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (formSubmitting) return;

                    const btn = $('#submitBtn');
                    const originalText = btn.text();

                    // Validate basic fields (HTML5 validation does this but double check)
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }

                    formSubmitting = true;
                    btn.prop('disabled', true).html('<div class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>');

                    // If files exist, process queue (which sends the request)
                    if (dz.getQueuedFiles().length > 0) {
                        dz.processQueue();
                    } else {
                        // Send manually via AJAX if no files
                        submitFormManually(new FormData(form));
                    }
                });

                // Append other form data
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
                    formSubmitting = false;
                    $('#submitBtn').prop('disabled', false).text('إنشاء النشاط');
                });
            }
        });

        function submitFormManually(formData) {
            $.ajax({
                url: '/activities/store',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    handleSuccess(response);
                },
                error: function (xhr) {
                    handleError(xhr.responseJSON || { message: 'حدث خطأ غير متوقع' });
                    formSubmitting = false;
                    $('#submitBtn').prop('disabled', false).text('إنشاء النشاط');
                }
            });
        }

        function handleSuccess(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '/activities';
                });
            } else {
                Swal.fire('خطأ', response.message || 'حدث خطأ ما', 'error');
                formSubmitting = false;
                $('#submitBtn').prop('disabled', false).text('إنشاء النشاط');
            }
        }

        function handleError(response) {
            // If response is a string (html error page from dropzone), it's bad
            let msg = 'حدث خطأ أثناء الرفع';
            if (typeof response === 'object' && response.message) msg = response.message;

            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: msg
            });
        }
    });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>