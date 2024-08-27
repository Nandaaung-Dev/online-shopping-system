<?php
session_start(); // Start the session at the beginning

// Database connection
include 'db.php'; // Assume you have this file to handle database connections

$message = null; // Variable to hold the message

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['otp'])) {
    $entered_otp = $_POST['otp'];

    // Fetch the OTP stored in the database
    $result = mysqli_query($con, "SELECT otp FROM user_otp WHERE user_id = '$_SESSION[uid]'");
    $row = mysqli_fetch_assoc($result);
    $stored_otp = $row['otp'];

    if ($entered_otp == $stored_otp) {
        // OTP is correct, user is registered
        $message = "OTP verified successfully. You are registered!";
        $_SESSION["stored_otp"] = $stored_otp;
        echo "<script> location.href='store.php'; </script>";
        exit;
    } else {
        // OTP is incorrect
        $message = "Invalid OTP. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        /* Optional: Additional styling for the OTP inputs */
        input.otp-input {
            text-align: center;
        }
    </style>
</head>

<body class="bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg p-8 max-w-xs md:max-w-md w-full">
        <h2 class="text-xl md:text-2xl font-bold text-center text-gray-800 mb-6">Verify Your OTP</h2>
        <p class="text-center text-gray-600 mb-4">Enter the 6-digit OTP sent to your email.</p>

        <?php if ($message): ?>
            <div class="text-red-500 text-center mb-4"><?php echo $message; ?></div>
        <?php endif; ?>

        <form id="otpForm" method="POST" action="">
            <div class="flex space-x-2 justify-center mb-2">
                <input type="text" id="otp1" maxlength="1" class="otp-input w-10 h-10 md:w-12 md:h-12 border border-gray-300 rounded-lg text-center text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500" />
                <input type="text" id="otp2" maxlength="1" class="otp-input w-10 h-10 md:w-12 md:h-12 border border-gray-300 rounded-lg text-center text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500" />
                <input type="text" id="otp3" maxlength="1" class="otp-input w-10 h-10 md:w-12 md:h-12 border border-gray-300 rounded-lg text-center text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500" />
                <input type="text" id="otp4" maxlength="1" class="otp-input w-10 h-10 md:w-12 md:h-12 border border-gray-300 rounded-lg text-center text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500" />
                <input type="text" id="otp5" maxlength="1" class="otp-input w-10 h-10 md:w-12 md:h-12 border border-gray-300 rounded-lg text-center text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500" />
                <input type="text" id="otp6" maxlength="1" class="otp-input w-10 h-10 md:w-12 md:h-12 border border-gray-300 rounded-lg text-center text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500" />
            </div>
            <input type="hidden" name="otp" id="otpValue" value="">
            <div id="timer" class="text-red-500 text-sm text-center mb-2"></div>
            <button type="submit" id="verify_otp" class="w-full bg-purple-500 text-white py-2 md:py-3 rounded-lg font-semibold hover:bg-purple-600 transition duration-200">Verify OTP</button>
        </form>
        <p class="text-center text-gray-600 mt-6">
            Didn’t receive the code? <a id="resendLink" href="index.php" class="text-purple-500 hover:underline">Resend OTP</a>

        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.otp-input');
            const otpValueField = document.getElementById('otpValue');
            const form = document.getElementById('otpForm');
            const resendLink = document.getElementById('resendLink');
            const timerDisplay = document.getElementById('timer');
            const verifyOtp = document.getElementById('verify_otp');

            // Initialize countdown
            let countdown = 2; // 60 seconds
            const timerInterval = setInterval(() => {
                countdown--;
                timerDisplay.textContent = ` ${countdown} secs`;

                if (countdown <= 0) {
                    clearInterval(timerInterval);
                    resendLink.style.pointerEvents = 'auto'; // Enable the resend link
                    resendLink.style.color = '#7c3aed'; // Change color to make it look enabled
                    timerDisplay.textContent = ''; // Clear the timer text
                    // verifyOtp.style.pointerEvents = 'none';
                    verifyOtp.disabled = true; 
                    verifyOtp.style.cursor = 'not-allowed'
                }
            }, 1000);

            resendLink.style.pointerEvents = 'none'; // Disable the resend link initially
            resendLink.style.color = '#a3a3a3'; // Make the resend link look disabled

            inputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const value = e.target.value;

                    // Move to the next input if filled
                    if (value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }

                    // Move to the previous input if empty
                    if (value.length === 0 && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                // Concatenate all OTP inputs values
                const otpValue = Array.from(inputs).map(input => input.value).join('');
                otpValueField.value = otpValue;

                // Submit the form
                form.submit();
            });
        });
    </script>
</body>

</html>