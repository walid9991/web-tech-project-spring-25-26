<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>All Tasks</h1>

<form method="get" action="AdminController.php">
    <input type="hidden" name="action" value="tasks">
    <select name="status">
        <option value="">All Status</option>
        <option value="todo" <?= ($_GET['status']??'')=='todo'?'selected':'' ?>>To Do</option>
        <option value="in_progress" <?= ($_GET['status']??'')=='in_progress'?'selected':'' ?>>In Progress</option>
        <option value="review" <?= ($_GET['status']??'')=='review'?'selected':'' ?>>Review</option>
        <option value="done" <?= ($_GET['status']??'')=='done'?'selected':'' ?>>Done</option>
    </select>
    <select name="priority">
        <option value="">All Priority</option>
        <option value="low" <?= ($_GET['priority']??'')=='low'?'selected':'' ?>>Low</option>
        <option value="medium" <?= ($_GET['priority']??'')=='medium'?'selected':'' ?>>Medium</option>
        <option value="high" <?= ($_GET['priority']??'')=='high'?'selected':'' ?>>High</option>
        <option value="critical" <?= ($_GET['priority']??'')=='critical'?'selected':'' ?>>Critical</option>
    </select>
    <input type="number" name="assigned_to" placeholder="Assignee ID" value="<?= $_GET['assigned_to']??'' ?>">
    <button type="submit">Filter</button>
</form>

<table border="1" cellpadding="5">
<tr>
    <th>ID</th><th>Title</th><th>Project</th><th>Assignee</th><th>Status</th><th>Priority</th><th>Actions</th>
</tr>
<?php foreach($tasks as $t): ?>
<tr>
    <td><?= $t['id'] ?></td>
    <td><?= htmlspecialchars($t['title']) ?></td>
    <td><?= htmlspecialchars($t['project_name']) ?></td>
    <td><?= htmlspecialchars($t['assignee_name']) ?></td>
    <td><?= $t['status'] ?></td>
    <td><?= $t['priority'] ?></td>
    <td>
        <button onclick="deleteTask(<?= $t['id'] ?>)">Delete</button>
    </td>
</tr>
<?php endforeach; ?>
</table>

<script>
function deleteTask(id){
    if(!confirm('Delete this task?')) return;
    var xhr=new XMLHttpRequest();
    xhr.open('POST','AdminController.php?action=task_action',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){
        var res=JSON.parse(this.responseText);
        alert(res.message);
        if(res.success) location.reload();
    };
    xhr.send('type=delete&id='+id);
}
</script>