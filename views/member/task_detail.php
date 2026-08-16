<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Task Detail</h1>

<h3><?= htmlspecialchars($task['title']) ?></h3>
<p><?= htmlspecialchars($task['description']) ?></p>
<p>Priority: <?= $task['priority'] ?></p>
<p>Status: <?= $task['status'] ?></p>
<p>Due Date: <?= $task['due_date'] ?></p>
<p>Estimated Hours: <?= $task['estimated_hours'] ?></p>

<h4>Attachments</h4>
<ul>
<?php foreach($attachments as $f): ?>
    <li><a href="<?= htmlspecialchars($f['file_path']) ?>" target="_blank"><?= htmlspecialchars($f['file_name']) ?></a></li>
<?php endforeach; ?>
</ul>

<h4>Comments</h4>
<ul id="commentList">
<?php foreach($comments as $c): ?>
    <li>
        <?= htmlspecialchars($c['body']) ?> - <?= $c['created_at'] ?>
        <?php if($c['user_id']==$_SESSION['user_id']): ?>
            <button onclick="deleteComment(<?= $c['id'] ?>)">Delete</button>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>

<textarea id="commentBody" placeholder="Write a comment"></textarea>
<button onclick="addComment(<?= $task['id'] ?>)">Post Comment</button>

<h4>Log Time</h4>
<input type="number" id="hours" step="0.1" placeholder="Hours">
<input type="text" id="note" placeholder="Optional note">
<button onclick="logTime(<?= $task['id'] ?>)">Log</button>

<script>
function addComment(task_id){
    var body=document.getElementById('commentBody').value;
    if(!body) return;
    var xhr=new XMLHttpRequest();
    xhr.open('POST','MemberController.php?action=add_comment',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){ var res=JSON.parse(this.responseText); if(res.success) location.reload(); else alert('Failed'); };
    xhr.send('task_id='+task_id+'&body='+encodeURIComponent(body));
}

function deleteComment(comment_id){
    var xhr=new XMLHttpRequest();
    xhr.open('POST','MemberController.php?action=delete_comment',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){ var res=JSON.parse(this.responseText); if(res.success) location.reload(); else alert('Failed'); };
    xhr.send('comment_id='+comment_id);
}

function logTime(task_id){
    var hours=document.getElementById('hours').value;
    var note=document.getElementById('note').value;
    var xhr=new XMLHttpRequest();
    xhr.open('POST','MemberController.php?action=log_time',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){ var res=JSON.parse(this.responseText); alert(res.success?'Time logged':'Failed'); location.reload(); };
    xhr.send('task_id='+task_id+'&hours='+hours+'&note='+encodeURIComponent(note));
}
</script>