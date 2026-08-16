<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Project Time Report</h1>

<table border="1" cellpadding="5">
<tr>
    <th>Member Name</th>
    <th>Total Hours Logged</th>
</tr>
<?php foreach($time_report as $tr): ?>
<tr>
    <td><?= htmlspecialchars($tr['member_name']) ?></td>
    <td><?= $tr['total_hours'] ?></td>
</tr>
<?php endforeach; ?>
</table>