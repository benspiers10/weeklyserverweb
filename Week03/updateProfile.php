<?php
include './config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}
$user_id = $_SESSION['user_id'];

if (isset($_POST['updateProfile'])) {
    $updateName = mysqli_real_escape_string($conn, $_POST['updateName']);
    $updateEmail = mysqli_real_escape_string($conn, $_POST['updateEmail']);

    // Update user details
    mysqli_query($conn, "UPDATE `user_form` SET name = '$updateName', email = '$updateEmail' WHERE id = '$user_id'") or die(mysqli_error($conn));

    $oldPass = $_POST['oldPass'];
    $updatePass = $_POST['updatePass'];
    $newPass = $_POST['newPass'];
    $confirmPass = $_POST['confirmPass'];

    if (!empty($updatePass) && !empty($newPass) && !empty($confirmPass)) {
        $result = mysqli_query($conn, "SELECT password FROM `user_form` WHERE id = '$user_id'") or die(mysqli_error($conn));
        $row = mysqli_fetch_assoc($result);
        $hashedOldPass = $row['password'];

        if (!password_verify($updatePass, $hashedOldPass)) {
            $message[] = 'Old password is incorrect';
        } elseif ($newPass !== $confirmPass) {
            $message[] = 'New password and confirm password do not match';
        } else {
            $hashedNewPass = password_hash($confirmPass, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE `user_form` SET password = '$hashedNewPass' WHERE id = '$user_id'") or die(mysqli_error($conn));
            $message[] = 'Password updated successfully';
        }
    }

    // Handle Image Upload
    if (!empty($_FILES['updateImage']['name'])) {
        $updateImage = $_FILES['updateImage']['name'];
        $updateImageSize = $_FILES['updateImage']['size'];
        $updateImageTmpName = $_FILES['updateImage']['tmp_name'];
        $updateImageFolder = 'uploadedimg/' . $updateImage;

        if ($updateImageSize > 5000000) {
            $message[] = 'Image size should be less than 5MB';
        } else {
            $imageUpdateQuery = mysqli_query($conn, "SELECT image FROM `user_form` WHERE id = '$user_id'") or die(mysqli_error($conn));
            $row = mysqli_fetch_assoc($imageUpdateQuery);

            if ($row['image'] != "" && file_exists('uploadedimg/' . $row['image'])) {
                unlink('uploadedimg/' . $row['image']); // Delete old image
            }

            move_uploaded_file($updateImageTmpName, $updateImageFolder);
            mysqli_query($conn, "UPDATE `user_form` SET image = '$updateImage' WHERE id = '$user_id'") or die(mysqli_error($conn));
            $message[] = 'Image uploaded successfully';
        }
    }
}

// Fetch User Data
$select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die(mysqli_error($conn));
if (mysqli_num_rows($select) > 0) {
    $row = mysqli_fetch_assoc($select);
} else {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="update-profile">
        <form action="" method="post" enctype="multipart/form-data">
            <?php if (!empty($row['image'])): ?>
                <img src="uploadedimg/<?= $row['image'] ?>" alt="Profile pic" class="profile-pic">
            <?php else: ?>
                <img src="images/default.png" alt="Profile pic" class="profile-pic">
            <?php endif; ?>

            <?php
            if (isset($message)) {
                foreach ($message as $msg) {
                    echo '<p class="message">' . $msg . '</p>';
                }
            }
            ?>

            <div class="flex">
                <div class="inputBox">
                    <span>Username :</span>
                    <input type="text" name="updateName" value="<?= $row['name'] ?>" class="box" required>
                    <span>Your email :</span>
                    <input type="email" name="updateEmail" value="<?= $row['email'] ?>" class="box" required>
                    <span>Update your pic :</span>
                    <input type="file" name="updateImage" accept="image/jpg, image/jpeg, image/png" class="box">
                </div>
                <div class="inputBox">
                    <input type="hidden" name="oldPass" value="<?= $row['password'] ?>">
                    <span>Old password :</span>
                    <input type="password" name="updatePass" placeholder="Enter previous password" class="box">
                    <span>New password :</span>
                    <input type="password" name="newPass" placeholder="Enter new password" class="box">
                    <span>Confirm password :</span>
                    <input type="password" name="confirmPass" placeholder="Confirm new password" class="box">
                </div>
            </div>
            <input type="submit" value="Update Profile" name="updateProfile" class="btn">
            <a href="profile.php" class="delete-btn">Go back</a>
        </form>
    </div>

</body>
</html>
