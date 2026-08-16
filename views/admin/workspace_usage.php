<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Workspace Usage Report</h1>

<table border="1" cellpadding="5">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Tasks</th>
    <th>Active Members</th>
    <th>Storage Used (MB)</th>
</tr>
<?php foreach($usage as $u): ?>
<tr>
    <td><?= $u['id'] ?></td>
    <td><?= htmlspecialchars($u['name']) ?></td>
    <td><?= $u['task_count'] ?></td>
    <td><?= $u['active_members'] ?></td>
    <td><?= round($u['storage_used_bytes']/1024/1024,2) ?></td>
</tr>
<?php endforeach; ?>
</table>