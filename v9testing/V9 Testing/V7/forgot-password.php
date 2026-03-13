<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['new_password'];

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET password='$hashed' WHERE email='$email'";

    if(mysqli_query($conn,$sql)){
        header("Location: login.html?reset=success");
exit();
    }else{
        echo "Error updating password";
    }
}
?>