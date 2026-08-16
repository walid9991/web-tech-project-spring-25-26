<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Task Board</h1>

<div style="display:flex;gap:20px;">
    <?php
    $statuses = ['todo'=>'To Do','in_progress'=>'In Progress','review'=>'Review','done'=>'Done'];
    foreach($statuses as $key => $label): ?>
    <div style="flex:1;border:1px solid #ccc;padding:10px;">
        <h3><?= $label ?></h3>
        <?php foreach($tasks as $t):
            if($t['status']==$key): ?>
                <div style="border:1px solid #aaa;margin:5px;padding:5px;">
                    <a href="MemberController.php?action=task_detail&task_id=<?= $t['id'] ?>"><?= htmlspecialchars($t['title']) ?></a>
                    <br>
                    <select onchange="updateStatus(<?= $t['id'] ?>,this.value)">
                        <?php foreach($statuses as $skey=>$slabel): ?>
                            <option value="<?= $skey ?>" <?= $t['status']==$skey?'selected':'' ?>><?= $slabel ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button onclick="flagTask(<?= $t['id'] ?>)">Flag Blocked</button>
                    <button onclick="unflagTask(<?= $t['id'] ?>)">Unflag</button>
                </div>
        <?php endif; endforeach; ?>
    </div>
    <?php endforeach; ?>
</div>

<script>
function updateStatus(task_id,status){
    var xhr=new XMLHttpRequest();
    xhr.open('POST','MemberController.php?action=update_task_status',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){ var res=JSON.parse(this.responseText); if(!res.success) alert('Failed to update status'); location.reload(); };
    xhr.send('task_id='+task_id+'&status='+status);
}

function flagTask(task_id){
    var reason=prompt("Enter reason for blocking task:");
    if(!reason) return;
    var xhr=new XMLHttpRequest();
    xhr.open('POST','MemberController.php?action=flag_task',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){ var res=JSON.parse(this.responseText); alert(res.success?'Task flagged':'Failed'); location.reload(); };
    xhr.send('task_id='+task_id+'&reason='+encodeURIComponent(reason));
}

function unflagTask(task_id){
    var xhr=new XMLHttpRequest();
    xhr.open('POST','MemberController.php?action=unflag_task',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){ var res=JSON.parse(this.responseText); alert(res.success?'Task unflagged':'Failed'); location.reload(); };
    xhr.send('task_id='+task_id);
}
</script>