<?php

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

session_start();
include "db.php";

if (isset($_POST["f_name"])) {

	$f_name = $_POST["f_name"];
	$l_name = $_POST["l_name"];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$repassword = $_POST['repassword'];
	$mobile = $_POST['mobile'];
	$address1 = $_POST['address1'];
	$address2 = $_POST['address2'];

	$name = "/^[a-zA-Z ]+$/";
	$emailValidation = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9]+(\.[a-z]{2,4})$/";
	$number = "/^[0-9]+$/";

	if (
		empty($f_name) || empty($l_name) || empty($email) || empty($password) || empty($repassword) ||
		empty($mobile) || empty($address1) || empty($address2)
	) {
		echo "<div class='alert alert-warning'>
                <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                <b>Please fill all fields!</b>
              </div>";
		exit();
	} else {
		if (!preg_match($name, $f_name)) {
			echo "<div class='alert alert-warning'>
                    <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                    <b>The first name '$f_name' is not valid!</b>
                  </div>";
			exit();
		}
		if (!preg_match($name, $l_name)) {
			echo "<div class='alert alert-warning'>
                    <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                    <b>The last name '$l_name' is not valid!</b>
                  </div>";
			exit();
		}
		if (!preg_match($emailValidation, $email)) {
			echo "<div class='alert alert-warning'>
                    <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                    <b>The email address '$email' is not valid!</b>
                  </div>";
			exit();
		}
		if (strlen($password) < 9) {
			echo "<div class='alert alert-warning'>
                    <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                    <b>Password is weak</b>
                  </div>";
			exit();
		}
		if ($password != $repassword) {
			echo "<div class='alert alert-warning'>
                    <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                    <b>Passwords do not match</b>
                  </div>";
			exit();
		}
		if (!preg_match($number, $mobile)) {
			echo "<div class='alert alert-warning'>
                    <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                    <b>Mobile number '$mobile' is not valid</b>
                  </div>";
			exit();
		}
		if (strlen($mobile) != 10) {
			echo "<div class='alert alert-warning'>
                    <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                    <b>Mobile number must be 10 digits</b>
                  </div>";
			exit();
		}

		// Check if email already exists
		$sql = "SELECT user_id FROM user_info WHERE email = '$email' LIMIT 1";
		$check_query = mysqli_query($con, $sql);
		$count_email = mysqli_num_rows($check_query);
		if ($count_email > 0) {
			echo "<div class='alert alert-danger'>
                    <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                    <b>Email address is already registered. Try another email address</b>
                  </div>";
			exit();
		} else {

			// Hash the password before storing
			$hashed_password = password_hash($password, PASSWORD_DEFAULT);

			// Insert user information into the database
			$sql = "INSERT INTO `user_info` 
                    (`user_id`, `first_name`, `last_name`, `email`, `password`, `mobile`, `address1`, `address2`) 
                    VALUES (NULL, '$f_name', '$l_name', '$email', '$hashed_password', '$mobile', '$address1', '$address2')";
			$run_query = mysqli_query($con, $sql);

			if ($run_query) {
				$_SESSION["uid"] = mysqli_insert_id($con);
				$_SESSION["name"] = $f_name;
				$ip_add = getenv("REMOTE_ADDR");

				// Update cart with the new user ID
				$sql = "UPDATE cart SET user_id = '$_SESSION[uid]' WHERE ip_add='$ip_add' AND user_id = -1";
				if (mysqli_query($con, $sql)) {

					// Generate OTP
					$otp = rand(100000, 999999);

					// Store OTP in the database for this user
					$otp_sql = "INSERT INTO user_otp (user_id, otp) VALUES ('$_SESSION[uid]', '$otp')";
					mysqli_query($con, $otp_sql);

					// Send OTP via email
					$mail = new PHPMailer(true);

					try {
						// Server settings
						$mail->isSMTP();
						$mail->Host = 'sandbox.smtp.mailtrap.io';
						$mail->SMTPAuth = true;
						$mail->Port = 2525;
						$mail->Username = '9578f11c4b2268';
						$mail->Password = 'a15fefc5be3d75';

						// Recipients
						$mail->setFrom('onelinshop@gmail.com', 'Onelin Shop');
						$mail->addAddress($email, "$f_name $l_name"); // Use the user’s email address

						// Content
						$mail->isHTML(true); // Set email format to HTML
						$mail->Subject = 'Your OTP Code';
						$mail->Body    = "Hello $f_name $l_name,<br><br>Your OTP code is <b>$otp</b>.<br><br>Thank you.";

						$mail->send();
						echo 'OTP has been sent to your email.';
					} catch (Exception $e) {
						echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
					}

					echo "<script> location.href='verify_otp.php'; </script>";
					exit;
				}
			}
		}
	}
}
