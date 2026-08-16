<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Project Comments</h1>

<ul>
<?php foreach($comments as $c): ?>
    <li>
        <?= htmlspecialchars($c['body']) ?> - <?= $c['created_at'] ?> 
        (User ID: <?= $c['user_id'] ?>)
    </li>
<?php endforeach; ?>
</ul>

<p>All comments are visible, including internal comments for team lead review.</p>