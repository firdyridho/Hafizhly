<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP Version: " . phpversion() . "<br>";
echo "vendor/autoload.php exists: " . (file_exists(__DIR__ . '/vendor/autoload.php') ? 'YES' : 'NO') . "<br>";
echo "config/email.php exists: " . (file_exists(__DIR__ . '/config/email.php') ? 'YES' : 'NO') . "<br>";

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    try {
        require_once __DIR__ . '/vendor/autoload.php';
        echo "PHPMailer loaded: YES<br>";
        echo "PHPMailer version: " . (class_exists('PHPMailer\PHPMailer\PHPMailer') ? 'OK' : 'NOT FOUND') . "<br>";
    } catch (Throwable $e) {
        echo "PHPMailer error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "<b style='color:red'>vendor/autoload.php TIDAK ADA! Upload folder vendor/ via FTP.</b><br>";
}
