<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Projects Assigned to You</h1>

<ul>
<?php foreach($projects as $p): ?>
    <li>
        <?= htmlspecialchars($p['name']) ?> -
        <a href="ClientController.php?action=milestones&project_id=<?= $p['id'] ?>">View Milestones</a>
    </li>
<?php endforeach; ?>
</ul>