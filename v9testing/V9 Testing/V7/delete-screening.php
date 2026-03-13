<?php
include "config.php";

if (isset($_GET['id'])) {
    $screening_id = $_GET['id'];
    
    // Delete the screening record
    $query = "DELETE FROM screenings WHERE id = '$screening_id'";
    mysqli_query($conn, $query);
    
    echo json_encode(["status" => "deleted"]);
}
?>