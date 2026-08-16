<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Project Analytics</h1>

<p>Total Tasks: <?= $analytics['total_tasks'] ?></p>
<p>Tasks Completed: <?= $analytics['completed_tasks'] ?></p>

<p>Completion Rate: 
<?= $analytics['total_tasks']>0 ? round(($analytics['completed_tasks']/$analytics['total_tasks'])*100,2) : 0 ?>%</p>