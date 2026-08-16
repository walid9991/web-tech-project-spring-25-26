<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Notifications</h1>

<ul>
<?php foreach($notes as $n): ?>
    <li style="<?= $n['is_read']?'':'font-weight:bold' ?>">
        <?= htmlspecialchars($n['message']) ?> - <?= $n['created_at'] ?>
        <?php if(!$n['is_read']): ?>
            <button onclick="markRead(<?= $n['id'] ?>)">Mark as Read</button>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>

<script>
function markRead(note_id){
    var xhr=new XMLHttpRequest();
    xhr.open('POST','MemberController.php?action=mark_notification',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){
        var res=JSON.parse(this.responseText);
        if(res.success) location.reload(); else alert('Failed');
    };
    xhr.send('note_id='+note_id);
}
</script>