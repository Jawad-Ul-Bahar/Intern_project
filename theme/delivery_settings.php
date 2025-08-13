<?php
include("connection.php");

if (isset($_POST['update'])) {
    $charges = $_POST['charges'];
    mysqli_query($con, "UPDATE delivery_settings SET charges='$charges' WHERE id=1");
    echo "<script>alert('Delivery charges updated successfully');</script>";
}

// Fetch current charges
$result = mysqli_query($con, "SELECT charges FROM delivery_settings WHERE id=1");
$row = mysqli_fetch_assoc($result);
$current_charges = $row['charges'];
?>

<form method="POST">
    <label>Delivery Charges:</label>
    <input type="number" name="charges" value="<?php echo $current_charges; ?>" step="0.01" required>
    <button type="submit" name="update">Update</button>
</form>
