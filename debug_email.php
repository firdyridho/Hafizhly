<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
require_once 'config/email.php';

echo "Step 1: session_start... ";
session_start();
echo "OK<br>";

echo "Step 2: random_int... ";
$kode = sprintf("%06d", random_int(0, 999999));
echo "OK ($kode)<br>";

echo "Step 3: database query... ";
$email = 'hifzhlyid@gmail.com';
$expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
$q = mysqli_query($conn, "UPDATE users SET reset_code = '$kode', reset_expiry = '$expiry' WHERE email = '$email'");
if ($q) {
    echo "OK<br>";
} else {
    echo "FAILED: " . mysqli_error($conn) . "<br>";
}

echo "Step 4: kirim_email... ";
$result = kirim_email($email, 'Test Debug', "<h1>Test</h1><p>Kode: $kode</p>");
if ($result) {
    echo "SUKSES<br>";
} else {
    echo "GAGAL<br>";
}

echo "<br><b>Selesai!</b>";
