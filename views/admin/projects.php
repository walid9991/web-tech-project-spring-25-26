<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>All Projects</h1>

<form method="get" action="AdminController.php">
    <input type="hidden" name="action" value="projects">
    <input type="number" name="workspace_id" placeholder="Workspace ID" value="<?= $_GET['workspace_id']??'' ?>">
    <select name="status">
        <option value="">All Status</option>
        <option value="planning" <?= ($_GET['status']??'')=='planning'?'selected':'' ?>>Planning</option>
        <option value="active" <?= ($_GET['status']??'')=='active'?'selected':'' ?>>Active</option>
        <option value="on_hold" <?= ($_GET['status']??'')=='on_hold'?'selected':'' ?>>On Hold</option>
        <option value="completed" <?= ($_GET['status']??'')=='completed'?'selected':'' ?>>Completed</option>
    </select>
    <input type="number" name="team_lead_id" placeholder="Team Lead ID" value="<?= $_GET['team_lead_id']??'' ?>">
    <button type="submit">Filter</button>
</form>

<table border="1" cellpadding="5">
<tr>
    <th>ID</th><th>Name</th><th>Client</th><th>Status</th><th>Deadline</th>
</tr>
<?php foreach($projects as $p): ?>
<tr>
    <td><?= $p['id'] ?></td>
    <td><?= htmlspecialchars($p['name']) ?></td>
    <td><?= htmlspecialchars($p['client_name']) ?></td>
    <td><?= $p['status'] ?></td>
    <td><?= $p['deadline'] ?></td>
</tr>
<?php endforeach; ?>
</table>