<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE id='$user_id'");
$user = $result->fetch_assoc();
?>


<form action="edit-profile.php" method="POST">

<div class="form-group">
<label>First Name</label>
<input type="text" name="first_name" value="<?php echo $user['first_name']; ?>" required>
</div>

<div class="form-group">
<label>Last Name</label>
<input type="text" name="last_name" value="<?php echo $user['last_name']; ?>" required>
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email" value="<?php echo $user['email']; ?>" required>
</div>

<div class="form-group">
<label>Phone</label>
<input type="text" name="phone_number" value="<?php echo $user['phone_number']; ?>">
</div>

<div class="form-group">
<label>Birthday</label>
<input type="date" name="birthday" value="<?php echo $user['birthday']; ?>">
</div>

<div class="form-group">
<label>Gender</label>
<select name="gender">
<option value="Male" <?php if($user['gender']=='Male') echo 'selected'; ?>>Male</option>
<option value="Female" <?php if($user['gender']=='Female') echo 'selected'; ?>>Female</option>
<option value="Other" <?php if($user['gender']=='Other') echo 'selected'; ?>>Other</option>
</select>
</div>

<div class="form-group">
<label>Blood Type</label>
<input type="text" name="blood_type" value="<?php echo $user['blood_type']; ?>">
</div>

<div class="form-group">
<label>Weight</label>
<input type="number" name="weight" value="<?php echo $user['weight']; ?>">
</div>

<div class="form-group">
<label>Street Address</label>
<input type="text" name="street_address" value="<?php echo $user['street_address']; ?>">
</div>

<div class="form-group">
<label>City</label>
<input type="text" name="city" value="<?php echo $user['city']; ?>">
</div>

<div class="form-group">
<label>Emergency Contact Name</label>
<input type="text" name="contact_name" value="<?php echo $user['contact_name']; ?>">
</div>

<div class="form-group">
<label>Emergency Contact Phone</label>
<input type="text" name="contact_phone" value="<?php echo $user['contact_phone']; ?>">
</div>

<button type="submit" class="btn btn-red">Update Profile</button>

</form>

</form>