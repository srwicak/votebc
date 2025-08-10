<?php

// Connect to the database
$db = mysqli_connect('localhost', 'root', 'Root1234!', 'evoting_db');

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Update the operator user - use a valid role from the allowed list: admin, mahasiswa, kandidat
$query = "UPDATE users SET role = 'admin' WHERE nim = 'OPERATOR001'";
$result = mysqli_query($db, $query);

if ($result) {
    echo "Operator user updated successfully\n";
} else {
    echo "Error updating operator user: " . mysqli_error($db) . "\n";
}

mysqli_close($db);