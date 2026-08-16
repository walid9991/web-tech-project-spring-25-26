<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Platform Activity Logs</h1>

<form method="get" action="AdminController.php">
    <input type="hidden" name="action" value="activity_logs">
    From: <input type="date" name="date_from" value="<?= $_GET['date_from']??'' ?>">
    To: <input type="date" name="date_to" value="<?= $_GET['date_to']??'' ?>">
    Action Type: <input type="text" name="action_type" value="<?= $_GET['action_type']??'' ?>">
    <button type="submit">Filter</button>
</form>

<table border="1" cellpadding="5">
<tr><th>ID</th><th>User</th><th>Workspace</th><th>Action</th><th>Description</th><th>Time</th></tr>
<?php foreach($logs as $l): ?>
<tr>
    <td><?= $l['id'] ?></td>
    <td><?= htmlspecialchars($l['user_name']) ?></td>
    <td><?= htmlspecialchars($l['workspace_name']) ?></td>
    <td><?= $l['action_type'] ?></td>
    <td><?= htmlspecialchars($l['description']) ?></td>
    <td><?= $l['created_at'] ?></td>
</tr>
<?php endforeach; ?>
</table>