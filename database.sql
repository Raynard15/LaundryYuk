-- ============================================================
-- LaundryKu - Struktur database
-- Import file ini lewat phpMyAdmin atau: mysql -u root -p < database.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS laundry_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE laundry_db;

-- ------------------------------------------------------------
-- Tabel admin
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin (
    id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(50)  NOT NULL,
    password VARCHAR(255) NOT NULL COMMENT 'Hash bcrypt (password_hash)',
    PRIMARY KEY (id),
    UNIQUE KEY uq_admin_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabel transaksi
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS transaksi (
    id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    no_resi       VARCHAR(20)   NOT NULL,
    nama_customer VARCHAR(100)  NOT NULL,
    no_hp         VARCHAR(20)   NOT NULL,
    jenis_layanan VARCHAR(50)   NOT NULL,
    berat_kg      DECIMAL(6,2)  NOT NULL,
    total_harga   INT UNSIGNED  NOT NULL,
    status_cucian ENUM('Baru Masuk','Sedang Dicuci','Disetrika','Siap Diambil','Selesai')
                  NOT NULL DEFAULT 'Baru Masuk',
    tanggal_masuk DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_transaksi_resi (no_resi),
    KEY idx_transaksi_status (status_cucian)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Data awal
-- Akun admin default -> username: admin | password: admin123
-- (SEGERA ganti password setelah instalasi)
-- ------------------------------------------------------------
INSERT INTO admin (username, password) VALUES
('admin', '$2y$10$oN.p/qRYhao/rNg9BifYz.DpJbsDUlNkjyY3q6FzHTZCV179RClIC');

INSERT INTO transaksi (no_resi, nama_customer, no_hp, jenis_layanan, berat_kg, total_harga, status_cucian) VALUES
('RSI-DEMO01', 'Budi Santoso', '081234567890', 'Cuci Kiloan',    3.50, 24500, 'Sedang Dicuci'),
('RSI-DEMO02', 'Siti Aminah',  '082345678901', 'Express',        2.00, 24000, 'Siap Diambil'),
('RSI-DEMO03', 'Andi Wijaya',  '083456789012', 'Cuci + Setrika', 5.00, 45000, 'Baru Masuk');
