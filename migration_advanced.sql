-- =============================================================
-- MIGRATION: Thêm các cột nâng cao cho bảng users
-- =============================================================

USE `my_store`;

-- Thêm các cột mới vào bảng users
ALTER TABLE `users`
    ADD COLUMN `phone`       VARCHAR(20)   DEFAULT NULL AFTER `name`,
    ADD COLUMN `address`     VARCHAR(255)  DEFAULT NULL AFTER `phone`,
    ADD COLUMN `avatar`      VARCHAR(255)  DEFAULT NULL AFTER `address`,
    ADD COLUMN `is_active`   TINYINT(1)    NOT NULL DEFAULT 1 AFTER `avatar`,
    ADD COLUMN `reset_token` VARCHAR(64)   DEFAULT NULL AFTER `is_active`,
    ADD COLUMN `reset_expires` DATETIME    DEFAULT NULL AFTER `reset_token`,
    ADD COLUMN `updated_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Index hỗ trợ tra cứu token
ALTER TABLE `users` ADD INDEX `idx_reset_token` (`reset_token`);
