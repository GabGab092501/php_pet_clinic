<?php
session_start();
//error_reporting(0);
include("./includes/config.php");
if (!isset($_SESSION['Employee_id'])) {
  require('./includes/login_functions.inc.php');
  redirect_user(); //pagnarollback
}
//print_r($_SESSION);
//try
else {
  mysqli_query($conn, 'START TRANSACTION');

  $querry = 'INSERT INTO transaction(Employee_id,Pet_id,Schedule) VALUES (?,?,NOW())';
  $Employee_id =  $_SESSION['Employee_id'];
  $flag = true;

  $stmt1 = mysqli_prepare($conn, $querry);
  mysqli_stmt_bind_param($stmt1, 'ii', $Employee_id,$Pet_id);

  foreach ($_SESSION["carts"] as $cart_itm) {
     $Name = $cart_itm["Pet_name"];
     $Pet_id = $cart_itm["Pet_id"];

     mysqli_stmt_execute($stmt1);

  $Transaction_id = mysqli_insert_id($conn);
  $querry2 = 'INSERT INTO transaction_line(Transaction_id ,Service_id)VALUES (?, ?)';
  $stmt2 = mysqli_prepare($conn, $querry2);
  mysqli_stmt_bind_param($stmt2, 'ii', $Transaction_id, $Service_id);

  foreach ($_SESSION["cart"] as $cart_itm) {
    $Service_name = $cart_itm["Name"];
    $Cost = $cart_itm["Price"];
    $Service_id = $cart_itm["Service_id"];

    mysqli_stmt_execute($stmt2);

    if ((mysqli_stmt_affected_rows($stmt2) > 0)) {

      if ($flag == true) {
        mysqli_commit($conn);
        //echo "success";
        header('Location: index.php');
      } else {
        mysqli_rollback($conn);
        echo "fail";
      }
    }
  }
}
        unset($_SESSION['carts']);
        unset($_SESSION['cart']);
}
