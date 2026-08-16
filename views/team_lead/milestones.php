<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Milestones for Project</h1>

<ul>
<?php foreach($milestones as $m): ?>
    <li>
        <?= htmlspecialchars($m['title']) ?> -
        Due: <?= $m['due_date'] ?> -
        Status: <?= $m['status'] ?> 
        <?php if($m['status'] != 'completed'): ?>
            <button onclick="completeMilestone(<?= $m['id'] ?>)">Mark Completed</button>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>

<h2>Create New Milestone</h2>
<form method="post" action="TeamLeadController.php?action=create_milestone">
    <input type="hidden" name="project_id" value="<?= $_GET['project_id'] ?? '' ?>">
    <label>Title:</label><br>
    <input type="text" name="title" required><br><br>
    <label>Description:</label><br>
    <textarea name="description"></textarea><br><br>
    <label>Due Date:</label><br>
    <input type="date" name="due_date"><br><br>
    <label>Client Visible:</label>
    <input type="checkbox" name="is_client_visible" value="1"><br><br>
    <button type="submit">Create Milestone</button>
</form>

<script>
function completeMilestone(id){
    if(!confirm('Mark milestone as completed?')) return;
    var xhr=new XMLHttpRequest();
    xhr.open('POST','TeamLeadController.php?action=complete_milestone',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){ var res=JSON.parse(this.responseText); if(res.success) location.reload(); else alert('Failed'); };
    xhr.send('milestone_id='+id);
}
</script>