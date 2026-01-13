-- Shabab Setif Association Management System
-- Seed Data for Initial Setup
-- Created: 2026-01-13

SET NAMES utf8mb4;

-- ----------------------------
-- Seed: committees
-- ----------------------------
INSERT INTO `committees` (`id`, `name`, `description`) VALUES
(1, 'المكتب التنفيذي', 'الإدارة العليا للجمعية ومتابعة كافة الأنشطة'),
(2, 'لجنة الإعلام والاتصال', 'مسؤولة عن التواصل مع الإعلام ومنصات التواصل الاجتماعي'),
(3, 'لجنة الأنشطة الثقافية', 'تنظيم الأنشطة الثقافية والتعليمية'),
(4, 'لجنة الأنشطة الرياضية', 'تنظيم البطولات والأنشطة الرياضية'),
(5, 'لجنة التضامن والعمل الاجتماعي', 'الأعمال الخيرية والتطوعية');

-- ----------------------------
-- Seed: users (Admin account)
-- Password is the member_card_id: SS-2025-001
-- ----------------------------
INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `role`, `member_card_id`, `password_hash`, `points_balance`, `committee_id`, `is_active`) VALUES
(1, 'مدير النظام', 'admin@shababsetif.org', '0550000001', 'admin', 'SS-2025-001', '$argon2id$v=19$m=65536,t=4,p=1$ekNsQTdpQUliOGVrUzdLNQ$xi9EqEcQiGY065rp79sP2s9afYVns0hJ7O5m6ziGFmw', 100, 1, 1),
(2, 'رئيس لجنة الإعلام', 'media@shababsetif.org', '0550000002', 'head', 'SS-2025-002', '$argon2id$v=19$m=65536,t=4,p=1$V0JOdms3R3k4cDEycmF2aQ$WKN95xEbFbNxHDPFBWNpOYnGMUVjMxkoZbD+DXCa1P8', 75, 2, 1),
(3, 'رئيس لجنة الثقافة', 'culture@shababsetif.org', '0550000003', 'head', 'SS-2025-003', '$argon2id$v=19$m=65536,t=4,p=1$QmR0REQuU2M5Lm0wM0hnRg$8x/HO862h9OZDsHYh7heroq1yWwyRQVzxqx+z7uwQyM', 80, 3, 1),
(4, 'أحمد بن محمد', 'ahmed@example.com', '0550000004', 'member', 'SS-2025-004', '$argon2id$v=19$m=65536,t=4,p=1$bVZ4Z0JiYm1NYWVKcXRoVA$1qX9ip5um7dYKmxDVLOvUIFWKZNQMncXNHObXKpwkvA', 45, 2, 1),
(5, 'سارة أمين', 'sara@example.com', '0550000005', 'member', 'SS-2025-005', '$argon2id$v=19$m=65536,t=4,p=1$dnNXd0o4US5JZjV5TnVpNQ$HkIjw4IublrsFc7zrI02dZwo9wdJbb9Nbiavi4VW7vs', 60, 3, 1);

-- ----------------------------
-- Seed: settings
-- ----------------------------
INSERT INTO `settings` (`key`, `value`, `description`) VALUES
('app_name', 'شباب سطيف', 'اسم الجمعية'),
('app_email', 'contact@shababsetif.org', 'البريد الإلكتروني الرسمي'),
('points_activity_default', '10', 'النقاط الافتراضية لحضور النشاط'),
('points_office_visit', '5', 'نقاط زيارة المقر'),
('points_social_interaction', '3', 'نقاط التفاعل على السوشيال ميديا'),
('card_prefix', 'SS', 'بادئة رقم البطاقة'),
('card_year', '2025', 'السنة الحالية للبطاقات');

-- ----------------------------
-- Seed: sample activities
-- ----------------------------
INSERT INTO `activities` (`id`, `title`, `description`, `date`, `time`, `location`, `points_value`, `created_by`, `committee_id`, `status`) VALUES
(1, 'حملة تنظيف الحي', 'حملة تطوعية لتنظيف حي المدينة الجديدة', '2025-01-20', '09:00:00', 'حي المدينة الجديدة - سطيف', 15, 1, 5, 'completed'),
(2, 'ورشة تطوير الذات', 'ورشة عمل حول تطوير المهارات الشخصية', '2025-01-25', '14:00:00', 'مقر الجمعية', 10, 3, 3, 'completed'),
(3, 'دوري كرة القدم', 'البطولة السنوية لكرة القدم بين اللجان', '2025-02-01', '16:00:00', 'ملعب 8 ماي 1945', 20, 1, 4, 'upcoming');

-- ----------------------------
-- Seed: sample attendance
-- ----------------------------
INSERT INTO `attendance` (`activity_id`, `user_id`, `status`, `marked_by`) VALUES
(1, 4, 'present', 1),
(1, 5, 'present', 1),
(2, 4, 'present', 3),
(2, 5, 'absent', 3);

-- ----------------------------
-- Seed: sample points_log
-- ----------------------------
INSERT INTO `points_log` (`user_id`, `points`, `reason`, `reference_type`, `reference_id`, `added_by`, `date_logged`) VALUES
(4, 15, 'حضور حملة تنظيف الحي', 'activity', 1, 1, '2025-01-20'),
(5, 15, 'حضور حملة تنظيف الحي', 'activity', 1, 1, '2025-01-20'),
(4, 10, 'حضور ورشة تطوير الذات', 'activity', 2, 3, '2025-01-25'),
(4, 5, 'زيارة مقر الجمعية', 'office_visit', NULL, 1, '2025-01-22'),
(5, 3, 'مشاركة منشور على فيسبوك', 'social', NULL, 2, '2025-01-23'),
(4, 10, 'مساعدة في تنظيم الأرشيف', 'manual', NULL, 1, '2025-01-24');
