<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Platform Analytics</h1>

<ul>
    <li>Active Users: <?= $analytics['active_users'] ?></li>
    <li>Total Tasks: <?= $analytics['total_tasks'] ?></li>
    <li>Tasks Completed: <?= $analytics['tasks_done'] ?></li>
    <li>Total Workspaces: <?= $analytics['workspace_count'] ?></li>
</ul>