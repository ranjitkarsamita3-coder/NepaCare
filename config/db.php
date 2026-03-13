<?php
$conn = mysqli_connect("localhost", "root", "", "nepacare_db");

if (!$conn) {
    die("Database connection failed");
}

/**
 * Check whether a given column exists on the users table.
 */
function userColumnExists($conn, $column) {
    $column = mysqli_real_escape_string($conn, $column);
    $res = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE '$column'");
    return $res && mysqli_num_rows($res) > 0;
}

/**
 * Return a list of columns from the requested list that exist on the users table.
 */
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
