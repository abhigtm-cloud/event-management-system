<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details from the database
$conn = new mysqli("localhost", "root", "Root@123", "event_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT first_name, last_name, contact_number, email FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Create the uploads directory if it doesn't exist
    }

    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a valid image
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "<p class='error'>File is not an image.</p>";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["profile_picture"]["size"] > 500000) {
        echo "<p class='error'>Sorry, your file is too large.</p>";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        echo "<p class='error'>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</p>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "<p class='error'>Sorry, your file was not uploaded.</p>";
    } else {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            echo "<p class='success'>The file " . htmlspecialchars(basename($_FILES["profile_picture"]["name"])) . " has been uploaded.</p>";
        } else {
            echo "<p class='error'>Sorry, there was an error uploading your file.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Home - Event Management System</title>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Welcome, <?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></h1>
        </header>
        <main>
            <section class="user-details">
                <h2>Your Registered Details</h2>
                <div class="details">
                    <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['first_name']); ?></p>
                    <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['last_name']); ?></p>
                    <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($user['contact_number']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </section>

            <section class="upload-section">
                <h2>Upload Profile Picture</h2>
                <form action="home.php" method="post" enctype="multipart/form-data" class="upload-form">
                    <input type="file" name="profile_picture" required>
                    <button type="submit" class="btn">Upload</button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>