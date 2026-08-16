<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Personal Productivity Summary</h1>

<p>Tasks Completed This Week: <?= $tasks_completed_this_week ?? 0 ?></p>
<p>Total Hours Logged: <?= $total_hours_logged ?? 0 ?></p>
<p>On-Time Completion Rate: <?= $on_time_completion_rate ?? 0 ?>%</p>

<!-- Optional: can add charts or tables if data available -->