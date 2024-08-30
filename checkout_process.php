<?php
session_start();
include "db.php";

if (isset($_SESSION["uid"])) {

    $f_name = $_POST["firstname"];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'] || 1689;
    $phonenumber = $_POST['phoneNumber'];
    $cardnumber = $_POST['cardNumber'];
    $expdate = $_POST['expdate'];
    $cvv = $_POST['cvv'];
    $user_id = $_SESSION["uid"];
    $cardnumberstr = (string)$cardnumber;
    $total_count = $_POST['total_count'];
    $prod_total = $_POST['total_price'];

    // Get the next order ID
    $sql0 = "SELECT MAX(order_id) AS max_val FROM `orders_info`";
    $runquery = mysqli_query($con, $sql0);

    if ($runquery) {
        $row = mysqli_fetch_array($runquery);
        $order_id = $row ? $row["max_val"] + 1 : 1;
    } else {
        echo mysqli_error($con);
        exit();
    }

    // Insert order information
    $sql = "INSERT INTO `orders_info` 
        (`order_id`,`user_id`,`f_name`, `email`,`address`, 
        `city`, `state`, `zip`, `phonenumber`,`cardnumber`,`expdate`,`prod_count`,`total_amt`,`cvv`) 
        VALUES ($order_id, '$user_id', '$f_name', '$email', 
        '$address', '$city', '$state', '$zip', '$phonenumber', '$cardnumberstr', '$expdate', '$total_count', '$prod_total', '$cvv')";

    if (mysqli_query($con, $sql)) {
        // Loop through each product and insert into order_products
        for ($i = 1; $i <= $total_count; $i++) {
            $prod_id = $_POST['prod_id_' . $i];
            $prod_price = $_POST['prod_price_' . $i];
            $prod_qty = $_POST['prod_qty_' . $i];
            $sub_total = (int)$prod_price * (int)$prod_qty;

            $sql1 = "INSERT INTO `order_products` 
            (`order_pro_id`, `order_id`, `product_id`, `qty`, `amt`) 
            VALUES (NULL, '$order_id', '$prod_id', '$prod_qty', '$sub_total')";

            if (!mysqli_query($con, $sql1)) {
                echo mysqli_error($con);
                exit();
            }
        }

        // Delete items from cart
        $del_sql = "DELETE FROM cart WHERE user_id = $user_id";
        if (mysqli_query($con, $del_sql)) {
            echo "<script>window.location.href='store.php'</script>";
        } else {
            echo mysqli_error($con);
        }
    } else {
        echo mysqli_error($con);
    }
} else {
    echo "<script>window.location.href='index.php'</script>";
}
