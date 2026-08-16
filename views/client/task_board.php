<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Task Board (Read-Only)</h1>

<div style="display:flex;gap:20px;">
<?php
$statuses = ['todo'=>'To Do','in_progress'=>'In Progress','review'=>'Review','done'=>'Done'];
foreach($statuses as $key => $label): ?>
    <div style="flex:1;border:1px solid #ccc;padding:10px;">
        <h3><?= $label ?></h3>
        <?php foreach($tasks as $t): 
            if($t['status']==$key): ?>
                <div style="border:1px solid #aaa;margin:5px;padding:5px;">
                    <strong><?= htmlspecialchars($t['title']) ?></strong><br>
                    Assigned: <?= htmlspecialchars($t['assigned_name']) ?><br>
                    Priority: <?= $t['priority'] ?>
                </div>
        <?php endif; endforeach; ?>
    </div>
<?php endforeach; ?>
</div>