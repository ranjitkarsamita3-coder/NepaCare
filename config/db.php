<?php
$conn = mysqli_connect("localhost", "root", "", "nepacare_db");

if (!$conn) {
    die("Database connection failed");
}

function userColumnExists($conn, $column) {
    $column = mysqli_real_escape_string($conn, $column);
    $res = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE '$column'");
    return $res && mysqli_num_rows($res) > 0;
}

function getExistingUserColumns($conn, array $cols) {
    $result = [];
    foreach ($cols as $col) {
        if (userColumnExists($conn, $col)) {
            $result[] = $col;
        }
    }
    return $result;
}
?>
