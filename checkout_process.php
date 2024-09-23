<?php
session_start();
include "db.php";

if (isset($_SESSION["uid"])) {
    // Retrieve POST data safely with default values
    $f_name = $_POST["firstname"] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $state = $_POST['state'] ?? '';
    $zip = $_POST['zip'] ?? 123; // Default value
    $expdate = $_POST['expdate'] ?? '';
    $cvv = $_POST['cvv'] ?? 1689; // Default value
    $phonenumber = $_POST['phoneNumber'] ?? '';
    $cardnumber = $_POST['cardNumber'] ?? '';
    $user_id = $_SESSION["uid"];
    $cardnumberstr = (string)$cardnumber;
    $total_count = $_POST['total_count'] ?? 0; // Default to 0
    $prod_total = $_POST['total_price'] ?? 0; // Default to 0

    // Get the next order ID safely
    $sql0 = "SELECT COALESCE(MAX(order_id), 0) AS max_val FROM `orders_info`";
    $runquery = mysqli_query($con, $sql0);

    if ($runquery) {
        $row = mysqli_fetch_array($runquery);
        $order_id = $row["max_val"] + 1; // Increment max order_id by 1
    } else {
        echo mysqli_error($con);
        exit();
    }

    // Prepare statement for inserting order information
    $stmt = $con->prepare("INSERT INTO `orders_info` 
        (`order_id`, `user_id`, `f_name`, `email`, `address`, 
        `city`, `state`, `zip`, `phonenumber`, `cardnumber`, 
        `expdate`, `prod_count`, `total_amt`, `cvv`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Bind parameters
    $stmt->bind_param(
        'iissssssssssis',
        $order_id,
        $user_id,
        $f_name,
        $email,
        $address,
        $city,
        $state,
        $zip,
        $phonenumber,
        $cardnumberstr,
        $expdate,
        $total_count,
        $prod_total,
        $cvv
    );

    // Execute the insertion
    if ($stmt->execute()) {
        // Prepare statements for order_products and orders
        $stmt1 = $con->prepare("INSERT INTO `order_products` (`order_pro_id`, `order_id`, `product_id`, `qty`, `amt`) VALUES (NULL, ?, ?, ?, ?)");
        $stmt2 = $con->prepare("INSERT INTO `orders` (`order_id`, `user_id`, `product_id`, `qty`, `trx_id`, `p_status`) VALUES (?, ?, ?, ?, ?, ?)");

        // Bind parameters for order_products
        $stmt1->bind_param('iiss', $order_id, $prod_id, $prod_qty, $sub_total);

        // Bind parameters for orders
        $stmt2->bind_param('iissss', $order_id, $user_id, $prod_id, $prod_qty, $trx_id, $p_status);

        // Loop through each product and insert into order_products
        for ($i = 1; $i <= $total_count; $i++) {
            $prod_id = $_POST['prod_id_' . $i] ?? 0; // Default to 0
            $prod_price = $_POST['prod_price_' . $i] ?? 0; // Default to 0
            $prod_qty = $_POST['prod_qty_' . $i] ?? 0; // Default to 0
            $sub_total = (int)$prod_price * (int)$prod_qty;

            // Execute order_products insertion
            if (!$stmt1->execute()) {
                echo "Error inserting into order_products: " . $stmt1->error;
                exit();
            }

            // Prepare transaction ID and status
            $trx_id = $cardnumberstr; // Generate or securely retrieve this in production
            $p_status = 'Pending';

            // Execute orders insertion
            if (!$stmt2->execute()) {
                echo "Error inserting into orders: " . $stmt2->error;
                exit();
            }
        }

        // Close statements
        $stmt1->close();
        $stmt2->close();

        // Delete items from cart
        $del_stmt = $con->prepare("DELETE FROM cart WHERE user_id = ?");
        $del_stmt->bind_param('i', $user_id);

        if ($del_stmt->execute()) {
            echo "<script>window.location.href='store.php'</script>";
        } else {
            echo "Error deleting from cart: " . $del_stmt->error;
        }

        // Close delete statement
        $del_stmt->close();
    } else {
        echo "Error inserting into orders: " . $stmt->error;
    }

    // Close the main statement
    $stmt->close();
} else {
    echo "<script>window.location.href='index.php'</script>";
}
