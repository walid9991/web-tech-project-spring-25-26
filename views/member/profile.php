<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>My Profile</h1>

<?php if(isset($msg)) echo "<p style='color:green'>".htmlspecialchars($msg)."</p>"; ?>

<form method="post" action="MemberController.php?action=update_profile">
    <label>Name:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($profile['name']) ?>"><br><br>
    <label>Phone:</label><br>
    <input type="text" name="phone" value="<?= htmlspecialchars($profile['phone']) ?>"><br><br>
    <button type="submit">Update Profile</button>
</form>

<h2>Change Password</h2>
<form method="post" action="MemberController.php?action=change_password">
    <label>Current Password:</label><br>
    <input type="password" name="current"><br><br>
    <label>New Password:</label><br>
    <input type="password" name="new"><br><br>
    <label>Confirm New Password:</label><br>
    <input type="password" name="confirm"><br><br>
    <button type="submit">Change Password</button>
</form>