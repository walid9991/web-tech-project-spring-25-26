<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Tasks for Project</h1>

<table border="1" cellpadding="5">
<tr>
    <th>ID</th>
    <th>Title</th>
    <th>Assigned To</th>
    <th>Priority</th>
    <th>Status</th>
    <th>Due Date</th>
    <th>Estimated Hours</th>
    <th>Actions</th>
</tr>
<?php foreach($tasks as $t): ?>
<tr>
    <td><?= $t['id'] ?></td>
    <td><?= htmlspecialchars($t['title']) ?></td>
    <td><?= htmlspecialchars($t['assigned_name']) ?></td>
    <td><?= $t['priority'] ?></td>
    <td><?= $t['status'] ?></td>
    <td><?= $t['due_date'] ?></td>
    <td><?= $t['estimated_hours'] ?></td>
    <td>
        <a href="TeamLeadController.php?action=update_task&task_id=<?= $t['id'] ?>">Edit</a> |
        <button onclick="deleteTask(<?= $t['id'] ?>)">Delete</button>
    </td>
</tr>
<?php endforeach; ?>
</table>

<h2>Create New Task</h2>
<form method="post" action="TeamLeadController.php?action=create_task">
    <input type="hidden" name="project_id" value="<?= $_GET['project_id'] ?? '' ?>">
    <label>Title:</label><br>
    <input type="text" name="title" required><br><br>
    <label>Description:</label><br>
    <textarea name="description"></textarea><br><br>
    <label>Assign To (User ID):</label><br>
    <input type="number" name="assigned_to" required><br><br>
    <label>Priority:</label><br>
    <select name="priority">
        <option value="low">Low</option>
        <option value="medium" selected>Medium</option>
        <option value="high">High</option>
        <option value="critical">Critical</option>
    </select><br><br>
    <label>Due Date:</label><br>
    <input type="date" name="due_date"><br><br>
    <label>Estimated Hours:</label><br>
    <input type="number" name="estimated_hours" step="0.1"><br><br>
    <label>Milestone ID (optional):</label><br>
    <input type="number" name="milestone_id"><br><br>
    <button type="submit">Create Task</button>
</form>

<script>
function deleteTask(task_id){
    if(!confirm('Are you sure you want to delete this task?')) return;
    var xhr=new XMLHttpRequest();
    xhr.open('POST','TeamLeadController.php?action=delete_task',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){ var res=JSON.parse(this.responseText); if(res.success) location.reload(); else alert('Failed to delete task'); };
    xhr.send('task_id='+task_id);
}
</script>