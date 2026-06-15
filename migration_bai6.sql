-- ============================================
-- MIGRATION BÀI 5-6: Web API + JWT
-- Chạy file này sau tuan5sql.sql
-- ============================================

USE `my_store`;

-- Cập nhật bảng orders: thêm user_id, total, status, payment
ALTER TABLE `orders`
  ADD COLUMN IF NOT EXISTS `user_id`        INT          DEFAULT NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `total`          DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER `address`,
  ADD COLUMN IF NOT EXISTS `status`         ENUM('pending','processing','shipped','delivered','cancelled')
                                            NOT NULL DEFAULT 'pending' AFTER `total`,
  ADD COLUMN IF NOT EXISTS `payment_status` ENUM('unpaid','paid') NOT NULL DEFAULT 'unpaid' AFTER `status`,
  ADD COLUMN IF NOT EXISTS `payment_method` VARCHAR(50) DEFAULT NULL AFTER `payment_status`;

-- Thêm foreign key user_id → users nếu chưa có
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_user`
  FOREIGN KEY IF NOT EXISTS (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL;

