<?php
$email = $password = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid Email is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // If no errors, check credentials
    if (empty($errors)) {
        $conn = new mysqli("localhost", "root", "Root@123", "event_management");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashedPassword);
            $stmt->fetch();

            if (password_verify($password, $hashedPassword)) {
                session_start();
                $_SESSION['user_id'] = $id;
                header("Location: home.php"); // Redirect to home.php
                exit;
            } else {
                $errors[] = "Invalid email or password.";
            }
        } else {
            $errors[] = "Invalid email or password.";
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
    <title>Login</title>
</head>
<body>
<header>
    <h1>Event Management System</h1>
</header>
<main>
    <h2>Login</h2>
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
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $email; ?>">

        <label for="password">Password:</label>
        <input type="password" id="password" name="password">

        <input type="submit" value="Login">
    </form>
</main>
<footer>
    <p>&copy; 2023 Event Management System</p>
</footer>
</body>
</html>
