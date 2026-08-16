<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Project Messages</h1>

<ul id="messageList">
<?php foreach($messages as $m): ?>
    <li>
        <?= htmlspecialchars($m['message']) ?> - <?= $m['created_at'] ?>
    </li>
<?php endforeach; ?>
</ul>

<textarea id="messageBody" placeholder="Write a message"></textarea>
<button onclick="postMessage(<?= $project_id ?>)">Post Message</button>

<script>
function postMessage(project_id){
    var body = document.getElementById('messageBody').value;
    if(!body) return;

    var xhr = new XMLHttpRequest();
    xhr.open('POST','ClientController.php?action=post_message',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload = function(){
        var res = JSON.parse(this.responseText);
        alert(res.success ? 'Message posted' : 'Failed');
        if(res.success) location.reload();
    };
    xhr.send('project_id='+project_id+'&message='+encodeURIComponent(body));
}
</script>