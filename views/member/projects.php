<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Projects in Workspace</h1>

<ul>
<?php foreach($projects as $p): ?>
    <li>
        <?= htmlspecialchars($p['name']) ?> 
        <a href="MemberController.php?action=task_board&project_id=<?= $p['id'] ?>">View Tasks</a>
    </li>
<?php endforeach; ?>
</ul>