<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Project Members</h1>

<table border="1" cellpadding="5">
<tr>
    <th>User ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Assigned At</th>
</tr>
<?php foreach($members as $m): ?>
<tr>
    <td><?= $m['id'] ?></td>
    <td><?= htmlspecialchars($m['name']) ?></td>
    <td><?= htmlspecialchars($m['email']) ?></td>
    <td><?= $m['assigned_at'] ?></td>
</tr>
<?php endforeach; ?>
</table>