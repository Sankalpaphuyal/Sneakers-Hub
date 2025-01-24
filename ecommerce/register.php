<?php
session_start();
if(isset($_SESSION['email'])) {
    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register Page</title>
    <!-- Include css link below to assets/css/style.css -->
    <link rel="stylesheet" type="text/css" href="css/form_styles.css">
    <!-- Include js link below to assets/js/script.js -->
    <script src="js/script.js"></script>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
            <?php
            if(isset($_SESSION['error'])){
                echo "<span class='error-text auth-err'>". $_SESSION['error'] . "</span>";
                unset($_SESSION['error']);
            }
            if(isset($_SESSION['success'])){
                echo "<span class='success-text auth-err'>". $_SESSION['success'] . "</span>";
                unset($_SESSION['success']);
            }
            ?>
        <form action="process/register_process.php" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" onkeyup="validatePassword()" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" onkeyup="validatePassword()" required>
            </div>
            <div class="form-group">
                <span class="error-text" id="password_error_msg" style="display: none;">The password doesn't match</span>
                <span class="success-text" id="password_success_msg" style="display: none;">The password matches</span>
            </div>
            <div class="form-group">
                <button type="submit" id="register_btn" disabled>Register</button>
            </div>
            <div class="form-group">
                <a href="login.php">Already have an account? Login</a>
            </div>
        </form>
    </div>
</body>
</html>
