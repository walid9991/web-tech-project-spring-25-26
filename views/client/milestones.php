<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Milestones for Project</h1>

<ul>
<?php foreach($milestones as $m): ?>
    <li>
        <?= htmlspecialchars($m['title']) ?> -
        Due: <?= $m['due_date'] ?> -
        Status: <?= $m['status'] ?> -
        <a href="ClientController.php?action=milestone_detail&milestone_id=<?= $m['id'] ?>">View Details / Feedback</a>
    </li>
<?php endforeach; ?>
</ul>