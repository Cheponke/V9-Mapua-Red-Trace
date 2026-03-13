<?php
include "config.php";
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != "staff"){
    header("Location: login.html");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | Mapúa RedTrace</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="help-page">
    <header id="main-header">
        <nav class="navbar">
            <div class="logo">
                <img src="mapua-logo.png" alt="Mapúa Logo"> 
                <span>Mapúa RedTrace</span>
            </div>

            <div class="nav-actions">

                <span style="font-weight:600;">
                Welcome, <?php echo $_SESSION['name']; ?>
                </span>

            </div>
        </nav>
    </header>

    <main class="dashboard-container">
        <section class="donor-profile-header">
            <div class="profile-info-main">
                <div class="icon-box-pink" style="width: 60px; height: 60px; font-size: 1.5rem; background: var(--primary-red); color: white;">
                    <i class="fa-solid fa-hospital-user"></i>
                </div>
                <div class="profile-meta">
                    <h1>Staff Dashboard</h1>
                    <p>Hospital Staff Portal</p>
                </div>
            </div>
           <a href="logout.php" class="btn btn-white btn-signout" style="text-decoration:none;">
            <i class="fa-solid fa-right-from-bracket"></i> Sign Out
            </a>
        </section>

        <div class="dashboard-tabs">
            <button class="dash-tab active" id="tab-manage-donors-btn" onclick="toggleStaffView('donors')">
                <i class="fa-solid fa-user-group"></i> Donors
            </button>
            <button class="dash-tab" id="tab-manage-inventory-btn" onclick="toggleStaffView('inventory')">
                <i class="fa-solid fa-box-archive"></i> Inventory
            </button>
			<button class="dash-tab" id="tab-manage-notifications-btn" onclick="toggleStaffView('notifications')">
                <i class="fa-solid fa-bell"></i> Notifications
            </button>
        </div>

        <section id="donor-management-section" class="dash-content-card">
            <div class="card-header-flex">
                <h2>Donor Management</h2>
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search donors..." id="donorSearch">
                    <select class="filter-select" id="donorStatusFilter">
                        <option>All Status</option>
                        <option>Active</option>
                        <option>Pending</option>
						<option>Inactive</option>
                    </select>
                </div>
            </div>
			
			<?php
			// total donors
			$total_query = "SELECT COUNT(*) as total FROM users WHERE role='donor'";
			$total_result = mysqli_query($conn,$total_query);
			$total_donors = mysqli_fetch_assoc($total_result)['total'];

			// pending donors
			$pending_query = "SELECT COUNT(*) as total FROM users WHERE role='donor' AND status='pending'";
			$pending_result = mysqli_query($conn,$pending_query);
			$pending_donors = mysqli_fetch_assoc($pending_result)['total'];

			// active donors
			$active_query = "SELECT COUNT(*) as total FROM users WHERE role='donor' AND status='active'";
			$active_result = mysqli_query($conn,$active_query);
			$active_donors = mysqli_fetch_assoc($active_result)['total'];

			// inactive donors
			$inactive_query = "SELECT COUNT(*) as total FROM users WHERE role='donor' AND status='inactive'";
			$inactive_result = mysqli_query($conn,$inactive_query);
			$inactive_donors = mysqli_fetch_assoc($inactive_result)['total'];
			?>

			<div class="notif-stats-grid">
				<div class="notif-stat-card blue-bg">
					<div class="icon-box blue-light"><i class="fa-solid fa-users"></i></div>
					<div class="stat-info">
						<span class="count"><?php echo $total_donors; ?></span>
						<span class="label">Total Donors</span>
					</div>
				</div>
				<div class="notif-stat-card orange-bg">
					<div class="icon-box orange-light"><i class="fa-solid fa-clock"></i></div>
					<div class="stat-info">
						<span class="count"><?php echo $pending_donors; ?></span>
						<span class="label">Pending Approval</span>
					</div>
				</div>
				<div class="notif-stat-card green-bg">
					<div class="icon-box green-light"><i class="fa-solid fa-check-circle"></i></div>
					<div class="stat-info">
						<span class="count"><?php echo $active_donors; ?></span>
						<span class="label">Active Donors</span>
					</div>
				</div>
				<div class="notif-stat-card gray-bg">
					<div class="icon-box gray-light"><i class="fa-solid fa-xmark"></i></div>
					<div class="stat-info">
						<span class="count"><?php echo $inactive_donors; ?></span>
						<span class="label">Inactive Donors</span>
					</div>
				</div>
			</div>
            
            <table class="history-table staff-table">
                <thead>
                    <tr>
                        <th>Donor ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Blood Type</th>
                        <th>Status</th>
                        <th>Donations</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

            <?php
			include "config.php";

			$sql = "SELECT * FROM users WHERE role='donor'";
			$result = mysqli_query($conn, $sql);

			while ($row = mysqli_fetch_assoc($result)) {
				$id = $row['id'];
				$name = $row['first_name'] . " " . $row['last_name'];
				$email = $row['email'];
				$blood = $row['blood_type'];
				$status = $row['status'];

				// 1. Get total completed donations
				$count_query = "SELECT COUNT(*) as total FROM inventory i 
								JOIN screenings s ON i.DonationID = s.id 
								WHERE s.donor_id = '$id'";
				$count_result = mysqli_query($conn, $count_query);
				$donation_count = mysqli_fetch_assoc($count_result)['total'];

				// 2. REFINED: Look for a screening that has vitals filled but isn't in inventory
				// We check if weight is NOT NULL (assuming weight is a required field in your modal)
				$unclaimed_screening_query = "SELECT s.id FROM screenings s 
											  LEFT JOIN inventory i ON s.id = i.DonationID 
											  WHERE s.donor_id = '$id' 
											  AND i.InventoryID IS NULL 
											  AND s.weight IS NOT NULL 
											  LIMIT 1";
				$unclaimed_result = mysqli_query($conn, $unclaimed_screening_query);
				$has_vitals_ready = mysqli_num_rows($unclaimed_result) > 0;

				// 3. Vitals Button Logic: Only enable if status is 'pending'
				$vitalsDisabled = ($status !== 'pending') ? "disabled" : "";
				$vitalsStyle = ($status !== 'pending') ? "opacity: 0.5; cursor: not-allowed; filter: grayscale(1);" : "cursor: pointer;";

				// 4. Check Button Logic: Requires 'pending' status AND encoded vitals
				$isEligibleToComplete = ($status === 'pending' && $has_vitals_ready);
				$checkDisabled = (!$isEligibleToComplete) ? "disabled" : "";
				$checkStyle = (!$isEligibleToComplete) ? "opacity: 0.5; cursor: not-allowed; filter:grayscale(1);" : "cursor: pointer;";

				echo "
				<tr>
					<td>$id</td>
					<td>$name</td>
					<td>$email</td>
					<td><span class='blood-badge'>$blood</span></td>
					<td>
						<form action='update_donor.php' method='POST'>
							<input type='hidden' name='id' value='$id'>
							<select name='status' onchange='this.form.submit()'>
								<option value='pending' " . ($status == 'pending' ? 'selected' : '') . ">Pending</option>
								<option value='active' " . ($status == 'active' ? 'selected' : '') . ">Active</option>
								<option value='inactive' " . ($status == 'inactive' ? 'selected' : '') . ">Inactive</option>
							</select>
						</form>
					</td>
					<td>$donation_count</td>
					<td>
						<div>
							<button class='icon-btn edit-btn' onclick='openVitalsModal(\"$id\",\"$name\")' style='$vitalsStyle' $vitalsDisabled title='" . ($status !== 'pending' ? "Status must be pending" : "Record Vitals") . "'>
								<i class='fa-solid fa-pen'></i>
							</button>
								
							<button class='icon-btn' onclick='openConfirmModal(\"$id\", \"$name\")' style='$checkStyle' $checkDisabled title='" . (!$isEligibleToComplete ? "Pending status and vitals required" : "Complete Donation") . "'>
								<i class='fa-solid fa-check'></i>
							</button>
						</div>
					</td>
				</tr>";
			}
			?>

            </tbody>
            </table>
           
        </section>

        <section id="inventory-management-section" class="dash-content-card" style="display: none;">
            <div class="card-header-flex">
                <h2>Blood Inventory Management</h2>
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search inventory..." id="inventorySearch">
                </div>
            </div>

            <?php
			include "config.php";

			// Fetch counts based on VARCHAR status strings
			$total_res     = mysqli_query($conn, "SELECT COUNT(*) AS total FROM inventory");
			$avail_res     = mysqli_query($conn, "SELECT COUNT(*) AS total FROM inventory WHERE Inventory_Status = 'Available'");
			$reserved_res  = mysqli_query($conn, "SELECT COUNT(*) AS total FROM inventory WHERE Inventory_Status = 'Reserved'");

			// Counts as Expired if status is 'Expired' OR if the date has passed
			$expired_res   = mysqli_query($conn, "SELECT COUNT(*) AS total FROM inventory WHERE Inventory_Status = 'Expired' OR Inventory_ExpDate < CURDATE()");

			$total     = mysqli_fetch_assoc($total_res)['total'];
			$available = mysqli_fetch_assoc($avail_res)['total'];
			$reserved  = mysqli_fetch_assoc($reserved_res)['total'];
			$expired   = mysqli_fetch_assoc($expired_res)['total'];
			?>
			
			<div class="notif-stats-grid">
				<div class="notif-stat-card blue-bg">
					<div class="icon-box blue-light"><i class="fa-solid fa-users"></i></div>
					<div class="stat-info"><span class="count"><?php echo $total; ?></span><span class="label">Total Units</span></div>
				</div>
				<div class="notif-stat-card green-bg">
					<div class="icon-box green-light"><i class="fa-solid fa-check-circle"></i></div>
					<div class="stat-info"><span class="count"><?php echo $available; ?></span><span class="label">Available</span></div>
				</div>
				<div class="notif-stat-card orange-bg">
					<div class="icon-box orange-light"><i class="fa-solid fa-calendar-day"></i></div>
					<div class="stat-info"><span class="count"><?php echo $reserved; ?></span><span class="label">Reserved</span></div>
				</div>
				<div class="notif-stat-card gray-bg">
					<div class="icon-box gray-light"><i class="fa-solid fa-xmark"></i></div>
					<div class="stat-info"><span class="count"><?php echo $expired; ?></span><span class="label">Expired</span></div>
				</div>
			</div>

            <table class="history-table staff-table">
                <thead>
                    <tr>
                        <th>Inventory ID</th>
                        <th>Donor Name</th>
                        <th>Blood Type</th>
                        <th>Volume</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
					mysqli_query($conn,"
					UPDATE inventory
					SET Inventory_Status='Expired'
					WHERE Inventory_ExpDate < CURDATE()
					AND Inventory_Status!='Expired'
					");

					$sql = "SELECT 
					inventory.InventoryID,
					inventory.Inventory_BloodType,
					inventory.Inventory_Volume,
					inventory.Inventory_ExpDate,
					inventory.Inventory_Status,
					CONCAT(users.first_name, ' ', users.last_name) AS donor_name,
					users.phone_number
					FROM inventory
					JOIN screenings ON inventory.DonationID = screenings.id
					JOIN users ON screenings.donor_id = users.id";

					$result = mysqli_query($conn,$sql);

					if(mysqli_num_rows($result) > 0){

					while($row = mysqli_fetch_assoc($result)){
					$inventory_id = $row['InventoryID'];
					$name = $row['donor_name'];
					$blood = $row['Inventory_BloodType'];
					$volume = $row['Inventory_Volume'];
					$exp = $row['Inventory_ExpDate'];
					$status = $row['Inventory_Status'];
					$phone = $row['phone_number'] ?? '';

					if($status == "Available"){
						$statusClass = "success";
					}
					elseif($status == "Reserved"){
						$statusClass = "warning";
					}
					elseif($status == "Expired"){
						$statusClass = "expired";
					}
					else{
						$statusClass = "";
					}

					echo "
					<tr>
					<td>$inventory_id</td>
					<td>" . htmlspecialchars($name) . "</td>
					<td><span class='blood-badge'>$blood</span></td>
					<td>{$volume} ml</td>
					<td>$exp</td>
					<td><span class='status-pill $statusClass'>$status</span></td>
					<td>

					<button class='icon-btn' style='color: #3b82f6;' 
                    onclick='sendSMSNotification(\"$phone\", \"$name\", \"$blood\")'>
						<i class='fa-solid fa-bell'></i>
					</button>

					<button class='icon-btn' style='color:#ef4444;' onclick='deleteInventory($inventory_id)'>
						<i class='fa-solid fa-trash'></i>
					</button>

					</td>
					</tr>
					";
					}

					}else{

					echo "<tr><td colspan='7'>No inventory records found</td></tr>";

					}
					?>
                </tbody>
            </table>
        </section>
		
		<section id="notification-management-section" class="dash-content-card" style="display: none;">
            <div class="card-header-flex">
                <h2>Notification Management</h2>
                <div>
                    <button class="btn btn-notif" onclick="openNotifsModal()"><i class="fa-solid fa-plus"></i> Send New Notification</button>
                </div>
            </div>

            <?php
			include 'config.php';

			if(!isset($_SESSION['user_id']) || $_SESSION['role'] != "staff"){
				header("Location: login.html");
				exit();
			}

			// count urgent
			$urgent_count = $conn->query("SELECT COUNT(*) as total FROM notifications WHERE type='urgent'")
			->fetch_assoc()['total'];

			// count events
			$event_count = $conn->query("SELECT COUNT(*) as total FROM notifications WHERE type='event'")
			->fetch_assoc()['total'];

			// count reminders
			$reminder_count = $conn->query("SELECT COUNT(*) as total FROM notifications WHERE type='reminder'")
			->fetch_assoc()['total'];

			// total notifications
			$total_count = $conn->query("SELECT COUNT(*) as total FROM notifications")
			->fetch_assoc()['total'];

			?>

			<div class="notif-stats-grid">
				<div class="notif-stat-card red-bg">
					<div class="icon-box red-light"><i class="fa-solid fa-triangle-exclamation"></i></div>
					<div class="stat-info"><span class="count"><?php echo $urgent_count; ?></span><span class="label">Urgent Alerts</span></div>
				</div>
				<div class="notif-stat-card orange-bg">
					<div class="icon-box orange-light"><i class="fa-solid fa-calendar-day"></i></div>
					<div class="stat-info"><span class="count"><?php echo $event_count; ?></span><span class="label">Event Notices</span></div>
				</div>
				<div class="notif-stat-card green-bg">
					<div class="icon-box green-light"><i class="fa-solid fa-bell"></i></div>
					<div class="stat-info"><span class="count"><?php echo $reminder_count; ?></span><span class="label">Reminders</span></div>
				</div>
				<div class="notif-stat-card blue-bg">
					<div class="icon-box blue-light"><i class="fa-solid fa-paper-plane"></i></div>
					<div class="stat-info"><span class="count"><?php echo $total_count; ?></span><span class="label">Total Sent</span></div>
				</div>
			</div>

			<div class="notif-actions-bar">
            <div class="filter-pills">
                <button class="pill active" data-filter="all">All Notifications</button>
                <button class="pill" data-filter="urgent">Urgent</button>
                <button class="pill" data-filter="event">Events</button>
                <button class="pill" data-filter="reminder">Reminders</button>
                <button class="pill" data-filter="info">Info</button>
            </div>
        </div>

        <div class="notif-list" id="notif-list">

            <?php
            include 'config.php';

            $sql = "SELECT * FROM notifications ORDER BY created_at DESC";
            $result = $conn->query($sql);

            while($row = $result->fetch_assoc()){

            $type = $row['type'];

            $icon = "fa-circle-info";
            $color = "blue-light";

            if($type == "urgent"){
            $icon = "fa-triangle-exclamation";
            $color = "red-light";
            }

            if($type == "event"){
            $icon = "fa-calendar-day";
            $color = "orange-light";
            }

            if($type == "reminder"){
            $icon = "fa-bell";
            $color = "green-light";
            }

            ?>

            <div class="notif-card <?php echo $row['type']; ?>">

            <div class="icon-box <?php echo $color; ?>">
            <i class="fa-solid <?php echo $icon; ?>"></i>
            </div>

            <div class="card-content">

            <div class="card-title-row">
            <h3><?php echo $row['title']; ?></h3>

            <div class="badges">
            <span class="badge <?php echo $row['priority']; ?>">
            <?php echo strtoupper($row['priority']); ?>
            </span>
            <span class="badge status">Sent</span>
            </div>

            </div>

            <p><?php echo $row['message']; ?></p>

            <div class="card-meta">
            <span><i class="fa-regular fa-calendar"></i> <?php echo $row['created_at']; ?></span>
            <span><i class="fa-solid fa-users"></i> <?php echo $row['recipients']; ?></span>
            </div>

            </div>
            </div>

            <?php } ?>

            </div>
        </section>
    </main>

    <div id="vitalsModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <h2 style="margin:0;">Physical Screening</h2>
                    <small id="modalDonorInfo" style="color: #666; font-weight: bold;"></small>
                </div>
                <button class="close-btn" onclick="closeVitalsModal()">&times;</button>
            </div>
        
            <form id="vitalsForm" action="save_screening.php" method="POST">

                <input type="hidden" id="currentDonorId" name="donor_id">

                <div class="form-grid">

                <div class="input-group">
                <label>Blood Pressure (mmHg)</label>
                <input type="text" name="blood_pressure" placeholder="120/80" required>
                </div>

                <div class="input-group">
                <label>Pulse Rate (bpm)</label>
                <input type="number" name="pulse_rate" placeholder="72" required>
                </div>

                <div class="input-group">
                <label>Temperature (°C)</label>
                <input type="number" step="0.1" name="temperature" placeholder="36.5" required>
                </div>

                <div class="input-group">
                <label>Hemoglobin (g/dL)</label>
                <input type="number" step="0.1" name="hemoglobin_level" placeholder="13.5" required>
                </div>

                <div class="input-group full-width">
                <label>Weight (lbs)</label>
                <input type="number" name="weight" placeholder="165" required>
                </div>

                <div class="input-group full-width">
                <label>Donation Location</label>
                <input type="text" name="location" placeholder="Mapua Medical Center Room 201" required>
                </div>

                </div>

                <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeVitalsModal()">Cancel</button>
                <button type="submit" class="btn-save">Save Screening</button>
                </div>

                </form>
        </div>
	</div>
	
	<div id="confirmModal" class="modal-overlay">
		<div class="modal-card">
			<div class="modal-header">
				<div>
					<h2 style="margin:0;">Complete Donation</h2>
					<p id="confirmModalText"></p>
				</div>
			</div>
			
			<div style="padding: 20px;">
				<div class="form-group">
					<label style="font-weight: bold; display: block; margin-bottom: 8px;">Blood Volume Collected (mL) *</label>
					<input type="number" id="final_volume" class="form-control" 
						   placeholder="e.g. 450" 
						   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem;"
						   required>
					<small style="color: #888;">Standard donation is usually 450mL - 500mL.</small>
				</div>
			</div>

			<input type="hidden" id="confirm_donor_id">
			
			<div style="display: flex; gap: 10px; margin-top: 10px;">
				<button type="button" class="btn-cancel" onclick="closeConfirmModal()">Cancel</button>
				<button type="button" class="btn-save" onclick="executeDonation()">Complete</button>
			</div>
		</div>
	</div>
	
	<div id="smsConfirmModal" class="modal-overlay">
		<div class="modal-card">
			<div class="modal-header">
				<div>
				<h2 style="margin:0;">Send Notification</h2>
				<p id="smsConfirmText"></p>
				</div>
			</div>

			<div class="modal-footer">
				<button class="btn-cancel" onclick="closeSMSConfirmModal()">Cancel</button>
				<button class="btn-save" id="confirmSMSBtn">Send</button>
			</div>
		</div>
	</div>
		
	<div id="deleteModal" class="modal-overlay">
		<div class="modal-card">
			<div class="modal-header">
				<div>
                    <h2 style="margin:0;">Delete Inventory</h2>
                    <p>Are you sure you want to delete this inventory record?</p>
                </div>
			</div>

			<div class="modal-footer">
				<button class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
				<button class="btn-save" id="confirmDeleteBtn">Delete</button>
			</div>
		</div>
	</div>
	
	<div id="notifsModal" class="modal-overlay">
		<div class="modal-card">
			<div class="modal-header">
				<h2>Send New Notification</h2>
				<button class="close-btn" id="closeModal">&times;</button>
			</div>
			
			<form action="send_notification.php" method="POST">

				<div class="form-group">
					<label>Notification Type *</label>
					<select name="type" required>
						<option value="information">Information</option>
						<option value="urgent">Urgent Alert</option>
						<option value="event">Event Notice</option>
						<option value="reminder">Reminder</option>
					</select>
				</div>

				<div class="form-group">
					<label>Title *</label>
					<input type="text" name="title" placeholder="Enter notification title" required>
				</div>

				<div class="form-group">
					<label>Message *</label>
					<textarea name="message" placeholder="Enter notification message" rows="4" required></textarea>
				</div>

				<div class="form-group">
					<label>Priority *</label>
						<select name="priority" required>
						<option value="low">Low</option>
						<option value="medium" selected>Medium</option>
						<option value="high">High</option>
					</select>
				</div>

				<div class="form-group">
					<label>Recipients *</label>
					<select name="recipients" required>
						<option value="all">All Donors</option>
						<option value="active">Active Donors Only</option>
						<option value="o-neg">O- Blood Type</option>
						<option value="a-neg">A- Blood Type</option>
						<option value="b-neg">B- Blood Type</option>
						<option value="ab-neg">AB- Blood Type</option>
						<option value="scheduled">Scheduled Donors</option>
					</select>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-red">
						<i class="fa-solid fa-paper-plane"></i> Send Notification
					</button>

					<button type="button" class="btn btn-cancel" id="cancelModal">
						Cancel
					</button>
				</div>
			</form>
		</div>
	</div>

    <footer>
        <div class="footer-container">
            <div class="footer-brand">
                <div class="logo">
                    <img src="mapua-logo.png" alt="Mapúa Logo"> 
                    <span>RedTrace</span>
                </div>
                <p>Empowering our university community to save lives through efficient blood donation management and transparent tracking systems.</p>
                <div class="social-icons">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>
            
            
            <div class="footer-contact">
                <h4>Contact Us</h4>
                <p><i class="fa-solid fa-location-dot"></i> University Medical Center Room 201, Main Campus</p>
                <p><i class="fa-solid fa-phone"></i> +1 (555) 123-4567</p>
                <p><i class="fa-solid fa-envelope"></i> info@redtrace.edu</p>
                <p><i class="fa-solid fa-clock"></i> Mon-Fri: 9AM - 5PM</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 RedTrace. All rights reserved.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>
