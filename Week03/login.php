<?php
include 'config.php';
session_start();

if (isset($_POST['submit'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];

    $select = mysqli_query($conn, "SELECT * FROM user_form WHERE email = '$email'") or die("Query Failed");

    if(mysqli_num_rows($select) > 0) {
        $row = mysqli_fetch_assoc($select);
        if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            header('Location: ./profile.php');
        } else {
            $message[] = "Invalid email or password";
        }
    } else {
        $message[] = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="form-container">
    <form action="" method="post" enctype="multipart/form-data">

            <h3>Login Now</h3>
            <?php
            if (isset($message)) {
                foreach ($message as $msg) {
                    echo "<p class='error'>$msg</p>";
                }
            }
            ?>
            <input type="email" name="email" placeholder="Enter email" class="box" required>
            <input type="password" name="password" placeholder="Enter password" class="box" required>
            <input type="submit" name="submit" value="Login Now" class="btn">
            <p>Don't have an account? <a href="register.php">Register now</a></p>
        </form>
    </div>

</body>

</html>