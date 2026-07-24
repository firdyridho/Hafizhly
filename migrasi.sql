-- Jalankan SQL ini di phpMyAdmin InfinityFree (tab SQL)
-- Atau langsung di database if0_42360028_hifzly_db

ALTER TABLE users ADD COLUMN is_verified TINYINT(1) DEFAULT 0;
ALTER TABLE users ADD COLUMN verification_code VARCHAR(64) DEFAULT NULL;
ALTER TABLE users ADD COLUMN verification_expiry DATETIME DEFAULT NULL;
ALTER TABLE users ADD COLUMN reset_code VARCHAR(64) DEFAULT NULL;
ALTER TABLE users ADD COLUMN reset_expiry DATETIME DEFAULT NULL;

-- Set is_verified = 1 untuk semua user yang sudah ada (biar bisa login)
UPDATE users SET is_verified = 1;
