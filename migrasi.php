<?php
require_once 'config/database.php';

$queries = [
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS is_verified TINYINT(1) DEFAULT 0",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS verification_code VARCHAR(64) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS verification_expiry DATETIME DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_code VARCHAR(64) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_expiry DATETIME DEFAULT NULL",
];

foreach ($queries as $q) {
    if (mysqli_query($conn, $q)) {
        echo "OK: $q<br>";
    } else {
        if (strpos(mysqli_error($conn), 'Duplicate column') !== false) {
            echo "SKIP (already exists): $q<br>";
        } else {
            echo "ERROR: " . mysqli_error($conn) . "<br>";
        }
    }
}

echo "<br>Migrasi selesai!";
