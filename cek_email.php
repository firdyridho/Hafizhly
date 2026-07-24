<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
require_once 'config/email.php';

echo "Mencoba kirim email test...<br>";

$result = kirim_email('hifzhlyid@gmail.com', 'Test Hifzhly', '<h1>Test</h1><p>Ini test dari Hifzhly</p>');

if ($result) {
    echo "<b style='color:green'>SUKSES! Email terkirim.</b>";
} else {
    echo "<b style='color:red'>GAGAL! Email tidak terkirim.</b>";
}
