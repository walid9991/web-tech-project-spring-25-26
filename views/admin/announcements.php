<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>System Announcements</h1>

<button onclick="showCreateForm()">Create Announcement</button>

<div id="createForm" style="display:none">
    <input type="text" id="announcementTitle" placeholder="Title">
    <textarea id="announcementMessage" placeholder="Message"></textarea>
    <button onclick="createAnnouncement()">Post</button>
</div>

<table border="1" cellpadding="5">
<tr><th>ID</th><th>Title</th><th>Message</th><th>Created At</th></tr>
<?php foreach($announcements as $a): ?>
<tr>
    <td><?= $a['id'] ?></td>
    <td><?= htmlspecialchars($a['title']) ?></td>
    <td><?= htmlspecialchars($a['message']) ?></td>
    <td><?= $a['created_at'] ?></td>
</tr>
<?php endforeach; ?>
</table>

<script>
function showCreateForm(){ document.getElementById('createForm').style.display='block'; }

function createAnnouncement(){
    var title=document.getElementById('announcementTitle').value;
    var message=document.getElementById('announcementMessage').value;

    var xhr=new XMLHttpRequest();
    xhr.open('POST','AdminController.php?action=announcement_action',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){
        var res=JSON.parse(this.responseText);
        alert(res.message);
        if(res.success) location.reload();
    };
    xhr.send('title='+encodeURIComponent(title)+'&message='+encodeURIComponent(message));
}
</script>