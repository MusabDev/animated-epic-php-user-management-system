<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
}

include 'config.php';

if (isset($_POST["submit"])) {
    $full_name = mysqli_real_escape_string($conn, $_POST["full_name"]);
    $password = mysqli_real_escape_string($conn, md5($_POST["password"]));
    $cpassword = mysqli_real_escape_string($conn, md5($_POST["cpassword"]));

    if ($password === $cpassword) {
        $photo_name = mysqli_real_escape_string($conn, $_FILES["photo"]["name"]);
        $photo_tmp_name = $_FILES["photo"]["tmp_name"];
        $photo_size = $_FILES["photo"]["size"];
        $photo_new_name = rand() . $photo_name;

        if ($photo_size > 5242880) {
            echo "<script>alert('Photo is very big. Maximum photo uploading size is 5MB.');</script>";
        } else {
            $sql = "UPDATE users SET full_name='$full_name', password='$password', photo='$photo_new_name' WHERE id='{$_SESSION["user_id"]}'";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                echo "<script>alert('Profile Updated successfully.');</script>";
                move_uploaded_file($photo_tmp_name, "uploads/" . $photo_new_name);
            } else {
                echo "<script>alert('Profile can not Updated.');</script>";
                echo  $conn->error;
            }
        }
    } else {
        echo "<script>alert('Password not matched. Please try again.');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css">
    <title>Profile Page - Pure Coding</title>
</head>

<body class="profile-page">
    <div class="wrapper">
        <p>Wanna logout?
            <a href="logout.php">Click Here</a>
        </p>
        <h2>Profile</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <?php

            $sql = "SELECT * FROM users WHERE id='{$_SESSION["user_id"]}'";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
            ?>
                    <div class="inputBox">
                        <input type="text" id="full_name" name="full_name" placeholder="Full Name" value="<?php echo $row['full_name']; ?>" required>
                    </div>
                    <div class="inputBox">
                        <input type="email" id="email" name="email" placeholder="Email Address" value="<?php echo $row['email']; ?>" disabled required>
                    </div>
                    <div class="inputBox">
                        <input type="password" id="password" name="password" placeholder="Password" value="<?php echo $row['password']; ?>" required>
                    </div>
                    <div class="inputBox">
                        <input type="password" id="cpassword" name="cpassword" placeholder="Confirm Password" value="<?php echo $row['password']; ?>" required>
                    </div>
                    <div class="inputBox">
                        <label for="photo">Photo</label>
                        <input type="file" accept="image/*" id="photo" name="photo" required>
                    </div>
                    <img src="uploads/<?php echo $row["photo"]; ?>" width="150px" height="auto" alt="">
            <?php
                }
            }

            ?>
            <div>
                <button type="submit" name="submit" class="btn">Update Profile</button>
            </div>
        </form>
    </div>
</body>

</html>