<?php
session_start();
require_once '../config/db_connect.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            // Chuyển hướng sang trang login với thông báo thành công
            header("Location: login.php?registration=success");
            exit();
        } else {
            $error = "Registration failed. Email may already be in use.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create an account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .register-container {
            max-width: 430px;
            margin: 40px auto;
            background: #fff;
            border-radius: 8px;
            padding: 32px 32px 16px 32px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.08);
        }

        .register-container h1 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 16px;
        }

        .register-container p {
            color: #222;
            margin-bottom: 24px;
            font-size: 1rem;
        }

        .register-container label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
            margin-top: 16px;
        }

        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #bdbdbd;
            border-radius: 4px;
            font-size: 1rem;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        .register-container button {
            width: 100%;
            background: #0071c2;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 14px 0;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 24px;
            margin-top: 8px;
        }

        .error-message {
            color: #d8000c;
            background: #ffd2d2;
            border: 1px solid #d8000c;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 16px;
            text-align: center;
        }

        .register-footer {
            color: #222;
            font-size: 0.95rem;
            text-align: center;
            margin-top: 16px;
        }

        .register-footer a {
            color: #0071c2;
            text-decoration: none;
        }

        .register-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 500px) {
            .register-container {
                padding: 16px 4vw;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h1>Create an account</h1>
        <p>Join Booking.com to start booking your trips today.</p>
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="Enter your full name" required>

            <label for="email">Email address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email address" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a password" required>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>

            <button type="submit">Create Account</button>
        </form>
        <div class="register-footer">
            Already have an account? <a href="login.php">Sign in here</a>
        </div>
    </div>
</body>

</html>