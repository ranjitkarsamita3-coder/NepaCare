<?php
/**
 * NepaCare Database Migrations
 * Run this file once to add required columns to existing tables
 * Access via: http://localhost/Nepacare/migrations.php
 */

session_start();
include 'config/db.php';

$migrations = [];

function migrate_add_reminder_frequency($conn) {
    $table_name = 'reminders';
    $column_name = 'reminder_frequency';

    $check_query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_NAME = '$table_name' AND COLUMN_NAME = '$column_name'";
    $result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($result) === 0) {
        $alter_query = "ALTER TABLE $table_name ADD COLUMN $column_name VARCHAR(20) DEFAULT 'once'";
        if (mysqli_query($conn, $alter_query)) {
            return ['success' => true, 'message' => 'Added reminder_frequency column to reminders table'];
        } else {
            return ['success' => false, 'message' => 'Error: ' . mysqli_error($conn)];
        }
    } else {
        return ['success' => true, 'message' => 'reminder_frequency column already exists'];
    }
}

function migrate_create_feedback_table($conn) {
    $table_name = 'feedback';

    $check_query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '$table_name'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) === 0) {
        $create_query = "CREATE TABLE $table_name (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            user_role VARCHAR(20) NOT NULL,
            subject VARCHAR(255) DEFAULT NULL,
            message TEXT NOT NULL,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        if (mysqli_query($conn, $create_query)) {
            return ['success' => true, 'message' => 'Created feedback table'];
        } else {
            return ['success' => false, 'message' => 'Error: ' . mysqli_error($conn)];
        }
    } else {
        return ['success' => true, 'message' => 'feedback table already exists'];
    }
}

function migrate_add_feedback_is_read_column($conn) {
    $table_name = 'feedback';
    $column_name = 'is_read';

    $check_query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_NAME = '$table_name' AND COLUMN_NAME = '$column_name'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) === 0) {
        $alter_query = "ALTER TABLE $table_name ADD COLUMN $column_name TINYINT(1) NOT NULL DEFAULT 0";
        if (mysqli_query($conn, $alter_query)) {
            return ['success' => true, 'message' => 'Added is_read column to feedback table'];
        } else {
            return ['success' => false, 'message' => 'Error: ' . mysqli_error($conn)];
        }
    } else {
        return ['success' => true, 'message' => 'is_read column already exists'];
    }
}

function migrate_add_user_profile_fields($conn) {
    $table_name = 'users';

    $columns = [
        'address' => "VARCHAR(255) DEFAULT ''",
        'age' => "INT DEFAULT NULL"
    ];

    $results = [];

    foreach ($columns as $column_name => $definition) {
        $check_query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_NAME = '$table_name' AND COLUMN_NAME = '$column_name'";
        $result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($result) === 0) {
            $alter_query = "ALTER TABLE $table_name ADD COLUMN $column_name $definition";
            if (mysqli_query($conn, $alter_query)) {
                $results[] = "Added $column_name column to $table_name table";
            } else {
                return ['success' => false, 'message' => 'Error adding ' . $column_name . ': ' . mysqli_error($conn)];
            }
        } else {
            $results[] = "$column_name column already exists";
        }
    }

    return ['success' => true, 'message' => implode('; ', $results)];
}

$migrations[] = migrate_add_reminder_frequency($conn);
$migrations[] = migrate_create_feedback_table($conn);
$migrations[] = migrate_add_feedback_is_read_column($conn);
$migrations[] = migrate_add_user_profile_fields($conn);

?>
<!DOCTYPE html>
<html>
<head>
    <title>NepaCare - Database Migrations</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .migration-result {
            margin: 15px 0;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid;
        }
        .success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        h1 {
            color: #333;
        }
    </style>
</head>
<body>
    <h1>NepaCare Database Migrations</h1>
    <p>Running database migrations..</p>
    
    <?php foreach ($migrations as $index => $migration): ?>
        <div class="migration-result <?php echo $migration['success'] ? 'success' : 'error'; ?>">
            <strong>Migration <?php echo $index + 1; ?>:</strong> <?php echo htmlspecialchars($migration['message']); ?>
        </div>
    <?php endforeach; ?>
    
    <p style="margin-top: 30px; color: #666;">
        <strong>Note:</strong> All migrations have been executed. The reminder_frequency field is now available in your database with options: once, daily, weekly, monthly.
    </p>
</body>
</html>
