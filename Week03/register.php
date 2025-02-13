<?php
include './config.php';
if (isset($_POST['submit'])) {

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, $_POST['password']);
   $cpass = mysqli_real_escape_string($conn, $_POST['cpassword']);
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = './uploadedimg/' . $image;

   $select = mysqli_query($conn, "SELECT * FROM user_form WHERE email = '$email'") or die("Query Failed");

   if (mysqli_num_rows($select) > 0) {
      $message[] = 'User already exists';
   } else {
      if ($pass != $cpass) {
         $message[] = 'Password does not match';
      } elseif ($image_size > 2097152) {
         $message[] = 'Image size should be less than 2mb';
      } else {
         $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
         $insert = mysqli_query($conn, "INSERT INTO user_form (name, email, password, image) VALUES ('$name', '$email', '$hashed_pass', '$image')") or die("Query Failed");

         if ($insert) {
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'User registered successfully';
            header('Location: ./login.php');
         } else {
            $message[] = 'User not registered';
         }
      }
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

   <!-- custom css file link  -->
   <link rel="stylesheet" href="style.css">

</head>

<body>

   <div class="form-container">
      <form action="" method="post" enctype="multipart/form-data">
         <h3>register now</h3>
         <?php if (isset($message)) {
            foreach ($message as $msg) {
               echo "<p class='error'>$msg</p>";
            }
         } ?>

         <input type="text" name="name" placeholder="enter username" class="box" required>
         <input type="email" name="email" placeholder="enter email" class="box" required>
         <input type="password" name="password" placeholder="enter password" class="box" required>
         <input type="password" name="cpassword" placeholder="confirm password" class="box" required>
         <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
         <input type="submit" name="submit" value="register now" class="btn">
         <p>already have an account? <a href="login.php">login now</a></p>
      </form>

   </div>

</body>

</html>