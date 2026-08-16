<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Workspace Members</h1>

<table border="1" cellpadding="5">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Joined At</th>
    <th>Action</th>
</tr>
<?php foreach($members as $m): ?>
<tr>
    <td><?= $m['id'] ?></td>
    <td><?= htmlspecialchars($m['name']) ?></td>
    <td><?= htmlspecialchars($m['email']) ?></td>
    <td><?= $m['workspace_role'] ?></td>
    <td><?= $m['joined_at'] ?></td>
    <td>
        <button onclick="removeMember(<?= $m['id'] ?>)">Remove</button>
    </td>
</tr>
<?php endforeach; ?>
</table>

<script>
function removeMember(id){
    if(!confirm('Remove this member?')) return;
    var xhr=new XMLHttpRequest();
    xhr.open('POST','AdminController.php?action=workspace_action',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){
        var res=JSON.parse(this.responseText);
        alert(res.message);
        if(res.success) location.reload();
    };
    xhr.send('type=remove_member&id='+id);
}
</script>