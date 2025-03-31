<?php
$servername = "localhost";
$username = "root";
$password = "Root@123"; // If you have set a password for the root user, enter it here.

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS event_management";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully.<br>";
    } else {
        throw new Exception("Error creating database: " . $conn->error);
    }

    // Select the database
    $conn->select_db("event_management");

    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        contact_number VARCHAR(15) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        event VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    if ($conn->query($sql) === TRUE) {
        echo "Table 'users' created successfully.<br>";
    } else {
        throw new Exception("Error creating table: " . $conn->error);
    }

    $conn->close();
} catch (Exception $e) {
    die($e->getMessage());
}
?>
