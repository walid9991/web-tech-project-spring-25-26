<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Client Dashboard</h1>

<p>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</p>

<h2>Your Projects</h2>

<ul>
<?php foreach($projects as $p): ?>
    <li>
        <?= htmlspecialchars($p['name']) ?> -
        <a href="ClientController.php?action=milestones&project_id=<?= $p['id'] ?>">View Milestones</a> |
        <a href="ClientController.php?action=task_board&project_id=<?= $p['id'] ?>">View Tasks</a> |
        <a href="ClientController.php?action=messages&project_id=<?= $p['id'] ?>">Messages</a> |
        <a href="ClientController.php?action=files&project_id=<?= $p['id'] ?>">Files</a> |
        <a href="ClientController.php?action=time_summary&project_id=<?= $p['id'] ?>">Time Summary</a>
    </li>
<?php endforeach; ?>
</ul>