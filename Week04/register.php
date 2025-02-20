<?php
include 'config.php';

session_start();

if(isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $cpass = mysqli_real_escape_string($conn, $_POST['cpassword']);

    // Use password_hash for secure password hashing
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/'.$image;
    $code = rand(999999, 111111);

    $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email'") or die('query failed');

    if(mysqli_num_rows($select) > 0) {
        $message[] = 'user already exists';
    } else {
        if($pass != $cpass) {
            $message[] = 'confirm password not matched!';
        } elseif($image_size > 2000000) {
            $message[] = 'image size is too large!';
        } else {
            // Temporarily store user data in session
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['hashed_pass'] = $hashed_pass;
            $_SESSION['image'] = $image;
            $_SESSION['image_tmp_name'] = $image_tmp_name;
            $_SESSION['image_folder'] = $image_folder;
            $_SESSION['code'] = $code;

            // Handle email verification
            $subject = "Email Verification Code";
            $message1 = "Your verification code is $code";
            $sender = "From: benjidev01@gmail.com";

            if(mail($email, $subject, $message1, $sender)) {
                $message[] = 'Now please check your email, enter the OTP to verify and complete your registration!';
                $_SESSION['show_otp_form'] = true;
            } else {
                $message[] = 'Failed while sending code!';
            }
        }
    }
}

// If user clicks verification code submit button
if(isset($_POST['check'])) {
    $OTP = mysqli_real_escape_string($conn, $_POST['OTP']);
    
    if($OTP == $_SESSION['code']) {
        $name = $_SESSION['name'];
        $email = $_SESSION['email'];
        $hashed_pass = $_SESSION['hashed_pass'];
        $image = $_SESSION['image'];
        $image_tmp_name = $_SESSION['image_tmp_name'];
        $image_folder = $_SESSION['image_folder'];

        $insert = mysqli_query($conn, "INSERT INTO `user_form`(name, email, password, image, code) 
        VALUES ('$name', '$email', '$hashed_pass', '$image', '$OTP')") or die('query failed');
        
        if($insert) {
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'Registration successful!';
            header('location: login.php');
        } else {
            $message[] = 'Registration failed!';
        }
    } else {
        $message[] = "wrong OTP - $OTP, try again!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- custom css file link -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-container">
    <form id="register-form" action="" method="post" enctype="multipart/form-data" <?php if(isset($_SESSION['show_otp_form'])) echo 'style="display: none;"'; ?>>
        <h3>Step 1: Register now</h3>
        <?php
        if(isset($message)) {
            foreach($message as $msg) {
                echo '<div class="message">'.$msg.'</div>';
            }
        }
        ?>
        <input type="text" name="name" placeholder="Enter username" class="box" required>
        <input type="email" name="email" placeholder="Enter email" class="box" required>
        <input type="password" name="password" placeholder="Enter password" class="box" required>
        <input type="password" name="cpassword" placeholder="Confirm password" class="box" required>
        <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
        <input type="submit" name="submit" value="Submit now to receive email" class="btn">
    </form>

    <form id="otp-form" action="" method="post" enctype="multipart/form-data" <?php if(!isset($_SESSION['show_otp_form'])) echo 'style="display: none;"'; ?>>
        <h3>Step 2: Enter OTP to verify your email</h3>
        <?php
        if(isset($message)) {
            foreach($message as $msg) {
                echo '<div class="message">'.$msg.'</div>';
            }
        }
        ?>
        <input type="text" name="OTP" placeholder="Enter OTP" class="box" required>
        <input type="submit" name="check" value="Register now" class="btn">
        <p>Already have an account? <a href="login.php">Login now</a></p>
    </form>
</div>
</body>
</html>
