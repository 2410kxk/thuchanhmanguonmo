-- =============================================================
-- MIGRATION: Thêm bảng users cho hệ thống đăng ký / đăng nhập
-- Database: my_store
-- =============================================================

USE `my_store`;

-- Tạo bảng users
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT          NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('admin','user') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- Tài khoản mặc định để test
--   admin@store.com  /  admin123   (role = admin)
--   user@store.com   /  user123    (role = user)
-- Password được hash bằng PHP password_hash(..., PASSWORD_BCRYPT)
-- =============================================================
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
(
    'Administrator',
    'admin@store.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin'
),
(
    'Người dùng thường',
    'user@store.com',
    '$2y$10$TKh8H1.PfbuNDQ6KLv3v3eH3z1yIX.4cq/Qj8dQVNMFZvqYZiWbO2',
    'user'
);

-- Ghi chú:
--   Hash trên là ví dụ demo. Khi chạy thực tế, hãy đăng ký qua form
--   hoặc tạo hash mới bằng: password_hash('your_password', PASSWORD_BCRYPT)
