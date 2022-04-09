<?php

include 'config.php';

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

session_start();

error_reporting(0);

if (isset($_SESSION["user_id"])) {
  header("Location: welcome.php");
}

if (isset($_POST["resetPassword"])) {
  $email = mysqli_real_escape_string($conn, $_POST["email"]);

  $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

  if (mysqli_num_rows($check_email) > 0) {
    $data = mysqli_fetch_assoc($check_email);

    $to = $email;
    $subject = "Reset Password - Pure Coding YouTube";

    $message = "
    <html>
    <head>
    <title>{$subject}</title>
    </head>
    <body>
    <p><strong>Dear {$data['full_name']},</strong></p>
    <p>Forgot Password? Not a problem. Click below link to reset your password.</p>
    <p><a href='{$base_url}reset-password.php?token={$data['token']}'>Reset Password</a></p>
    </body>
    </html>
    ";

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
      //Server settings
      $mail->SMTPDebug = 0;                      //Enable verbose debug output
      $mail->isSMTP();                                            //Send using SMTP
      $mail->Host       = $smtp['host'];                     //Set the SMTP server to send through
      $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
      $mail->Username   = $smtp['user'];                     //SMTP username
      $mail->Password   = $smtp['pass'];                               //SMTP password
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
      $mail->Port       = $smtp['port'];                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

      //Recipients
      $mail->setFrom($my_email);
      $mail->addAddress($email, $data['full_name']);     //Add a recipient

      //Content
      $mail->isHTML(true);                                  //Set email format to HTML
      $mail->Subject = $subject;
      $mail->Body    = $message;

      $mail->send();
      echo "<script>alert('We have sent a reset password link to your email - {$email}.');</script>";
    } catch (Exception $e) {
      echo "<script>alert('Mail not sent. Please try again.');</script>";
    }
  } else {
    echo "<script>alert('Email not found.');</script>";
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css" />
  <title>Sign in & Sign up Form - Pure Coding</title>
</head>

<body>
  <div class="container">
    <div class="forms-container">
      <div class="signin-signup">
        <form action="" method="post" class="sign-in-form">
          <h2 class="title">Reset Password</h2>
          <div class="input-field">
            <i class="fas fa-user"></i>
            <input type="text" placeholder="Email Address" name="email" value="<?php echo $_POST['email']; ?>" required />
          </div>
          <input type="submit" value="Send Verification Link" name="resetPassword" class="btn solid" />
        </form>
      </div>
    </div>

    <div class="panels-container">
      <div class="panel left-panel">
        <div class="content">
          <h3>Forgot Password ?</h3>
          <p>
            Lorem ipsum, dolor sit amet consectetur adipisicing elit. Debitis,
            ex ratione. Aliquid!
          </p>
        </div>
        <img src="img/log.svg" class="image" alt="" />
      </div>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
  <script src="app.js"></script>
</body>

</html>