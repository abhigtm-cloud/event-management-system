<?php
$firstName = $lastName = $contactNumber = $email = $event = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = htmlspecialchars($_POST['first_name']);
    $lastName = htmlspecialchars($_POST['last_name']);
    $contactNumber = htmlspecialchars($_POST['contact_number']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $event = htmlspecialchars($_POST['event']);

    // Validation
    if (empty($firstName) || !preg_match("/^[a-zA-Z]+$/", $firstName)) {
        $errors[] = "First Name is required and must contain only letters.";
    }
    if (empty($lastName) || !preg_match("/^[a-zA-Z]+$/", $lastName)) {
        $errors[] = "Last Name is required and must contain only letters.";
    }
    if (empty($contactNumber) || !is_numeric($contactNumber)) {
        $errors[] = "Contact Number is required and must be numeric.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid Email is required.";
    }
    if (empty($password) || strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }
    if (empty($event)) {
        $errors[] = "Please select an event.";
    }

    // If no errors, insert into database
    if (empty($errors)) {
        $conn = new mysqli("localhost", "root", "Root@123", "event_management");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, contact_number, email, password, event) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $firstName, $lastName, $contactNumber, $email, $hashedPassword, $event);

        if ($stmt->execute()) {
            echo "<p class='success'>Registration successful! You can now <a href='login.php'>login</a>.</p>";
        } else {
            $errors[] = "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Registration</title>
</head>
<body>
<header>
    <h1>Event Management System</h1>
</header>
<main>
    <h2>Register</h2>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo $firstName; ?>">

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo $lastName; ?>">

        <label for="contact_number">Contact Number:</label>
        <input type="text" id="contact_number" name="contact_number" value="<?php echo $contactNumber; ?>">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $email; ?>">

        <label for="password">Password:</label>
        <input type="password" id="password" name="password">

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password">

        <label for="event">Event:</label>
        <select id="event" name="event">
            <option value="">Select an event</option>
            <option value="Dance" <?php echo $event == "Dance" ? "selected" : ""; ?>>Dance</option>
            <option value="Music" <?php echo $event == "Music" ? "selected" : ""; ?>>Music</option>
            <option value="Poetry" <?php echo $event == "Poetry" ? "selected" : ""; ?>>Poetry</option>
            <option value="Art" <?php echo $event == "Art" ? "selected" : ""; ?>>Art</option>
            <!-- Add more options as needed -->
        </select>

        <input type="submit" value="Register">
    </form>
</main>
<footer>
    <p>&copy; 2023 Event Management System</p>
</footer>
</body>
</html>
