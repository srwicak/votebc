<?php

// Database configuration
$hostname = 'localhost';
$username = 'root';
$password = 'Root1234!';
$database = 'evoting_db';

// Connect to MySQL
$mysqli = new mysqli($hostname, $username, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "Connected to database successfully.\n";

// Disable foreign key checks to avoid constraint errors
$mysqli->query('SET FOREIGN_KEY_CHECKS = 0');

// Get all tables in the database
$tables = [];
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

// Drop each table
foreach ($tables as $table) {
    $mysqli->query("DROP TABLE IF EXISTS `$table`");
    echo "Dropped table: $table\n";
}

// Re-enable foreign key checks
$mysqli->query('SET FOREIGN_KEY_CHECKS = 1');

// Close the connection
$mysqli->close();

echo "All tables have been dropped successfully.\n";
echo "Now running migrations...\n";

// Run the migrations using the CodeIgniter CLI
passthru('php spark migrate');

echo "Now running the EsaUnggul seeder...\n";

// Run the EsaUnggul seeder
passthru('php spark db:seed EsaUnggulSeeder');

echo "Database reset completed successfully!\n";