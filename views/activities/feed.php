<!-- Activities Feed Page -->
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="page-title"><i class="bi bi-rss text-primary me-2"></i>آخر الأنشطة</h2>
                <div class="btn-group">
                    <a href="/activities" class="btn btn-outline-primary"><i class="bi bi-list"></i></a>
                    <a href="/activities/feed" class="btn btn-primary"><i class="bi bi-grid"></i></a>
                </div>
            </div>

            <div class="activity-feed">
                <?php if (!empty($activities)): ?>
                    <?php foreach ($activities as $activity): ?>
                        <div class="feed-card card mb-4">
                            <div class="card-header d-flex align-items-center">
                                <img src="<?= $activity['creator_avatar'] ?? '/assets/images/default-avatar.png' ?>"
                                    class="rounded-circle me-3" width="45" height="45">
                                <div class="flex-grow-1">
                                    <strong>
                                        <?= htmlspecialchars($activity['creator_name'] ?? 'النظام') ?>
                                    </strong>
                                    <small class="text-muted d-block">
                                        <?= date('d M Y', strtotime($activity['created_at'])) ?>
                                    </small>
                                </div>
                                <span class="badge bg-<?= $activity['status'] === 'completed' ? 'success' : 'primary' ?>">
                                    <?= $activity['status'] === 'completed' ? 'مكتمل' : 'قادم' ?>
                                </span>
                            </div>

                            <?php
                            $images = json_decode($activity['images_json'] ?? '[]', true);
                            if (!empty($images)):
                                ?>
                                <div class="feed-images">
                                    <?php if (count($images) === 1): ?>
                                        <img src="<?= $images[0] ?>" class="img-fluid w-100" alt="">
                                    <?php else: ?>
                                        <div class="row g-1">
                                            <?php foreach (array_slice($images, 0, 4) as $i => $img): ?>
                                                <div class="col-6"><img src="<?= $img ?>" class="img-fluid" alt=""></div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="card-body">
                                <h5 class="card-title">
                                    <?= htmlspecialchars($activity['title']) ?>
                                </h5>
                                <p class="card-text text-muted">
                                    <?= nl2br(htmlspecialchars(substr($activity['description'] ?? '', 0, 200))) ?>
                                </p>

                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="badge bg-light text-dark"><i class="bi bi-calendar me-1"></i>
                                        <?= $activity['date'] ?>
                                    </span>
                                    <?php if ($activity['location']): ?>
                                        <span class="badge bg-light text-dark"><i class="bi bi-geo-alt me-1"></i>
                                            <?= htmlspecialchars($activity['location']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-star me-1"></i>
                                        <?= $activity['points_value'] ?> نقطة
                                    </span>
                                    <span class="badge bg-info"><i class="bi bi-people me-1"></i>
                                        <?= $activity['attendee_count'] ?? 0 ?> حاضر
                                    </span>
                                </div>

                                <a href="/activities/<?= $activity['id'] ?>" class="btn btn-outline-primary btn-sm">عرض
                                    التفاصيل</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x display-1 text-muted"></i>
                        <p class="mt-3 text-muted">لا توجد أنشطة حالياً</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .feed-card {
        border: none;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        border-radius: 16px;
        overflow: hidden;
    }

    .feed-card .card-header {
        background: white;
        border-bottom: 1px solid #f0f0f0;
    }

    .feed-images img {
        object-fit: cover;
        height: 300px;
    }

    .feed-images .row img {
        height: 150px;
    }
</style>