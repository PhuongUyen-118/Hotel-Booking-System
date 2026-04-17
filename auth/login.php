<?php
session_start();
require_once '../config/db_connect.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_role"] = $user["role"];

            // Chuyển hướng dựa trên role
            if ($user["role"] === "admin") {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign in or create an account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .login-container {
            max-width: 430px;
            margin: 40px auto;
            background: #fff;
            border-radius: 8px;
            padding: 32px 32px 16px 32px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.08);
        }

        .login-container h1 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 16px;
        }

        .login-container p {
            color: #222;
            margin-bottom: 24px;
            font-size: 1rem;
        }

        .login-container label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
            margin-top: 16px;
        }

        .login-container input[type="email"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #bdbdbd;
            border-radius: 4px;
            font-size: 1rem;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        .login-container button {
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

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 24px 0 16px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #ddd;
        }

        .divider:not(:empty)::before {
            margin-right: .75em;
        }

        .divider:not(:empty)::after {
            margin-left: .75em;
        }

        .social-login {
            display: flex;
            justify-content: center;
            gap: 32px;
            margin-bottom: 24px;
        }

        .social-btn {
            width: 80px;
            height: 80px;
            background: #f7f7f7;
            border: 1px solid #eee;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            cursor: pointer;
            transition: box-shadow 0.2s;
        }

        .social-btn:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .login-footer {
            color: #222;
            font-size: 0.95rem;
            text-align: center;
            margin-top: 16px;
        }

        .login-footer a {
            color: #0071c2;
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
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

        @media (max-width: 500px) {
            .login-container {
                padding: 16px 4vw;
            }

            .social-btn {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>Sign in or create an account</h1>
        <p>You can sign in using your Booking.com account to access our services.</p>
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            <button type="submit">Continue with email</button>
        </form>
        <div class="divider">or use one of these options</div>
        <div class="social-login">
            <div class="social-btn" title="Sign in with Google">
                <img src="https://tse2.mm.bing.net/th/id/OIP.Din44az7iZZDfbsrD1kfGQHaHa?rs=1&pid=ImgDetMain&o=7&rm=3" alt="Google" style="width:32px;">
            </div>
            <div class="social-btn" title="Sign in with Apple">
                <i class="fab fa-apple" style="color:#222;"></i>
            </div>
            <div class="social-btn" title="Sign in with Facebook">
                <i class="fab fa-facebook-f" style="color:#1877f3;"></i>
            </div>
        </div>
        <div class="login-footer" style="margin-top:32px;">
            By signing in or creating an account, you agree with our
            <a href="#">Terms &amp; conditions</a> and <a href="#">Privacy statement</a>
            <br><br>
            All rights reserved.<br>
            Copyright (2006 - <?php echo date("Y"); ?>) - Booking.com™
        </div>
    </div>
</body>

</html>