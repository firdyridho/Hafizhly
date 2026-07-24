<?php
require_once 'config/database.php';
$q = mysqli_query($conn, "SHOW COLUMNS FROM users");
echo "Kolom di tabel users:<br>";
while ($row = mysqli_fetch_assoc($q)) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
}
